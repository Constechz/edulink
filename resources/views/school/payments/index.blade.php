@extends('layouts.app')

@section('title', 'Payment Receipts Log')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Payments Log</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Payment Transactions & Receipts</h1>
                    <p class="text-muted mb-0 small">Log of student payment allocations, references, and receipt ledger states.</p>
                </div>
                <div>
                    <a href="{{ route('school.finance.invoices.index') }}" class="btn btn-outline-primary px-4 me-2">
                        <i class="bi bi-receipt me-1"></i> Invoices Directory
                    </a>
                </div>
            </div>

            <!-- Validation/Success Feedback Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                        <div>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Search and Filter Panel -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('school.finance.payments.index') }}" class="row g-3">
                        <div class="col-md-5">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text border-end-0 bg-transparent text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search receipt number, reference, or student name..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="method" class="form-select" onchange="this.form.submit()">
                                <option value="">All Payment Methods</option>
                                <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="momo" {{ request('method') == 'momo' ? 'selected' : '' }}>Mobile Money (MoMo)</option>
                                <option value="bank_transfer" {{ request('method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="cheque" {{ request('method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="online" {{ request('method') == 'online' ? 'selected' : '' }}>Online Card</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex justify-content-end">
                            <a href="{{ route('school.finance.payments.index') }}" class="btn btn-light px-3 me-2">Reset</a>
                            <button type="submit" class="btn btn-primary px-4">Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payments List Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Receipt Number</th>
                                    <th>Student</th>
                                    <th>Invoice Number</th>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark">{{ $payment->receipt_number }}</td>
                                        <td>
                                            @if($payment->student)
                                                <div class="fw-semibold text-dark">{{ $payment->student->first_name }} {{ $payment->student->last_name }}</div>
                                                <div class="text-muted small">{{ $payment->student->student_id_number }}</div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($payment->invoice)
                                                <a href="{{ route('school.finance.invoices.show', $payment->invoice->id) }}" class="text-decoration-none fw-semibold">
                                                    {{ $payment->invoice->invoice_number }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border py-2 px-3 text-uppercase">
                                                @if($payment->method == 'momo')
                                                    <i class="bi bi-phone me-1 text-warning"></i> MoMo
                                                @elseif($payment->method == 'cash')
                                                    <i class="bi bi-cash me-1 text-success"></i> Cash
                                                @elseif($payment->method == 'bank_transfer')
                                                    <i class="bi bi-bank me-1 text-primary"></i> Bank
                                                @else
                                                    <i class="bi bi-credit-card me-1 text-info"></i> {{ $payment->method }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <code class="small text-dark">{{ $payment->reference_number ?? 'N/A' }}</code>
                                        </td>
                                        <td class="fw-bold text-dark">
                                            GHS {{ number_format($payment->amount, 2) }}
                                        </td>
                                        <td>
                                            @if($payment->is_reversed)
                                                <span class="badge bg-danger-soft text-danger py-2 px-3 fw-bold rounded-pill">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reversed
                                                </span>
                                            @else
                                                <span class="badge bg-success-soft text-success py-2 px-3 fw-bold rounded-pill">
                                                    <i class="bi bi-check-circle me-1"></i> Success
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex justify-content-end align-items-center gap-2">
                                                <a href="{{ route('school.finance.invoices.show', $payment->invoice_id) }}" class="btn btn-sm btn-outline-secondary" title="View Bill Ledger">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if(!$payment->is_reversed)
                                                    <form action="{{ route('school.finance.payments.reverse', $payment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reverse this payment? This will revert the invoice balance and register a reversing entry in the General Ledger.');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Reverse Payment">
                                                            <i class="bi bi-arrow-counterclockwise"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="bi bi-credit-card fs-1 d-block mb-3 text-secondary"></i>
                                            No payment records found matching the filter criteria.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($payments->hasPages())
                    <div class="card-footer bg-transparent border-top">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
