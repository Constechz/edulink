@extends('layouts.app')

@section('title', 'School Dashboard | ' . config('app.name', 'EduLink'))
@section('header_title', 'School Administration Dashboard')

@section('content')
<div class="container-fluid p-0">

    @php
        $tenant = app('tenant');
        $schoolId = $tenant ? $tenant->id : (Auth::user() ? Auth::user()->school_id : null);

        // Core Counts & Metrics
        $studentCount = \App\Models\Student::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count();
        $staffCount = \App\Models\Staff::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count();
        $classCount = \App\Models\SchoolClass::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count();
        $campusCount = \App\Models\Campus::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count();
        $activeCampus = \App\Models\Campus::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->first();

        // Academic Calendar
        $activeYear = \App\Models\AcademicYear::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->where('is_current', true)->first();
        $activeTerm = \App\Models\Term::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->where('is_current', true)->first();

        // Financial & Billing
        $totalCollected = \App\Models\Payment::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->sum('amount') ?? 0;
        $totalInvoices = \App\Models\Invoice::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count();

        // Trial calculation
        $daysLeft = ($tenant && $tenant->trial_ends_at) ? now()->diffInDays($tenant->trial_ends_at, false) : 0;
    @endphp

    @if ($tenant && $tenant->subscription_status === 'trial')
        <div class="alert alert-warning border-0 rounded-4 shadow-sm d-flex flex-column flex-md-row align-items-md-center justify-content-between p-3.5 mb-4" style="background-color: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.25) !important;">
            <div class="d-flex align-items-center gap-3 mb-2 mb-md-0">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: rgba(245, 158, 11, 0.2); color: #d97706;">
                    <i class="bi bi-clock-history fs-5"></i>
                </div>
                <div>
                    @if ($daysLeft > 0)
                        <div class="fw-bold" style="color: var(--text-heading);">Free Trial Active &bull; {{ ceil($daysLeft) }} Days Remaining</div>
                        <div class="text-muted small">Your institution is currently on the free trial. Upgrade now to unlock full unlimited access to SMS blasts, SBA reports, and portals.</div>
                    @else
                        <div class="fw-bold" style="color: var(--text-heading);">Trial Evaluation Mode</div>
                        <div class="text-muted small">Upgrade to an active subscription to access automated MoMo fee reconciliation, parent portals, and terminal report card broadcasting.</div>
                    @endif
                </div>
            </div>
            <a href="{{ route('school.billing.index') }}" class="btn btn-warning fw-bold px-3.5 py-2 rounded-3 text-nowrap flex-shrink-0 shadow-xs">
                <i class="bi bi-credit-card me-1.5"></i>Upgrade Subscription
            </a>
        </div>
    @endif

    <!-- Executive Header Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 p-md-4 text-white rounded-4 shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #002244 0%, #003366 65%, #0f4c81 100%); border: 1px solid rgba(255, 215, 0, 0.18);">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="badge" style="background: rgba(255, 215, 0, 0.18); color: #FFD700; border: 1px solid rgba(255, 215, 0, 0.35); font-size: 0.72rem;">
                                <i class="bi bi-patch-check-fill me-1"></i>Ghana GES SBA Standard Compliant
                            </span>
                            @if($activeYear && $activeTerm)
                                <span class="badge" style="background: rgba(255, 255, 255, 0.12); color: #ffffff; font-size: 0.72rem;">
                                    <i class="bi bi-calendar3 me-1"></i>{{ $activeYear->name }} &bull; {{ $activeTerm->name }}
                                </span>
                            @endif
                            @if($activeCampus)
                                <span class="badge" style="background: rgba(255, 255, 255, 0.12); color: #ffffff; font-size: 0.72rem;">
                                    <i class="bi bi-building me-1"></i>{{ $activeCampus->name }}
                                </span>
                            @endif
                        </div>

                        <h2 class="fw-bold mb-1" style="font-size: clamp(1.4rem, 2.5vw, 1.85rem); letter-spacing: -0.5px;">
                            {{ $tenant ? $tenant->name : 'Green Valley International School' }}
                        </h2>
                        <p class="text-white-50 mb-0 small">
                            Administrator: <strong class="text-white">{{ Auth::user()->name }}</strong> &bull; Role: <span style="color: var(--accent-color, #FFD700); font-weight: 600;">{{ Auth::user()->role ? Auth::user()->role->name : 'School Administrator' }}</span>
                        </p>
                    </div>
                    
                    <div class="col-lg-4 text-lg-end d-flex flex-wrap justify-content-lg-end gap-2">
                        @if(Auth::user()->hasPermission('manage-enrollments'))
                            <a href="{{ route('school.students') }}" class="btn btn-warning rounded-3 px-3 py-2 fw-bold text-decoration-none shadow-sm text-nowrap">
                                <i class="bi bi-person-plus-fill me-1.5"></i>Enrol Student
                            </a>
                        @endif
                        <a href="{{ route('school.billing.index') }}" class="btn btn-outline-light rounded-3 px-3 py-2 fw-semibold text-decoration-none text-nowrap" style="background: rgba(255, 255, 255, 0.08); border-color: rgba(255, 255, 255, 0.25);">
                            <i class="bi bi-wallet2 me-1.5"></i>Billing Hub
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 High-Impact KPI Metric Cards -->
    <div class="row g-3 g-md-4 mb-4">
        
        <!-- Total Students -->
        <div class="col-6 col-lg-3">
            <div class="glass-card p-3.5 p-md-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Enrolled Students</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(0, 51, 102, 0.1); color: var(--primary-color);">
                        <i class="bi bi-mortarboard-fill fs-5"></i>
                    </div>
                </div>
                <div>
                    <div class="card-metric mb-1" style="font-size: clamp(1.6rem, 2.8vw, 2.1rem); font-weight: 800; color: var(--primary-color); line-height: 1.1;">
                        {{ number_format($studentCount) }}
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top" style="border-color: var(--border-color) !important;">
                        <span class="text-muted small" style="font-size: 0.76rem;">Active pupil records</span>
                        <a href="{{ route('school.students') }}" class="text-primary text-decoration-none small fw-semibold" style="font-size: 0.76rem;">View &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Staff -->
        <div class="col-6 col-lg-3">
            <div class="glass-card p-3.5 p-md-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Staff &amp; Faculty</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(245, 158, 11, 0.12); color: #d97706;">
                        <i class="bi bi-person-workspace fs-5"></i>
                    </div>
                </div>
                <div>
                    <div class="card-metric mb-1" style="font-size: clamp(1.6rem, 2.8vw, 2.1rem); font-weight: 800; color: var(--primary-color); line-height: 1.1;">
                        {{ number_format($staffCount) }}
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top" style="border-color: var(--border-color) !important;">
                        <span class="text-muted small" style="font-size: 0.76rem;">Teachers &amp; HODs</span>
                        <a href="{{ route('school.staff') }}" class="text-primary text-decoration-none small fw-semibold" style="font-size: 0.76rem;">Manage &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Classes -->
        <div class="col-6 col-lg-3">
            <div class="glass-card p-3.5 p-md-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Classrooms</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(0, 51, 102, 0.1); color: var(--primary-color);">
                        <i class="bi bi-grid-1x2-fill fs-5"></i>
                    </div>
                </div>
                <div>
                    <div class="card-metric mb-1" style="font-size: clamp(1.6rem, 2.8vw, 2.1rem); font-weight: 800; color: var(--primary-color); line-height: 1.1;">
                        {{ number_format($classCount) }}
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top" style="border-color: var(--border-color) !important;">
                        <span class="text-muted small" style="font-size: 0.76rem;">Active study streams</span>
                        <a href="{{ route('school.academics') }}" class="text-primary text-decoration-none small fw-semibold" style="font-size: 0.76rem;">Setup &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Collections -->
        <div class="col-6 col-lg-3">
            <div class="glass-card p-3.5 p-md-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Fee Collections</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(16, 185, 129, 0.12); color: #059669;">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                </div>
                <div>
                    <div class="card-metric mb-1" style="font-size: clamp(1.35rem, 2.2vw, 1.75rem); font-weight: 800; color: #059669; line-height: 1.1;">
                        GHS {{ number_format($totalCollected, 2) }}
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top" style="border-color: var(--border-color) !important;">
                        <span class="text-muted small" style="font-size: 0.76rem;">MoMo &amp; Cash Total</span>
                        <a href="{{ route('school.billing.index') }}" class="text-success text-decoration-none small fw-semibold" style="font-size: 0.76rem;">Billing &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Core Module Hubs -->
    <div class="row g-4 mb-4">

        <!-- Pillar 1: Academic & Student Operations -->
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100 d-flex flex-column">
                <div class="d-flex align-items-center gap-2.5 mb-3.5 pb-2.5 border-bottom" style="border-color: var(--border-color) !important;">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(0, 51, 102, 0.1); color: var(--primary-color);">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="color: var(--text-heading);">Academic Operations</h6>
                        <span class="text-muted small" style="font-size: 0.74rem;">Student records &amp; scheduling</span>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 flex-grow-1">
                    @if(Auth::user()->hasPermission('manage-enrollments'))
                    <a href="{{ route('school.students') }}" class="p-2.5 rounded-3 text-decoration-none d-flex align-items-center gap-3 transition-hover" style="background: rgba(0, 51, 102, 0.03);">
                        <i class="bi bi-person-lines-fill fs-5" style="color: var(--primary-color);"></i>
                        <div>
                            <div class="fw-semibold text-dark small">Student Registry</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Bio profiles, enrollment &amp; guardians</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>
                    @endif

                    @if(Auth::user()->hasPermission('manage-enrollments') || Auth::user()->hasPermission('enter-scores'))
                    <a href="{{ route('school.attendance') }}" class="p-2.5 rounded-3 text-decoration-none d-flex align-items-center gap-3 transition-hover" style="background: rgba(0, 51, 102, 0.03);">
                        <i class="bi bi-calendar-check fs-5" style="color: var(--primary-color);"></i>
                        <div>
                            <div class="fw-semibold text-dark small">Attendance &amp; QR Kiosk</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Roll calls &amp; instant parent SMS alerts</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>
                    @endif

                    @if(Auth::user()->hasPermission('manage-academics') || Auth::user()->hasPermission('enter-scores'))
                    <a href="{{ route('school.timetable') }}" class="p-2.5 rounded-3 text-decoration-none d-flex align-items-center gap-3 transition-hover" style="background: rgba(0, 51, 102, 0.03);">
                        <i class="bi bi-calendar2-week fs-5" style="color: var(--primary-color);"></i>
                        <div>
                            <div class="fw-semibold text-dark small">Class Timetable</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Period allocation &amp; clash detector</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>
                    @endif

                    @if(Auth::user()->hasPermission('manage-academics'))
                    <a href="{{ route('school.subjects') }}" class="p-2.5 rounded-3 text-decoration-none d-flex align-items-center gap-3 transition-hover" style="background: rgba(0, 51, 102, 0.03);">
                        <i class="bi bi-book fs-5" style="color: var(--primary-color);"></i>
                        <div>
                            <div class="fw-semibold text-dark small">Subjects &amp; Curriculum</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Core &amp; elective subject assignment</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pillar 2: GES SBA Scoring & Reports -->
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100 d-flex flex-column">
                <div class="d-flex align-items-center gap-2.5 mb-3.5 pb-2.5 border-bottom" style="border-color: var(--border-color) !important;">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(245, 158, 11, 0.12); color: #d97706;">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="color: var(--text-heading);">GES SBA Grading &amp; Reports</h6>
                        <span class="text-muted small" style="font-size: 0.74rem;">Continuous assessment &amp; reports</span>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 flex-grow-1">
                    @if(Auth::user()->hasPermission('configure-scoring'))
                    <a href="{{ route('school.scoring-configs.index') }}" class="p-2.5 rounded-3 text-decoration-none d-flex align-items-center gap-3 transition-hover" style="background: rgba(245, 158, 11, 0.04);">
                        <i class="bi bi-sliders fs-5" style="color: #d97706;"></i>
                        <div>
                            <div class="fw-semibold text-dark small">SBA Weights &amp; Rules</div>
                            <div class="text-muted" style="font-size: 0.72rem;">GES 30/70 &bull; 50/50 grading standards</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>
                    @endif

                    @if(Auth::user()->hasPermission('enter-scores'))
                    <a href="{{ route('school.scores.enter') }}" class="p-2.5 rounded-3 text-decoration-none d-flex align-items-center gap-3 transition-hover" style="background: rgba(245, 158, 11, 0.04);">
                        <i class="bi bi-table fs-5" style="color: #d97706;"></i>
                        <div>
                            <div class="fw-semibold text-dark small">Score Entry Grid</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Class marks, Excel copy-paste &amp; save</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>
                    @endif

                    @if(Auth::user()->hasPermission('publish-reports') || Auth::user()->hasPermission('enter-scores'))
                    <a href="{{ route('school.reports.index') }}" class="p-2.5 rounded-3 text-decoration-none d-flex align-items-center gap-3 transition-hover" style="background: rgba(245, 158, 11, 0.04);">
                        <i class="bi bi-file-earmark-bar-graph fs-5" style="color: #d97706;"></i>
                        <div>
                            <div class="fw-semibold text-dark small">Terminal Report Cards</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Student broadsheets &amp; PDF generation</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>
                    @endif

                    @if(Auth::user()->hasPermission('manage-enrollments') || (Auth::user()->role && Auth::user()->role->slug === 'class-teacher'))
                    <a href="{{ route('school.students.promotion') }}" class="p-2.5 rounded-3 text-decoration-none d-flex align-items-center gap-3 transition-hover" style="background: rgba(245, 158, 11, 0.04);">
                        <i class="bi bi-arrow-up-circle fs-5" style="color: #d97706;"></i>
                        <div>
                            <div class="fw-semibold text-dark small">Student Promotions</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Batch promote or repeat cohorts</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pillar 3: Administration & Finance -->
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100 d-flex flex-column">
                <div class="d-flex align-items-center gap-2.5 mb-3.5 pb-2.5 border-bottom" style="border-color: var(--border-color) !important;">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(16, 185, 129, 0.12); color: #059669;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="color: var(--text-heading);">Finances &amp; Settings</h6>
                        <span class="text-muted small" style="font-size: 0.74rem;">Billing, staff &amp; infrastructure</span>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 flex-grow-1">
                    <a href="{{ route('school.billing.index') }}" class="p-2.5 rounded-3 text-decoration-none d-flex align-items-center gap-3 transition-hover" style="background: rgba(16, 185, 129, 0.03);">
                        <i class="bi bi-credit-card-2-front fs-5 text-success"></i>
                        <div>
                            <div class="fw-semibold text-dark small">MTN MoMo &amp; Billing</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Invoices, fee collections &amp; receipts</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>

                    @if(Auth::user()->hasPermission('manage-staff'))
                    <a href="{{ route('school.staff') }}" class="p-2.5 rounded-3 text-decoration-none d-flex align-items-center gap-3 transition-hover" style="background: rgba(0, 51, 102, 0.03);">
                        <i class="bi bi-people fs-5" style="color: var(--primary-color);"></i>
                        <div>
                            <div class="fw-semibold text-dark small">Staff HR &amp; Accounts</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Instructor logins &amp; role permissions</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>
                    @endif

                    @if(Auth::user()->hasPermission('manage-campuses'))
                    <a href="{{ route('school.campuses') }}" class="p-2.5 rounded-3 text-decoration-none d-flex align-items-center gap-3 transition-hover" style="background: rgba(0, 51, 102, 0.03);">
                        <i class="bi bi-building fs-5" style="color: var(--primary-color);"></i>
                        <div>
                            <div class="fw-semibold text-dark small">Campus &amp; Branches</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Multi-campus management</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>
                    @endif

                    @if(Auth::user()->hasPermission('manage-settings'))
                    <a href="{{ route('school.settings') }}" class="p-2.5 rounded-3 text-decoration-none d-flex align-items-center gap-3 transition-hover" style="background: rgba(0, 51, 102, 0.03);">
                        <i class="bi bi-gear-fill fs-5" style="color: var(--primary-color);"></i>
                        <div>
                            <div class="fw-semibold text-dark small">Institution Settings</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Crest, branding, SMS &amp; system config</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- Live Infrastructure Status Bar -->
    <div class="row">
        <div class="col-12">
            <div class="glass-card p-3.5 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge-live-pulse" style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block;"></span>
                    <span class="small fw-semibold" style="color: var(--text-heading); font-size: 0.82rem;">
                        EduLink Ghana ERP Infrastructure &bull; All Subsystems Active
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="text-muted small d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                        <i class="bi bi-check-circle-fill text-success"></i> GES SBA Engine: <strong>30/70 &amp; 50/50 Ready</strong>
                    </span>
                    <span class="text-muted small d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                        <i class="bi bi-check-circle-fill text-success"></i> MoMo Gateway: <strong>Connected</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
