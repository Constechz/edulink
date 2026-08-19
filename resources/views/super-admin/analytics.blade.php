@extends('layouts.app')

@section('title', 'Platform Revenue & Analytics | ' . config('app.name', 'EduLink') . ' Admin')
@section('header_title', config('app.name', 'EduLink') . ' SaaS Platform Analytics & Telemetry')

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

    <!-- 1. Executive Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 text-white rounded-4 shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #002244 0%, #003366 60%, #0f4c81 100%); border: 1px solid rgba(255, 215, 0, 0.15);">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <h2 class="fw-bold mb-2" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                            Financial Metrics &amp; <span style="color: var(--accent-color, #FFD700);">Tenant Telemetry</span>
                        </h2>
                        <p class="text-white-50 mb-0" style="font-size: 0.9rem; max-width: 650px;">
                            Real-time monitoring of institutional recurring revenue (MRR/ARR), plan paywall overrides, and automated SMS gateway balance accounting in Ghana Cedis (GHS).
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('super-admin.plans.index') }}" class="btn btn-warning rounded-3 px-3 py-2 fw-bold text-decoration-none">
                            <i class="bi bi-award-fill me-1"></i>Configure Plans
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Metric Summary Cards -->
    <div class="row g-3 g-lg-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">Tenant Schools</span>
                        <h2 class="fw-bold mt-1 mb-0" style="color: #003366;">{{ $totalSchools }}</h2>
                    </div>
                    <div class="p-2.5 rounded-3" style="background: rgba(0, 51, 102, 0.1); color: #003366;">
                        <i class="bi bi-building fs-4"></i>
                    </div>
                </div>
                <div class="pt-2 border-top small text-muted">
                    <span class="badge bg-primary-subtle text-primary fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Institutions</span>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">Active Subscriptions</span>
                        <h2 class="fw-bold mt-1 mb-0" style="color: #10b981;">{{ $activeSubsCount }}</h2>
                    </div>
                    <div class="p-2.5 rounded-3" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                        <i class="bi bi-patch-check-fill fs-4"></i>
                    </div>
                </div>
                <div class="pt-2 border-top small text-muted">
                    <span class="badge bg-success-subtle text-success fw-bold"><i class="bi bi-arrow-repeat me-1"></i>Recurring Billing</span>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">Monthly Recurring (MRR)</span>
                        <h2 class="fw-bold mt-1 mb-0" style="color: #003366; font-size: 1.7rem;">GHS {{ number_format($mrr, 2) }}</h2>
                    </div>
                    <div class="p-2.5 rounded-3" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                        <i class="bi bi-currency-exchange fs-4"></i>
                    </div>
                </div>
                <div class="pt-2 border-top small text-muted">
                    <span class="badge bg-warning-subtle text-warning-emphasis fw-bold"><i class="bi bi-graph-up me-1"></i>Monthly Run-Rate</span>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">Annual Projected (ARR)</span>
                        <h2 class="fw-bold mt-1 mb-0 text-info" style="font-size: 1.7rem;">GHS {{ number_format($arr, 2) }}</h2>
                    </div>
                    <div class="p-2.5 rounded-3" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                        <i class="bi bi-graph-up-arrow fs-4"></i>
                    </div>
                </div>
                <div class="pt-2 border-top small text-muted">
                    <span class="badge bg-info-subtle text-info fw-bold"><i class="bi bi-calendar3 me-1"></i>Annual Forecast</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tenant Directory & Controls -->
    <div class="row g-4 mb-4">
        <!-- School Tenant Directory -->
        <div class="col-lg-8">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-buildings-fill text-primary me-2"></i>School Tenant Subscriptions &amp; Overrides</h5>
                    <span class="badge bg-primary-subtle text-primary fw-semibold">{{ count($schools) }} Tenants</span>
                </div>
                <div class="table-responsive border rounded-3">
                    <table class="table align-middle table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="min-width: 180px;">School Info</th>
                                <th style="min-width: 170px;">Subdomain</th>
                                <th style="min-width: 120px;">Current Tier</th>
                                <th style="min-width: 130px;">Status</th>
                                <th style="min-width: 250px;">Direct Override Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schools as $school)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark d-block" style="font-size: 0.88rem;">{{ $school->name }}</span>
                                        <span class="small text-muted">Owner: {{ $school->owner_name }}</span>
                                    </td>
                                    <td>
                                        <code class="px-2 py-1 bg-light border rounded text-primary small d-block mb-1">
                                            {{ $school->subdomain }}.{{ request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1' ? strtolower(config('app.name', 'EduLink')) . '.local' : preg_replace('/^(admin|www)\./', '', request()->getHost()) }}
                                        </code>
                                        @if($school->custom_domain)
                                            <span class="badge bg-info-subtle text-info small">{{ $school->custom_domain }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 px-2 py-1 fw-bold">
                                            <i class="bi bi-award me-1 text-warning"></i>{{ $school->plan ? $school->plan->name : 'Free / Trial' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(!$school->is_active)
                                            <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 px-2 py-1 fw-bold">Pending Approval</span>
                                        @else
                                            <span class="badge {{ $school->subscription_status === 'active' ? 'bg-success-subtle text-success border border-success border-opacity-25' : 'bg-warning-subtle text-warning-emphasis border border-warning border-opacity-25' }} px-2 py-1 fw-bold text-capitalize">
                                                {{ $school->subscription_status ?: 'Trial' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap" style="min-width: 240px;">
                                        @if(!$school->is_active)
                                            <form action="{{ route('super-admin.schools.approve', $school->id) }}" method="POST" class="d-inline-block m-0" onsubmit="return confirm('Approve and activate {{ addslashes($school->name) }}?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-3 px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm text-nowrap" style="font-size: 0.82rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    <span>Approve School</span>
                                                </button>
                                            </form>
                                        @else
                                            <!-- Override form -->
                                            <form action="{{ route('super-admin.billing.override', $school->id) }}" method="POST" class="d-flex align-items-center gap-1">
                                                @csrf
                                                <select name="plan_id" class="form-select form-select-sm rounded-2 py-1" style="font-size: 0.78rem; width: 95px;">
                                                    @foreach($plans as $plan)
                                                        <option value="{{ $plan->id }}" {{ $school->plan_id == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                                                    @endforeach
                                                </select>
                                                <select name="subscription_status" class="form-select form-select-sm rounded-2 py-1" style="font-size: 0.78rem; width: 85px;">
                                                    <option value="active" {{ $school->subscription_status == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="trial" {{ $school->subscription_status == 'trial' ? 'selected' : '' }}>Trial</option>
                                                    <option value="expired" {{ $school->subscription_status == 'expired' ? 'selected' : '' }}>Expired</option>
                                                    <option value="suspended" {{ $school->subscription_status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                                </select>
                                                <input type="date" name="trial_ends_at" class="form-control form-control-sm rounded-2 py-1" style="font-size: 0.78rem; width: 105px;" value="{{ $school->trial_ends_at ? $school->trial_ends_at->format('Y-m-d') : '' }}" title="Trial Ends Date">
                                                
                                                <input type="hidden" name="api_access" value="0">
                                                <div class="form-check m-0 d-flex align-items-center gap-1 px-2 border rounded-2 bg-white" style="height: 29px;" title="Toggle API Credentials Access">
                                                    <input class="form-check-input m-0 cursor-pointer shadow-none" type="checkbox" name="api_access" value="1" id="apiAccess{{ $school->id }}" {{ $school->isFeatureEnabled('api_access', false) ? 'checked' : '' }}>
                                                    <label class="form-check-label text-secondary fw-bold small m-0 cursor-pointer" style="font-size: 0.68rem;" for="apiAccess{{ $school->id }}">API</label>
                                                </div>

                                                <button type="submit" class="btn btn-primary btn-sm rounded-2 px-2" title="Save Overrides" style="height: 29px;">
                                                    <i class="bi bi-save"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SMS Credit Adjustment & Platform Configs -->
        <div class="col-lg-4">
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-chat-left-text-fill text-info me-2"></i>SMS Credit Controls</h5>
                <p class="text-muted small">Top up or deduct SMS balances for individual institutions manually.</p>
                
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
                        <input type="text" class="form-control rounded-3 py-2" id="note" name="note" placeholder="e.g., MoMo Transaction #">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                        <i class="bi bi-arrow-down-up me-2"></i>Apply Credit Adjustment
                    </button>
                </form>
            </div>
            
            <!-- SMS metrics -->
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-graph-up text-secondary me-2"></i>SMS Usage Summary</h5>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between border-bottom pb-2">
                        <span class="text-muted small">Total SMS Units Issued</span>
                        <span class="fw-bold text-dark">{{ number_format($totalSmsPurchased) }} units</span>
                    </div>
                    <div class="d-flex justify-content-between pb-2">
                        <span class="text-muted small">Total SMS Units Dispatched</span>
                        <span class="fw-bold text-dark">{{ number_format($totalSmsUsed) }} units</span>
                    </div>
                </div>
            </div>

            <!-- Platform Settings -->
            <div class="glass-card p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-sliders text-primary me-2"></i>Custom Website Unlock Price</h5>
                <p class="text-muted small">Global addon fee to activate custom school portal domains.</p>
                
                <form action="{{ route('super-admin.settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-secondary">GHS</span>
                            <input type="number" class="form-control rounded-end-3 py-2" id="website_builder_unlock_price" name="website_builder_unlock_price" step="0.01" min="0" required value="{{ $websiteUnlockPrice }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                        <i class="bi bi-check-circle me-2"></i>Save Configuration
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 4. Recent SMS Ledger Activity -->
    <div class="row">
        <div class="col-12">
            <div class="glass-card p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-list-columns-reverse text-secondary me-2"></i>Recent SMS Ledger Activity (System-wide)</h5>
                <div class="table-responsive border rounded-3">
                    <table class="table align-middle table-hover mb-0">
                        <thead>
                            <tr>
                                <th>School Name</th>
                                <th>Activity Type</th>
                                <th>Credits Adjusted</th>
                                <th>New Balance</th>
                                <th>Reference</th>
                                <th>Notes</th>
                                <th>Date &amp; Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $tx)
                                <tr>
                                    <td><span class="fw-bold text-dark">{{ $tx->school ? $tx->school->name : 'N/A' }}</span></td>
                                    <td>
                                        <span class="badge {{ $tx->type === 'purchase' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border px-2 py-1 fw-bold text-capitalize">
                                            {{ $tx->type }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark">{{ number_format($tx->credits) }}</td>
                                    <td>{{ number_format($tx->balance_after) }}</td>
                                    <td><code class="text-primary">{{ $tx->reference }}</code></td>
                                    <td><span class="text-muted">{{ $tx->note }}</span></td>
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
