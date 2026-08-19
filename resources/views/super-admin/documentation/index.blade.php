@extends('layouts.app')

@section('title', 'Platform Guides & Docs | ' . \App\Models\SystemSetting::getVal('platform_name', 'EduLink') . ' Admin')
@section('header_title', 'Documentation & Knowledge Base Governance')

@section('content')
<div class="container-fluid p-0">
    <!-- Status Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm border-0 d-flex align-items-center" role="alert" style="background: rgba(16, 185, 129, 0.12); color: #059669; border-left: 4px solid #10b981 !important;">
            <i class="bi bi-check-circle-fill fs-5 me-2.5"></i>
            <div>{{ session('success') }}</div>
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
                            Platform Documentation &amp; <span style="color: var(--accent-color, #FFD700);">Help Guides</span>
                        </h2>
                        <p class="text-white-50 mb-0" style="font-size: 0.9rem; max-width: 650px;">
                            Create and manage contextual help guides, standard operating procedures (SOPs), and tutorial articles across Super Admin, School Admin, Teacher, Student, and Parent portals.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('super-admin.documentation.create') }}" class="btn btn-warning rounded-3 px-3 py-2 fw-bold text-decoration-none shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i>Create New Guide
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="glass-card p-4 mb-4 shadow-sm">
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
                <button type="submit" class="btn btn-primary rounded-3 w-100 py-2 fw-semibold">Filter</button>
                <a href="{{ route('super-admin.documentation.index') }}" class="btn btn-outline-secondary rounded-3 w-100 py-2 fw-semibold">Clear</a>
            </div>
        </form>
    </div>

    <!-- Documentation Articles Table -->
    <div class="glass-card p-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-journal-text text-primary me-2"></i>Published &amp; Draft Help Articles</h5>
            <span class="badge bg-primary-subtle text-primary fw-semibold">{{ $articles->total() }} Articles</span>
        </div>
        <div class="table-responsive border rounded-3 mb-3">
            <table class="table align-middle table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 32%;">Article Title</th>
                        <th style="width: 16%;">Target Portal</th>
                        <th style="width: 16%;">Category</th>
                        <th style="width: 8%;" class="text-center">Order</th>
                        <th style="width: 10%;" class="text-center">Status</th>
                        <th style="width: 18%;" class="text-center">Action Controls</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark d-block" style="font-size: 0.88rem;">{{ $article->title }}</span>
                                <span class="text-muted d-block small" style="font-size: 0.72rem;">Slug: <code class="text-primary">{{ $article->slug }}</code></span>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 px-2 py-1 fw-bold">
                                    {{ Str::title(str_replace('-', ' ', $article->portal)) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-secondary fw-semibold small">{{ $article->category }}</span>
                            </td>
                            <td class="text-center fw-bold text-primary">{{ $article->display_order }}</td>
                            <td class="text-center">
                                <span class="badge {{ $article->is_published ? 'bg-success-subtle text-success border border-success border-opacity-25' : 'bg-secondary bg-opacity-10 text-secondary' }} px-2 py-1 fw-bold">
                                    <i class="bi {{ $article->is_published ? 'bi-check-circle-fill' : 'bi-pause-circle-fill' }} me-1"></i>{{ $article->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1.5">
                                    <a href="{{ route('super-admin.documentation.edit', $article->id) }}" class="btn btn-sm btn-outline-primary rounded-2 px-2.5 py-1" title="Edit Article">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </a>
                                    <form action="{{ route('super-admin.documentation.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this guide?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2.5 py-1" title="Delete Article">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-2 d-block mb-2 text-secondary"></i>
                                <span>No documentation guides found matching current filter.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">Showing {{ $articles->firstItem() }} to {{ $articles->lastItem() }} of {{ $articles->total() }} guides</span>
            <div>{{ $articles->links() }}</div>
        </div>
    </div>
</div>
@endsection
