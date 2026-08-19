@extends('layouts.app')

@section('title', 'Super Admin Dashboard | ' . config('app.name', 'EduLink'))
@section('header_title', config('app.name', 'EduLink') . ' SaaS Platform Governance')

@section('content')
<style>
    /* ==========================================================================
       SUPER ADMIN DASHBOARD BRAND THEME (EDULINK NAVY & GOLD)
       ========================================================================== */
    :root {
        --sa-navy-primary: #003366;
        --sa-navy-dark: #002244;
        --sa-navy-light: #0f4c81;
        --sa-navy-subtle: #eef4fb;
        --sa-gold: #FFD700;
        --sa-gold-dark: #d97706;
        --sa-emerald: #10b981;
        --sa-cyan: #0284c7;
        --sa-card-bg: #ffffff;
        --sa-border: #e2e8f0;
        --sa-text-head: #0f172a;
        --sa-text-body: #334155;
        --sa-text-muted: #64748b;
    }

    [data-bs-theme="dark"] {
        --sa-navy-subtle: rgba(15, 76, 129, 0.2);
        --sa-card-bg: #111a2e;
        --sa-border: rgba(255, 255, 255, 0.08);
        --sa-text-head: #f8fafc;
        --sa-text-body: #cbd5e1;
        --sa-text-muted: #94a3b8;
    }

    /* Executive Hero Header */
    .sa-hero-banner {
        background: linear-gradient(135deg, #002244 0%, #003366 60%, #0f4c81 100%);
        border-radius: 18px;
        padding: 2rem 2.25rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 30px -5px rgba(0, 51, 102, 0.25);
        border: 1px solid rgba(255, 215, 0, 0.15);
    }

    .sa-hero-banner::after {
        content: "";
        position: absolute;
        right: -30px;
        bottom: -40px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.12) 0%, rgba(255, 215, 0, 0) 70%);
        pointer-events: none;
    }

    .sa-live-pulse {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.3rem 0.85rem;
        border-radius: 50px;
        background: rgba(16, 185, 129, 0.15);
        border: 1px solid rgba(16, 185, 129, 0.35);
        color: #10b981;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .sa-live-pulse-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 8px #10b981;
        animation: saPulse 2s infinite ease-in-out;
    }

    @keyframes saPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(1.3); }
    }

    /* Brand Metric Cards */
    .sa-metric-card {
        background: var(--sa-card-bg);
        border: 1px solid var(--sa-border);
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.25s ease;
        box-shadow: 0 4px 14px -2px rgba(0, 51, 102, 0.04);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .sa-metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -4px rgba(0, 51, 102, 0.1);
        border-color: rgba(0, 51, 102, 0.25);
    }

    [data-bs-theme="dark"] .sa-metric-card:hover {
        border-color: rgba(255, 215, 0, 0.3);
        box-shadow: 0 10px 25px -4px rgba(0, 0, 0, 0.5);
    }

    .sa-metric-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .sa-metric-val {
        font-size: 2.1rem;
        font-weight: 800;
        color: var(--sa-text-head);
        letter-spacing: -0.5px;
        line-height: 1.1;
    }

    .sa-metric-label {
        font-size: 0.78rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.6px;
        color: var(--sa-text-muted);
        margin-bottom: 0.35rem;
    }

    /* Subscriptions & Guide Callout */
    .sa-guide-callout {
        background: var(--sa-navy-subtle);
        border-left: 4px solid var(--sa-navy-primary);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        border-top: 1px solid var(--sa-border);
        border-right: 1px solid var(--sa-border);
        border-bottom: 1px solid var(--sa-border);
    }

    [data-bs-theme="dark"] .sa-guide-callout {
        border-left-color: var(--sa-gold);
    }

    /* Custom Nav Pills */
    .sa-nav-pills {
        gap: 0.5rem;
        border-bottom: 1px solid var(--sa-border);
        padding-bottom: 0.75rem;
    }

    .sa-nav-pills .nav-link {
        color: var(--sa-text-body);
        font-weight: 600;
        font-size: 0.88rem;
        padding: 0.6rem 1.25rem;
        border-radius: 10px;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        background: transparent;
    }

    .sa-nav-pills .nav-link:hover {
        color: var(--sa-navy-primary);
        background: var(--sa-navy-subtle);
    }

    [data-bs-theme="dark"] .sa-nav-pills .nav-link:hover {
        color: var(--sa-gold);
    }

    .sa-nav-pills .nav-link.active {
        background: linear-gradient(135deg, #002244 0%, #003366 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(0, 51, 102, 0.25);
        border-color: rgba(255, 215, 0, 0.2);
    }

    [data-bs-theme="dark"] .sa-nav-pills .nav-link.active {
        background: linear-gradient(135deg, #0f4c81 0%, #003366 100%) !important;
        color: var(--sa-gold) !important;
        border-color: rgba(255, 215, 0, 0.3);
    }

    /* Modern Table Styling */
    .sa-table {
        margin-bottom: 0;
    }

    .sa-table thead th {
        background-color: var(--sa-navy-subtle);
        color: var(--sa-text-head);
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-bottom: 1px solid var(--sa-border);
        padding: 0.85rem 1rem;
    }

    .sa-table tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--sa-border);
        color: var(--sa-text-body);
        font-size: 0.86rem;
    }

    .sa-avatar-chip {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #003366 0%, #0f4c81 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.82rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(0, 51, 102, 0.2);
    }

    /* Brand Buttons */
    .btn-brand-navy {
        background: linear-gradient(135deg, #003366 0%, #0f4c81 100%);
        color: #ffffff !important;
        border: none;
        font-weight: 600;
        border-radius: 8px;
        padding: 0.45rem 0.95rem;
        transition: all 0.2s ease;
    }

    .btn-brand-navy:hover {
        background: linear-gradient(135deg, #002244 0%, #003366 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 51, 102, 0.25);
    }

    .btn-brand-gold {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff !important;
        border: none;
        font-weight: 700;
        border-radius: 8px;
        padding: 0.45rem 1rem;
        transition: all 0.2s ease;
        box-shadow: 0 3px 8px rgba(217, 119, 6, 0.25);
    }

    .btn-brand-gold:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        transform: translateY(-1px);
    }

    .btn-brand-outline-navy {
        border: 1.5px solid #003366;
        color: #003366;
        background: transparent;
        font-weight: 600;
        border-radius: 8px;
        padding: 0.4rem 0.9rem;
        transition: all 0.2s ease;
    }

    .btn-brand-outline-navy:hover {
        background: #003366;
        color: #ffffff;
    }

    [data-bs-theme="dark"] .btn-brand-outline-navy {
        border-color: var(--sa-gold);
        color: var(--sa-gold);
    }

    [data-bs-theme="dark"] .btn-brand-outline-navy:hover {
        background: var(--sa-gold);
        color: #002244;
    }

    /* Quick Action Shortcuts Grid */
    .sa-quick-shortcut {
        background: var(--sa-card-bg);
        border: 1px solid var(--sa-border);
        border-radius: 12px;
        padding: 1rem 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        text-decoration: none;
        color: var(--sa-text-body);
        transition: all 0.2s ease;
    }

    .sa-quick-shortcut:hover {
        background: var(--sa-navy-subtle);
        border-color: #003366;
        color: #003366;
        transform: translateY(-2px);
    }

    [data-bs-theme="dark"] .sa-quick-shortcut:hover {
        border-color: var(--sa-gold);
        color: var(--sa-gold);
    }
</style>

<div class="container-fluid p-0">
    <!-- Session Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm border-0 d-flex align-items-center" role="alert" style="background: rgba(16, 185, 129, 0.12); color: #059669; border-left: 4px solid #10b981 !important;">
            <i class="bi bi-check-circle-fill fs-5 me-2.5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 1. Executive Welcome & Hero Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="sa-hero-banner text-white">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <h2 class="fw-bold mb-2" style="font-size: 1.85rem; letter-spacing: -0.5px;">
                            {{ config('app.name', 'EduLink') }} <span style="color: var(--sa-gold);">SaaS Governance Console</span>
                        </h2>
                        <p class="text-white-50 mb-0" style="font-size: 0.92rem; max-width: 680px;">
                            Unified multi-tenant orchestration, real-time institutional subscriptions, mobile money billing telemetry, and school onboarding control in Ghana.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                            <a href="{{ route('super-admin.plans.index') }}" class="btn-brand-gold text-decoration-none d-inline-flex align-items-center">
                                <i class="bi bi-award-fill me-1.5"></i>Manage SaaS Plans
                            </a>
                            <a href="{{ route('super-admin.analytics') }}" class="btn btn-outline-light d-inline-flex align-items-center text-decoration-none" style="border-radius: 8px; font-weight: 600; font-size: 0.88rem;">
                                <i class="bi bi-graph-up-arrow me-1.5"></i>Analytics
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Super Admin Guide Callout -->
    <div class="sa-guide-callout mb-4">
        <div class="d-flex align-items-start gap-3">
            <div class="sa-avatar-chip" style="background: linear-gradient(135deg, #003366 0%, #0f4c81 100%); width: 44px; height: 44px; font-size: 1.25rem;">
                <i class="bi bi-shield-check text-warning"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Multi-Tier Subscription & Module Gatekeeping Policy</h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">Route Gatekeeper Active</span>
                </div>
                <p class="small text-secondary mb-2" style="font-size: 0.84rem;">
                    You have master control to restrict operational modules and enforce paywalls per subscription plan. When a module is unchecked in a plan, the route-level middleware immediately restricts tenant access.
                </p>
                <div class="d-flex flex-wrap gap-3 small">
                    <a href="{{ route('super-admin.plans.index') }}" class="fw-bold text-primary text-decoration-none d-inline-flex align-items-center">
                        <i class="bi bi-sliders me-1"></i>Configure SaaS Tiers &bull; Feature Toggles &rarr;
                    </a>
                    <a href="{{ route('super-admin.access-logs') }}" class="fw-bold text-secondary text-decoration-none d-inline-flex align-items-center">
                        <i class="bi bi-journal-text me-1"></i>View System Access Logs &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Key Operational KPI Metric Cards -->
    <div class="row g-3 g-lg-4 mb-4">
        <!-- Metric 1: Active Schools -->
        <div class="col-sm-6 col-xl-3">
            <div class="sa-metric-card">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="sa-metric-label">Registered Schools</div>
                            <div class="sa-metric-val">{{ $totalSchools }}</div>
                        </div>
                        <div class="sa-metric-icon" style="background: rgba(0, 51, 102, 0.1); color: #003366;">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="border-color: var(--sa-border) !important;">
                    <span class="badge bg-primary-subtle text-primary fw-bold" style="font-size: 0.72rem;">
                        <i class="bi bi-check-circle-fill me-1"></i>Live Tenants
                    </span>
                    <a href="?tab=schools" class="text-muted small text-decoration-none fw-semibold">View List &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Metric 2: SaaS Plans -->
        <div class="col-sm-6 col-xl-3">
            <div class="sa-metric-card">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="sa-metric-label">SaaS Pricing Tiers</div>
                            <div class="sa-metric-val">{{ $totalPlans }}</div>
                        </div>
                        <div class="sa-metric-icon" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                            <i class="bi bi-award-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="border-color: var(--sa-border) !important;">
                    <span class="badge bg-warning-subtle text-warning-emphasis fw-bold" style="font-size: 0.72rem;">
                        <i class="bi bi-shield-lock me-1"></i>Active Tiers
                    </span>
                    <a href="{{ route('super-admin.plans.index') }}" class="text-muted small text-decoration-none fw-semibold">Configure &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Metric 3: Platform Users -->
        <div class="col-sm-6 col-xl-3">
            <div class="sa-metric-card">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="sa-metric-label">Platform Accounts</div>
                            <div class="sa-metric-val">{{ $totalUsers }}</div>
                        </div>
                        <div class="sa-metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="border-color: var(--sa-border) !important;">
                    <span class="badge bg-success-subtle text-success fw-bold" style="font-size: 0.72rem;">
                        <i class="bi bi-shield-check me-1"></i>Staff & Admins
                    </span>
                    <a href="?tab=users" class="text-muted small text-decoration-none fw-semibold">Manage &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Metric 4: Regions in Ghana -->
        <div class="col-sm-6 col-xl-3">
            <div class="sa-metric-card">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="sa-metric-label">Geographic Footprint</div>
                            <div class="sa-metric-val">{{ count($regionCounts) }} <span class="fs-6 fw-normal text-muted">Regions</span></div>
                        </div>
                        <div class="sa-metric-icon" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="border-color: var(--sa-border) !important;">
                    <span class="badge bg-info-subtle text-info-emphasis fw-bold" style="font-size: 0.72rem;">
                        <i class="bi bi-globe2 me-1"></i>Ghana Coverage
                    </span>
                    <a href="{{ route('super-admin.analytics') }}" class="text-muted small text-decoration-none fw-semibold">Analytics &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Quick Governance Shortcuts Toolbar -->
    <div class="row g-2 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('super-admin.plans.index') }}" class="sa-quick-shortcut">
                <i class="bi bi-award text-warning fs-5"></i>
                <span class="small fw-semibold">SaaS Plans</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('super-admin.analytics') }}" class="sa-quick-shortcut">
                <i class="bi bi-graph-up-arrow text-primary fs-5"></i>
                <span class="small fw-semibold">Analytics</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('super-admin.sms-credits') }}" class="sa-quick-shortcut">
                <i class="bi bi-chat-dots text-success fs-5"></i>
                <span class="small fw-semibold">SMS Hub</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('super-admin.landing-page.edit') }}" class="sa-quick-shortcut">
                <i class="bi bi-layout-text-window-reverse text-info fs-5"></i>
                <span class="small fw-semibold">Landing Page</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('super-admin.email-settings') }}" class="sa-quick-shortcut">
                <i class="bi bi-envelope-gear text-danger fs-5"></i>
                <span class="small fw-semibold">Email SMTP</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('super-admin.settings') }}" class="sa-quick-shortcut">
                <i class="bi bi-gear-fill text-secondary fs-5"></i>
                <span class="small fw-semibold">Settings</span>
            </a>
        </div>
    </div>

    <!-- 5. Main Directory & Tabbed Management Console -->
    <div class="row">
        <div class="col-12">
            <div class="sa-metric-card p-4">
                
                <!-- Nav Pills -->
                <ul class="nav nav-pills sa-nav-pills mb-4" id="dashboardTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
                            <i class="bi bi-pie-chart-fill me-2"></i>Platform Overview & Visual Telemetry
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="schools-tab" data-bs-toggle="tab" data-bs-target="#schools" type="button" role="tab" aria-controls="schools" aria-selected="false">
                            <i class="bi bi-building me-2"></i>Registered Schools Registry ({{ $totalSchools }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="false">
                            <i class="bi bi-people-fill me-2"></i>SaaS Platform Accounts ({{ $totalUsers }})
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="dashboardTabContent">
                    
                    <!-- TAB 1: Charts & Analytics Overview -->
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                        <div class="row g-4">
                            <!-- Subscription Status Chart -->
                            <div class="col-lg-6">
                                <div class="p-3.5 rounded-4 h-100 border" style="background: var(--sa-card-bg); border-color: var(--sa-border) !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold text-dark mb-0">
                                            <i class="bi bi-pie-chart-fill text-primary me-2"></i>Subscription Status Distribution
                                        </h6>
                                        <span class="badge bg-primary-subtle text-primary fw-bold" style="font-size: 0.72rem;">Tenants</span>
                                    </div>
                                    <div style="position: relative; height: 290px;">
                                        <canvas id="subscriptionChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Regional Distribution Chart -->
                            <div class="col-lg-6">
                                <div class="p-3.5 rounded-4 h-100 border" style="background: var(--sa-card-bg); border-color: var(--sa-border) !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold text-dark mb-0">
                                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>Tenant Distribution by Region (Ghana)
                                        </h6>
                                        <span class="badge bg-danger-subtle text-danger fw-bold" style="font-size: 0.72rem;">GES Regions</span>
                                    </div>
                                    <div style="position: relative; height: 290px;">
                                        <canvas id="regionChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Registered Schools Directory -->
                    <div class="tab-pane fade" id="schools" role="tabpanel" aria-labelledby="schools-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">
                                    <i class="bi bi-building text-primary me-2"></i>Institutional Tenants Directory
                                </h6>
                                <span class="text-muted small">Manage school subscriptions, impersonation logins, and active lifecycle states.</span>
                            </div>
                            
                            <!-- Search Form -->
                            <form action="{{ route('super-admin.dashboard') }}" method="GET" class="d-flex gap-2">
                                <input type="hidden" name="tab" value="schools">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" class="form-control form-control-sm border-start-0 ps-0" name="school_search" placeholder="Search school, code, email..." value="{{ request('school_search') }}">
                                    <button type="submit" class="btn btn-brand-navy btn-sm px-3">Search</button>
                                </div>
                                @if(request('school_search'))
                                    <a href="{{ route('super-admin.dashboard', ['tab' => 'schools']) }}" class="btn btn-outline-secondary btn-sm" title="Clear search">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                @endif
                            </form>
                        </div>

                        @if($schoolsList->isEmpty())
                            <div class="text-center py-5 text-muted border rounded-4 my-2">
                                <div class="sa-avatar-chip mx-auto mb-3" style="width: 54px; height: 54px; font-size: 1.6rem; background: var(--sa-navy-subtle); color: var(--sa-navy-primary);">
                                    <i class="bi bi-building-exclamation"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">No Registered Schools Found</h6>
                                <p class="small text-secondary mb-0">Try clearing or adjusting your search criteria.</p>
                            </div>
                        @else
                            <div class="table-responsive border rounded-4 mb-3">
                                <table class="table sa-table align-middle table-hover">
                                    <thead>
                                        <tr>
                                            <th>School & Code</th>
                                            <th>Tenant Subdomain</th>
                                            <th>Administrator / Contact</th>
                                            <th>SaaS Tier</th>
                                            <th>Region</th>
                                            <th>Lifecycle Status</th>
                                            <th class="text-center">Action Controls</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($schoolsList as $sch)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2.5">
                                                        <div class="sa-avatar-chip">
                                                            {{ strtoupper(substr($sch->name, 0, 2)) }}
                                                        </div>
                                                        <div>
                                                            <span class="fw-bold text-dark d-block" style="font-size: 0.9rem;">{{ $sch->name }}</span>
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.7rem;">Code: {{ $sch->school_code }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <code class="px-2 py-1 bg-light border rounded text-primary" style="font-size: 0.78rem;">
                                                        {{ $sch->subdomain }}.{{ request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1' ? strtolower(config('app.name', 'EduLink')) . '.local' : preg_replace('/^(admin|www)\./', '', request()->getHost()) }}
                                                    </code>
                                                </td>
                                                <td>
                                                    <span class="d-block fw-semibold text-dark" style="font-size: 0.84rem;">{{ $sch->owner_name }}</span>
                                                    <span class="text-muted small">{{ $sch->owner_email }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem;">
                                                        <i class="bi bi-award me-1 text-warning"></i>{{ $sch->plan ? $sch->plan->name : 'No Plan' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="small fw-semibold text-secondary">{{ $sch->region ?: 'Ghana Standard' }}</span>
                                                </td>
                                                <td>
                                                    @if($sch->is_active)
                                                        <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem;">
                                                            <i class="bi bi-check-circle-fill me-1"></i>Active Tenant
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem;">
                                                            <i class="bi bi-pause-circle-fill me-1"></i>Suspended
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center text-nowrap">
                                                    <div class="d-flex justify-content-center align-items-center gap-1.5">
                                                        @if(!$sch->is_active)
                                                            <form action="{{ route('super-admin.schools.approve', $sch->id) }}" method="POST" class="d-inline-block m-0" onsubmit="return confirm('Approve and activate {{ addslashes($sch->name) }}?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-sm px-2.5 py-1 rounded-2 fw-semibold d-inline-flex align-items-center gap-1 text-nowrap shadow-xs" style="font-size: 0.78rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;" title="Approve & Activate School">
                                                                    <i class="bi bi-check-circle-fill"></i>
                                                                    <span>Approve School</span>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('super-admin.schools.toggle-status', $sch->id) }}" method="POST" class="d-inline-block m-0" onsubmit="return confirm('Are you sure you want to suspend this institution?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-outline-danger btn-sm px-2.5 py-1 rounded-2 text-nowrap" style="font-size: 0.78rem;" title="Suspend Tenant">
                                                                    <i class="bi bi-shield-slash me-1"></i>Suspend
                                                                </button>
                                                            </form>

                                                            <form action="{{ route('super-admin.schools.impersonate', $sch->id) }}" method="POST" class="d-inline-block m-0">
                                                                @csrf
                                                                <button type="submit" class="btn btn-brand-navy btn-sm px-2.5 py-1 rounded-2 text-nowrap" style="font-size: 0.78rem;" title="Sign in as School Admin">
                                                                    <i class="bi bi-box-arrow-in-right me-1"></i>Login As
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <span class="text-muted small">Showing {{ $schoolsList->firstItem() }} to {{ $schoolsList->lastItem() }} of {{ $schoolsList->total() }} institutions</span>
                                <div>{{ $schoolsList->links() }}</div>
                            </div>
                        @endif
                    </div>

                    <!-- TAB 3: SaaS Users Directory -->
                    <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">
                                    <i class="bi bi-people-fill text-warning me-2"></i>Platform Accounts Directory
                                </h6>
                                <span class="text-muted small">Manage super administrators, headteachers, accountants, teachers, and custom tenant roles.</span>
                            </div>
                            
                            <!-- Search & Filter Form -->
                            <form action="{{ route('super-admin.dashboard') }}" method="GET" class="d-flex flex-wrap gap-2">
                                <input type="hidden" name="tab" value="users">
                                
                                <select class="form-select form-select-sm" name="role_filter" style="width: 160px;">
                                    <option value="">-- All Roles --</option>
                                    @foreach($rolesList as $role)
                                        <option value="{{ $role->slug }}" {{ request('role_filter') === $role->slug ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control form-control-sm" name="user_search" placeholder="Search name, email..." value="{{ request('user_search') }}">
                                    <button type="submit" class="btn btn-brand-navy btn-sm px-3">Filter</button>
                                </div>
                                
                                @if(request('user_search') || request('role_filter'))
                                    <a href="{{ route('super-admin.dashboard', ['tab' => 'users']) }}" class="btn btn-outline-secondary btn-sm" title="Clear filters">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                @endif
                            </form>
                        </div>

                        @if($usersList->isEmpty())
                            <div class="text-center py-5 text-muted border rounded-4 my-2">
                                <div class="sa-avatar-chip mx-auto mb-3" style="width: 54px; height: 54px; font-size: 1.6rem; background: var(--sa-navy-subtle); color: var(--sa-navy-primary);">
                                    <i class="bi bi-person-x"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">No Accounts Found</h6>
                                <p class="small text-secondary mb-0">No user records matched your current query.</p>
                            </div>
                        @else
                            <div class="table-responsive border rounded-4 mb-3">
                                <table class="table sa-table align-middle table-hover">
                                    <thead>
                                        <tr>
                                            <th>Account Name</th>
                                            <th>Authentication Email</th>
                                            <th>School Association</th>
                                            <th>Assigned Role</th>
                                            <th>Access Status</th>
                                            <th class="text-center">Action Control</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($usersList as $usr)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2.5">
                                                        <div class="sa-avatar-chip" style="background: linear-gradient(135deg, #0f4c81 0%, #003366 100%);">
                                                            {{ strtoupper(substr($usr->name, 0, 2)) }}
                                                        </div>
                                                        <span class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $usr->name }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <code class="text-primary small">{{ $usr->email }}</code>
                                                </td>
                                                <td>
                                                    @if($usr->school)
                                                        <span class="badge bg-primary-subtle text-primary border px-2 py-1">
                                                            <i class="bi bi-building me-1"></i>{{ $usr->school->name }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border px-2 py-1">
                                                            <i class="bi bi-shield-lock-fill me-1"></i>Platform SuperAdmin
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 text-capitalize fw-semibold" style="font-size: 0.75rem;">
                                                        {{ $usr->role ? $usr->role->name : 'General User' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($usr->is_active)
                                                        <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-2 py-1 fw-bold" style="font-size: 0.72rem;">
                                                            <i class="bi bi-check-circle-fill me-1"></i>Active
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 px-2 py-1 fw-bold" style="font-size: 0.72rem;">
                                                            <i class="bi bi-slash-circle-fill me-1"></i>Blocked
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <form action="{{ route('super-admin.users.toggle-status', $usr->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn {{ $usr->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} btn-sm px-2.5 py-1 rounded-2" style="font-size: 0.78rem;" title="{{ $usr->is_active ? 'Block Access' : 'Restore Access' }}">
                                                            <i class="bi {{ $usr->is_active ? 'bi-lock-fill' : 'bi-unlock-fill' }} me-1"></i>
                                                            {{ $usr->is_active ? 'Block' : 'Unblock' }}
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <span class="text-muted small">Showing {{ $usersList->firstItem() }} to {{ $usersList->lastItem() }} of {{ $usersList->total() }} accounts</span>
                                <div>{{ $usersList->links() }}</div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Tab state persistence using History API
        const urlParams = new URLSearchParams(window.location.search);
        let activeTab = urlParams.get('tab');
        if (activeTab) {
            const tabButton = document.querySelector(`#dashboardTab button[data-bs-target="#${activeTab}"]`);
            if (tabButton) {
                const tab = new bootstrap.Tab(tabButton);
                tab.show();
            }
        }

        document.querySelectorAll('#dashboardTab button').forEach(button => {
            button.addEventListener('shown.bs.tab', function (e) {
                const target = e.target.getAttribute('data-bs-target').substring(1);
                const url = new URL(window.location);
                url.searchParams.set('tab', target);
                window.history.pushState({}, '', url);
            });
        });

        // Theme-aware Chart Font & Color Config
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const labelColor = isDark ? '#cbd5e1' : '#475569';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.05)';

        // 1. Subscription Distribution Donut Chart (EduLink Brand Palette)
        const subCtx = document.getElementById('subscriptionChart');
        if (subCtx) {
            const statusCounts = @json($statusCounts);
            const rawLabels = Object.keys(statusCounts).length ? Object.keys(statusCounts) : ['active', 'trial', 'suspended'];
            const data = Object.keys(statusCounts).length ? Object.values(statusCounts) : [1, 0, 0];

            // Official Brand Colors: Navy, Gold/Amber, Emerald, Sky Blue, Slate
            const brandColors = ['#003366', '#f59e0b', '#10b981', '#0284c7', '#64748b'];

            new Chart(subCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: rawLabels.map(l => l.toUpperCase() + ' TIERS'),
                    datasets: [{
                        data: data,
                        backgroundColor: brandColors,
                        borderWidth: 2,
                        borderColor: isDark ? '#111a2e' : '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: labelColor,
                                font: { family: 'Outfit', size: 12, weight: 'bold' },
                                padding: 16
                            }
                        }
                    },
                    cutout: '68%'
                }
            });
        }

        // 2. Regional Distribution Bar Chart (Ghana GES Regions)
        const regCtx = document.getElementById('regionChart');
        if (regCtx) {
            const regionCounts = @json($regionCounts);
            const regLabels = regionCounts.length ? regionCounts.map(r => r.region) : ['Greater Accra', 'Ashanti', 'Western', 'Eastern', 'Central'];
            const regData = regionCounts.length ? regionCounts.map(r => r.count) : [0, 0, 0, 0, 0];

            new Chart(regCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: regLabels,
                    datasets: [{
                        label: 'Registered Institutions',
                        data: regData,
                        backgroundColor: '#003366',
                        hoverBackgroundColor: '#0f4c81',
                        borderColor: '#FFD700',
                        borderWidth: 1,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, color: labelColor },
                            grid: { color: gridColor }
                        },
                        y: {
                            ticks: { color: labelColor, font: { family: 'Outfit', weight: 'bold' } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
