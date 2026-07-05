@extends('layouts.app')

@section('title', 'Platform Roles & Permissions | EduLink')
@section('header_title', 'Roles & Permissions Management')

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
            <h5 class="fw-bold mb-0 text-dark">Global System Roles</h5>
            <p class="text-muted small mb-0">Manage global platform roles and adjust their administrative permission mappings.</p>
        </div>
        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
            <a href="{{ route('super-admin.roles.create') }}" class="btn btn-primary rounded-3 px-3 py-2 fw-semibold shadow-xs">
                <i class="bi bi-plus-lg me-1"></i>Create Custom Role
            </a>
        </div>
    </div>

    <!-- Roles Grid -->
    <div class="row g-4">
        <div class="col-12">
            <div class="glass-card p-4 shadow-sm">
                <div class="table-responsive">
                    <table class="table align-middle table-hover small">
                        <thead>
                            <tr>
                                <th>Role ID</th>
                                <th>Role Name</th>
                                <th>Role Slug</th>
                                <th>Description</th>
                                <th>Active Permissions</th>
                                <th>Role Type</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td><strong>#{{ $role->id }}</strong></td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $role->name }}</span>
                                    </td>
                                    <td><code>{{ $role->slug }}</code></td>
                                    <td class="text-muted text-wrap" style="max-width: 250px;">
                                        {{ $role->description ?: 'No description provided.' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border px-2 py-1">
                                            <i class="bi bi-shield-check me-1"></i>{{ $role->permissions_count }} Permissions
                                        </span>
                                    </td>
                                    <td>
                                        @if($role->is_system)
                                            <span class="badge bg-secondary text-dark px-2 py-1"><i class="bi bi-lock-fill me-1"></i>System Role</span>
                                        @else
                                            <span class="badge bg-light text-secondary border px-2 py-1"><i class="bi bi-unlock me-1"></i>Custom Role</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('super-admin.roles.edit', $role->id) }}" class="btn btn-xs btn-outline-dark rounded-2 px-2 py-1" title="Edit Role & Permissions">
                                                <i class="bi bi-pencil-square me-1"></i>Edit
                                            </a>
                                            @if(!$role->is_system)
                                                <form action="{{ route('super-admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this custom role?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-outline-danger rounded-2 px-2 py-1" title="Delete Role">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-xs btn-outline-secondary rounded-2 px-2 py-1" disabled title="System roles cannot be deleted.">
                                                    <i class="bi bi-trash"></i>
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
