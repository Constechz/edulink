@extends('layouts.app')

@section('title', 'Platform Roles & Permissions | ' . config('app.name', 'EduLink'))
@section('header_title', 'Roles & Permissions Governance')

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
                            Platform Roles &amp; <span style="color: var(--accent-color, #FFD700);">Access Control</span>
                        </h2>
                        <p class="text-white-50 mb-0" style="font-size: 0.9rem; max-width: 650px;">
                            Configure role-based permissions, administrative hierarchies, and security credentials across global and institutional tenant levels.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('super-admin.roles.create') }}" class="btn btn-warning rounded-3 px-3 py-2 fw-bold text-decoration-none shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i>Create Custom Role
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Grid -->
    <div class="row g-4">
        <div class="col-12">
            <div class="glass-card p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-person-badge-fill text-primary me-2"></i>Global System &amp; Custom Roles ({{ count($roles) }})</h5>
                    <span class="badge bg-primary-subtle text-primary fw-semibold">Role-Based Access Control</span>
                </div>
                <div class="table-responsive border rounded-3">
                    <table class="table align-middle table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Role ID</th>
                                <th>Role Name</th>
                                <th>System Slug</th>
                                <th>Description</th>
                                <th>Permission Count</th>
                                <th>Role Scope</th>
                                <th class="text-center">Action Controls</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td><code class="text-primary fw-bold">#{{ $role->id }}</code></td>
                                    <td>
                                        <span class="fw-bold text-dark fs-6">{{ $role->name }}</span>
                                    </td>
                                    <td><code class="text-secondary small">{{ $role->slug }}</code></td>
                                    <td class="text-muted text-wrap" style="max-width: 250px; font-size: 0.84rem;">
                                        {{ $role->description ?: 'System role with default preset permissions.' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 px-2.5 py-1.5 fw-bold">
                                            <i class="bi bi-shield-check me-1"></i>{{ $role->permissions_count }} Permissions
                                        </span>
                                    </td>
                                    <td>
                                        @if($role->is_system)
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning border-opacity-25 px-2.5 py-1.5 fw-bold">
                                                <i class="bi bi-lock-fill me-1"></i>Core System
                                            </span>
                                        @else
                                            <span class="badge bg-info-subtle text-info border border-info border-opacity-25 px-2.5 py-1.5 fw-bold">
                                                <i class="bi bi-unlock-fill me-1"></i>Custom Role
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1.5">
                                            <a href="{{ route('super-admin.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary rounded-2 px-3 py-1" title="Edit Role & Permissions">
                                                <i class="bi bi-pencil-square me-1"></i>Edit
                                            </a>
                                            @if(!$role->is_system)
                                                <form action="{{ route('super-admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this custom role?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2.5 py-1" title="Delete Role">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-sm btn-outline-secondary rounded-2 px-2.5 py-1" disabled title="System roles cannot be deleted.">
                                                    <i class="bi bi-lock"></i>
                                                </button>
                                            @endif
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
