@extends('layouts.app')

@section('title', 'Student Invoices | EduLink')
@section('header_title', 'Student Billing Directory')

@section('content')
<div class="container-fluid p-0">

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Metrics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="glass-card p-4 text-center">
                <span class="text-muted small uppercase font-weight-bold" style="font-weight: 600; letter-spacing: 0.5px;">TOTAL INVOICED</span>
                <h3 class="card-metric mt-2 mb-0 text-primary" style="font-weight: 700;">GHS {{ number_format($metrics['total_invoiced'], 2) }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 text-center">
                <span class="text-muted small uppercase font-weight-bold" style="font-weight: 600; letter-spacing: 0.5px;">TOTAL PAID COLLECTED</span>
                <h3 class="card-metric mt-2 mb-0 text-success" style="font-weight: 700;">GHS {{ number_format($metrics['total_paid'], 2) }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 text-center">
                <span class="text-muted small uppercase font-weight-bold" style="font-weight: 600; letter-spacing: 0.5px;">TOTAL OUTSTANDING BALANCE</span>
                <h3 class="card-metric mt-2 mb-0 text-danger" style="font-weight: 700;">GHS {{ number_format($metrics['total_balance'], 2) }}</h3>
            </div>
        </div>
    </div>

    <!-- Actions & Filter Bar -->
    <div class="glass-card p-4 mb-4">
        <form action="{{ route('school.finance.invoices.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small font-weight-bold">Search Student / Invoice</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Name, ID or INV number...">
            </div>
            <div class="col-md-3">
                <label class="form-label small font-weight-bold">Filter Class</label>
                <select class="form-select" name="class_id">
                    <option value="">All Classes</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small font-weight-bold">Payment Status</label>
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Fully Paid</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="waived" {{ request('status') == 'waived' ? 'selected' : '' }}>Waived</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary px-3 w-100"><i class="bi bi-filter me-1"></i>Filter</button>
                <a href="{{ route('school.finance.invoices.create') }}" class="btn btn-primary px-3 w-100 text-nowrap"><i class="bi bi-receipt me-1"></i>New Invoice</a>
            </div>
        </form>
    </div>

    <!-- Invoices List -->
    <div class="glass-card p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Invoice Number</th>
                        <th>Student Name (ID)</th>
                        <th>Academic Session</th>
                        <th>Total Amount</th>
                        <th>Paid</th>
                        <th>Balance Due</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr>
                            <td class="font-weight-bold" style="font-weight: 600; color: var(--primary-color);">{{ $inv->invoice_number }}</td>
                            <td>
                                <div>{{ $inv->student->first_name }} {{ $inv->student->last_name }}</div>
                                <span class="text-muted small">ID: {{ $inv->student->student_id_number }}</span>
                            </td>
                            <td>
                                <div>{{ $inv->academicYear->name }}</div>
                                <span class="text-muted small">{{ $inv->term->name }}</span>
                            </td>
                            <td class="font-weight-bold">GHS {{ number_format($inv->total_amount, 2) }}</td>
                            <td class="text-success font-weight-bold">GHS {{ number_format($inv->amount_paid, 2) }}</td>
                            <td class="text-danger font-weight-bold">GHS {{ number_format($inv->balance, 2) }}</td>
                            <td>
                                <span class="badge @if($inv->status === 'paid') bg-success @elseif($inv->status === 'partial') bg-warning text-dark @else bg-danger @endif px-2.5 py-1.5 rounded-3">
                                    {{ ucfirst($inv->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end align-items-center gap-2">
                                    <a href="{{ route('school.finance.invoices.show', $inv->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" style="border-radius: 8px;">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="{{ route('school.finance.invoices.pdf', $inv->id) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center justify-content-center" style="border-radius: 8px; width: 32px; height: 32px; padding: 0;">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No student bills or invoices found matching filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
    </div>

</div>
@endsection
