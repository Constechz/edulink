@extends('layouts.app')

@section('title', $article->title . ' | Help Center')

@section('content')
<div class="container-fluid p-0">
    
    <!-- Header banner -->
    <div class="glass-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small text-muted">
                        <li class="breadcrumb-item"><a href="{{ route('school.help-center.index') }}" class="text-decoration-none text-secondary fw-semibold">Help Center</a></li>
                        <li class="breadcrumb-item text-secondary">{{ $article->category }}</li>
                        <li class="breadcrumb-item active text-dark" aria-current="page">{{ $article->title }}</li>
                    </ol>
                </nav>
                <h4 class="fw-bold text-dark mb-0">{{ $article->title }}</h4>
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print();" class="btn btn-outline-secondary rounded-3 py-2 px-3 fw-semibold shadow-xs">
                    <i class="bi bi-printer me-1"></i>Print Guide
                </button>
                <a href="{{ route('school.help-center.index') }}" class="btn btn-primary rounded-3 py-2 px-3 fw-semibold text-dark shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back to Hub
                </a>
            </div>
        </div>
    </div>

    <!-- Article layout split -->
    <div class="row g-4">
        <!-- Main Article Content -->
        <div class="col-lg-8">
            <div class="glass-card p-5 shadow-sm border border-light">
                <!-- Article Meta info -->
                <div class="d-flex align-items-center gap-3 text-muted small border-bottom pb-3 mb-4">
                    <span class="d-flex align-items-center gap-1"><i class="bi bi-folder-fill text-warning"></i>{{ $article->category }}</span>
                    <span class="text-secondary opacity-50">|</span>
                    <span class="d-flex align-items-center gap-1"><i class="bi bi-clock"></i>Last Updated: {{ $article->updated_at->format('M d, Y') }}</span>
                </div>

                <!-- Rich text container -->
                <div class="article-body text-dark" style="line-height: 1.7; font-size: 1.02rem;">
                    {!! nl2br($article->content) !!}
                </div>
            </div>
        </div>

        <!-- Sidebar - Related Articles -->
        <div class="col-lg-4">
            <div class="glass-card p-4 shadow-sm border border-light">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-journal-richtext text-primary me-2"></i>More in this Category</h5>
                
                @if($relatedArticles->count() > 0)
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                        @foreach($relatedArticles as $rel)
                            <li class="border-bottom pb-2 last-border-none">
                                <a href="{{ route('school.help-center.show', $rel->slug) }}" class="text-decoration-none d-flex align-items-start gap-2 hover-title">
                                    <i class="bi bi-file-text text-muted mt-1"></i>
                                    <div>
                                        <span class="fw-semibold text-secondary text-dark-hover" style="font-size: 0.95rem;">{{ $rel->title }}</span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted small mb-0">No other articles in this category.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* CSS styles to format lists and visual headers inside the custom content editor */
.article-body h1, .article-body h2, .article-body h3, .article-body h4 {
    color: #0f172a;
    font-weight: 700;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}
.article-body h1 { font-size: 1.5rem; }
.article-body h2 { font-size: 1.35rem; }
.article-body h3 { font-size: 1.2rem; }
.article-body p {
    margin-bottom: 1.25rem;
}
.article-body ul, .article-body ol {
    margin-bottom: 1.25rem;
    padding-left: 1.5rem;
}
.article-body li {
    margin-bottom: 0.5rem;
}
.article-body code {
    background: #f1f5f9;
    color: #0f172a;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-size: 0.9rem;
}
.hover-title:hover .text-secondary {
    color: var(--primary-color) !important;
    text-decoration: underline;
}
.last-border-none:last-child {
    border-bottom: 0 !important;
    padding-bottom: 0 !important;
}

@media print {
    /* Hide layout sidebars, top bars, footers on printing */
    .sidebar, .topbar, .btn, .breadcrumb, col-lg-4, header, nav {
        display: none !important;
    }
    .main-content, .content-container, .col-lg-8, .glass-card {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        box-shadow: none !important;
        border: none !important;
        background: transparent !important;
    }
}
</style>
@endsection
