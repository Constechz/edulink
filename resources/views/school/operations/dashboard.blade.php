@extends('layouts.app')

@section('title', 'Operations Center | EduLink')
@section('header_title', 'School Operations Center')

@section('content')
<div class="container-fluid p-0">
    <!-- Header banner -->
    <div class="glass-card p-4 mb-4" style="background: linear-gradient(135deg, rgba(0, 51, 102, 0.05) 0%, rgba(255, 215, 0, 0.05) 100%);">
        <h3 class="font-weight-bold mb-1" style="font-weight: 700; color: var(--primary-color);">Operational Services</h3>
        <p class="text-muted mb-0">Consolidated administration desk for school facilities, logistics, payroll, health, and discipline.</p>
    </div>

    <!-- Quick action links -->
    <div class="row g-4">
        <!-- Library Card -->
        <div class="col-md-4">
            <div class="glass-card p-4 h-100 d-flex flex-column">
                <div class="fs-2 text-primary mb-3"><i class="bi bi-book-half"></i></div>
                <h5 class="fw-bold text-dark mb-2">Library Catalog</h5>
                <p class="text-muted small mb-4">Manage book logs, overdue items, and borrow/return registers.</p>
                <div class="mt-auto d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Active Loans: <strong>{{ $activeLoansCount }}</strong></span>
                    <a href="{{ route('school.operations.library.index') }}" class="btn btn-sm btn-primary rounded-3 px-3">Manage</a>
                </div>
            </div>
        </div>

        <!-- Inventory Card -->
        <div class="col-md-4">
            <div class="glass-card p-4 h-100 d-flex flex-column">
                <div class="fs-2 text-warning mb-3"><i class="bi bi-box-seam"></i></div>
                <h5 class="fw-bold text-dark mb-2">Inventory Ledger</h5>
                <p class="text-muted small mb-4">Monitor central warehouse stock levels, adjustments, and logistics logs.</p>
                <div class="mt-auto d-flex justify-content-between align-items-center">
                    <span class="text-danger small">Low Stock Warning: <strong>{{ $lowStockCount }}</strong></span>
                    <a href="{{ route('school.operations.inventory.index') }}" class="btn btn-sm btn-warning text-dark rounded-3 px-3">Manage</a>
                </div>
            </div>
        </div>

        <!-- Hostel Card -->
        <div class="col-md-4">
            <div class="glass-card p-4 h-100 d-flex flex-column">
                <div class="fs-2 text-success mb-3"><i class="bi bi-house-door"></i></div>
                <h5 class="fw-bold text-dark mb-2">Hostels & Dormitories</h5>
                <p class="text-muted small mb-4">Register student bed allocations, vacancy trackers, and dormitory warden lists.</p>
                <div class="mt-auto d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Total Capacity: <strong>{{ $totalDormsCapacity }}</strong> Beds</span>
                    <a href="{{ route('school.operations.hostel.index') }}" class="btn btn-sm btn-success rounded-3 px-3">Manage</a>
                </div>
            </div>
        </div>

        <!-- Transport Card -->
        <div class="col-md-4">
            <div class="glass-card p-4 h-100 d-flex flex-column">
                <div class="fs-2 text-info mb-3"><i class="bi bi-bus-front"></i></div>
                <h5 class="fw-bold text-dark mb-2">Transport Services</h5>
                <p class="text-muted small mb-4">Manage school bus routes, driver rosters, vehicle allocations, and schedules.</p>
                <div class="mt-auto text-end">
                    <a href="{{ route('school.operations.transport.index') }}" class="btn btn-sm btn-info text-white rounded-3 px-3">Manage</a>
                </div>
            </div>
        </div>

        <!-- HR & Leave Card -->
        <div class="col-md-4">
            <div class="glass-card p-4 h-100 d-flex flex-column">
                <div class="fs-2 text-danger mb-3"><i class="bi bi-person-badge"></i></div>
                <h5 class="fw-bold text-dark mb-2">HR, Leave & Payroll</h5>
                <p class="text-muted small mb-4">Track staff leave applications, monthly payslips, and periodic payroll generations.</p>
                <div class="mt-auto d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Pending Leaves: <strong>{{ $pendingLeavesCount }}</strong></span>
                    <a href="{{ route('school.operations.hr.index') }}" class="btn btn-sm btn-danger rounded-3 px-3">Manage</a>
                </div>
            </div>
        </div>

        <!-- Health & Discipline Card -->
        <div class="col-md-4">
            <div class="glass-card p-4 h-100 d-flex flex-column">
                <div class="fs-2 text-secondary mb-3"><i class="bi bi-heart-pulse"></i></div>
                <h5 class="fw-bold text-dark mb-2">Health & Discipline</h5>
                <p class="text-muted small mb-4">Record clinic sickbay visits, student medical charts, warnings, and discipline cases.</p>
                <div class="mt-auto d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Pending Cases: <strong>{{ $disciplineCasesCount }}</strong></span>
                    <a href="{{ route('school.operations.health-discipline.index') }}" class="btn btn-sm btn-secondary rounded-3 px-3">Manage</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
