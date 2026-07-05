@extends('layouts.app')

@section('title', 'Fees Statements | Parent Portal')
@section('header_title', 'Billing & Fees Statements')

@section('content')
<style>
    .avatar-circle-lg {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.4rem;
        box-shadow: 0 4px 12px rgba(0, 51, 102, 0.15);
    }

    .metric-card-green {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid rgba(25, 135, 84, 0.12) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card-green:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(25, 135, 84, 0.08);
    }

    .metric-card-blue {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid rgba(13, 110, 253, 0.12) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card-blue:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(13, 110, 253, 0.08);
    }

    .metric-card-red {
        background: linear-gradient(135deg, #fff5f5 0%, #ffe3e3 100%);
        border: 1px solid rgba(220, 53, 69, 0.15) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card-red:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(220, 53, 69, 0.08);
    }

    .metric-card-amber {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border: 1px solid rgba(255, 193, 7, 0.25) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card-amber:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(255, 193, 7, 0.08);
    }

    .table-row-hover {
        transition: background-color 0.2s ease;
    }
    .table-row-hover:hover {
        background-color: rgba(0, 51, 102, 0.02) !important;
    }

    /* Text Visibility and High-Contrast Overrides */
    .text-muted {
        color: #64748b !important;
    }
    .text-secondary {
        color: #475569 !important;
    }
    .text-dark {
        color: #0f172a !important;
    }
    .card-metric {
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: -0.5px;
    }
</style>

<div class="container-fluid p-0">
    <!-- Header Child Switcher Banner -->
    <div class="glass-card p-4 mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.05);">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar-circle-lg bg-primary text-white">
                {{ substr($activeChild->first_name, 0, 1) }}{{ substr($activeChild->last_name, 0, 1) }}
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Student billing statements</span>
                <h3 class="fw-bold mb-1 text-primary" style="font-weight: 800;">{{ $activeChild->first_name }} {{ $activeChild->middle_name }} {{ $activeChild->last_name }}</h3>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2.5 py-1.5 fw-bold" style="border-radius: 6px; font-family: monospace; font-size: 0.76rem;">
                        ID: {{ $activeChild->student_id_number }}
                    </span>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1.5 fw-bold" style="border-radius: 6px; font-size: 0.76rem;">
                        Class: {{ $activeChild->currentClass->name ?? 'N/A' }}
                    </span>
                    @if($activeChild->currentStream)
                        <span class="badge bg-info bg-opacity-10 text-info px-2.5 py-1.5 fw-bold" style="border-radius: 6px; font-size: 0.76rem;">
                            Stream: {{ $activeChild->currentStream->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle rounded-3 py-2 px-3 fw-bold d-inline-flex align-items-center gap-2" type="button" id="childSelector" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.85rem; border-radius: 10px !important;">
                    <i class="bi bi-person-fill-gear"></i> Switch Child Profile
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="childSelector" style="border-radius: 12px;">
                    @foreach($children as $child)
                        <li>
                            <a class="dropdown-item py-2.5 fw-bold text-dark {{ $child->id === $activeChild->id ? 'active bg-primary text-white' : '' }}" href="{{ route('school.parent-portal.select-child', $child->id) }}" style="font-size: 0.85rem;">
                                <i class="bi bi-person-fill me-2"></i>{{ $child->first_name }} {{ $child->last_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    @php
        $totalBilled = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('amount_paid');
        $totalBalance = $invoices->sum('balance');
        $paymentRatio = $totalBilled > 0 ? round(($totalPaid / $totalBilled) * 100) : 100;
    @endphp

    <!-- Dynamic Metrics Panel -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-4 text-center metric-card-red">
                <div class="fs-1 text-danger mb-2"><i class="bi bi-exclamation-octagon-fill"></i></div>
                <div class="card-metric text-danger">GHS {{ number_format($totalBalance, 2) }}</div>
                <div class="text-muted small fw-bold text-uppercase mt-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Outstanding Fees</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="glass-card p-4 text-center metric-card-blue">
                <div class="fs-1 text-primary mb-2"><i class="bi bi-receipt"></i></div>
                <div class="card-metric text-primary">GHS {{ number_format($totalBilled, 2) }}</div>
                <div class="text-muted small fw-bold text-uppercase mt-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Billed</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card p-4 text-center metric-card-green">
                <div class="fs-1 text-success mb-2"><i class="bi bi-shield-check"></i></div>
                <div class="card-metric text-success">GHS {{ number_format($totalPaid, 2) }}</div>
                <div class="text-muted small fw-bold text-uppercase mt-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Paid</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card p-4 text-center metric-card-amber">
                <div class="fs-1 text-warning mb-2" style="color: #b08d00 !important;"><i class="bi bi-percent"></i></div>
                <div class="card-metric text-warning" style="color: #b08d00 !important;">{{ $paymentRatio }}%</div>
                <div class="text-muted small fw-bold text-uppercase mt-1" style="font-size: 0.72rem; letter-spacing: 0.5px; color: #b08d00 !important;">Settled Ratio</div>
            </div>
        </div>
    </div>

    <!-- Term Invoices Ledger Card -->
    <div class="glass-card p-4 mb-5" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.05);">
        <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-receipt me-2 text-primary"></i>Term Billing & Invoices Ledger</h5>

        @if($invoices->isEmpty())
            <!-- Redesigned Empty State -->
            <div class="text-center py-5 text-muted">
                <div class="text-secondary bg-secondary bg-opacity-10 p-3 rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                    <i class="bi bi-wallet2 display-5"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">No Invoices Issued</h5>
                <p class="mb-0">No invoices or billing statements have been generated for this student profile yet.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle" style="font-size: 0.9rem;">
                    <thead>
                        <tr class="table-light">
                            <th class="border-0 rounded-start">Invoice No.</th>
                            <th class="border-0">Term Reference</th>
                            <th class="border-0 text-end">Total Amount</th>
                            <th class="border-0 text-end">Amount Paid</th>
                            <th class="border-0 text-end">Balance Due</th>
                            <th class="border-0 text-center">Status</th>
                            <th class="border-0">Due Date</th>
                            <th class="border-0 rounded-end text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr class="table-row-hover">
                                <td>
                                    <span class="fw-bold text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-file-earmark-text text-primary"></i> {{ $invoice->invoice_number }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $invoice->term->name ?? 'N/A' }}</div>
                                    <div class="text-muted small">Calendar Year: {{ $invoice->academicYear->name ?? 'N/A' }}</div>
                                </td>
                                <td class="text-end fw-semibold text-secondary">GHS {{ number_format($invoice->total_amount, 2) }}</td>
                                <td class="text-end fw-semibold text-success">GHS {{ number_format($invoice->amount_paid, 2) }}</td>
                                <td class="text-end fw-bold text-danger">GHS {{ number_format($invoice->balance, 2) }}</td>
                                <td class="text-center">
                                    @php
                                        $badgeClass = 'bg-secondary bg-opacity-10 text-secondary';
                                        if ($invoice->status === 'paid') {
                                            $badgeClass = 'bg-success bg-opacity-10 text-success';
                                        } elseif ($invoice->status === 'partial') {
                                            $badgeClass = 'bg-warning bg-opacity-15 text-warning';
                                        } elseif ($invoice->status === 'pending') {
                                            $badgeClass = 'bg-danger bg-opacity-10 text-danger';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }} text-uppercase px-2.5 py-1.5 fw-bold" style="border-radius: 6px; font-size: 0.78rem;">
                                        @if($invoice->status === 'paid')
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                        @elseif($invoice->status === 'partial')
                                            <i class="bi bi-hourglass-split me-1"></i>
                                        @elseif($invoice->status === 'pending')
                                            <i class="bi bi-x-circle-fill me-1"></i>
                                        @endif
                                        {{ $invoice->status }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-secondary">
                                        <i class="bi bi-calendar-event me-1"></i> {{ date('M d, Y', strtotime($invoice->due_date)) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('school.finance.invoices.pdf', $invoice->id) }}" class="btn btn-sm btn-outline-danger px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5" style="border-radius: 8px; font-size: 0.8rem;">
                                        <i class="bi bi-file-earmark-pdf"></i> Download PDF
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
