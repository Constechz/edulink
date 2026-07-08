@extends('layouts.app')

@section('title', 'Email Gateway & Logs | ' . config('app.name', 'EduLink') . ' Admin')
@section('header_title', 'Email Gateway & Communications')

@section('styles')
<style>
    /* Premium UI Custom CSS overrides using app theme variables */
    .metric-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.01), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
    }
    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.05), 0 8px 15px -5px rgba(0, 0, 0, 0.03);
        border-color: var(--primary-color);
    }
    .pulse-indicator {
        width: 10px;
        height: 10px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 12px #10b981;
        animation: status-pulse 2s infinite;
    }
    @keyframes status-pulse {
        0% { transform: scale(0.9); opacity: 0.7; }
        50% { transform: scale(1.25); opacity: 1; box-shadow: 0 0 16px #10b981; }
        100% { transform: scale(0.9); opacity: 0.7; }
    }
    .status-badge-sent {
        background: rgba(16, 185, 129, 0.1) !important;
        color: #10b981 !important;
        border: 1px solid rgba(16, 185, 129, 0.2);
        box-shadow: 0 0 8px rgba(16, 185, 129, 0.05);
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .status-badge-failed {
        background: rgba(239, 68, 68, 0.1) !important;
        color: #ef4444 !important;
        border: 1px solid rgba(239, 68, 68, 0.2);
        box-shadow: 0 0 8px rgba(239, 68, 68, 0.05);
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .custom-input-group {
        position: relative;
    }
    .custom-input-group i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 1.1rem;
        z-index: 10;
        transition: color 0.2s;
    }
    .custom-input-group .form-control, .custom-input-group .form-select {
        padding-left: 42px;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        transition: all 0.2s ease;
        background-color: var(--theme-toggle-bg);
        color: var(--text-main);
    }
    .custom-input-group .form-control:focus, .custom-input-group .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.08);
        background-color: var(--card-bg);
        color: var(--text-main);
    }
    .custom-input-group:focus-within i {
        color: var(--primary-color);
    }
    .form-label-custom {
        font-weight: 600;
        color: var(--text-main);
        font-size: 0.85rem;
        margin-bottom: 0.4rem;
        display: inline-block;
    }
    .logs-table {
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    .logs-table tr {
        background-color: var(--card-bg);
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    .logs-table tr:hover {
        background-color: var(--card-bg) !important;
        transform: scale(1.002);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.015);
    }
    .logs-table td {
        border-top: 1px solid var(--border-color) !important;
        border-bottom: 1px solid var(--border-color) !important;
        padding: 0.85rem 1rem !important;
        color: var(--text-main);
    }
    .logs-table td:first-child {
        border-left: 1px solid var(--border-color) !important;
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }
    .logs-table td:last-child {
        border-right: 1px solid var(--border-color) !important;
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    .nav-pills .nav-link {
        background-color: var(--theme-toggle-bg);
        color: var(--text-muted);
        border: 1px solid transparent;
        transition: all 0.25s ease;
    }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #002244 0%, #003366 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(0, 51, 102, 0.15);
    }
    [data-bs-theme="dark"] .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #1e293b 0%, #0b0f19 100%) !important;
        color: #ffffff !important;
        border-color: var(--border-color) !important;
    }
    .nav-pills .nav-link:hover:not(.active) {
        background-color: var(--theme-toggle-bg-hover);
        color: var(--theme-toggle-color-hover);
    }
    .btn-action-view {
        background-color: var(--theme-toggle-bg);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        transition: all 0.2s ease;
    }
    .btn-action-view:hover {
        background-color: var(--text-main);
        color: var(--bg-color);
        border-color: var(--text-main);
        transform: translateY(-1px);
    }
    /* Modal premium overlays */
    .custom-modal-header {
        background: linear-gradient(135deg, #002244 0%, #003366 100%);
    }
    [data-bs-theme="dark"] .custom-modal-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
    }
    .custom-modal-close-btn {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
    .mail-preview-body {
        font-family: 'Inter', sans-serif;
        line-height: 1.6;
        font-size: 0.95rem;
        background: var(--bg-color);
        border-left: 4px solid var(--primary-color);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        color: var(--text-main);
    }
    
    /* Enforce dynamic theme colors on all common text utility classes inside dark mode */
    [data-bs-theme="dark"] .text-dark {
        color: var(--text-main) !important;
    }
    [data-bs-theme="dark"] .text-muted {
        color: var(--text-muted) !important;
    }
    [data-bs-theme="dark"] .text-secondary {
        color: var(--text-muted) !important;
    }
    [data-bs-theme="dark"] select.form-select option {
        background-color: #0f172a !important;
        color: #f1f5f9 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-sm-12">
            <h5 class="fw-bold mb-1 text-dark">Email Gateway Configuration</h5>
            <p class="text-muted small mb-0">Manage outbound SMTP settings and monitor broadcast transmission logs.</p>
        </div>
    </div>

    <!-- Session Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm p-3 mb-4 d-flex align-items-center" role="alert" style="background-color: rgba(16, 185, 129, 0.08);">
            <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
            <div class="text-success fw-semibold">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm p-3 mb-4" role="alert" style="background-color: rgba(239, 68, 68, 0.08);">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5 text-danger"></i>
                <div class="text-danger fw-bold">Please correct the following errors:</div>
            </div>
            <ul class="mb-0 ps-4 text-danger small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Top Metrics Row -->
    <div class="row g-3 mb-4">
        <!-- Metric 1: Gateway Status -->
        <div class="col-6 col-lg-3">
            <div class="metric-card p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">Gateway Status</span>
                    <span class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 1.15rem;">
                        <span class="pulse-indicator"></span> Active (SMTP)
                    </span>
                </div>
                <div class="p-2.5 rounded-4 border" style="background: rgba(16, 185, 129, 0.08); border-color: rgba(16, 185, 129, 0.15) !important;">
                    <i class="bi bi-shield-check text-success fs-4"></i>
                </div>
            </div>
        </div>

        <!-- Metric 2: Total Dispatched -->
        <div class="col-6 col-lg-3">
            <div class="metric-card p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">Total Broadcasts</span>
                    <span class="fw-extrabold text-dark fw-bold" style="font-size: 1.35rem;">
                        {{ number_format($totalEmails) }}
                    </span>
                </div>
                <div class="p-2.5 rounded-4 border" style="background: rgba(0, 80, 160, 0.08); border-color: var(--border-color) !important;">
                    <i class="bi bi-send-fill fs-4" style="color: var(--primary-color) !important;"></i>
                </div>
            </div>
        </div>

        <!-- Metric 3: Success Rate -->
        <div class="col-6 col-lg-3">
            <div class="metric-card p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">Delivery Success</span>
                    <span class="fw-bold text-dark" style="font-size: 1.35rem;">
                        {{ $successRate }}%
                    </span>
                </div>
                <div class="p-2.5 rounded-4 border" style="background: rgba(16, 185, 129, 0.08); border-color: var(--border-color) !important;">
                    <span class="badge bg-success-subtle text-success px-2 py-1.5 rounded-3 fw-bold small border" style="font-size: 0.75rem;">
                        <i class="bi bi-arrow-up-right me-0.5"></i>{{ number_format($sentEmails) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Metric 4: Failed rate -->
        <div class="col-6 col-lg-3">
            <div class="metric-card p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">Failed Dispatches</span>
                    <span class="fw-bold text-danger" style="font-size: 1.35rem;">
                        {{ $failedRate }}%
                    </span>
                </div>
                <div class="p-2.5 rounded-4 border" style="background: rgba(239, 68, 68, 0.08); border-color: var(--border-color) !important;">
                    <span class="badge bg-danger-subtle text-danger px-2 py-1.5 rounded-3 fw-bold small border" style="font-size: 0.75rem;">
                        <i class="bi bi-exclamation-octagon me-0.5"></i>{{ number_format($failedEmails) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Panels -->
    <div class="row g-4">
        <!-- Left: Gateway Configurations & Mail Composer -->
        <div class="col-lg-5">
            <div class="glass-card p-4 shadow-sm">
                <ul class="nav nav-pills nav-fill mb-4" id="emailPortalTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-uppercase py-2.5 rounded-3" id="gateway-tab" data-bs-toggle="tab" data-bs-target="#gateway" type="button" role="tab" aria-controls="gateway" aria-selected="true" style="font-size: 0.8rem;">
                            <i class="bi bi-hdd-network me-2"></i>Gateway Server
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-uppercase py-2.5 rounded-3" id="compose-tab" data-bs-toggle="tab" data-bs-target="#compose" type="button" role="tab" aria-controls="compose" aria-selected="false" style="font-size: 0.8rem;">
                            <i class="bi bi-envelope-plus me-2"></i>Compose Mail
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="emailPortalTabContent">
                    <!-- Tab 1: SMTP Config Form -->
                    <div class="tab-pane fade show active" id="gateway" role="tabpanel" aria-labelledby="gateway-tab">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-sliders text-primary fs-5 me-2"></i>
                            <h6 class="fw-bold text-dark mb-0">SMTP Configuration Settings</h6>
                        </div>
                        <p class="text-muted small mb-4">Set up SMTP configurations used to dispatch platform communications globally.</p>
                        
                        <form action="{{ route('super-admin.email-settings.update') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="smtp_host" class="form-label-custom">SMTP Host Server</label>
                                <div class="custom-input-group">
                                    <i class="bi bi-server"></i>
                                    <input type="text" class="form-control" id="smtp_host" name="smtp_host" placeholder="smtp.mailtrap.io" required value="{{ $smtpHost }}">
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label for="smtp_port" class="form-label-custom">SMTP Port</label>
                                    <div class="custom-input-group">
                                        <i class="bi bi-hash"></i>
                                        <input type="number" class="form-control" id="smtp_port" name="smtp_port" placeholder="2525" required value="{{ $smtpPort }}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="smtp_encryption" class="form-label-custom">Encryption Protocol</label>
                                    <div class="custom-input-group">
                                        <i class="bi bi-shield-lock"></i>
                                        <select class="form-select" id="smtp_encryption" name="smtp_encryption" required>
                                            <option value="tls" {{ $smtpEncryption === 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ $smtpEncryption === 'ssl' ? 'selected' : '' }}>SSL</option>
                                            <option value="none" {{ $smtpEncryption === 'none' ? 'selected' : '' }}>None</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="smtp_username" class="form-label-custom">SMTP Authenticated Username</label>
                                <div class="custom-input-group">
                                    <i class="bi bi-person-badge"></i>
                                    <input type="text" class="form-control" id="smtp_username" name="smtp_username" placeholder="Enter username" value="{{ $smtpUsername }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="smtp_password" class="form-label-custom">SMTP Authenticated Password</label>
                                <div class="custom-input-group">
                                    <i class="bi bi-key"></i>
                                    <input type="password" class="form-control" id="smtp_password" name="smtp_password" placeholder="Enter server password" value="{{ $smtpPassword }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="mail_from_address" class="form-label-custom">Default Sender Email Address (From)</label>
                                <div class="custom-input-group">
                                    <i class="bi bi-envelope-at"></i>
                                    <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" placeholder="hello@{{ strtolower(config('app.name', 'EduLink')) }}.com" required value="{{ $mailFromAddress }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="mail_from_name" class="form-label-custom">Default Sender Signature Name</label>
                                <div class="custom-input-group">
                                    <i class="bi bi-signature"></i>
                                    <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" placeholder="{{ config('app.name', 'EduLink') }} Ghana ERP" required value="{{ $mailFromName }}">
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2.5 fw-bold shadow-xs d-flex align-items-center justify-content-center gap-2">
                                        <i class="bi bi-save2"></i> Save Settings
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-primary w-100 rounded-3 py-2.5 fw-bold shadow-xs d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#testSmtpModal">
                                        <i class="bi bi-envelope-check"></i> Test SMTP
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Tab 2: Compose Broadcast Form -->
                    <div class="tab-pane fade" id="compose" role="tabpanel" aria-labelledby="compose-tab">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-pencil-square text-success fs-5 me-2"></i>
                            <h6 class="fw-bold text-dark mb-0">Compose Platform Broadcast</h6>
                        </div>
                        <p class="text-muted small mb-4">Compose dynamic announcements or notifications to selected user entities.</p>
                        
                        <form action="{{ route('super-admin.email-settings.send') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="target_type" class="form-label-custom">Broadcast Target Audience</label>
                                <div class="custom-input-group">
                                    <i class="bi bi-people"></i>
                                    <select class="form-select" id="target_type" name="target_type" required>
                                        <option value="all_admins">All School Administrators</option>
                                        <option value="all_users">All System Users Globally</option>
                                        <option value="specific_school">All Users of a Specific Tenant School</option>
                                        <option value="specific_user">Direct Manual Email Address</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Specific School Selector (Hidden by default) -->
                            <div class="mb-3 d-none" id="school_selector_group">
                                <label for="school_id" class="form-label-custom">Select School Context</label>
                                <div class="custom-input-group">
                                    <i class="bi bi-bank"></i>
                                    <select class="form-select" id="school_id" name="school_id">
                                        <option value="">-- Choose Tenant School --</option>
                                        @foreach($schools as $school)
                                            <option value="{{ $school->id }}">{{ $school->name }} ({{ $school->school_code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Specific User Email Input (Hidden by default) -->
                            <div class="mb-3 d-none" id="specific_email_group">
                                <label for="specific_email" class="form-label-custom">Recipient Email Address</label>
                                <div class="custom-input-group">
                                    <i class="bi bi-envelope"></i>
                                    <input type="email" class="form-control" id="specific_email" name="specific_email" placeholder="example@domain.com">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label-custom">Broadcast Email Subject</label>
                                <div class="custom-input-group">
                                    <i class="bi bi-sticky"></i>
                                    <input type="text" class="form-control" id="subject" name="subject" required placeholder="Enter message subject line">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="body" class="form-label-custom">Message Body Content</label>
                                <textarea class="form-control rounded-3 shadow-xs p-3 small" id="body" name="body" rows="6" required placeholder="Type your custom broadcast text here..." style="border-color: var(--border-color); background-color: var(--theme-toggle-bg); color: var(--text-main);"></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100 rounded-3 py-2.5 fw-bold shadow-xs">
                                <i class="bi bi-send-check-fill me-2"></i>Send Custom Broadcast
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Outgoing Mail Logs Table -->
        <div class="col-lg-7">
            <div class="glass-card p-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-2">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-activity text-secondary me-2"></i>Email Transmission History
                    </h5>
                    <span class="badge bg-light border text-secondary fw-semibold px-2.5 py-1.5 rounded-3">
                        Total Records: {{ number_format($emailLogs->total()) }}
                    </span>
                </div>
                
                @if($emailLogs->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-mailbox fs-1 d-block mb-3 text-secondary opacity-50"></i>
                        <span class="d-block fw-semibold">No outgoing system logs detected.</span>
                        <span class="small text-muted">Use the compose tab to dispatch system broadcasts.</span>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle logs-table table-borderless">
                            <thead>
                                <tr class="text-muted small fw-bold" style="font-size: 0.75rem;">
                                    <th class="ps-3 text-uppercase">Recipient Address</th>
                                    <th class="text-uppercase">Subject Title</th>
                                    <th class="text-uppercase">Status</th>
                                    <th class="text-uppercase">Dispatched Date</th>
                                    <th class="text-center text-uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($emailLogs as $log)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <div class="p-2 rounded-circle me-2.5 border text-muted" style="background-color: var(--theme-toggle-bg); border-color: var(--border-color) !important;">
                                                    <i class="bi bi-person" style="font-size: 0.85rem;"></i>
                                                </div>
                                                <span class="fw-semibold text-dark">{{ $log->recipient_email }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-dark fw-medium text-truncate d-inline-block" style="max-width: 160px;" title="{{ $log->subject }}">
                                                {{ $log->subject }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge rounded-3 px-2.5 py-1.5 status-badge-{{ $log->status === 'sent' ? 'sent' : 'failed' }} text-uppercase">
                                                <i class="bi {{ $log->status === 'sent' ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>{{ $log->status }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">
                                            {{ $log->created_at ? $log->created_at->format('M d, Y H:i') : 'N/A' }}
                                        </td>
                                        <td class="text-center">
                                             <div class="d-flex align-items-center justify-content-center gap-2">
                                                 <button type="button" class="btn btn-action-view btn-xs rounded-3 px-2.5 py-1.5 fw-bold" data-bs-toggle="modal" data-bs-target="#emailBodyModal{{ $log->id }}">
                                                     <i class="bi bi-eye-fill"></i> View
                                                 </button>
                                                 <form action="{{ route('super-admin.email-settings.logs.destroy', $log->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this email log?');" class="d-inline">
                                                     @csrf
                                                     @method('DELETE')
                                                     <button type="submit" class="btn btn-outline-danger btn-xs rounded-3 px-2.5 py-1.5 fw-bold">
                                                         <i class="bi bi-trash3-fill"></i> Delete
                                                     </button>
                                                 </form>
                                             </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <span class="text-muted small">
                            Showing {{ $emailLogs->firstItem() ?? 0 }} to {{ $emailLogs->lastItem() ?? 0 }} of {{ $emailLogs->total() }} dispatches
                        </span>
                        <div>
                            {{ $emailLogs->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Overlays (Rendered at top-level body root to prevent styling context bugs) -->
@if(!$emailLogs->isEmpty())
    @foreach($emailLogs as $log)
        <div class="modal fade" id="emailBodyModal{{ $log->id }}" tabindex="-1" aria-labelledby="emailBodyModalLabel{{ $log->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                    <!-- Modal Header -->
                    <div class="modal-header custom-modal-header text-white p-3 border-0">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-envelope-open-fill text-warning fs-5 me-2"></i>
                            <h6 class="modal-title fw-bold" id="emailBodyModalLabel{{ $log->id }}">
                                Email Dispatch: {{ $log->subject }}
                            </h6>
                        </div>
                        <button type="button" class="btn-close custom-modal-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <!-- Modal Body -->
                    <div class="modal-body p-4" style="background-color: var(--bg-color);">
                        <!-- Recipient details bar -->
                        <div class="p-3 rounded-4 mb-3 border shadow-xs d-flex flex-wrap gap-3 align-items-center justify-content-between text-muted small" style="background-color: var(--card-bg); border-color: var(--border-color) !important;">
                            <div>
                                <i class="bi bi-person-fill text-secondary me-1"></i>To: <strong class="text-dark">{{ $log->recipient_email }}</strong>
                            </div>
                            <div>
                                <i class="bi bi-clock-fill text-secondary me-1"></i>Sent At: <span class="text-dark fw-semibold">{{ $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'N/A' }}</span>
                            </div>
                        </div>

                        <!-- Mail HTML Body -->
                        <div class="p-4 border rounded-4 mail-preview-body overflow-auto" style="max-height: 400px; border-color: var(--border-color) !important;">
                            {!! $log->body !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Test SMTP Connection Modal -->
<div class="modal fade" id="testSmtpModal" tabindex="-1" aria-labelledby="testSmtpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header custom-modal-header text-white p-3 border-0">
                <div class="d-flex align-items-center">
                    <i class="bi bi-envelope-check-fill text-warning fs-5 me-2"></i>
                    <h6 class="modal-title fw-bold" id="testSmtpModalLabel">
                        Test SMTP Server Connection
                    </h6>
                </div>
                <button type="button" class="btn-close custom-modal-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('super-admin.email-settings.test') }}" method="POST">
                @csrf
                <div class="modal-body p-4" style="background-color: var(--bg-color);">
                    <p class="text-muted small mb-4">
                        Please save any unsaved configurations before testing. A diagnostic test message will be sent using your current database parameters.
                    </p>
                    
                    <div class="mb-3">
                        <label for="test_email" class="form-label-custom">Send Test Email To</label>
                        <div class="custom-input-group">
                            <i class="bi bi-envelope-at"></i>
                            <input type="email" class="form-control" id="test_email" name="test_email" placeholder="recipient@example.com" required value="{{ Auth::user()->email }}">
                        </div>
                        <div class="form-text text-muted">Defaults to your administrator email address.</div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 justify-content-between" style="background-color: var(--card-bg); border-color: var(--border-color) !important;">
                    <button type="button" class="btn btn-light rounded-3 px-3 py-2 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 fw-bold">
                        <i class="bi bi-send-fill me-1"></i>Send Test Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const targetSelect = document.getElementById('target_type');
        const schoolGroup = document.getElementById('school_selector_group');
        const emailGroup = document.getElementById('specific_email_group');
        const schoolInput = document.getElementById('school_id');
        const emailInput = document.getElementById('specific_email');

        function toggleTargetFields() {
            const val = targetSelect.value;
            if (val === 'specific_school') {
                schoolGroup.classList.remove('d-none');
                emailGroup.classList.add('d-none');
                schoolInput.setAttribute('required', 'required');
                emailInput.removeAttribute('required');
            } else if (val === 'specific_user') {
                schoolGroup.classList.add('d-none');
                emailGroup.classList.remove('d-none');
                emailInput.setAttribute('required', 'required');
                schoolInput.removeAttribute('required');
            } else {
                schoolGroup.classList.add('d-none');
                emailGroup.classList.add('d-none');
                schoolInput.removeAttribute('required');
                emailInput.removeAttribute('required');
            }
        }

        targetSelect.addEventListener('change', toggleTargetFields);
        toggleTargetFields(); // Run check on page load
    });
</script>
@endsection
