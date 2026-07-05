@extends('layouts.app')

@section('title', 'EduLink secure checkout gateway')

@section('content')
@php
    $isPaystack = $gateway === 'paystack';
    $primaryColor = $isPaystack ? '#3bb75e' : '#f5a623';
    $gatewayName = $isPaystack ? 'Paystack' : 'Flutterwave';
    $gatewayLogo = $isPaystack 
        ? 'https://js.paystack.co/v1/logo.png'
        : 'https://flutterwave.com/images/logo/logo-colored.svg';
@endphp

<style>
    .checkout-right-panel {
        background-color: #ffffff;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    [data-bs-theme="dark"] .checkout-right-panel {
        background-color: var(--card-bg) !important;
        color: #f1f5f9 !important;
        border-left: 1px solid var(--border-color);
    }
    [data-bs-theme="dark"] .checkout-card {
        background-color: var(--card-bg) !important;
        border: 1px solid var(--border-color) !important;
    }
    [data-bs-theme="dark"] .form-control:disabled {
        background-color: rgba(255, 255, 255, 0.03) !important;
        color: #cbd5e1 !important;
        border-color: var(--border-color) !important;
    }
    [data-bs-theme="dark"] .input-group-text {
        background-color: rgba(255, 255, 255, 0.03) !important;
        color: #cbd5e1 !important;
        border-color: var(--border-color) !important;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('school.billing.index') }}" class="text-decoration-none">Billing Panel</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Gateway Checkout</li>
                </ol>
            </nav>

            <div class="card border-0 shadow-lg overflow-hidden rounded-4 checkout-card">
                <div class="row g-0">
                    <!-- Left Panel: Invoice Details -->
                    <div class="col-md-5 bg-dark text-white p-5 d-flex flex-column justify-content-between">
                        <div>
                            <div class="mb-4">
                                <img src="{{ $gatewayLogo }}" alt="{{ $gatewayName }} Logo" height="28" class="opacity-75">
                            </div>
                            
                            <h4 class="fw-bold mb-1">Transaction Summary</h4>
                            <p class="text-muted small">Secure payment processed for {{ $school->name }}</p>
                            
                            <hr class="border-secondary my-4">
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Item</span>
                                <span class="small fw-semibold">
                                    @if($type === 'subscription')
                                        {{ $plan->name }} Subscription ({{ ucfirst($cycle) }})
                                    @elseif($type === 'website_unlock')
                                        Custom Website Builder Unlock Add-on
                                    @elseif($type === 'portals_unlock')
                                        Student & Parent Portals Activation Add-on
                                    @elseif($type === 'report_credits_purchase')
                                        Report Card Print Credits ({{ request('credits') }} units)
                                    @endif
                                </span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Subtotal</span>
                                <span class="small">GHS {{ number_format($amount, 2) }}</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Tax (VAT 0%)</span>
                                <span class="small">GHS 0.00</span>
                            </div>

                            <hr class="border-secondary my-4">

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-white fw-bold">Total Amount Due</span>
                                <span class="fs-4 fw-extrabold text-white" style="color: {{ $primaryColor }} !important;">GHS {{ number_format($amount, 2) }}</span>
                            </div>
                        </div>

                        <div class="mt-5 text-muted small">
                            <i class="bi bi-shield-lock-fill text-success me-1"></i> Secured with 256-bit SSL encryption.
                        </div>
                    </div>

                    <!-- Right Panel: Gateway Simulator / Live Checkout -->
                    <div class="col-md-7 p-5 checkout-right-panel">
                        @if($isPaystack && $paystackEnabled && !empty($paystackPublicKey))
                            <!-- Live Paystack Integration -->
                            <div class="d-flex flex-column justify-content-between h-100">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="fw-bold text-dark mb-0">Secure Gateway Payment</h5>
                                        <span class="badge bg-success text-white border py-2 px-3 rounded-pill small">
                                            @if(str_contains($paystackPublicKey, 'test'))
                                                Paystack Test Mode
                                            @else
                                                Paystack Live Mode
                                            @endif
                                        </span>
                                    </div>

                                    <div class="text-center py-4 my-3">
                                        <div class="payment-icon-wrap mb-3" style="font-size: 3.5rem; color: #3bb75e;">
                                            <i class="bi bi-shield-check"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark">Pay Securely via Paystack</h5>
                                        <p class="text-muted small px-3">
                                            You will be redirected to the secure Paystack checkout overlay to complete your transaction. Paystack accepts:
                                        </p>
                                        <div class="d-flex justify-content-center gap-3 mt-3 text-muted">
                                            <span class="small"><i class="bi bi-credit-card me-1"></i>Cards</span>
                                            <span class="small"><i class="bi bi-phone me-1"></i>Mobile Money</span>
                                            <span class="small"><i class="bi bi-bank me-1"></i>Bank Transfer</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="button" class="btn btn-lg w-100 text-white fw-bold shadow py-3" style="background-color: {{ $primaryColor }} !important;" onclick="payWithPaystack()">
                                        <i class="bi bi-credit-card-2-front me-1"></i> Pay GHS {{ number_format($amount, 2) }}
                                    </button>
                                    
                                    <div class="text-center mt-3">
                                        <a href="{{ route('school.billing.index') }}" class="text-muted small text-decoration-none">
                                            <i class="bi bi-arrow-left me-1"></i> Cancel and return to billing
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Gateway Simulator -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold text-dark mb-0">Select Payment Method</h5>
                                <span class="badge bg-warning text-black border-0 py-2 px-3 rounded-pill small fw-bold">Sandbox Simulator</span>
                            </div>

                            @if($isPaystack)
                                <div class="alert alert-warning border-0 rounded-3 small mb-4 py-2 px-3">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> <strong>Notice:</strong> Paystack API keys are not configured in system settings. Displaying the mock gateway simulator.
                                </div>
                            @endif

                            <!-- Tab Options -->
                            <ul class="nav nav-pills mb-4 p-1 bg-light rounded-3" id="paymentTabs" role="tablist">
                                <li class="nav-item flex-fill" role="presentation">
                                    <button class="nav-link active w-100 rounded-3 small fw-semibold" id="card-tab" data-bs-toggle="pill" data-bs-target="#card-method" type="button" role="tab">
                                        <i class="bi bi-credit-card me-1"></i> Credit / Debit Card
                                    </button>
                                </li>
                                <li class="nav-item flex-fill" role="presentation">
                                    <button class="nav-link w-100 rounded-3 small fw-semibold" id="momo-tab" data-bs-toggle="pill" data-bs-target="#momo-method" type="button" role="tab">
                                        <i class="bi bi-phone me-1"></i> Mobile Money
                                    </button>
                                </li>
                            </ul>

                            <!-- Tab Contents -->
                            <div class="tab-content mb-4" id="paymentTabContent">
                                <!-- Card Method -->
                                <div class="tab-pane fade show active" id="card-method" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-secondary">Card Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text border shadow-xs"><i class="bi bi-credit-card-2-front text-muted"></i></span>
                                                <input type="text" class="form-control py-2 shadow-xs" placeholder="4012  0000  1111  2222" value="4012 8831 2901 8847" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-secondary">Expiry Date</label>
                                            <input type="text" class="form-control py-2 shadow-xs" placeholder="MM / YY" value="12 / 29" disabled>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-secondary">CVV / CVC</label>
                                            <input type="password" class="form-control py-2 shadow-xs" placeholder="•••" value="123" disabled>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-secondary">Cardholder Name</label>
                                            <input type="text" class="form-control py-2 shadow-xs" value="{{ $school->owner_name }}" disabled>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mobile Money Method -->
                                <div class="tab-pane fade" id="momo-method" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-secondary">Mobile Network Provider</label>
                                            <select class="form-select py-2 shadow-xs" disabled>
                                                <option selected>MTN Mobile Money</option>
                                                <option>Telecel Cash</option>
                                                <option>AT Money</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-secondary">Mobile Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text border shadow-xs">+233</span>
                                                <input type="text" class="form-control py-2 shadow-xs" value="244123456" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Simulated Actions -->
                            <div class="d-grid gap-3">
                                <form action="{{ route('school.billing.process-payment') }}" method="POST" id="successForm">
                                    @csrf
                                    <input type="hidden" name="type" value="{{ $type }}">
                                    <input type="hidden" name="gateway" value="{{ $gateway }}">
                                    <input type="hidden" name="status" value="success">
                                    @if($type === 'subscription')
                                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                        <input type="hidden" name="cycle" value="{{ $cycle }}">
                                    @endif
                                    @if($type === 'report_credits_purchase')
                                        <input type="hidden" name="credits" value="{{ request('credits') }}">
                                        @if(!empty($classId))
                                            <input type="hidden" name="class_id" value="{{ $classId }}">
                                            <input type="hidden" name="term_id" value="{{ $termId }}">
                                            <input type="hidden" name="academic_year_id" value="{{ $academicYearId }}">
                                        @endif
                                    @endif
                                    <button type="button" class="btn btn-lg w-100 text-white fw-bold shadow-sm py-3" style="background-color: {{ $primaryColor }} !important;" onclick="simulatePayment('success')">
                                        <i class="bi bi-check-circle me-1"></i> Authorize & Simulate Success
                                    </button>
                                </form>

                                <form action="{{ route('school.billing.process-payment') }}" method="POST" id="failForm">
                                    @csrf
                                    <input type="hidden" name="type" value="{{ $type }}">
                                    <input type="hidden" name="gateway" value="{{ $gateway }}">
                                    <input type="hidden" name="status" value="failed">
                                    <button type="button" class="btn btn-outline-danger btn-lg w-100 fw-bold py-3" onclick="simulatePayment('failed')">
                                        <i class="bi bi-x-circle me-1"></i> Cancel & Simulate Decline
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Processing overlay modal -->
<div class="modal fade" id="processingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg text-center p-5 rounded-4">
            <div class="spinner-border mb-4" role="status" style="width: 3rem; height: 3rem; color: {{ $primaryColor }} !important;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h5 class="fw-bold text-dark mb-2">Processing Transaction</h5>
            <p class="text-muted mb-0 small">Please do not refresh the page. Securely returning callback payloads to EduLink ERP...</p>
        </div>
    </div>
</div>

@if($isPaystack && $paystackEnabled && !empty($paystackPublicKey))
    <!-- Paystack Pop SDK and script -->
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        function payWithPaystack() {
            var handler = PaystackPop.setup({
                key: '{{ $paystackPublicKey }}',
                email: '{{ $school->owner_email ?: auth()->user()->email }}',
                amount: {{ round($amount * 100) }},
                currency: 'GHS',
                ref: 'pay_paystack_' + Math.random().toString(36).substring(2, 15) + '_' + Date.now(),
                callback: function(response) {
                    var modalEl = document.getElementById('processingModal');
                    var modalTitle = modalEl.querySelector('h5');
                    var modalDesc = modalEl.querySelector('p');
                    
                    modalTitle.innerText = 'Verifying Payment';
                    modalDesc.innerText = 'Please wait while we verify your transaction status with Paystack secure servers...';
                    
                    var modal = new bootstrap.Modal(modalEl);
                    modal.show();

                    // Create form elements programmatically to securely POST payment reference
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("school.billing.process-payment") }}';

                    var csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);

                    var typeInput = document.createElement('input');
                    typeInput.type = 'hidden';
                    typeInput.name = 'type';
                    typeInput.value = '{{ $type }}';
                    form.appendChild(typeInput);

                    var gatewayInput = document.createElement('input');
                    gatewayInput.type = 'hidden';
                    gatewayInput.name = 'gateway';
                    gatewayInput.value = 'paystack';
                    form.appendChild(gatewayInput);

                    var statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'status';
                    statusInput.value = 'success';
                    form.appendChild(statusInput);

                    var refInput = document.createElement('input');
                    refInput.type = 'hidden';
                    refInput.name = 'reference';
                    refInput.value = response.reference;
                    form.appendChild(refInput);

                    @if($type === 'subscription')
                        var planIdInput = document.createElement('input');
                        planIdInput.type = 'hidden';
                        planIdInput.name = 'plan_id';
                        planIdInput.value = '{{ $plan->id }}';
                        form.appendChild(planIdInput);

                        var cycleInput = document.createElement('input');
                        cycleInput.type = 'hidden';
                        cycleInput.name = 'cycle';
                        cycleInput.value = '{{ $cycle }}';
                        form.appendChild(cycleInput);
                    @endif

                     @if($type === 'report_credits_purchase')
                        var creditsInput = document.createElement('input');
                        creditsInput.type = 'hidden';
                        creditsInput.name = 'credits';
                        creditsInput.value = '{{ request('credits') }}';
                        form.appendChild(creditsInput);

                        @if(!empty($classId))
                            var classInput = document.createElement('input');
                            classInput.type = 'hidden';
                            classInput.name = 'class_id';
                            classInput.value = '{{ $classId }}';
                            form.appendChild(classInput);

                            var termInput = document.createElement('input');
                            termInput.type = 'hidden';
                            termInput.name = 'term_id';
                            termInput.value = '{{ $termId }}';
                            form.appendChild(termInput);

                            var yearInput = document.createElement('input');
                            yearInput.type = 'hidden';
                            yearInput.name = 'academic_year_id';
                            yearInput.value = '{{ $academicYearId }}';
                            form.appendChild(yearInput);
                        @endif
                    @endif

                    document.body.appendChild(form);
                    
                    setTimeout(function() {
                        form.submit();
                    }, 1500);
                },
                onClose: function() {
                    // Closed without paying
                }
            });
            handler.openIframe();
        }
    </script>
@else
    <script>
        function simulatePayment(status) {
            var modal = new bootstrap.Modal(document.getElementById('processingModal'));
            modal.show();
            
            setTimeout(function() {
                if (status === 'success') {
                    document.getElementById('successForm').submit();
                } else {
                    document.getElementById('failForm').submit();
                }
            }, 2000);
        }
    </script>
@endif
@endsection
