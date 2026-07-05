@extends('layouts.app')

@section('title', 'SMS Credit Controls | ' . config('app.name', 'EduLink') . ' Admin')
@section('header_title', config('app.name', 'EduLink') . ' SMS Credits Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Session Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show glass-card p-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- SMS analytics grid -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="glass-card p-4 text-center d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Total SMS Units Issued</span>
                    <h2 class="card-metric mt-2 text-success">{{ number_format($totalSmsPurchased) }}</h2>
                    <div class="small text-muted mt-1">Platform total allotment</div>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-4 text-success">
                    <i class="bi bi-cloud-arrow-up fs-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="glass-card p-4 text-center d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Total SMS Units Dispatched</span>
                    <h2 class="card-metric mt-2 text-primary">{{ number_format($totalSmsUsed) }}</h2>
                    <div class="small text-muted mt-1">Platform total consumption</div>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-4 text-primary">
                    <i class="bi bi-send fs-2"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- SMS Top-up form -->
        <div class="col-md-4">
            <div class="glass-card p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-plus-slash-minus text-info me-2"></i>Manual Balance Override</h5>
                <p class="text-muted small">Credit or debit SMS balances for individual tenant schools.</p>
                
                <form action="{{ route('super-admin.billing.sms') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="school_id" class="form-label fw-semibold text-secondary small">Select School</label>
                        <select class="form-select rounded-3 py-2 border-light shadow-xs" id="school_id" name="school_id" required>
                            <option value="">-- Choose Tenant School --</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}">{{ $school->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="credits" class="form-label fw-semibold text-secondary small">SMS Credits</label>
                            <input type="number" class="form-control rounded-3 py-2 border-light shadow-xs" id="credits" name="credits" min="1" required placeholder="Units">
                        </div>
                        <div class="col-6">
                            <label for="action_type" class="form-label fw-semibold text-secondary small">Operation</label>
                            <select class="form-select rounded-3 py-2 border-light shadow-xs" id="action_type" name="action_type" required>
                                <option value="purchase">Purchase (+)</option>
                                <option value="deduction">Deduction (-)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label fw-semibold text-secondary small">Internal Note</label>
                        <input type="text" class="form-control rounded-3 py-2 border-light shadow-xs" id="note" name="note" placeholder="Reason / Invoice ref">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold shadow-xs">
                        <i class="bi bi-arrow-down-up me-2"></i>Apply Credit Adjustment
                    </button>
                </form>
            </div>
        </div>

        <!-- Ledger Log Table -->
        <div class="col-md-8">
            <div class="glass-card p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-list-columns-reverse text-secondary me-2"></i>SMS Ledger Log Activity</h5>
                <div class="table-responsive">
                    <table class="table align-middle table-sm small">
                        <thead>
                            <tr>
                                <th>School Name</th>
                                <th>Activity Type</th>
                                <th>Credits</th>
                                <th>New Balance</th>
                                <th>Reference</th>
                                <th>Notes</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $tx)
                                <tr>
                                    <td><span class="fw-bold text-dark">{{ $tx->school->name }}</span></td>
                                    <td>
                                        <span class="badge {{ $tx->type === 'purchase' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $tx->type }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark">{{ number_format($tx->credits) }}</td>
                                    <td>{{ number_format($tx->balance_after) }}</td>
                                    <td><code>{{ $tx->reference }}</code></td>
                                    <td><span class="text-muted">{{ $tx->note }}</span></td>
                                    <td>{{ $tx->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No SMS ledger transactions recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
