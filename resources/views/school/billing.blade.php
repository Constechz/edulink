@extends('layouts.app')

@section('title', 'SaaS Billing & Subscriptions | EduLink')
@section('header_title', 'Billing & SaaS Subscriptions')

@section('content')
<div class="container-fluid p-0">
    <!-- Session Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show glass-card p-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Current Subscription Summary -->
        <div class="col-md-4">
            <div class="glass-card p-4 mb-4" style="background: linear-gradient(135deg, rgba(0, 51, 102, 0.02) 0%, rgba(255, 215, 0, 0.02) 100%);">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-wallet2 text-primary me-2"></i>Current Subscription</h5>
                
                <div class="p-3 bg-light rounded-4 mb-4">
                    <span class="text-muted small d-block">Active Plan</span>
                    <h3 class="fw-bold text-primary mb-1">{{ $school->plan ? $school->plan->name : 'No Active Plan (Trial)' }}</h3>
                    <span class="badge bg-success bg-opacity-10 text-success text-uppercase">{{ $school->subscription_status ?: 'Trial' }}</span>
                </div>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between border-bottom pb-2">
                        <span class="text-muted small">Max Students Limit</span>
                        <span class="fw-bold text-dark">{{ ($school->plan && $school->plan->max_students == -1) ? 'Unlimited' : ($school->plan ? $school->plan->max_students : 50) }}</span>
                    </div>
                    <div class="d-flex justify-content-between pb-2">
                        <span class="text-muted small">Max Campuses Limit</span>
                        <span class="fw-bold text-dark">{{ ($school->plan && $school->plan->max_campuses == -1) ? 'Unlimited' : ($school->plan ? $school->plan->max_campuses : 1) }}</span>
                    </div>
                </div>
            </div>

            <!-- Custom Website Builder Add-on Card -->
            <div class="glass-card p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-globe text-warning me-2"></i>Website Builder Add-on</h5>
                @if($websiteUnlocked)
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 text-center mb-2">
                        <i class="bi bi-patch-check-fill fs-2 mb-2 d-block"></i>
                        <span class="fw-bold d-block">Permanently Unlocked</span>
                        <span class="small">You have full access to the Custom Website Builder under school settings.</span>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('school.website.pages.index') }}" class="btn btn-outline-success btn-sm w-100 rounded-3 py-2 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Manage Website Builder
                        </a>
                    </div>
                @else
                    <div class="p-3 bg-light rounded-4 mb-4 text-center">
                        <span class="text-muted small d-block mb-1">One-time Access Fee</span>
                        <h3 class="fw-extrabold text-primary mb-0">GHS {{ number_format($websiteUnlockPrice, 2) }}</h3>
                    </div>
                    
                    <p class="text-muted small mb-4">
                        Unlock a fully customizable public website builder to create, edit, and publish your school's web pages directly from your {{ config('app.name', 'EduLink') }} dashboard.
                    </p>

                    <form action="{{ route('school.billing.unlock-website') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Choose Payment Gateway</label>
                            <select class="form-select rounded-3 py-2" name="gateway">
                                <option value="paystack">Paystack Gateway</option>
                                <option value="flutterwave">Flutterwave Gateway</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning text-dark w-100 rounded-3 py-2 fw-bold">
                            <i class="bi bi-credit-card-2-front me-2"></i>Unlock Custom Website
                        </button>
                    </form>
                @endif
            </div>

            <!-- Portals Activation Add-on Card -->
            <div class="glass-card p-4 mt-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-shield-lock-fill text-info me-2"></i>Portals Activation</h5>
                @if($portalsUnlocked)
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 text-center mb-2">
                        <i class="bi bi-patch-check-fill fs-2 mb-2 d-block"></i>
                        <span class="fw-bold d-block">Active & Operational</span>
                        <span class="small text-muted d-block mt-1">Student and parent portal endpoints are unlocked. Students and parents can log in.</span>
                    </div>
                @else
                    <div class="p-3 bg-light rounded-4 mb-4 text-center">
                        <span class="text-muted small d-block mb-1">One-time Activation Fee</span>
                        <h3 class="fw-extrabold text-primary mb-0">GHS {{ number_format($portalUnlockPrice, 2) }}</h3>
                    </div>
                    
                    <p class="text-muted small mb-4">
                        Unlock parent and student portals to allow students and their guardians to log in, view notices, access rosters, and retrieve report cards.
                    </p>

                    <form action="{{ route('school.billing.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="portals_unlock">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Choose Payment Gateway</label>
                            <select class="form-select rounded-3 py-2" name="gateway">
                                <option value="paystack">Paystack Gateway</option>
                                <option value="flutterwave">Flutterwave Gateway</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info text-white w-100 rounded-3 py-2 fw-bold">
                            <i class="bi bi-unlock-fill me-2"></i>Unlock Portal Access
                        </button>
                    </form>
                @endif
            </div>

            <!-- Report Card Print Credits Card -->
            <div class="glass-card p-4 mt-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-pdf-fill text-success me-2"></i>Report Print Credits</h5>
                @if(\App\Models\SystemSetting::getVal('report_card_payment_enabled', '1') == '0')
                    <div class="p-4 bg-success bg-opacity-10 text-success rounded-4 text-center">
                        <i class="bi bi-gift-fill fs-1 mb-3 d-block text-success"></i>
                        <span class="fw-bold d-block fs-5 mb-1">Free Printing Active</span>
                        <span class="small d-block text-muted">Report card printing payment has been deactivated by the platform admin. You can download and print report cards for free!</span>
                    </div>
                @else
                    <div class="p-3 bg-light rounded-4 mb-4 text-center">
                        <span class="text-muted small d-block mb-1">Current Credits Balance</span>
                        <h3 class="fw-extrabold text-success mb-0">{{ $reportCredits }} <span style="font-size: 1rem;" class="text-muted fw-normal">credits</span></h3>
                        <span class="text-muted small d-block mt-1">Rate: GHS {{ number_format($reportCardPrice, 2) }} / student card</span>
                    </div>
                    
                    <p class="text-muted small mb-4">
                        Generate and download official PDF report cards for your students. Generating a student's card for the first time in a term consumes 1 credit. Subsequent downloads/prints are free.
                    </p>

                    <form action="{{ route('school.billing.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="report_credits_purchase">
                        <div class="mb-3">
                            <label for="credits" class="form-label fw-semibold small text-secondary">Number of Credits</label>
                            <input type="number" class="form-control rounded-3 py-2 border shadow-xs" id="credits" name="credits" min="1" required value="50" placeholder="e.g. 50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Choose Payment Gateway</label>
                            <select class="form-select rounded-3 py-2" name="gateway">
                                <option value="paystack">Paystack Gateway</option>
                                <option value="flutterwave">Flutterwave Gateway</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100 rounded-3 py-2 fw-bold">
                            <i class="bi bi-cart-plus-fill me-2"></i>Buy Print Credits
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Upgrade Pricing Grid -->
        <div class="col-md-8">
            <div class="glass-card p-4 h-100">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-box-arrow-in-up text-success me-2"></i>Upgrade Subscription Plan</h5>
                
                <div class="row g-3">
                    @foreach($plans as $plan)
                        @if($school->plan_id !== $plan->id)
                            <div class="col-md-6">
                                <div class="card border-light rounded-4 shadow-sm overflow-hidden h-100">
                                    <div class="p-4 text-center bg-light border-bottom">
                                        <h5 class="fw-bold text-dark mb-1">{{ $plan->name }}</h5>
                                        <h2 class="fw-extrabold text-primary mb-0">GHS {{ number_format($plan->price_monthly, 0) }}<span style="font-size: 1rem;" class="text-muted fw-normal">/term</span></h2>
                                    </div>
                                    <div class="p-4 d-flex flex-column justify-content-between h-100">
                                        <ul class="list-unstyled mb-4 small text-muted">
                                            <li class="mb-2"><i class="bi bi-check-lg text-success me-2"></i>{{ $plan->max_students == -1 ? 'Unlimited' : 'Up to ' . $plan->max_students }} Students</li>
                                            <li class="mb-2"><i class="bi bi-check-lg text-success me-2"></i>{{ $plan->max_campuses == -1 ? 'Unlimited' : 'Up to ' . $plan->max_campuses }} Campuses</li>
                                            <li class="mb-2"><i class="bi bi-check-lg text-success me-2"></i><strong>{{ $plan->sms_credits_monthly == -1 ? 'Unlimited' : $plan->sms_credits_monthly }}</strong> Free SMS Credits /term</li>
                                            <li class="mb-2"><i class="bi bi-check-lg text-success me-2"></i>Full Module Access</li>
                                        </ul>
                                        
                                        <form action="{{ route('school.billing.checkout') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                            <input type="hidden" name="cycle" value="monthly">
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small text-secondary">Choose Payment Gateway</label>
                                                <select class="form-select rounded-3 py-2" name="gateway">
                                                    <option value="paystack">Paystack Gateway</option>
                                                    <option value="flutterwave">Flutterwave Gateway</option>
                                                </select>
                                            </div>
                                            
                                            <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                                                Upgrade Plan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-md-6">
                                <div class="card border-primary border-2 rounded-4 shadow-sm overflow-hidden h-100">
                                    <div class="p-4 text-center bg-primary bg-opacity-10 border-bottom">
                                        <h5 class="fw-bold text-primary mb-1">{{ $plan->name }} (Current)</h5>
                                        <h2 class="fw-extrabold text-primary mb-0">Active Plan</h2>
                                    </div>
                                    <div class="p-4">
                                        <p class="text-muted small text-center my-5"><i class="bi bi-check-circle-fill text-success fs-1 mb-2 d-block"></i>You are currently subscribed to this plan. You can contact support for custom volume adjustments.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="col-md-12">
            <div class="glass-card p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history text-secondary me-2"></i>Billing & Transaction History</h5>
                
                @if($history->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-receipt fs-1 d-block mb-2 text-secondary"></i>
                        <span>No invoice or payment history recorded.</span>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Payment Reference</th>
                                    <th>Gateway</th>
                                    <th>Amount Paid</th>
                                    <th>Billing Cycle</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history as $sub)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $sub->description ?: ($sub->plan ? $sub->plan->name : 'Payment') }}</span>
                                        </td>
                                        <td>
                                            <code class="small text-secondary">{{ $sub->payment_reference }}</code>
                                        </td>
                                        <td class="text-uppercase small fw-semibold text-secondary">
                                            {{ $sub->payment_method }}
                                        </td>
                                        <td class="fw-bold text-dark">
                                            GHS {{ number_format($sub->amount_paid, 2) }}
                                        </td>
                                        <td class="small text-muted">
                                            @if($sub->plan_id)
                                                {{ $sub->starts_at->format('M d, Y') }} - {{ $sub->ends_at->format('M d, Y') }}
                                            @else
                                                {{ $sub->starts_at->format('M d, Y') }} <span class="badge bg-light text-secondary ms-1">One-time</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Paid</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
