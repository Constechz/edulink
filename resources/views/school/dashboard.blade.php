@extends('layouts.app')

@section('title', 'School Dashboard | EduLink')
@section('header_title', 'School Administration Dashboard')

@section('content')
<div class="container-fluid p-0">

    @php
        $tenant = app('tenant');
    @endphp

    @if ($tenant && $tenant->subscription_status === 'trial')
        @php
            $daysLeft = $tenant->trial_ends_at ? now()->diffInDays($tenant->trial_ends_at, false) : 0;
        @endphp
        <div class="alert alert-warning border-0 rounded-3 shadow-xs d-flex align-items-center justify-content-between p-3 mb-4" style="background-color: rgba(255, 215, 0, 0.1); border: 1px solid rgba(255, 215, 0, 0.25) !important;">
            <div class="text-dark">
                <i class="bi bi-info-circle me-2 fs-5 text-warning"></i>
                @if ($daysLeft > 0)
                    <strong>Free Trial Active:</strong> Your institution has <strong>{{ ceil($daysLeft) }} days</strong> left on your free trial.
                @else
                    <strong>Free Trial Plan:</strong> You are on the free trial plan. Upgrade to a paid subscription to unlock premium modules like Student/Parent Portals, SBA Scoring, and the Website Builder.
                @endif
            </div>
            <a href="{{ route('school.billing.index') }}" class="btn btn-warning btn-sm fw-bold px-3 py-1.5 rounded-3 shadow-xs" style="color: #020617; background-color: #FFD700; border: none;">
                <i class="bi bi-credit-card me-1"></i> Upgrade Subscription
            </a>
        </div>
    @endif

    <!-- Executive Header Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 text-white rounded-4 shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #002244 0%, #003366 60%, #0f4c81 100%); border: 1px solid rgba(255, 215, 0, 0.15);">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <h2 class="fw-bold mb-1" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                            {{ $tenant ? $tenant->name : \App\Models\SystemSetting::getVal('platform_name', 'EduLink') . ' Ghana' }}
                        </h2>
                        <p class="text-white-50 mb-0" style="font-size: 0.9rem;">
                            Logged in as <strong class="text-white">{{ Auth::user()->name }}</strong> &bull; <span style="color: var(--accent-color, #FFD700);">{{ Auth::user()->role ? Auth::user()->role->name : 'Administrator' }}</span>
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('school.billing.index') }}" class="btn btn-warning rounded-3 px-3 py-2 fw-bold text-decoration-none shadow-sm">
                            <i class="bi bi-credit-card me-1"></i>Subscription &amp; Invoices
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Shortcut Cards -->
    <h5 class="fw-bold mb-3 text-dark">Quick Navigation</h5>
    <div class="row g-4 mb-4">
        
        <!-- Student Registry -->
        <div class="col-md-4">
            <a href="{{ route('school.students') }}" class="text-decoration-none text-dark">
                <div class="glass-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="fs-2 text-primary bg-primary bg-opacity-10 p-3 rounded-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-mortarboard"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Student Registry</h6>
                        <span class="text-muted small">Manage student records & profiles</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Attendance -->
        <div class="col-md-4">
            <a href="{{ route('school.attendance') }}" class="text-decoration-none text-dark">
                <div class="glass-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="fs-2 text-primary bg-primary bg-opacity-10 p-3 rounded-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Student Attendance</h6>
                        <span class="text-muted small">Record daily roll calls & kiosk scans</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Timetable -->
        <div class="col-md-4">
            <a href="{{ route('school.timetable') }}" class="text-decoration-none text-dark">
                <div class="glass-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="fs-2 text-primary bg-primary bg-opacity-10 p-3 rounded-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-calendar2-week"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Class Timetable</h6>
                        <span class="text-muted small">Schedule classes & detect clashes</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- SBA Setup -->
        <div class="col-md-4">
            <a href="{{ route('school.scoring-configs.index') }}" class="text-decoration-none text-dark">
                <div class="glass-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="fs-2 text-warning bg-warning bg-opacity-10 p-3 rounded-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-sliders"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1" style="color: var(--primary-color);">SBA Setup</h6>
                        <span class="text-muted small">Configure raw component weights & limits</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Enter Scores -->
        <div class="col-md-4">
            <a href="{{ route('school.scores.enter') }}" class="text-decoration-none text-dark">
                <div class="glass-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="fs-2 text-warning bg-warning bg-opacity-10 p-3 rounded-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-table"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1" style="color: var(--primary-color);">Enter Scores</h6>
                        <span class="text-muted small">Grid inputs, Excel copy-paste & reviews</span>
                    </div>
                </div>
            </a>
        </div>

        @if(Auth::user()->role && in_array(Auth::user()->role->slug, ['school-admin', 'class-teacher']))
        <!-- Student Promotion -->
        <div class="col-md-4">
            <a href="{{ route('school.students.promotion') }}" class="text-decoration-none text-dark">
                <div class="glass-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="fs-2 text-primary bg-primary bg-opacity-10 p-3 rounded-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-arrow-up-circle"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Student Promotion</h6>
                        <span class="text-muted small">Promote, repeat, or graduate students</span>
                    </div>
                </div>
            </a>
        </div>
        @endif

        <!-- Report Hub -->
        <div class="col-md-4">
            <a href="{{ route('school.reports.index') }}" class="text-decoration-none text-dark">
                <div class="glass-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="fs-2 text-warning bg-warning bg-opacity-10 p-3 rounded-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1" style="color: var(--primary-color);">Report Hub</h6>
                        <span class="text-muted small">Stream student report cards & broadsheets</span>
                    </div>
                </div>
            </a>
        </div>

    </div>

</div>
@endsection
