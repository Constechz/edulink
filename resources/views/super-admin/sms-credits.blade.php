@extends('layouts.app')

@section('title', 'SMS Credit Controls | ' . config('app.name', 'EduLink') . ' Admin')
@section('header_title', config('app.name', 'EduLink') . ' SMS Credits & Gateway Hub')

@section('content')
<div class="container-fluid p-0">
    <!-- Session Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm border-0 d-flex align-items-center" role="alert" style="background: rgba(16, 185, 129, 0.12); color: #059669; border-left: 4px solid #10b981 !important;">
            <i class="bi bi-check-circle-fill fs-5 me-2.5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Executive Header Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 text-white rounded-4 shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #002244 0%, #003366 60%, #0f4c81 100%); border: 1px solid rgba(255, 215, 0, 0.15);">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <h2 class="fw-bold mb-2" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                            SMS Credit Hub &amp; <span style="color: var(--accent-color, #FFD700);">Quota Allocation</span>
                        </h2>
                        <p class="text-white-50 mb-0" style="font-size: 0.9rem; max-width: 650px;">
                            Monitor institutional SMS quota utilization, top up or debit balances manually, and audit complete system-wide SMS ledger dispatches in Ghana.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('super-admin.settings') }}" class="btn btn-warning rounded-3 px-3 py-2 fw-bold text-decoration-none shadow-sm">
                            <i class="bi bi-gear-fill me-1"></i>Gateway Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SMS analytics grid -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="glass-card p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">Total SMS Units Issued</span>
                    <h2 class="fw-bold mt-2 mb-0" style="color: #10b981;">{{ number_format($totalSmsPurchased) }}</h2>
                    <div class="small text-muted mt-1"><i class="bi bi-shield-check me-1 text-success"></i>Platform-wide allotment</div>
                </div>
                <div class="p-3 rounded-4" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                    <i class="bi bi-cloud-arrow-up fs-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="glass-card p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">Total SMS Units Dispatched</span>
                    <h2 class="fw-bold mt-2 mb-0" style="color: #003366;">{{ number_format($totalSmsUsed) }}</h2>
                    <div class="small text-muted mt-1"><i class="bi bi-send-check me-1 text-primary"></i>Consumed by schools</div>
                </div>
                <div class="p-3 rounded-4" style="background: rgba(0, 51, 102, 0.1); color: #003366;">
                    <i class="bi bi-send fs-2"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- SMS Top-up form -->
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-plus-slash-minus text-primary me-2"></i>Manual Balance Override</h5>
                <p class="text-muted small">Credit or debit SMS quota for individual tenant schools.</p>
                
                <form action="{{ route('super-admin.billing.sms') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="school_id" class="form-label fw-semibold text-secondary small">Select School</label>
                        <select class="form-select rounded-3 py-2" id="school_id" name="school_id" required>
                            <option value="">-- Choose Tenant School --</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}">{{ $school->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="credits" class="form-label fw-semibold text-secondary small">SMS Credits</label>
                            <input type="number" class="form-control rounded-3 py-2" id="credits" name="credits" min="1" required placeholder="Units">
                        </div>
                        <div class="col-6">
                            <label for="action_type" class="form-label fw-semibold text-secondary small">Operation</label>
                            <select class="form-select rounded-3 py-2" id="action_type" name="action_type" required>
                                <option value="purchase">Purchase (+)</option>
                                <option value="deduction">Deduction (-)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label fw-semibold text-secondary small">Internal Reference / Note</label>
                        <input type="text" class="form-control rounded-3 py-2" id="note" name="note" placeholder="e.g., MoMo Invoice #">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                        <i class="bi bi-arrow-down-up me-2"></i>Apply Credit Adjustment
                    </button>
                </form>
            </div>
        </div>

        <!-- Ledger Log Table -->
        <div class="col-lg-8">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-list-columns-reverse text-secondary me-2"></i>SMS Ledger Log Activity</h5>
                    <span class="badge bg-primary-subtle text-primary fw-semibold">Audit Trail</span>
                </div>
                <div class="table-responsive border rounded-3">
                    <table class="table align-middle table-hover mb-0">
                        <thead>
                            <tr>
                                <th>School Name</th>
                                <th>Activity Type</th>
                                <th>Credits</th>
                                <th>New Balance</th>
                                <th>Reference</th>
                                <th>Notes</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $tx)
                                <tr>
                                    <td><span class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $tx->school ? $tx->school->name : 'N/A' }}</span></td>
                                    <td>
                                        <span class="badge {{ $tx->type === 'purchase' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border px-2 py-1 fw-bold text-capitalize">
                                            {{ $tx->type }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark">{{ number_format($tx->credits) }}</td>
                                    <td>{{ number_format($tx->balance_after) }}</td>
                                    <td><code class="text-primary small">{{ $tx->reference }}</code></td>
                                    <td><span class="text-muted small">{{ $tx->note }}</span></td>
                                    <td><span class="small text-secondary">{{ $tx->created_at->format('Y-m-d H:i') }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No SMS ledger transactions recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
