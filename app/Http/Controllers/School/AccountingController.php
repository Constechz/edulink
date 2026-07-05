<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    /**
     * Display Chart of Accounts directory tree.
     */
    public function accountsIndex(Request $request)
    {
        $schoolId = $request->user()->school_id;

        // Ensure default accounts exist
        $this->ensureDefaultAccounts($schoolId);

        $accounts = ChartOfAccount::where('school_id', $schoolId)
            ->with(['parent', 'children'])
            ->get();

        // Group by account type
        $grouped = [
            'Asset' => $accounts->where('account_type', 'Asset'),
            'Liability' => $accounts->where('account_type', 'Liability'),
            'Equity' => $accounts->where('account_type', 'Equity'),
            'Revenue' => $accounts->where('account_type', 'Revenue'),
            'Expense' => $accounts->where('account_type', 'Expense'),
        ];

        return view('school.accounting.accounts', compact('grouped', 'accounts'));
    }

    /**
     * Store a new account.
     */
    public function accountsStore(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'account_code' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
        ]);

        // Ensure account code is unique for this school
        if (ChartOfAccount::where('school_id', $schoolId)->where('account_code', $request->account_code)->exists()) {
            return redirect()->back()->withErrors(['account_code' => 'This account code is already in use for your school.']);
        }

        ChartOfAccount::create([
            'school_id' => $schoolId,
            'account_code' => $request->account_code,
            'account_name' => $request->account_name,
            'account_type' => $request->account_type,
            'parent_id' => $request->parent_id,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Account added to Chart of Accounts successfully.');
    }

    /**
     * Display journal entry logs and creation form.
     */
    public function journalsIndex(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $journals = JournalEntry::where('school_id', $schoolId)
            ->with(['lines.account', 'creator'])
            ->latest()
            ->paginate(15);

        $accounts = ChartOfAccount::where('school_id', $schoolId)->where('is_active', true)->get();

        return view('school.accounting.journals', compact('journals', 'accounts'));
    }

    /**
     * Store a manual Journal Entry.
     */
    public function journalsStore(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'entry_date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'description' => 'required|string|max:1000',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
            'lines.*.description' => 'nullable|string|max:255',
        ]);

        $totalDebits = 0;
        $totalCredits = 0;
        $formattedLines = [];

        foreach ($request->lines as $line) {
            $debit = isset($line['debit']) ? floatval($line['debit']) : 0.00;
            $credit = isset($line['credit']) ? floatval($line['credit']) : 0.00;

            if ($debit == 0 && $credit == 0) {
                continue; // Skip empty rows
            }

            // Verify account belongs to school
            $account = ChartOfAccount::where('id', $line['account_id'])
                ->where('school_id', $schoolId)
                ->firstOrFail();

            $totalDebits += $debit;
            $totalCredits += $credit;

            $formattedLines[] = [
                'account_id' => $account->id,
                'debit' => $debit,
                'credit' => $credit,
                'description' => $line['description'] ?? null,
            ];
        }

        // Validate double entry constraints
        if (count($formattedLines) < 2) {
            return redirect()->back()->withInput()->withErrors(['lines' => 'A journal entry must contain at least two non-empty debit/credit lines.']);
        }

        // Match exact amounts with padding for float comparison
        if (abs($totalDebits - $totalCredits) > 0.001) {
            return redirect()->back()->withInput()->withErrors(['lines' => 'Unbalanced entry: Total debits (GHS ' . number_format($totalDebits, 2) . ') must equal total credits (GHS ' . number_format($totalCredits, 2) . '). Diff: GHS ' . number_format(abs($totalDebits - $totalCredits), 2)]);
        }

        if ($totalDebits <= 0) {
            return redirect()->back()->withInput()->withErrors(['lines' => 'Total transaction amount must be greater than zero.']);
        }

        try {
            DB::transaction(function () use ($request, $schoolId, $formattedLines) {
                $journalEntry = JournalEntry::create([
                    'school_id' => $schoolId,
                    'entry_date' => $request->entry_date,
                    'reference' => $request->reference,
                    'description' => $request->description,
                    'created_by' => $request->user()->id,
                    'status' => 'posted', // Auto-post for ease of demo flow
                ]);

                foreach ($formattedLines as $line) {
                    JournalLine::create([
                        'journal_entry_id' => $journalEntry->id,
                        'account_id' => $line['account_id'],
                        'debit' => $line['debit'],
                        'credit' => $line['credit'],
                        'description' => $line['description'],
                    ]);
                }
            });

            return redirect()->route('school.finance.journals.index')->with('success', 'Journal entry posted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to save entry: ' . $e->getMessage()]);
        }
    }

    /**
     * Compute financial reports: Trial Balance, Income Statement, Balance Sheet.
     */
    public function reportsIndex(Request $request)
    {
        $schoolId = $request->user()->school_id;

        // Fetch accounts and compute balance metrics based on posted journal entries
        $accounts = ChartOfAccount::where('school_id', $schoolId)->get();

        $balances = DB::table('journal_lines')
            ->join('journal_entries', 'journal_lines.journal_entry_id', '=', 'journal_entries.id')
            ->join('chart_of_accounts', 'journal_lines.account_id', '=', 'chart_of_accounts.id')
            ->where('chart_of_accounts.school_id', $schoolId)
            ->where('journal_entries.status', 'posted')
            ->select(
                'chart_of_accounts.id as account_id',
                'chart_of_accounts.account_name',
                'chart_of_accounts.account_code',
                'chart_of_accounts.account_type',
                DB::raw('SUM(journal_lines.debit) as total_debit'),
                DB::raw('SUM(journal_lines.credit) as total_credit')
            )
            ->groupBy('chart_of_accounts.id', 'chart_of_accounts.account_name', 'chart_of_accounts.account_code', 'chart_of_accounts.account_type')
            ->get()
            ->keyBy('account_id');

        // Compile Trial Balance Rows
        $trialBalance = [];
        $tbTotalDebit = 0;
        $tbTotalCredit = 0;

        foreach ($accounts as $acc) {
            $bal = $balances->get($acc->id);
            $debitVal = $bal ? floatval($bal->total_debit) : 0;
            $creditVal = $bal ? floatval($bal->total_credit) : 0;

            $netDebit = 0;
            $netCredit = 0;

            if ($debitVal > $creditVal) {
                $netDebit = $debitVal - $creditVal;
            } elseif ($creditVal > $debitVal) {
                $netCredit = $creditVal - $debitVal;
            }

            if ($netDebit > 0 || $netCredit > 0) {
                $trialBalance[] = [
                    'account_code' => $acc->account_code,
                    'account_name' => $acc->account_name,
                    'account_type' => $acc->account_type,
                    'debit' => $netDebit,
                    'credit' => $netCredit,
                ];
                $tbTotalDebit += $netDebit;
                $tbTotalCredit += $netCredit;
            }
        }

        // Compile Income Statement Rows (Revenue vs Expense)
        $revenueItems = [];
        $expenseItems = [];
        $totalRevenue = 0;
        $totalExpense = 0;

        foreach ($accounts as $acc) {
            $bal = $balances->get($acc->id);
            $debitVal = $bal ? floatval($bal->total_debit) : 0;
            $creditVal = $bal ? floatval($bal->total_credit) : 0;

            if ($acc->account_type === 'Revenue') {
                $netRevenue = $creditVal - $debitVal; // Credit nature
                if ($netRevenue != 0) {
                    $revenueItems[] = [
                        'account_code' => $acc->account_code,
                        'account_name' => $acc->account_name,
                        'balance' => $netRevenue,
                    ];
                    $totalRevenue += $netRevenue;
                }
            } elseif ($acc->account_type === 'Expense') {
                $netExpense = $debitVal - $creditVal; // Debit nature
                if ($netExpense != 0) {
                    $expenseItems[] = [
                        'account_code' => $acc->account_code,
                        'account_name' => $acc->account_name,
                        'balance' => $netExpense,
                    ];
                    $totalExpense += $netExpense;
                }
            }
        }

        $netSurplus = $totalRevenue - $totalExpense;

        // Compile Balance Sheet Rows (Asset, Liability, Equity)
        $assetItems = [];
        $liabilityItems = [];
        $equityItems = [];
        $totalAssets = 0;
        $totalLiabilities = 0;
        $totalEquity = 0;

        foreach ($accounts as $acc) {
            $bal = $balances->get($acc->id);
            $debitVal = $bal ? floatval($bal->total_debit) : 0;
            $creditVal = $bal ? floatval($bal->total_credit) : 0;

            if ($acc->account_type === 'Asset') {
                $netAsset = $debitVal - $creditVal; // Debit nature
                if ($netAsset != 0) {
                    $assetItems[] = [
                        'account_code' => $acc->account_code,
                        'account_name' => $acc->account_name,
                        'balance' => $netAsset,
                    ];
                    $totalAssets += $netAsset;
                }
            } elseif ($acc->account_type === 'Liability') {
                $netLiability = $creditVal - $debitVal; // Credit nature
                if ($netLiability != 0) {
                    $liabilityItems[] = [
                        'account_code' => $acc->account_code,
                        'account_name' => $acc->account_name,
                        'balance' => $netLiability,
                    ];
                    $totalLiabilities += $netLiability;
                }
            } elseif ($acc->account_type === 'Equity') {
                $netEquity = $creditVal - $debitVal; // Credit nature
                if ($netEquity != 0) {
                    $equityItems[] = [
                        'account_code' => $acc->account_code,
                        'account_name' => $acc->account_name,
                        'balance' => $netEquity,
                    ];
                    $totalEquity += $netEquity;
                }
            }
        }

        // Add net surplus to Equity/Retained Earnings
        if ($netSurplus != 0) {
            $equityItems[] = [
                'account_code' => '3999',
                'account_name' => 'Retained Earnings (Net Surplus)',
                'balance' => $netSurplus,
            ];
            $totalEquity += $netSurplus;
        }

        $balanceSheetBalanced = abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01;

        return view('school.accounting.reports', compact(
            'trialBalance', 'tbTotalDebit', 'tbTotalCredit',
            'revenueItems', 'expenseItems', 'totalRevenue', 'totalExpense', 'netSurplus',
            'assetItems', 'liabilityItems', 'equityItems', 'totalAssets', 'totalLiabilities', 'totalEquity',
            'balanceSheetBalanced'
        ));
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
