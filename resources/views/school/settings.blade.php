@extends('layouts.app')

@section('title', 'School Settings | EduLink')
@section('header_title', 'Settings & System Console')

@section('styles')
<style>
    .settings-tab-nav {
        border-bottom: 2px solid rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }
    .settings-tab-link {
        font-weight: 600;
        color: var(--text-muted);
        border: none;
        background: none;
        padding: 1rem 1.5rem;
        position: relative;
        transition: color 0.2s ease;
    }
    .settings-tab-link:hover, .settings-tab-link.active {
        color: var(--primary-color);
    }
    .settings-tab-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: var(--primary-color);
    }
    .feature-card {
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.25rem;
        background: var(--card-bg);
        transition: all 0.2s ease;
    }
    .feature-card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
    }
    .form-switch .form-check-input {
        width: 2.5em;
        height: 1.25em;
    }
    .form-switch .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .locked-badge {
        font-size: 0.75rem;
        font-weight: 700;
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        text-transform: uppercase;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="glass-card p-4">
                
                <!-- Nav tabs -->
                <div class="d-flex settings-tab-nav" id="settingsTab" role="tablist">
                    <button class="settings-tab-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                        <i class="bi bi-building me-2"></i>School Profile
                    </button>
                    <button class="settings-tab-link" id="gateway-tab" data-bs-toggle="tab" data-bs-target="#gateway" type="button" role="tab">
                        <i class="bi bi-hdd-network me-2"></i>SMTP & SMS Gateways
                    </button>
                    <button class="settings-tab-link" id="features-tab" data-bs-toggle="tab" data-bs-target="#features" type="button" role="tab">
                        <i class="bi bi-toggle-on me-2"></i>Active Modules
                    </button>
                    <button class="settings-tab-link" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic" type="button" role="tab">
                        <i class="bi bi-calendar-check me-2"></i>Academic Calendar
                    </button>
                    <button class="settings-tab-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab">
                        <i class="bi bi-credit-card me-2"></i>Payment Gateways
                    </button>
                    <button class="settings-tab-link" id="grading-tab" data-bs-toggle="tab" data-bs-target="#grading" type="button" role="tab">
                        <i class="bi bi-percent me-2"></i>Grading Scales
                    </button>
                    <a href="{{ route('school.settings.promotions.index') }}" class="settings-tab-link text-decoration-none">
                        <i class="bi bi-arrow-up-right-circle me-2"></i>Promotion Rules
                    </a>
                </div>

                <!-- Tab panes -->
                <div class="tab-content">
                    
                    <!-- TAB 1: School Profile -->
                    <div class="tab-pane fade show active" id="profile" role="tabpanel">
                        <h5 class="mb-4 font-weight-bold" style="font-weight: 700;">General School Details</h5>
                        <form action="{{ route('school.settings.profile') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600;">School Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $school->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600;">Short Name / Abbreviation</label>
                                    <input type="text" class="form-control" name="short_name" value="{{ old('short_name', $school->short_name) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600;">Contact Phone</label>
                                    <input type="text" class="form-control" name="phone" value="{{ old('phone', $school->phone) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600;">Primary Email Address</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email', $school->email) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-weight: 600;">Physical Address</label>
                                    <input type="text" class="form-control" name="address" value="{{ old('address', $school->address) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600;">Ghana Region</label>
                                    <select class="form-select" name="region">
                                        <option value="">Select Region</option>
                                        @foreach(['Greater Accra','Ashanti','Western','Northern','Central','Volta','Eastern','Upper East','Upper West','Bono','Bono East','Ahafo','Savannah','North East','Oti','Western North'] as $r)
                                            <option value="{{ $r }}" {{ $school->region == $r ? 'selected' : '' }}>{{ $r }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600;">District / Municipality</label>
                                    <input type="text" class="form-control" name="district" value="{{ old('district', $school->district) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600;">Subdomain Prefix</label>
                                    <input type="text" class="form-control bg-light" value="{{ $school->subdomain }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600;">Custom Mapping Domain</label>
                                    <input type="text" class="form-control" name="custom_domain" value="{{ old('custom_domain', $school->custom_domain) }}" placeholder="e.g. school.edu.gh">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600;">School Logo</label>
                                    <input type="file" class="form-control" name="logo" accept="image/*">
                                    <div class="form-text">Upload a PNG/JPG logo. Recommended dimension is square.</div>
                                    @if($school->logo && !\Illuminate\Support\Str::contains($school->logo, 'http'))
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $school->logo) }}" alt="School Logo" style="height: 40px; object-fit: contain; border: 1px solid #cbd5e1; padding: 2px; border-radius: 4px;">
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600;">Headteacher Signature</label>
                                    <input type="file" class="form-control" name="headteacher_signature" accept="image/*">
                                    <div class="form-text">Upload a clear signature scan for automated report card signatures.</div>
                                    @if(isset($school->settings['headteacher_signature']))
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $school->settings['headteacher_signature']) }}" alt="Headteacher Signature" style="height: 40px; object-fit: contain; border: 1px solid #cbd5e1; padding: 2px; border-radius: 4px;">
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600;">Attendance Cutoff Time (Late Threshold)</label>
                                    <input type="time" class="form-control" name="attendance_cutoff_time" value="{{ old('attendance_cutoff_time', isset($school->settings['attendance_cutoff_time']) ? substr($school->settings['attendance_cutoff_time'], 0, 5) : '08:30') }}">
                                    <div class="form-text">Students checking in after this time will be marked "Late". Default is 08:30 AM.</div>
                                </div>
                                <div class="col-12 mt-4">
                                    <hr class="my-3 opacity-10">
                                    <h6 class="font-weight-bold mb-3" style="font-weight: 700; color: var(--primary-color);"><i class="bi bi-card-heading me-2"></i>Student ID Auto-Generation Customizer</h6>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" style="font-weight: 600;">Student ID Prefix</label>
                                    <input type="text" class="form-control" name="student_id_prefix" value="{{ old('student_id_prefix', $school->settings['student_id_prefix'] ?? 'STD') }}" placeholder="e.g. STD" required>
                                    <div class="form-text">Main system prefix tag (e.g. STD, EDU, GVIS).</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" style="font-weight: 600;">ID Structure Format</label>
                                    <select class="form-select" name="student_id_format" required>
                                        @php
                                            $currFormat = $school->settings['student_id_format'] ?? '{PREFIX}-{YEAR}-{SEQUENCE}';
                                        @endphp
                                        <option value="{PREFIX}-{YEAR}-{SEQUENCE}" {{ $currFormat == '{PREFIX}-{YEAR}-{SEQUENCE}' ? 'selected' : '' }}>{PREFIX}-{YEAR}-{SEQUENCE} (e.g. STD-2026-0001)</option>
                                        <option value="{PREFIX}/{YEAR}/{SEQUENCE}" {{ $currFormat == '{PREFIX}/{YEAR}/{SEQUENCE}' ? 'selected' : '' }}>{PREFIX}/{YEAR}/{SEQUENCE} (e.g. STD/2026/0001)</option>
                                        <option value="{PREFIX}{YEAR}{SEQUENCE}" {{ $currFormat == '{PREFIX}{YEAR}{SEQUENCE}' ? 'selected' : '' }}>{PREFIX}{YEAR}{SEQUENCE} (e.g. STD20260001)</option>
                                        <option value="{YEAR}{SEQUENCE}" {{ $currFormat == '{YEAR}{SEQUENCE}' ? 'selected' : '' }}>{YEAR}{SEQUENCE} (e.g. 20260001)</option>
                                    </select>
                                    <div class="form-text">Defines how placeholders are combined.</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" style="font-weight: 600;">Next Sequence Number</label>
                                    <input type="number" class="form-control" name="student_id_next_sequence" value="{{ old('student_id_next_sequence', $school->settings['student_id_next_sequence'] ?? 1) }}" min="1" required>
                                    <div class="form-text">Counter index assigned to next admitted student.</div>
                                </div>
                                <div class="col-md-4 mt-3">
                                    <label class="form-label" style="font-weight: 600;">Admissions Welcome Channel</label>
                                    <select class="form-select" name="welcome_notification_channel" required>
                                        @php
                                            $currChannel = $school->settings['welcome_notification_channel'] ?? 'both';
                                        @endphp
                                        <option value="both" {{ $currChannel == 'both' ? 'selected' : '' }}>Email & SMS</option>
                                        <option value="email" {{ $currChannel == 'email' ? 'selected' : '' }}>Email Only</option>
                                        <option value="sms" {{ $currChannel == 'sms' ? 'selected' : '' }}>SMS Only</option>
                                    </select>
                                    <div class="form-text">Choose how portal login details are delivered.</div>
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Save General Settings</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 2: SMTP & SMS Gateways -->
                    <div class="tab-pane fade" id="gateway" role="tabpanel">
                        <div class="row g-5">
                            
                            <!-- Email SMTP Configurations -->
                            <div class="col-md-6">
                                <h5 class="mb-4 font-weight-bold" style="font-weight: 700;"><i class="bi bi-envelope-at me-2 text-primary"></i>Custom SMTP Email Gateway</h5>
                                <form action="{{ route('school.settings.gateway') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">SMTP Host</label>
                                            <input type="text" class="form-control" name="smtp_host" value="{{ old('smtp_host', $school->email_config['host'] ?? '') }}" placeholder="smtp.mailtrap.io">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">SMTP Port</label>
                                            <input type="number" class="form-control" name="smtp_port" value="{{ old('smtp_port', $school->email_config['port'] ?? '') }}" placeholder="2525">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Encryption Protocol</label>
                                            <select class="form-select" name="smtp_encryption">
                                                <option value="none" {{ ($school->email_config['encryption'] ?? '') == 'none' ? 'selected' : '' }}>None</option>
                                                <option value="tls" {{ ($school->email_config['encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                                                <option value="ssl" {{ ($school->email_config['encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">SMTP Username</label>
                                            <input type="text" class="form-control" name="smtp_username" value="{{ old('smtp_username', $school->email_config['username'] ?? '') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">SMTP Password</label>
                                            <input type="password" class="form-control" name="smtp_password" value="{{ old('smtp_password', $school->email_config['password'] ?? '') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Mail From Address</label>
                                            <input type="email" class="form-control" name="smtp_from_address" value="{{ old('smtp_from_address', $school->email_config['from_address'] ?? '') }}" placeholder="info@school.edu.gh">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Mail From Name</label>
                                            <input type="text" class="form-control" name="smtp_from_name" value="{{ old('smtp_from_name', $school->email_config['from_name'] ?? '') }}" placeholder="Primary Campus Mail">
                                        </div>
                                    </div>
                            </div>

                            <!-- SMS Gateway Configurations -->
                            <div class="col-md-6" style="border-left: 1px solid var(--border-color)">
                                <h5 class="mb-4 font-weight-bold" style="font-weight: 700;"><i class="bi bi-chat-left-text me-2 text-success"></i>SMS Gateway API Configurations</h5>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">SMS Provider</label>
                                            <select class="form-select" name="sms_provider">
                                                <option value="log" {{ ($school->sms_gateway_config['provider'] ?? '') == 'log' ? 'selected' : '' }}>Local Simulation (Log Only)</option>
                                                <option value="arkesel" {{ ($school->sms_gateway_config['provider'] ?? '') == 'arkesel' ? 'selected' : '' }}>Arkesel Ghana API</option>
                                                <option value="hubtel" {{ ($school->sms_gateway_config['provider'] ?? '') == 'hubtel' ? 'selected' : '' }}>Hubtel SMS API</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">API Gateway Key</label>
                                            <input type="password" class="form-control" name="sms_api_key" value="{{ old('sms_api_key', $school->sms_gateway_config['api_key'] ?? '') }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Default Sender ID (Max 11 characters)</label>
                                            <input type="text" class="form-control" name="sms_sender_id" value="{{ old('sms_sender_id', $school->sms_gateway_config['sender_id'] ?? '') }}" placeholder="EDULINK">
                                        </div>
                                    </div>
                            </div>
                            
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-success px-4"><i class="bi bi-save me-2"></i>Save Gateway Details</button>
                            </div>
                            </form>
                        </div>
                    </div>

                    <!-- TAB 3: Active Modules -->
                    <div class="tab-pane fade" id="features" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="m-0 font-weight-bold" style="font-weight: 700;">ERP Active Modules (Feature Flags)</h5>
                                <p class="text-muted mb-0 small">Toggle ERP features active. Availability is constrained by your current SaaS subscription plan: <strong>{{ $school->plan->name }}</strong>.</p>
                            </div>
                            <span class="badge bg-primary px-3 py-2 rounded-3 text-uppercase">Plan: {{ $school->plan->name }}</span>
                        </div>

                        <form action="{{ route('school.settings.features') }}" method="POST">
                            @csrf
                            <div class="row g-4">
                                
                                @php
                                    $allModules = [
                                        'finance' => ['name' => 'Billing & Accounting', 'desc' => 'Generate student fee invoices, record payments, and manage ledgers.', 'icon' => 'bi-wallet2'],
                                        'website_builder' => ['name' => 'Website Page Builder', 'desc' => 'Edit public school homepage using the GrapesJS drag-and-drop panel.', 'icon' => 'bi-window-sidebar'],
                                        'lms' => ['name' => 'LMS & Lesson Planner', 'desc' => 'Upload syllabus, conduct quizzes, host forum conversations.', 'icon' => 'bi-laptop'],
                                        'attendance' => ['name' => 'Attendance Management', 'desc' => 'Track student daily attendance, late entries, and absences.', 'icon' => 'bi-check2-square'],
                                        'sms' => ['name' => 'SMS Broadcasts', 'desc' => 'Auto-dispatch terminal grades and reminders to parents.', 'icon' => 'bi-chat-left-dots'],
                                        'custom_domain' => ['name' => 'Custom Domain', 'desc' => 'Bind portal access routes to your own customized domains.', 'icon' => 'bi-globe'],
                                        'safeguarding' => ['name' => 'Safeguarding & Case Escalations', 'desc' => 'Secure tracking of child safety cases and HOD alerts.', 'icon' => 'bi-shield-check'],
                                        'ai_analytics' => ['name' => 'AI Performance Metrics', 'desc' => 'Track students scores over time with automatic AI trendlines.', 'icon' => 'bi-cpu'],
                                    ];
                                    $planFeatures = $school->plan->features ?? [];
                                    $enabledModules = $school->settings['enabled_modules'] ?? $planFeatures;
                                @endphp

                                @foreach($allModules as $slug => $meta)
                                    @php
                                        $isAllowed = in_array($slug, $planFeatures);
                                        $isEnabled = in_array($slug, $enabledModules) && $isAllowed;
                                    @endphp
                                    <div class="col-md-6">
                                        <div class="feature-card d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center me-3">
                                                <div class="bg-light p-3 rounded-3 me-3 text-secondary">
                                                    <i class="bi {{ $meta['icon'] }} fs-4"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 font-weight-bold" style="font-weight: 600;">{{ $meta['name'] }}</h6>
                                                    <p class="text-muted mb-0 small" style="line-height: 1.3;">{{ $meta['desc'] }}</p>
                                                </div>
                                            </div>
                                            <div>
                                                @if($isAllowed)
                                                    <div class="form-check form-switch">
                                                        <input type="hidden" name="modules[{{ $slug }}]" value="0">
                                                        <input class="form-check-input" type="checkbox" name="modules[{{ $slug }}]" value="1" {{ $isEnabled ? 'checked' : '' }}>
                                                    </div>
                                                @else
                                                    <span class="locked-badge"><i class="bi bi-lock-fill me-1"></i>Upgrade</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endphp
                                @endforeach

                                <div class="col-12 text-end mt-4">
                                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Save Modules Toggles</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 4: Academic Calendar -->
                    <div class="tab-pane fade" id="academic" role="tabpanel">
                        <h5 class="mb-4 font-weight-bold" style="font-weight: 700;">Academic Calendar Overview</h5>
                        <p class="text-muted mb-4">View seeded academic years and active terms inside the school database.</p>
                        
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Academic Year</th>
                                        <th>Active State</th>
                                        <th>Term Names</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($academicYears as $year)
                                        <tr>
                                            <td class="font-weight-bold" style="font-weight: 600;">{{ $year->name }}</td>
                                            <td>
                                                @if($year->is_active)
                                                    <span class="badge bg-success">Active Calendar</span>
                                                @else
                                                    <span class="badge bg-secondary">Archived</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $terms = \App\Models\Term::where('academic_year_id', $year->id)->pluck('name')->toArray();
                                                @endphp
                                                {{ count($terms) ? implode(', ', $terms) : 'No terms seeded.' }}
                                            </td>
                                            <td>{{ $year->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No academic calendars initialized. Complete onboarding first.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
 
                    <!-- TAB 5: Payment Gateways -->
                    <div class="tab-pane fade" id="payment" role="tabpanel">
                        <h5 class="mb-2 font-weight-bold" style="font-weight: 700;"><i class="bi bi-credit-card text-success me-2"></i>Payment Gateways & Merchant Credentials</h5>
                        <p class="text-muted small mb-4">Configure custom developer integration keys to process parent and guardian school fee payments directly into your school merchant account.</p>

                        @php
                            $paystack = $school->settings['payment_gateways']['paystack'] ?? [];
                            $flutterwave = $school->settings['payment_gateways']['flutterwave'] ?? [];
                        @endphp

                        <form action="{{ route('school.settings.payments') }}" method="POST">
                            @csrf
                            <div class="row g-4">
                                <!-- Paystack -->
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-4 border">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="fw-bold text-dark small"><i class="bi bi-wallet2 text-primary me-2"></i>Paystack Settings</span>
                                            <div class="form-check form-switch m-0">
                                                <input class="form-check-input" type="checkbox" id="paystack_enabled" name="paystack_enabled" value="1" {{ ($paystack['enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-semibold text-muted ms-1" for="paystack_enabled">Active</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Public Key</label>
                                            <input type="text" class="form-control rounded-3" name="paystack_public_key" value="{{ $paystack['public_key'] ?? '' }}" placeholder="pk_live_...">
                                        </div>
                                        <div>
                                            <label class="form-label small text-muted">Secret Key</label>
                                            <input type="password" class="form-control rounded-3" name="paystack_secret_key" value="{{ $paystack['secret_key'] ?? '' }}" placeholder="sk_live_...">
                                        </div>
                                    </div>
                                </div>

                                <!-- Flutterwave -->
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-4 border">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="fw-bold text-dark small"><i class="bi bi-wallet2 text-info me-2"></i>Flutterwave Settings</span>
                                            <div class="form-check form-switch m-0">
                                                <input class="form-check-input" type="checkbox" id="flutterwave_enabled" name="flutterwave_enabled" value="1" {{ ($flutterwave['enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-semibold text-muted ms-1" for="flutterwave_enabled">Active</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Public Key</label>
                                            <input type="text" class="form-control rounded-3" name="flutterwave_public_key" value="{{ $flutterwave['public_key'] ?? '' }}" placeholder="FLWPUBK-...">
                                        </div>
                                        <div>
                                            <label class="form-label small text-muted">Secret Key</label>
                                            <input type="password" class="form-control rounded-3" name="flutterwave_secret_key" value="{{ $flutterwave['secret_key'] ?? '' }}" placeholder="FLWSECK-...">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Save Gateways Settings</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 6: Grading Systems -->
                    <div class="tab-pane fade" id="grading" role="tabpanel">
                        <h5 class="mb-4 font-weight-bold" style="font-weight: 700;">Grading Systems Configuration</h5>
                        <p class="text-muted small">Configure grade letters, min/max score ranges, and grade points for each level (KG, Primary, JHS, SHS). The report card legends will render automatically based on these active ranges.</p>

                        <div class="row g-4 mt-2">
                            @forelse($gradingScales as $scale)
                                <div class="col-md-12">
                                    <div class="card bg-light rounded-4 border-0 p-4 shadow-sm mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="fw-bold text-primary mb-1"><i class="bi bi-gear-wide-connected me-2"></i>{{ $scale->name }}</h6>
                                                <span class="badge bg-secondary">Level: {{ $scale->level }}</span>
                                                @if($scale->is_default)
                                                    <span class="badge bg-success ms-1">Default Scale</span>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-sm btn-primary px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#editScaleModal{{ $scale->id }}" style="border-radius: 8px;">
                                                <i class="bi bi-sliders me-1"></i>Configure Ranges
                                            </button>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm align-middle mb-0 text-center" style="font-size: 0.85rem;">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>Grade</th>
                                                        <th>Min Score</th>
                                                        <th>Max Score</th>
                                                        <th>Grade Point</th>
                                                        <th>Description / Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($scale->items as $item)
                                                        <tr>
                                                            <td class="fw-bold text-dark">{{ $item->grade }}</td>
                                                            <td class="text-dark">{{ floatval($item->min_score) }}%</td>
                                                            <td class="text-dark">{{ floatval($item->max_score) }}%</td>
                                                            <td class="text-dark">{{ floatval($item->grade_point) }}</td>
                                                            <td class="text-dark">{{ $item->description }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            @empty
                                <div class="col-12 text-center text-muted py-4">No grading scales configured for this school.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @foreach($gradingScales as $scale)
        <!-- Configure Scale Modal -->
        <div class="modal fade" id="editScaleModal{{ $scale->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <form action="{{ route('school.settings.grading-scale.update', $scale->id) }}" method="POST" class="modal-content border-0 shadow-lg text-start" style="border-radius: 16px;">
                    @csrf
                    <div class="modal-header border-bottom-0 p-4 pb-0">
                        <h6 class="modal-title fw-bold text-primary"><i class="bi bi-sliders me-2"></i>Configure Ranges: {{ $scale->name }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless align-middle mb-0 text-center">
                                <thead>
                                    <tr class="text-muted small font-weight-bold">
                                        <th style="width: 12%;">Grade</th>
                                        <th style="width: 18%;">Min Score (%)</th>
                                        <th style="width: 18%;">Max Score (%)</th>
                                        <th style="width: 15%;">Grade Point</th>
                                        <th>Description / Remarks</th>
                                        <th style="width: 10%;">Remove</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($scale->items as $index => $item)
                                        <tr style="border-bottom: 1px solid var(--border-color);">
                                            <td>
                                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                <input type="text" class="form-control text-center fw-bold" name="items[{{ $index }}][grade]" value="{{ $item->grade }}" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control text-center" name="items[{{ $index }}][min_score]" value="{{ floatval($item->min_score) }}" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control text-center" name="items[{{ $index }}][max_score]" value="{{ floatval($item->max_score) }}" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control text-center" name="items[{{ $index }}][grade_point]" value="{{ floatval($item->grade_point) }}" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="items[{{ $index }}][description]" value="{{ $item->description }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()" title="Delete range">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4">
                        <button type="button" class="btn btn-outline-success me-auto btn-add-row" data-scale-id="{{ $scale->id }}" style="border-radius: 8px;">
                            <i class="bi bi-plus-circle me-1"></i>Add Row
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="border-radius: 8px;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-add-row').forEach(button => {
            button.addEventListener('click', function() {
                const scaleId = this.getAttribute('data-scale-id');
                const tbody = document.querySelector(`#editScaleModal${scaleId} tbody`);
                const index = 'new_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
                
                const newRow = document.createElement('tr');
                newRow.style.borderBottom = '1px solid var(--border-color)';
                newRow.innerHTML = `
                    <td>
                        <input type="text" class="form-control text-center fw-bold" name="items[${index}][grade]" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control text-center" name="items[${index}][min_score]" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control text-center" name="items[${index}][max_score]" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control text-center" name="items[${index}][grade_point]" required>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="items[${index}][description]">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()" title="Delete range">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(newRow);
            });
        });
    });
</script>
@endsection
@endsection
