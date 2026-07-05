@extends('layouts.app')

@section('title', 'Financial Reports')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Financial Statements</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Financial Reports & Statements</h1>
                    <p class="text-muted mb-0 small">Real-time Trial Balance, Income Statement (P&L), and Balance Sheet calculated directly from double-entry logs.</p>
                </div>
            </div>

            <!-- Tabs Nav -->
            <ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-4 py-2" id="trial-balance-tab" data-bs-toggle="tab" data-bs-target="#trial-balance-pane" type="button" role="tab" aria-controls="trial-balance-pane" aria-selected="true">
                        <i class="bi bi-scale me-1"></i> Trial Balance
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-2" id="income-statement-tab" data-bs-toggle="tab" data-bs-target="#income-statement-pane" type="button" role="tab" aria-controls="income-statement-pane" aria-selected="false">
                        <i class="bi bi-graph-up-arrow me-1"></i> Income Statement
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-2" id="balance-sheet-tab" data-bs-toggle="tab" data-bs-target="#balance-sheet-pane" type="button" role="tab" aria-controls="balance-sheet-pane" aria-selected="false">
                        <i class="bi bi-wallet2 me-1"></i> Balance Sheet
                    </button>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content" id="reportTabsContent">
                
                <!-- Trial Balance Tab -->
                <div class="tab-pane fade show active" id="trial-balance-pane" role="tabpanel" aria-labelledby="trial-balance-tab" tabindex="0">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0">Trial Balance Matrix</h5>
                            @if(abs($tbTotalDebit - $tbTotalCredit) < 0.01)
                                <span class="badge bg-success-soft text-success px-3 py-2 rounded-pill fw-bold">
                                    <i class="bi bi-shield-check me-1"></i> Ledger Balanced
                                </span>
                            @else
                                <span class="badge bg-danger-soft text-danger px-3 py-2 rounded-pill fw-bold">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Unbalanced Discrepancy (GHS {{ number_format(abs($tbTotalDebit - $tbTotalCredit), 2) }})
                                </span>
                            @endif
                        </div>
                        <div class="card-body p-0 mt-3">
                            <div class="table-responsive">
                                <table class="table align-middle table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Code</th>
                                            <th>Account Name</th>
                                            <th>Type</th>
                                            <th class="text-end">Debit Balance</th>
                                            <th class="text-end pe-4">Credit Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($trialBalance as $row)
                                            <tr>
                                                <td class="ps-4 fw-mono text-muted">{{ $row['account_code'] }}</td>
                                                <td class="fw-semibold text-dark">{{ $row['account_name'] }}</td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">{{ $row['account_type'] }}</span>
                                                </td>
                                                <td class="text-end fw-bold text-dark">
                                                    {{ $row['debit'] > 0 ? 'GHS ' . number_format($row['debit'], 2) : '—' }}
                                                </td>
                                                <td class="text-end pe-4 fw-bold text-dark">
                                                    {{ $row['credit'] > 0 ? 'GHS ' . number_format($row['credit'], 2) : '—' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    No account ledger activity logged.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="table-light fw-bold border-top">
                                        <tr>
                                            <td colspan="3" class="ps-4 text-dark fs-6">Total Totals</td>
                                            <td class="text-end text-dark fs-6">GHS {{ number_format($tbTotalDebit, 2) }}</td>
                                            <td class="text-end pe-4 text-dark fs-6">GHS {{ number_format($tbTotalCredit, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Income Statement Tab -->
                <div class="tab-pane fade" id="income-statement-pane" role="tabpanel" aria-labelledby="income-statement-tab" tabindex="0">
                    <div class="row">
                        <!-- P&L Sheet -->
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                                    <h5 class="fw-bold text-dark mb-0">Statement of Revenue & Expenses</h5>
                                </div>
                                <div class="card-body px-4 pb-4 pt-3">
                                    <!-- Revenue Section -->
                                    <h6 class="text-uppercase fw-bold text-primary border-bottom pb-2 mb-3">Revenues</h6>
                                    <table class="table table-borderless align-middle mb-4">
                                        <tbody>
                                            @forelse($revenueItems as $item)
                                                <tr>
                                                    <td class="fw-semibold text-dark">{{ $item['account_name'] }} ({{ $item['account_code'] }})</td>
                                                    <td class="text-end fw-bold text-dark">GHS {{ number_format($item['balance'], 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-muted italic">No revenue transactions recorded.</td>
                                                </tr>
                                            @endforelse
                                            <tr class="border-top fw-bold">
                                                <td class="text-dark">Total Revenue</td>
                                                <td class="text-end text-dark">GHS {{ number_format($totalRevenue, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Expenses Section -->
                                    <h6 class="text-uppercase fw-bold text-danger border-bottom pb-2 mb-3">Expenses</h6>
                                    <table class="table table-borderless align-middle mb-4">
                                        <tbody>
                                            @forelse($expenseItems as $item)
                                                <tr>
                                                    <td class="fw-semibold text-dark">{{ $item['account_name'] }} ({{ $item['account_code'] }})</td>
                                                    <td class="text-end fw-bold text-dark">GHS {{ number_format($item['balance'], 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-muted italic">No expense transactions recorded.</td>
                                                </tr>
                                            @endforelse
                                            <tr class="border-top fw-bold">
                                                <td class="text-dark">Total Expenses</td>
                                                <td class="text-end text-dark">GHS {{ number_format($totalExpense, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Panel -->
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm bg-gradient-light">
                                <div class="card-body p-4">
                                    <h5 class="fw-bold text-dark mb-3">Net Profitability</h5>
                                    <div class="p-4 rounded-3 text-center {{ $netSurplus >= 0 ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                        <div class="small text-uppercase fw-bold mb-1">Net Surplus / Deficit</div>
                                        <div class="fs-2 fw-bold mb-2">GHS {{ number_format($netSurplus, 2) }}</div>
                                        <div>
                                            @if($netSurplus >= 0)
                                                <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-graph-up"></i> Operating Surplus</span>
                                            @else
                                                <span class="badge bg-danger rounded-pill px-3 py-2"><i class="bi bi-graph-down"></i> Operating Deficit</span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-muted small mt-4 mb-0">The net surplus is dynamically calculated and automatically carried forward as Retained Earnings in the Equity section of the Balance Sheet.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Balance Sheet Tab -->
                <div class="tab-pane fade" id="balance-sheet-pane" role="tabpanel" aria-labelledby="balance-sheet-tab" tabindex="0">
                    <div class="row">
                        <!-- Balance Sheet List -->
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                                    <h5 class="fw-bold text-dark mb-0">Statement of Financial Position</h5>
                                </div>
                                <div class="card-body px-4 pb-4 pt-3">
                                    
                                    <!-- Assets Section -->
                                    <h6 class="text-uppercase fw-bold text-primary border-bottom pb-2 mb-3">Assets</h6>
                                    <table class="table table-borderless align-middle mb-4">
                                        <tbody>
                                            @forelse($assetItems as $item)
                                                <tr>
                                                    <td class="fw-semibold text-dark">{{ $item['account_name'] }} ({{ $item['account_code'] }})</td>
                                                    <td class="text-end fw-bold text-dark">GHS {{ number_format($item['balance'], 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-muted italic">No assets registered with activity.</td>
                                                </tr>
                                            @endforelse
                                            <tr class="border-top fw-bold">
                                                <td class="text-dark">Total Assets</td>
                                                <td class="text-end text-dark">GHS {{ number_format($totalAssets, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Liabilities Section -->
                                    <h6 class="text-uppercase fw-bold text-warning border-bottom pb-2 mb-3">Liabilities</h6>
                                    <table class="table table-borderless align-middle mb-4">
                                        <tbody>
                                            @forelse($liabilityItems as $item)
                                                <tr>
                                                    <td class="fw-semibold text-dark">{{ $item['account_name'] }} ({{ $item['account_code'] }})</td>
                                                    <td class="text-end fw-bold text-dark">GHS {{ number_format($item['balance'], 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-muted italic">No liabilities recorded.</td>
                                                </tr>
                                            @endforelse
                                            <tr class="border-top fw-bold">
                                                <td class="text-dark">Total Liabilities</td>
                                                <td class="text-end text-dark">GHS {{ number_format($totalLiabilities, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Equity Section -->
                                    <h6 class="text-uppercase fw-bold text-success border-bottom pb-2 mb-3">Equity</h6>
                                    <table class="table table-borderless align-middle mb-4">
                                        <tbody>
                                            @forelse($equityItems as $item)
                                                <tr>
                                                    <td class="fw-semibold text-dark">{{ $item['account_name'] }} ({{ $item['account_code'] }})</td>
                                                    <td class="text-end fw-bold text-dark">GHS {{ number_format($item['balance'], 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-muted italic">No equity recorded.</td>
                                                </tr>
                                            @endforelse
                                            <tr class="border-top fw-bold">
                                                <td class="text-dark">Total Equity</td>
                                                <td class="text-end text-dark">GHS {{ number_format($totalEquity, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Total L+E Row -->
                                    <table class="table table-borderless align-middle border-top border-2 border-dark mb-0 fw-bold fs-6">
                                        <tbody>
                                            <tr>
                                                <td class="text-dark">Total Liabilities & Equity</td>
                                                <td class="text-end text-dark">GHS {{ number_format($totalLiabilities + $totalEquity, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Check Balanced Card -->
                        <div class="col-lg-4">
                            @if($balanceSheetBalanced)
                                <div class="card border-0 shadow-sm bg-success text-white">
                                    <div class="card-body p-4 text-center">
                                        <i class="bi bi-check-circle fs-1 mb-2"></i>
                                        <h5 class="fw-bold mb-1">Balance Sheet Balanced!</h5>
                                        <p class="mb-0 small opacity-75">Assets (GHS {{ number_format($totalAssets, 2) }}) exactly equal Liabilities + Equity (GHS {{ number_format($totalLiabilities + $totalEquity, 2) }}).</p>
                                    </div>
                                </div>
                            @else
                                <div class="card border-0 shadow-sm bg-danger text-white">
                                    <div class="card-body p-4 text-center">
                                        <i class="bi bi-exclamation-triangle fs-1 mb-2"></i>
                                        <h5 class="fw-bold mb-1">Balance Sheet Out of Balance!</h5>
                                        <p class="mb-0 small opacity-75">Discrepancy of GHS {{ number_format(abs($totalAssets - ($totalLiabilities + $totalEquity)), 2) }} exists between Assets and Liabilities + Equity.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
