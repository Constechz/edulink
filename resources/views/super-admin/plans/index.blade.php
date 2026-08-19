@extends('layouts.app')

@section('title', 'Platform SaaS Plans | ' . config('app.name', 'EduLink'))
@section('header_title', 'SaaS Plans & Subscription Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm border-0 d-flex align-items-center" role="alert" style="background: rgba(16, 185, 129, 0.12); color: #059669; border-left: 4px solid #10b981 !important;">
            <i class="bi bi-check-circle-fill fs-5 me-2.5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4 shadow-sm border-0" role="alert" style="background: rgba(239, 68, 68, 0.1); color: #dc2626; border-left: 4px solid #ef4444 !important;">
            <div class="d-flex align-items-center mb-1">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <strong>Action Failed:</strong>
            </div>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Executive Header Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 text-white rounded-4 shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #002244 0%, #003366 60%, #0f4c81 100%); border: 1px solid rgba(255, 215, 0, 0.15);">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <h2 class="fw-bold mb-2" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                            Subscription Tier <span style="color: var(--accent-color, #FFD700);">Configuration Hub</span>
                        </h2>
                        <p class="text-white-50 mb-0" style="font-size: 0.9rem; max-width: 650px;">
                            Define pricing schedules, student and campus quotas, automatic SMS credit allocations, and feature paywall toggles across all tenant institutions.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('super-admin.plans.create') }}" class="btn btn-warning rounded-3 px-3 py-2 fw-bold text-decoration-none shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i>Create New SaaS Plan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Plans Table Card -->
    <div class="row g-4">
        <div class="col-12">
            <div class="glass-card p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-layers-fill text-primary me-2"></i>Active SaaS Pricing Tiers ({{ count($plans) }})</h5>
                    <span class="badge bg-primary-subtle text-primary fw-semibold">Dynamic Quotas &amp; Limits</span>
                </div>
                <div class="table-responsive border rounded-3">
                    <table class="table align-middle table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Plan ID</th>
                                <th>Plan Name</th>
                                <th>Termly Rate</th>
                                <th>Yearly Rate</th>
                                <th>Enrollment Quotas</th>
                                <th>Active Tenants</th>
                                <th>SMS Package</th>
                                <th>Status</th>
                                <th class="text-center">Action Controls</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plans as $plan)
                                <tr>
                                    <td><code class="text-primary fw-bold">#{{ $plan->id }}</code></td>
                                    <td>
                                        <span class="fw-bold text-dark fs-6">{{ $plan->name }}</span>
                                    </td>
                                    <td class="fw-bold text-primary">GHS {{ number_format($plan->price_monthly, 2) }}</td>
                                    <td class="fw-bold text-success">GHS {{ number_format($plan->price_yearly, 2) }}</td>
                                    <td class="text-muted text-wrap">
                                        <ul class="list-unstyled mb-0 small">
                                            <li><i class="bi bi-person-fill text-secondary me-1"></i>Students: <strong>{{ $plan->max_students === -1 ? 'Unlimited' : number_format($plan->max_students) }}</strong></li>
                                            <li><i class="bi bi-people-fill text-secondary me-1"></i>Staff: <strong>{{ $plan->max_staff === -1 ? 'Unlimited' : number_format($plan->max_staff) }}</strong></li>
                                            <li><i class="bi bi-building-fill text-secondary me-1"></i>Campuses: <strong>{{ $plan->max_campuses === -1 ? 'Unlimited' : number_format($plan->max_campuses) }}</strong></li>
                                        </ul>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 px-2.5 py-1.5 fw-semibold rounded-3">
                                            <i class="bi bi-building me-1"></i>{{ $plan->schools_count }} Schools
                                        </span>
                                    </td>
                                    <td class="text-muted">
                                        <span class="badge bg-info-subtle text-info fw-bold">
                                            <i class="bi bi-chat-fill me-1"></i>{{ number_format($plan->sms_credits_monthly) }} SMS/term
                                        </span>
                                    </td>
                                    <td>
                                        @if($plan->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded-3 fw-bold">
                                                <i class="bi bi-check-circle-fill me-1"></i>Active Tier
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 px-2.5 py-1.5 rounded-3 fw-bold">
                                                <i class="bi bi-x-circle-fill me-1"></i>Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1.5">
                                            <a href="{{ route('super-admin.plans.edit', $plan->id) }}" class="btn btn-sm btn-outline-primary rounded-2 px-3 py-1" title="Edit Plan Details">
                                                <i class="bi bi-pencil-square me-1"></i>Edit
                                            </a>
                                            <form action="{{ route('super-admin.plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this subscription plan?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2.5 py-1" title="Delete Plan">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
