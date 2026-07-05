@extends('layouts.app')

@section('title', 'Platform Revenue & Analytics | ' . config('app.name', 'EduLink') . ' Admin')
@section('header_title', config('app.name', 'EduLink') . ' Platform Analytics & Subscriptions')

@section('content')
<div class="container-fluid p-0">
    <!-- Session Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show glass-card p-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Metric Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-4 text-center">
                <span class="text-muted small text-uppercase fw-bold">Total Tenant Schools</span>
                <h2 class="card-metric mt-2">{{ $totalSchools }}</h2>
                <div class="small text-success mt-2"><i class="bi bi-building"></i> Registered tenants</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center">
                <span class="text-muted small text-uppercase fw-bold">Active Subscriptions</span>
                <h2 class="card-metric mt-2 text-success">{{ $activeSubsCount }}</h2>
                <div class="small text-success mt-2"><i class="bi bi-patch-check-fill"></i> Recurring payment active</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center">
                <span class="text-muted small text-uppercase fw-bold">Monthly Recurring (MRR)</span>
                <h2 class="card-metric mt-2 text-primary">GHS {{ number_format($mrr, 2) }}</h2>
                <div class="small text-muted mt-2"><i class="bi bi-arrow-up-right"></i> Current monthly run-rate</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center">
                <span class="text-muted small text-uppercase fw-bold">Annual Recurring (ARR)</span>
                <h2 class="card-metric mt-2 text-info">GHS {{ number_format($arr, 2) }}</h2>
                <div class="small text-muted mt-2"><i class="bi bi-graph-up-arrow"></i> Projected annual revenue</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Tenant Directory & Plan override -->
        <div class="col-md-8">
            <div class="glass-card p-4 h-100">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-buildings-fill text-primary me-2"></i>School Tenant Directory</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>School Info</th>
                                <th>Domain / Subdomain</th>
                                <th>Billing Plan</th>
                                <th>Status</th>
                                <th>Override Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schools as $school)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark d-block">{{ $school->name }}</span>
                                        <span class="small text-muted">Owner: {{ $school->owner_name }}</span>
                                    </td>
                                    <td>
                                         <span class="badge bg-light text-dark small d-block mb-1">{{ $school->subdomain }}.{{ request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1' ? strtolower(config('app.name', 'EduLink')) . '.local' : preg_replace('/^(admin|www)\./', '', request()->getHost()) }}</span>
                                        @if($school->custom_domain)
                                            <span class="badge bg-info bg-opacity-10 text-info small">{{ $school->custom_domain }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary text-uppercase">{{ $school->plan ? $school->plan->name : 'Free / Trial' }}</span>
                                    </td>
                                    <td>
                                        @if(!$school->is_active)
                                            <span class="badge bg-danger text-uppercase">Pending Approval</span>
                                        @else
                                            <span class="badge {{ $school->subscription_status === 'active' ? 'bg-success' : 'bg-warning text-dark' }} text-uppercase">
                                                {{ $school->subscription_status ?: 'Trial' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$school->is_active)
                                            <form action="{{ route('super-admin.schools.approve', $school->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm rounded-3 px-3 fw-bold shadow-xs">
                                                    <i class="bi bi-check2-circle me-1"></i> Approve School
                                                </button>
                                            </form>
                                        @else
                                            <!-- Override form -->
                                            <form action="{{ route('super-admin.billing.override', $school->id) }}" method="POST" class="d-flex align-items-center gap-1">
                                                @csrf
                                                <select name="plan_id" class="form-select form-select-sm rounded-3 py-1 shadow-xs" style="font-size: 0.8rem; width: 100px;">
                                                    @foreach($plans as $plan)
                                                        <option value="{{ $plan->id }}" {{ $school->plan_id == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                                                     @endforeach
                                                </select>
                                                <select name="subscription_status" class="form-select form-select-sm rounded-3 py-1 shadow-xs" style="font-size: 0.8rem; width: 90px;">
                                                    <option value="active" {{ $school->subscription_status == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="trial" {{ $school->subscription_status == 'trial' ? 'selected' : '' }}>Trial</option>
                                                    <option value="expired" {{ $school->subscription_status == 'expired' ? 'selected' : '' }}>Expired</option>
                                                    <option value="suspended" {{ $school->subscription_status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                                </select>
                                                <input type="date" name="trial_ends_at" class="form-control form-control-sm rounded-3 py-1 shadow-xs" style="font-size: 0.8rem; width: 110px;" value="{{ $school->trial_ends_at ? $school->trial_ends_at->format('Y-m-d') : '' }}" title="Trial Ends Date">
                                                
                                                <input type="hidden" name="api_access" value="0">
                                                <div class="form-check m-0 d-flex align-items-center gap-1 px-2 border rounded-3 bg-white" style="height: 31px;" title="Toggle API Credentials Access">
                                                    <input class="form-check-input m-0 cursor-pointer shadow-none" type="checkbox" name="api_access" value="1" id="apiAccess{{ $school->id }}" {{ $school->isFeatureEnabled('api_access', false) ? 'checked' : '' }}>
                                                    <label class="form-check-label text-secondary fw-bold small m-0 cursor-pointer" style="font-size: 0.7rem;" for="apiAccess{{ $school->id }}">API</label>
                                                </div>

                                                <button type="submit" class="btn btn-dark btn-sm rounded-3 px-2 shadow-xs" title="Apply Override" style="height: 31px;">
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

        <!-- SMS Credit Adjustment -->
        <div class="col-md-4">
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-chat-left-text-fill text-info me-2"></i>SMS Credit Controls</h5>
                <p class="text-muted small">Super Admin override interface to manually top up or deduct SMS balances for individual schools.</p>
                
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
                                <option value="purchase">Purchase (+) </option>
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
            
            <!-- SMS metrics -->
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-graph-up text-secondary me-2"></i>SMS Usage Analytics</h5>
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
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-sliders text-primary me-2"></i>Platform Configurations</h5>
                <p class="text-muted small">Update global SaaS pricing and module setting configurations.</p>
                
                <form action="{{ route('super-admin.settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="website_builder_unlock_price" class="form-label fw-semibold text-secondary small">Custom Website Unlock Price (GHS)</label>
                        <div class="input-group">
                            <span class="input-group-text border-light bg-light fw-bold text-secondary">GHS</span>
                            <input type="number" class="form-control rounded-end-3 py-2 border-light shadow-xs" id="website_builder_unlock_price" name="website_builder_unlock_price" step="0.01" min="0" required value="{{ $websiteUnlockPrice }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold shadow-xs">
                        <i class="bi bi-check-circle me-2"></i>Save Configuration
                    </button>
                </form>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-md-12">
            <div class="glass-card p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-list-columns-reverse text-secondary me-2"></i>Recent SMS Ledger Activity (System-wide)</h5>
                <div class="table-responsive">
                    <table class="table align-middle table-sm small">
                        <thead>
                            <tr>
                                <th>School Name</th>
                                <th>Activity Type</th>
                                <th>Credits Adjusted</th>
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
            </div>
        </div>
    </div>
</div>
@endsection
