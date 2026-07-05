@extends('layouts.app')

@section('title', 'Edit Guide | ' . \App\Models\SystemSetting::getVal('platform_name', 'EduLink') . ' Admin')
@section('header_title', 'Update Help Center Article')

@section('content')
<div class="container-fluid p-0">
    <div class="glass-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-1">Edit Help Guide</h5>
                <p class="text-muted small mb-0">Modify instructions for: <strong>{{ $article->title }}</strong></p>
            </div>
            <a href="{{ route('super-admin.documentation.index') }}" class="btn btn-outline-secondary rounded-3 py-2 px-3 fw-semibold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i>Back to Directory
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 bg-danger text-white mb-4" style="border-radius: 12px; --bs-bg-opacity: 0.2;">
            <ul class="mb-0 list-unstyled">
                @foreach($errors->all() as $error)
                    <li><i class="bi bi-exclamation-circle-fill me-2"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass-card p-4">
        <form action="{{ route('super-admin.documentation.update', $article->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="portal" class="form-label text-secondary small fw-semibold">Target User Portal</label>
                    <select name="portal" id="portal" class="form-select rounded-3 py-2 border shadow-xs" required>
                        <option value="">-- Select Portal Audience --</option>
                        <option value="super-admin" {{ old('portal', $article->portal) === 'super-admin' ? 'selected' : '' }}>Super Admin Portal</option>
                        <option value="school-admin" {{ old('portal', $article->portal) === 'school-admin' ? 'selected' : '' }}>School Admin Portal</option>
                        <option value="teacher" {{ old('portal', $article->portal) === 'teacher' ? 'selected' : '' }}>Teacher Portal</option>
                        <option value="student" {{ old('portal', $article->portal) === 'student' ? 'selected' : '' }}>Student Portal</option>
                        <option value="parent" {{ old('portal', $article->portal) === 'parent' ? 'selected' : '' }}>Parent Portal</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="category" class="form-label text-secondary small fw-semibold">Article Category</label>
                    <select name="category" id="category" class="form-select rounded-3 py-2 border shadow-xs" required>
                        <option value="">-- Choose Category --</option>
                        <option value="General" {{ old('category', $article->category) === 'General' ? 'selected' : '' }}>General</option>
                        <option value="Billing" {{ old('category', $article->category) === 'Billing' ? 'selected' : '' }}>Billing & Credits</option>
                        <option value="Academics" {{ old('category', $article->category) === 'Academics' ? 'selected' : '' }}>Academics & Timetables</option>
                        <option value="SMS & Notifications" {{ old('category', $article->category) === 'SMS & Notifications' ? 'selected' : '' }}>SMS & Notifications</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <label for="title" class="form-label text-secondary small fw-semibold">Article Title</label>
                    <input type="text" name="title" id="title" class="form-control rounded-3 py-2 border shadow-xs" placeholder="e.g. Setting up Paystack Subscription checkout" value="{{ old('title', $article->title) }}" required>
                </div>
                <div class="col-md-4">
                    <label for="display_order" class="form-label text-secondary small fw-semibold">Sort Order</label>
                    <input type="number" name="display_order" id="display_order" class="form-control rounded-3 py-2 border shadow-xs" value="{{ old('display_order', $article->display_order) }}" min="0" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="content" class="form-label text-secondary small fw-semibold">Article Content (HTML/Markdown Supported)</label>
                <textarea name="content" id="content" class="form-control rounded-3 border shadow-xs" rows="15" placeholder="Write step-by-step guides using headers, paragraphs, lists..." required>{{ old('content', $article->content) }}</textarea>
            </div>

            <div class="d-flex align-items-center justify-content-between border-top pt-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', $article->is_published) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold text-secondary small" for="is_published">Published</label>
                </div>
                <button type="submit" class="btn btn-primary rounded-3 py-2.5 px-4 text-dark fw-semibold shadow-sm">
                    <i class="bi bi-cloud-check-fill me-1"></i>Save Updates
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
