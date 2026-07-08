@extends('layouts.app')

@section('title', 'Platform Settings | ' . config('app.name', 'EduLink') . ' Admin')
@section('header_title', config('app.name', 'EduLink') . ' System Settings & Configurations')

@section('content')
<div class="container-fluid p-0">
    <!-- Session Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('super-admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4">
            <!-- Left: Pricing & Controls -->
            <div class="col-lg-6">
                <div class="glass-card p-4 h-100 shadow-sm">
                    <h5 class="fw-bold text-dark mb-4"><i class="bi bi-sliders2-vertical text-primary me-2"></i>Monetization & Controls</h5>
                    <p class="text-muted small mb-4">Manage monetization variables and core platform status control flags.</p>
                    
                    <div class="mb-4">
                        <label for="platform_name" class="form-label fw-semibold text-secondary small">Platform Branding Name</label>
                        <input type="text" class="form-control rounded-3 py-2 border shadow-xs" id="platform_name" name="platform_name" required value="{{ $platformName }}" placeholder="e.g. {{ config('app.name', 'EduLink') }}">
                        <span class="text-muted small d-block mt-2">The system-wide name used across headers, login logos, and titles.</span>
                    </div>

                    <div class="mb-4">
                        <label for="favicon" class="form-label fw-semibold text-secondary small">Platform Favicon (PNG/ICO)</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="border rounded p-2 bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <img src="{{ asset('favicon.png') }}?v={{ time() }}" alt="Favicon" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>
                            <input type="file" class="form-control rounded-3 py-2 border shadow-xs" id="favicon" name="favicon" accept="image/png, image/x-icon, image/vnd.microsoft.icon">
                        </div>
                        <span class="text-muted small d-block mt-2">Upload a custom square image (PNG or ICO format, max 2MB) to override the platform favicon.</span>
                    </div>

                    <div class="mb-4">
                        <label for="super_admin_notification_email" class="form-label fw-semibold text-secondary small">Super Admin Notification Email</label>
                        <input type="email" class="form-control rounded-3 py-2 border shadow-xs" id="super_admin_notification_email" name="super_admin_notification_email" required value="{{ $superAdminNotificationEmail }}" placeholder="admin@edulink.com">
                        <span class="text-muted small d-block mt-2">The email address where platform alerts (such as new school registrations) will be sent.</span>
                    </div>

                    <div class="mb-4">
                        <label for="website_builder_unlock_price" class="form-label fw-semibold text-secondary small">Custom Website Unlock Price (GHS)</label>
                        <div class="input-group">
                            <span class="input-group-text border-light bg-light fw-bold text-secondary">GHS</span>
                            <input type="number" class="form-control rounded-end-3 py-2 border shadow-xs" id="website_builder_unlock_price" name="website_builder_unlock_price" step="0.01" min="0" required value="{{ $websiteUnlockPrice }}">
                        </div>
                        <span class="text-muted small d-block mt-2">The one-time billing price for tenant schools to unlock the site builder.</span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="report_card_price" class="form-label fw-semibold text-secondary small">Report Card Fee (GHS / student)</label>
                            <div class="input-group">
                                <span class="input-group-text border-light bg-light fw-bold text-secondary">GHS</span>
                                <input type="number" class="form-control rounded-end-3 py-2 border shadow-xs" id="report_card_price" name="report_card_price" step="0.01" min="0" required value="{{ $reportCardPrice }}">
                            </div>
                            <span class="text-muted small d-block mt-2">Charge per student report card generation.</span>
                        </div>
                        <div class="col-md-6">
                            <label for="portal_unlock_price" class="form-label fw-semibold text-secondary small">Portals Activation Price (GHS)</label>
                            <div class="input-group">
                                <span class="input-group-text border-light bg-light fw-bold text-secondary">GHS</span>
                                <input type="number" class="form-control rounded-end-3 py-2 border shadow-xs" id="portal_unlock_price" name="portal_unlock_price" step="0.01" min="0" required value="{{ $portalUnlockPrice }}">
                            </div>
                            <span class="text-muted small d-block mt-2">One-time fee to unlock student/parent portals.</span>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3 mt-4">Feature Control Switches</h6>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="self_registration_enabled" name="self_registration_enabled" value="1" {{ $selfRegistration == '1' ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark small ms-2" for="self_registration_enabled">Enable School Self-Registration</label>
                        <span class="text-muted small d-block">When disabled, guest signup routes will be disabled and new school tenants can only be manually added by a platform administrator.</span>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="report_card_payment_enabled" name="report_card_payment_enabled" value="1" {{ $reportCardPaymentEnabled == '1' ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark small ms-2" for="report_card_payment_enabled">Enable Report Card Printing Payment</label>
                        <span class="text-muted small d-block">When enabled, schools must purchase credits to download/print report cards. When disabled, report card printing is free for all schools.</span>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input text-danger" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" {{ $maintenanceMode == '1' ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-danger small ms-2" for="maintenance_mode">Enable Platform Maintenance Mode</label>
                        <span class="text-muted small d-block text-danger opacity-75">When enabled, the entire system blocks non-super-admin sessions and serves a platform-wide offline banner. Use with caution.</span>
                    </div>

                    <hr class="my-4 text-muted opacity-25">

                    <h6 class="fw-bold text-dark mb-3">Paid-Only (Paywalled) Modules</h6>
                    <p class="text-muted small mb-3">Check the modules that require a paid subscription to access. Schools on trial or free tier will not have access to these modules.</p>
                    
                    @foreach([
                        'website_builder' => 'Custom Website Builder',
                        'finance' => 'Finance & Invoicing',
                        'academics' => 'Academics & Scoring Systems',
                        'sms' => 'SMS Blast Campaigns',
                        'lms' => 'LMS Courseware & Online Classes',
                        'portals' => 'Student & Parent Portals'
                    ] as $key => $label)
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="paid_only_{{ $key }}" name="paid_only_modules[]" value="{{ $key }}" {{ in_array($key, $paidOnlyModules) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-dark small ms-2" for="paid_only_{{ $key }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Platform Payment Gateways -->
            <div class="col-lg-6">
                <div class="glass-card p-4 h-100 shadow-sm">
                    <h5 class="fw-bold text-dark mb-4"><i class="bi bi-credit-card text-success me-2"></i>Payment Gateways (SaaS Subscriptions)</h5>
                    <p class="text-muted small mb-4">Configure API keys for the platform-level merchant accounts used to collect subscription fees from schools.</p>

                    <!-- Paystack -->
                    <div class="p-3 bg-light rounded-4 mb-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold text-dark small"><i class="bi bi-wallet2 text-primary me-2"></i>Paystack Integration</span>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="platform_paystack_enabled" name="platform_paystack_enabled" value="1" {{ $paystackEnabled == '1' ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold text-muted ms-1" for="platform_paystack_enabled">Active</label>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small text-muted">Public Key</label>
                            <input type="text" class="form-control form-control-sm rounded-2" name="platform_paystack_public_key" value="{{ $paystackPublicKey }}" placeholder="pk_test_...">
                        </div>
                        <div>
                            <label class="form-label small text-muted">Secret Key</label>
                            <input type="password" class="form-control form-control-sm rounded-2" name="platform_paystack_secret_key" value="{{ $paystackSecretKey }}" placeholder="sk_test_...">
                        </div>
                    </div>

                    <!-- Flutterwave -->
                    <div class="p-3 bg-light rounded-4 border">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold text-dark small"><i class="bi bi-wallet2 text-info me-2"></i>Flutterwave Integration</span>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="platform_flutterwave_enabled" name="platform_flutterwave_enabled" value="1" {{ $flutterwaveEnabled == '1' ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold text-muted ms-1" for="platform_flutterwave_enabled">Active</label>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small text-muted">Public Key</label>
                            <input type="text" class="form-control form-control-sm rounded-2" name="platform_flutterwave_public_key" value="{{ $flutterwavePublicKey }}" placeholder="FLWPUBK_TEST-...">
                        </div>
                        <div>
                            <label class="form-label small text-muted">Secret Key</label>
                            <input type="password" class="form-control form-control-sm rounded-2" name="platform_flutterwave_secret_key" value="{{ $flutterwaveSecretKey }}" placeholder="FLWSECK_TEST-...">
                        </div>
                    </div>

                    <!-- Platform SMS Notifications -->
                    <div class="glass-card p-4 shadow-sm mt-4">
                        <h5 class="fw-bold text-dark mb-4"><i class="bi bi-chat-left-text-fill text-info me-2"></i>Platform SMS Notifications</h5>
                        <p class="text-muted small mb-4">Configure the message template and gateway settings for notifications sent to newly registered school owners.</p>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="sms_gateway_provider" class="form-label fw-semibold text-secondary small">SMS Gateway Provider</label>
                                <select class="form-select rounded-3 py-2 border shadow-xs" id="sms_gateway_provider" name="sms_gateway_provider" required>
                                    <option value="simulation" {{ $smsGatewayProvider === 'simulation' ? 'selected' : '' }}>Simulation (Logs Only)</option>
                                    <option value="arkesel" {{ $smsGatewayProvider === 'arkesel' ? 'selected' : '' }}>Arkesel (Ghana)</option>
                                    <option value="twilio" {{ $smsGatewayProvider === 'twilio' ? 'selected' : '' }}>Twilio (Global)</option>
                                    <option value="bms" {{ $smsGatewayProvider === 'bms' ? 'selected' : '' }}>BMS Africa</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="sms_gateway_sender_id" class="form-label fw-semibold text-secondary small">Platform Sender ID</label>
                                <input type="text" class="form-control rounded-3 py-2 border shadow-xs" id="sms_gateway_sender_id" name="sms_gateway_sender_id" value="{{ $smsGatewaySenderId }}" required max="11" placeholder="e.g. {{ substr(config('app.name', 'EduLink'), 0, 11) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="sms_gateway_api_key" class="form-label fw-semibold text-secondary small">SMS API Key / Credentials</label>
                            <input type="text" class="form-control rounded-3 py-2 border shadow-xs" id="sms_gateway_api_key" name="sms_gateway_api_key" value="{{ $smsGatewayApiKey }}" placeholder="API Key / SID,Token,FromNumber">
                            <span class="text-muted small d-block mt-1">For Twilio, use format: <code>AccountSID,AuthToken,FromNumber</code></span>
                        </div>

                        <div class="mb-3">
                            <label for="whatsapp_channel_url" class="form-label fw-semibold text-secondary small">WhatsApp Channel URL</label>
                            <input type="url" class="form-control rounded-3 py-2 border shadow-xs" id="whatsapp_channel_url" name="whatsapp_channel_url" value="{{ $whatsappChannelUrl }}" placeholder="https://whatsapp.com/channel/...">
                        </div>

                        <div class="mb-3">
                            <label for="school_registration_sms_template" class="form-label fw-semibold text-secondary small">School Registration Received SMS Template</label>
                            <textarea class="form-control rounded-3 py-2 border shadow-xs" id="school_registration_sms_template" name="school_registration_sms_template" rows="5" required>{{ $schoolRegistrationSmsTemplate }}</textarea>
                            <span class="text-muted small d-block mt-2">Available variables: <code>{admin_name}</code>, <code>{school_name}</code>, <code>{whatsapp_link}</code>.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Save Action bar -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="glass-card p-3 d-flex justify-content-between align-items-center shadow-xs">
                    <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Saving updates all platform configurations globally.</span>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold">
                        <i class="bi bi-save me-1"></i>Save All Settings
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- SMS Connectivity Tester Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="glass-card p-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-send-check text-primary me-2"></i>SMS Connectivity Tester</h5>
                <p class="text-muted small">Verify that your configured SMS API Gateway credentials are working correctly by sending a live or simulated text message.</p>

                @if($errors->has('test_sms'))
                    <div class="alert alert-danger border-0 bg-danger text-white mb-3" style="border-radius: 12px; --bs-bg-opacity: 0.2;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first('test_sms') }}
                    </div>
                @endif

                <form action="{{ route('super-admin.settings.sms-test') }}" method="POST" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-3">
                        <label for="test_phone" class="form-label fw-semibold text-secondary small">Recipient Phone Number</label>
                        <input type="text" class="form-control rounded-3 py-2 border shadow-xs" id="test_phone" name="test_phone" required placeholder="e.g. +233240000000" value="{{ old('test_phone') }}">
                    </div>
                    <div class="col-md-7">
                        <label for="test_message" class="form-label fw-semibold text-secondary small">Test Message Body</label>
                        <input type="text" class="form-control rounded-3 py-2 border shadow-xs" id="test_message" name="test_message" required placeholder="Type a brief text message..." value="{{ old('test_message', 'Hello! This is a test message from ' . \App\Models\SystemSetting::getVal('platform_name', config('app.name', 'EduLink')) . ' SMS Gateway.') }}" max="160">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary rounded-3 w-100 py-2 fw-semibold">
                            <i class="bi bi-send me-1"></i>Send Test SMS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bottom: Stats -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="glass-card p-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-cpu text-info me-2"></i>Platform Infrastructure Analytics</h5>
                
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="p-3 bg-light rounded-4">
                            <span class="text-muted small d-block mb-1">Framework Version</span>
                            <span class="fw-bold text-dark">Laravel v{{ app()->version() }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="p-3 bg-light rounded-4">
                            <span class="text-muted small d-block mb-1">PHP Interpreter</span>
                            <span class="fw-bold text-dark">v{{ PHP_VERSION }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="p-3 bg-light rounded-4">
                            <span class="text-muted small d-block mb-1">Local Database Connection</span>
                            <span class="fw-bold text-dark text-uppercase">{{ config('database.default') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="p-3 bg-light rounded-4">
                            <span class="text-muted small d-block mb-1">Platform Mode</span>
                            <span class="badge bg-success bg-opacity-10 text-success text-uppercase py-1 px-2 mt-1">Production Ready</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
