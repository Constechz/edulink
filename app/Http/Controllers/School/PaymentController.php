<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $query = Payment::where('school_id', $schoolId)
            ->with(['invoice', 'student'])
            ->latest();

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->paginate(25);

        return view('school.payments.index', compact('payments'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'method' => 'required|in:cash,momo,bank_transfer,cheque,online',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $invoice = Invoice::where('id', $request->invoice_id)
            ->where('school_id', $schoolId)
            ->firstOrFail();

        // Check if amount is greater than remaining balance
        if ($request->amount > $invoice->balance) {
            return redirect()->back()->withErrors(['amount' => 'The payment amount cannot exceed the current invoice balance of GHS ' . number_format($invoice->balance, 2)]);
        }

        try {
            DB::transaction(function () use ($request, $schoolId, $invoice) {
                // Ensure default accounts exist
                $this->ensureDefaultAccounts($schoolId);

                // Generate receipt number
                $receiptNumber = 'REC-' . date('Y') . '-' . date('m') . '-' . sprintf('%04d', mt_rand(1, 9999));
                while (Payment::where('school_id', $schoolId)->where('receipt_number', $receiptNumber)->exists()) {
                    $receiptNumber = 'REC-' . date('Y') . '-' . date('m') . '-' . sprintf('%04d', mt_rand(1, 9999));
                }

                // Create payment
                $payment = Payment::create([
                    'school_id' => $schoolId,
                    'invoice_id' => $invoice->id,
                    'student_id' => $invoice->student_id,
                    'amount' => $request->amount,
                    'payment_date' => $request->payment_date,
                    'method' => $request->method,
                    'reference_number' => $request->reference_number,
                    'received_by' => $request->user()->id,
                    'receipt_number' => $receiptNumber,
                    'notes' => $request->notes,
                ]);

                // Update invoice balances
                $newPaid = $invoice->amount_paid + $request->amount;
                $newBalance = max(0, $invoice->total_amount - $newPaid);
                $status = ($newBalance <= 0) ? 'paid' : 'partial';

                $invoice->update([
                    'amount_paid' => $newPaid,
                    'balance' => $newBalance,
                    'status' => $status,
                ]);

                // double-entry bookkeeping:
                // Debit: Cash/MoMo/Bank (Asset)
                // Credit: Accounts Receivable (Asset)
                $debitAccountCode = match ($request->method) {
                    'cash' => '1100',
                    'momo' => '1200',
                    default => '1300', // bank, cheque, online
                };

                $debitAccount = ChartOfAccount::where('school_id', $schoolId)->where('account_code', $debitAccountCode)->firstOrFail();
                $creditAccount = ChartOfAccount::where('school_id', $schoolId)->where('account_code', '1400')->firstOrFail();

                $journalEntry = JournalEntry::create([
                    'school_id' => $schoolId,
                    'entry_date' => $request->payment_date,
                    'reference' => $receiptNumber,
                    'description' => "Payment of GHS " . number_format($request->amount, 2) . " for Invoice " . $invoice->invoice_number,
                    'created_by' => $request->user()->id,
                    'status' => 'posted',
                ]);

                JournalLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $debitAccount->id,
                    'debit' => $request->amount,
                    'credit' => 0.00,
                    'description' => "Received payment via " . strtoupper($request->method),
                ]);

                JournalLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $creditAccount->id,
                    'debit' => 0.00,
                    'credit' => $request->amount,
                    'description' => "Invoice receivable reduction",
                ]);
            });

            return redirect()->back()->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to record payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Reverse a payment.
     */
    public function reverse(Request $request, Payment $payment)
    {
        $schoolId = $request->user()->school_id;

        if ($payment->school_id !== $schoolId) {
            abort(403);
        }

        if ($payment->is_reversed) {
            return redirect()->back()->withErrors(['error' => 'This payment is already reversed.']);
        }

        try {
            DB::transaction(function () use ($request, $schoolId, $payment) {
                // Mark payment as reversed
                $payment->update([
                    'is_reversed' => true,
                ]);

                // Update invoice balances
                $invoice = $payment->invoice;
                $newPaid = max(0, $invoice->amount_paid - $payment->amount);
                $newBalance = $invoice->total_amount - $newPaid;
                
                $status = 'pending';
                if ($newPaid > 0) {
                    $status = 'partial';
                }

                $invoice->update([
                    'amount_paid' => $newPaid,
                    'balance' => $newBalance,
                    'status' => $status,
                ]);

                // double-entry reverse booking:
                // Debit: Accounts Receivable (Asset)
                // Credit: Cash/MoMo/Bank (Asset)
                $creditAccountCode = match ($payment->method) {
                    'cash' => '1100',
                    'momo' => '1200',
                    default => '1300',
                };

                $debitAccount = ChartOfAccount::where('school_id', $schoolId)->where('account_code', '1400')->firstOrFail();
                $creditAccount = ChartOfAccount::where('school_id', $schoolId)->where('account_code', $creditAccountCode)->firstOrFail();

                $journalEntry = JournalEntry::create([
                    'school_id' => $schoolId,
                    'entry_date' => now(),
                    'reference' => 'REV-' . $payment->receipt_number,
                    'description' => "REVERSAL of Payment " . $payment->receipt_number . " (Invoice " . $invoice->invoice_number . ")",
                    'created_by' => $request->user()->id,
                    'status' => 'posted',
                ]);

                JournalLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $debitAccount->id,
                    'debit' => $payment->amount,
                    'credit' => 0.00,
                    'description' => "Re-established receivable due to payment reversal",
                ]);

                JournalLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $creditAccount->id,
                    'debit' => 0.00,
                    'credit' => $payment->amount,
                    'description' => "Reversed payment fund deduction",
                ]);
            });

            return redirect()->back()->with('success', 'Payment reversed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to reverse payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Help ensure default accounts are available.
     */
    private function ensureDefaultAccounts($schoolId)
    {
        $defaults = [
            ['account_code' => '1100', 'account_name' => 'Cash on Hand', 'account_type' => 'Asset'],
            ['account_code' => '1200', 'account_name' => 'Mobile Money Wallet', 'account_type' => 'Asset'],
            ['account_code' => '1300', 'account_name' => 'Bank Account', 'account_type' => 'Asset'],
            ['account_code' => '1400', 'account_name' => 'Accounts Receivable', 'account_type' => 'Asset'],
            ['account_code' => '4100', 'account_name' => 'Tuition Revenue', 'account_type' => 'Revenue'],
            ['account_code' => '5100', 'account_name' => 'General Expenses', 'account_type' => 'Expense'],
        ];

        foreach ($defaults as $def) {
            ChartOfAccount::firstOrCreate(
                ['school_id' => $schoolId, 'account_code' => $def['account_code']],
                ['account_name' => $def['account_name'], 'account_type' => $def['account_type'], 'is_active' => true]
            );
        }
    }
}
