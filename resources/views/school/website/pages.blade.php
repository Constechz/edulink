@extends('layouts.app')

@section('title', 'Website Pages Management')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Website Pages</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">School Website Pages</h1>
                    <p class="text-muted mb-0 small">Create, edit, and publish web pages to your public portal website.</p>
                </div>
                <div>
                    <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#createPageModal">
                        <i class="bi bi-plus-circle me-1"></i> New Web Page
                    </button>
                </div>
            </div>

            <!-- Validation/Success Feedback Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                        <div>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Pages Catalog Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width: 25%;">Page Title</th>
                                    <th style="width: 20%;">Web Slug</th>
                                    <th style="width: 15%;">Page Type</th>
                                    <th style="width: 15%;">Publish Status</th>
                                    <th style="width: 10%;">Homepage</th>
                                    <th class="text-end pe-4" style="width: 15%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pages as $page)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">{{ $page->title }}</div>
                                            <div class="text-muted small">{{ Str::limit($page->meta_description ?? 'No meta description set.', 50) }}</div>
                                        </td>
                                        <td>
                                            <code class="text-primary">/{{ $page->slug }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border text-capitalize">{{ $page->page_type }}</span>
                                        </td>
                                        <td>
                                            @if($page->is_published)
                                                <span class="badge bg-success-soft text-success px-3 py-2 rounded-pill">
                                                    <i class="bi bi-check-circle me-1"></i> Live
                                                </span>
                                            @else
                                                <span class="badge bg-warning-soft text-warning px-3 py-2 rounded-pill">
                                                    <i class="bi bi-pencil me-1"></i> Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($page->is_homepage)
                                                <span class="badge bg-primary text-white py-1 px-2 rounded"><i class="bi bi-house-door-fill"></i> Home</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex justify-content-end align-items-center gap-2">
                                                <a href="{{ route('school.website.pages.builder', $page->id) }}" class="btn btn-sm btn-primary" title="Open GrapesJS Web Builder">
                                                    <i class="bi bi-layout-text-window-reverse me-1"></i> Builder
                                                </a>
                                                
                                                @php
                                                    $school = auth()->user()->school;
                                                    $viewUrl = $school && $school->subdomain
                                                        ? url('/' . $school->subdomain . '/' . $page->slug)
                                                        : route('public.site.page', $page->slug) . '?school_id=' . $school->id;
                                                @endphp
                                                <a href="{{ $viewUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View Page">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                @if(!$page->is_homepage)
                                                    <form action="{{ route('school.website.pages.destroy', $page->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this page?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Page">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-window fs-1 d-block mb-3 text-secondary"></i>
                                            No pages configured yet. Click "New Web Page" to establish your first homepage block structure.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Page Modal -->
<div class="modal fade" id="createPageModal" tabindex="-1" aria-labelledby="createPageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="createPageModalLabel">Create Website Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('school.website.pages.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">Page Title</label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="e.g. About Our School, Admissions Info" required>
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label fw-bold">Web Slug (URL Part)</label>
                        <input type="text" name="slug" id="slug" class="form-control" placeholder="e.g. about, admissions" required>
                        <div class="form-text small text-muted">Use lowercase alphanumeric and dashes. Example: `/about-us`</div>
                    </div>

                    <div class="mb-3">
                        <label for="page_type" class="form-label fw-bold">Layout Template Type</label>
                        <select name="page_type" id="page_type" class="form-select" required>
                            <option value="custom">Blank/Custom</option>
                            <option value="home">Homepage Structure</option>
                            <option value="about">About Us Information</option>
                            <option value="admissions">Admissions Portal</option>
                            <option value="contact">Contact Form & Maps</option>
                            <option value="news">School News Board</option>
                            <option value="events">Calendar Schedules</option>
                            <option value="gallery">Photo Albums Gallery</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="meta_description" class="form-label fw-bold">SEO Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="2" class="form-control" placeholder="Short summary of this page for search engines..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Create Page</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
