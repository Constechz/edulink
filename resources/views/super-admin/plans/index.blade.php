@extends('layouts.app')

@section('title', 'Platform SaaS Plans | EduLink')
@section('header_title', 'SaaS Plans & Subscription Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i><strong>Action Failed:</strong>
            <ul class="mb-0 mt-2 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-sm-6">
            <h5 class="fw-bold mb-0 text-dark">Subscription Tier Configuration</h5>
            <p class="text-muted small mb-0">Create and modify SaaS plans, pricing structures, and dynamic limits for school tenants.</p>
        </div>
        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
            <a href="{{ route('super-admin.plans.create') }}" class="btn btn-primary rounded-3 px-3 py-2 fw-semibold shadow-xs">
                <i class="bi bi-plus-lg me-1"></i>Create New SaaS Plan
            </a>
        </div>
    </div>

    <!-- Plans Table -->
    <div class="row g-4">
        <div class="col-12">
            <div class="glass-card p-4 shadow-sm">
                <div class="table-responsive">
                    <table class="table align-middle table-hover small">
                        <thead>
                            <tr>
                                <th>Plan ID</th>
                                <th>Plan Name</th>
                                <th>Termly Price</th>
                                <th>Yearly Price</th>
                                <th>Enrollment Quotas</th>
                                <th>Active Tenants</th>
                                <th>SMS Package</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plans as $plan)
                                <tr>
                                    <td><strong>#{{ $plan->id }}</strong></td>
                                    <td>
                                        <span class="fw-bold text-dark fs-6">{{ $plan->name }}</span>
                                    </td>
                                    <td class="fw-bold" style="color: var(--primary-color) !important;">GHS {{ number_format($plan->price_monthly, 2) }}</td>
                                    <td class="fw-bold text-success">GHS {{ number_format($plan->price_yearly, 2) }}</td>
                                    <td class="text-muted text-wrap">
                                        <ul class="list-unstyled mb-0 small">
                                            <li><i class="bi bi-person-fill text-secondary me-1"></i>Students: <strong>{{ $plan->max_students === -1 ? 'Unlimited' : number_format($plan->max_students) }}</strong></li>
                                            <li><i class="bi bi-people-fill text-secondary me-1"></i>Staff: <strong>{{ $plan->max_staff === -1 ? 'Unlimited' : number_format($plan->max_staff) }}</strong></li>
                                            <li><i class="bi bi-building-fill text-secondary me-1"></i>Campuses: <strong>{{ $plan->max_campuses === -1 ? 'Unlimited' : number_format($plan->max_campuses) }}</strong></li>
                                        </ul>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info border px-2 py-1.5 fw-semibold rounded-3">
                                            <i class="bi bi-building me-1"></i>{{ $plan->schools_count }} Schools
                                        </span>
                                    </td>
                                    <td class="text-muted">
                                        <ul class="list-unstyled mb-0 small">
                                            <li><i class="bi bi-chat-fill text-secondary me-1"></i>SMS Credits: <strong>{{ number_format($plan->sms_credits_monthly) }}/term</strong></li>
                                        </ul>
                                    </td>
                                    <td>
                                        @if($plan->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success border px-2 py-1.5 rounded-3"><i class="bi bi-check-circle-fill me-1"></i>Active</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border px-2 py-1.5 rounded-3"><i class="bi bi-x-circle-fill me-1"></i>Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('super-admin.plans.edit', $plan->id) }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3.5 py-1.5" title="Edit Plan Details">
                                                <i class="bi bi-pencil-square me-1"></i>Edit
                                            </a>
                                            <form action="{{ route('super-admin.plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this subscription plan?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1.5" title="Delete or Deactivate Plan">
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
