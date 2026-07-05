@extends('layouts.app')

@section('title', 'Page Revision History')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('school.website.pages.index') }}" class="text-decoration-none">Website Pages</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Revision History</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Revision History: {{ $page->title }}</h1>
                    <p class="text-muted mb-0 small">Audit historical GrapesJS saves, drafts, and published versions. Rollback to any revision instantly.</p>
                </div>
                <div>
                    <a href="{{ route('school.website.pages.builder', $page->id) }}" class="btn btn-outline-primary px-4">
                        <i class="bi bi-chevron-left me-1"></i> Back to Builder
                    </a>
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

            <!-- Revisions List Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width: 15%;">Revision No.</th>
                                    <th style="width: 25%;">Save Date</th>
                                    <th style="width: 20%;">Saved By</th>
                                    <th style="width: 25%;">Status / Notes</th>
                                    <th class="text-end pe-4" style="width: 15%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revisions as $rev)
                                    <tr class="{{ $rev->is_current_draft ? 'table-primary-soft' : '' }}">
                                        <td class="ps-4 fw-bold text-dark">#{{ $rev->revision_number }}</td>
                                        <td>{{ $rev->created_at->format('M d, Y \a\t h:i A') }}</td>
                                        <td>{{ $rev->creator->name ?? 'System' }}</td>
                                        <td>
                                            @if($rev->is_published)
                                                <span class="badge bg-success-soft text-success px-3 py-2 rounded-pill fw-bold">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Published Live
                                                </span>
                                            @elseif($rev->is_current_draft)
                                                <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill fw-bold">
                                                    <i class="bi bi-pencil-fill me-1"></i> Current Draft
                                                </span>
                                            @else
                                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">History</span>
                                            @endif
                                            
                                            @if($rev->notes)
                                                <div class="text-muted small mt-1 italic">{{ $rev->notes }}</div>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            @if(!$rev->is_current_draft)
                                                <form action="{{ route('school.website.revisions.rollback', $rev->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to rollback to revision #{{ $rev->revision_number }}? This will replace your current draft.');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning px-3">
                                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Rollback
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted small">Active Draft</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            No page revisions recorded yet.
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
@endsection
