@extends('layouts.app')

@section('title', 'Platform Guides & Docs | ' . \App\Models\SystemSetting::getVal('platform_name', 'EduLink') . ' Admin')
@section('header_title', 'Documentation & Knowledge Base Manager')

@section('content')
<div class="container-fluid p-0">
    
    <!-- Status Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show glass-card p-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="glass-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">Manage Platform Documentation</h5>
                <p class="text-muted small mb-0">Create and modify dynamic help guides mapped to specific user portals.</p>
            </div>
            <a href="{{ route('super-admin.documentation.create') }}" class="btn btn-primary rounded-3 py-2 px-3 fw-semibold text-dark shadow-sm">
                <i class="bi bi-plus-lg me-1"></i>Create New Guide
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="glass-card p-4 mb-4">
        <form action="{{ route('super-admin.documentation.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="portal" class="form-label text-secondary small fw-semibold">Filter by Portal</label>
                <select name="portal" id="portal" class="form-select rounded-3 py-2 border shadow-xs">
                    <option value="">All Portals</option>
                    <option value="super-admin" {{ request('portal') === 'super-admin' ? 'selected' : '' }}>Super Admin Portal</option>
                    <option value="school-admin" {{ request('portal') === 'school-admin' ? 'selected' : '' }}>School Admin Portal</option>
                    <option value="teacher" {{ request('portal') === 'teacher' ? 'selected' : '' }}>Teacher Portal</option>
                    <option value="student" {{ request('portal') === 'student' ? 'selected' : '' }}>Student Portal</option>
                    <option value="parent" {{ request('portal') === 'parent' ? 'selected' : '' }}>Parent Portal</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="category" class="form-label text-secondary small fw-semibold">Filter by Category</label>
                <select name="category" id="category" class="form-select rounded-3 py-2 border shadow-xs">
                    <option value="">All Categories</option>
                    <option value="General" {{ request('category') === 'General' ? 'selected' : '' }}>General</option>
                    <option value="Billing" {{ request('category') === 'Billing' ? 'selected' : '' }}>Billing</option>
                    <option value="Academics" {{ request('category') === 'Academics' ? 'selected' : '' }}>Academics</option>
                    <option value="SMS & Notifications" {{ request('category') === 'SMS & Notifications' ? 'selected' : '' }}>SMS & Notifications</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="search" class="form-label text-secondary small fw-semibold">Search Keywords</label>
                <input type="text" name="search" id="search" class="form-control rounded-3 py-2 border shadow-xs" placeholder="e.g. scores, payments, rollover" value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary rounded-3 w-100 py-2 fw-semibold">Filter</button>
                <a href="{{ route('super-admin.documentation.index') }}" class="btn btn-outline-secondary rounded-3 w-100 py-2 fw-semibold">Clear</a>
            </div>
        </form>
    </div>

    <!-- Documentation Articles Table -->
    <div class="glass-card p-4">
        <div class="table-responsive">
            <table class="table align-middle table-sm small table-hover">
                <thead>
                    <tr>
                        <th style="width: 30%;">Article Title</th>
                        <th style="width: 15%;">Target Portal</th>
                        <th style="width: 15%;">Category</th>
                        <th style="width: 10%;" class="text-center">Order</th>
                        <th style="width: 10%;" class="text-center">Status</th>
                        <th style="width: 20%;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark d-block">{{ $article->title }}</span>
                                <span class="text-muted d-block small" style="font-size: 0.7rem;">Slug: <code>{{ $article->slug }}</code></span>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border px-2 py-1">
                                    {{ Str::title(str_replace('-', ' ', $article->portal)) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-dark">{{ $article->category }}</span>
                            </td>
                            <td class="text-center fw-bold">{{ $article->display_order }}</td>
                            <td class="text-center">
                                <span class="badge {{ $article->is_published ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }} px-2 py-1">
                                    {{ $article->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('super-admin.documentation.edit', $article->id) }}" class="btn btn-outline-primary btn-xs rounded-2 px-2 py-1">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </a>
                                    <form action="{{ route('super-admin.documentation.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this guide?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-xs rounded-2 px-2 py-1">
                                            <i class="bi bi-trash-fill me-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No documentation articles found. Click "Create New Guide" to write your first article.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $articles->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection
