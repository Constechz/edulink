<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Term;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * List all invoices with metrics and filters.
     */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $query = Invoice::where('school_id', $schoolId)->with(['student', 'term', 'academicYear']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('class_id')) {
            $classId = $request->class_id;
            $query->whereHas('student', function ($q) use ($classId) {
                $q->where('current_class_id', $classId);
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('student_id_number', 'like', "%{$search}%");
                    });
            });
        }

        $invoices = $query->latest()->paginate(25);

        // Metrics calculations
        $metrics = [
            'total_invoiced' => Invoice::where('school_id', $schoolId)->sum('total_amount'),
            'total_paid' => Invoice::where('school_id', $schoolId)->sum('amount_paid'),
            'total_balance' => Invoice::where('school_id', $schoolId)->sum('balance'),
        ];

        $classes = SchoolClass::where('school_id', $schoolId)->get();

        return view('school.invoices.index', compact('invoices', 'metrics', 'classes'));
    }

    /**
     * Show page to create individual or bulk invoices.
     */
    public function create(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $academicYears = AcademicYear::where('school_id', $schoolId)->get();
        $terms = Term::where('school_id', $schoolId)->get();
        $classes = SchoolClass::where('school_id', $schoolId)->get();
        $feeStructures = FeeStructure::where('school_id', $schoolId)->get();
        
        $students = Student::where('school_id', $schoolId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('school.invoices.create', compact('academicYears', 'terms', 'classes', 'feeStructures', 'students'));
    }

    /**
     * Store individual invoice.
     */
    public function store(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'required|exists:terms,id',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
            'fees' => 'required|array|min:1',
            'fees.*.id' => 'required|exists:fee_structures,id',
            'fees.*.discount' => 'nullable|numeric|min:0',
        ]);

        $student = Student::where('id', $request->student_id)->where('school_id', $schoolId)->firstOrFail();

        try {
            DB::transaction(function () use ($request, $schoolId, $student) {
                // Generate invoice number
                $invoiceNumber = 'INV-' . date('Y') . '-' . sprintf('%05d', mt_rand(1, 99999));

                $invoice = Invoice::create([
                    'school_id' => $schoolId,
                    'campus_id' => $student->campus_id,
                    'student_id' => $student->id,
                    'academic_year_id' => $request->academic_year_id,
                    'term_id' => $request->term_id,
                    'invoice_number' => $invoiceNumber,
                    'due_date' => $request->due_date,
                    'notes' => $request->notes,
                    'status' => 'pending',
                    'created_by' => $request->user()->id,
                ]);

                $totalAmount = 0;

                foreach ($request->fees as $feeInput) {
                    $fee = FeeStructure::findOrFail($feeInput['id']);
                    $discount = isset($feeInput['discount']) ? floatval($feeInput['discount']) : 0;
                    $net = max(0, $fee->amount - $discount);

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'fee_structure_id' => $fee->id,
                        'description' => $fee->name,
                        'amount' => $fee->amount,
                        'discount_amount' => $discount,
                        'net_amount' => $net,
                    ]);

                    $totalAmount += $net;
                }

                $invoice->update([
                    'total_amount' => $totalAmount,
                    'balance' => $totalAmount,
                ]);

                // Ensure default accounts exist
                $this->ensureDefaultAccounts($schoolId);

                // double-entry bookkeeping: Debit Accounts Receivable (1400), Credit Tuition Revenue (4100)
                $debitAccount = \App\Models\ChartOfAccount::where('school_id', $schoolId)->where('account_code', '1400')->firstOrFail();
                $creditAccount = \App\Models\ChartOfAccount::where('school_id', $schoolId)->where('account_code', '4100')->firstOrFail();

                $journalEntry = \App\Models\JournalEntry::create([
                    'school_id' => $schoolId,
                    'entry_date' => now(),
                    'reference' => $invoiceNumber,
                    'description' => "Invoice " . $invoiceNumber . " for student " . $student->first_name . " " . $student->last_name,
                    'created_by' => $request->user()->id,
                    'status' => 'posted',
                ]);

                \App\Models\JournalLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $debitAccount->id,
                    'debit' => $totalAmount,
                    'credit' => 0.00,
                    'description' => "Tuition and fees receivable",
                ]);

                \App\Models\JournalLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $creditAccount->id,
                    'debit' => 0.00,
                    'credit' => $totalAmount,
                    'description' => "Tuition revenue recognition",
                ]);
            });

            return redirect()->route('school.finance.invoices.index')->with('success', 'Student invoice created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to generate invoice: ' . $e->getMessage()]);
        }
    }

    /**
     * Store bulk class billing invoices.
     */
    public function bulkStore(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'required|exists:terms,id',
            'due_date' => 'required|date',
            'fee_structure_ids' => 'required|array|min:1',
            'fee_structure_ids.*' => 'required|exists:fee_structures,id',
        ]);

        $students = Student::where('school_id', $schoolId)
            ->where('current_class_id', $request->class_id)
            ->where('status', 'active')
            ->get();

        if ($students->isEmpty()) {
            return redirect()->back()->withErrors(['error' => 'No active students found in the selected class.']);
        }

        $fees = FeeStructure::whereIn('id', $request->fee_structure_ids)->get();

        try {
            DB::transaction(function () use ($request, $schoolId, $students, $fees) {
                foreach ($students as $student) {
                    $invoiceNumber = 'INV-' . date('Y') . '-' . sprintf('%05d', mt_rand(1, 99999));

                    $invoice = Invoice::create([
                        'school_id' => $schoolId,
                        'campus_id' => $student->campus_id,
                        'student_id' => $student->id,
                        'academic_year_id' => $request->academic_year_id,
                        'term_id' => $request->term_id,
                        'invoice_number' => $invoiceNumber,
                        'due_date' => $request->due_date,
                        'status' => 'pending',
                        'created_by' => $request->user()->id,
                    ]);

                    $totalAmount = 0;

                    foreach ($fees as $fee) {
                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'fee_structure_id' => $fee->id,
                            'description' => $fee->name,
                            'amount' => $fee->amount,
                            'discount_amount' => 0,
                            'net_amount' => $fee->amount,
                        ]);

                        $totalAmount += $fee->amount;
                    }

                    $invoice->update([
                        'total_amount' => $totalAmount,
                        'balance' => $totalAmount,
                    ]);

                    // Ensure default accounts exist
                    $this->ensureDefaultAccounts($schoolId);

                    // double-entry bookkeeping: Debit Accounts Receivable (1400), Credit Tuition Revenue (4100)
                    $debitAccount = \App\Models\ChartOfAccount::where('school_id', $schoolId)->where('account_code', '1400')->firstOrFail();
                    $creditAccount = \App\Models\ChartOfAccount::where('school_id', $schoolId)->where('account_code', '4100')->firstOrFail();

                    $journalEntry = \App\Models\JournalEntry::create([
                        'school_id' => $schoolId,
                        'entry_date' => now(),
                        'reference' => $invoiceNumber,
                        'description' => "Invoice " . $invoiceNumber . " (Bulk billing) for student " . $student->first_name . " " . $student->last_name,
                        'created_by' => $request->user()->id,
                        'status' => 'posted',
                    ]);

                    \App\Models\JournalLine::create([
                        'journal_entry_id' => $journalEntry->id,
                        'account_id' => $debitAccount->id,
                        'debit' => $totalAmount,
                        'credit' => 0.00,
                        'description' => "Tuition and fees receivable (bulk)",
                    ]);

                    \App\Models\JournalLine::create([
                        'journal_entry_id' => $journalEntry->id,
                        'account_id' => $creditAccount->id,
                        'debit' => 0.00,
                        'credit' => $totalAmount,
                        'description' => "Tuition revenue recognition (bulk)",
                    ]);
                }
            });

            return redirect()->route('school.finance.invoices.index')->with('success', 'Bulk billing generated successfully for ' . $students->count() . ' students.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to generate bulk invoices: ' . $e->getMessage()]);
        }
    }

    /**
     * Show invoice details and receipt options.
     */
    public function show(Request $request, Invoice $invoice)
    {
        $schoolId = $request->user()->school_id;

        if ($invoice->school_id !== $schoolId) {
            abort(403);
        }

        $invoice->load(['student', 'items', 'payments', 'term', 'academicYear']);

        return view('school.invoices.show', compact('invoice'));
    }

    /**
     * Download Invoice PDF.
     */
    public function downloadPdf(Request $request, Invoice $invoice)
    {
        $schoolId = $request->user()->school_id;

        if ($invoice->school_id !== $schoolId) {
            abort(403);
        }

        $invoice->load(['student.currentClass', 'items', 'payments', 'term', 'academicYear', 'campus']);

        $pdf = Pdf::loadView('school.invoices.pdf', compact('invoice'));
        
        return $pdf->download('Invoice_' . $invoice->invoice_number . '.pdf');
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
            \App\Models\ChartOfAccount::firstOrCreate(
                ['school_id' => $schoolId, 'account_code' => $def['account_code']],
                ['account_name' => $def['account_name'], 'account_type' => $def['account_type'], 'is_active' => true]
            );
        }
    }
}
