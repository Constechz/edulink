@extends('layouts.app')

@section('title', 'Help & Guides Center | ' . \App\Models\SystemSetting::getVal('platform_name', 'EduLink'))
@section('header_title', 'Help & Support Hub')

@section('content')
<div class="container-fluid p-0">
    <!-- Header banner -->
    <div class="glass-card p-5 mb-4 text-center" style="background: linear-gradient(135deg, rgba(0, 51, 102, 0.08) 0%, rgba(255, 215, 0, 0.08) 100%); border: 1px solid rgba(255, 255, 255, 0.3);">
        <i class="bi bi-question-circle text-primary display-4 mb-3 d-block animate-bounce"></i>
        <h3 class="fw-bold text-dark">Welcome to the Help Hub</h3>
        <p class="text-secondary mb-4 mx-auto" style="max-width: 600px;">Search our dynamic knowledge base guides, step-by-step instructions, and FAQs designed specifically for your user portal.</p>
        
        <!-- Search bar -->
        <form action="{{ route('school.help-center.index') }}" method="GET" class="mx-auto" style="max-width: 500px;">
            <div class="input-group">
                <input type="text" name="search" class="form-control rounded-start-3 border shadow-xs py-2.5 px-3" placeholder="Search guides, keywords, or topics..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary px-4 fw-semibold text-dark rounded-end-3 shadow-xs">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Articles directory -->
    <div class="row g-4">
        @forelse($articles as $category => $categoryArticles)
            <div class="col-md-6 col-lg-4">
                <div class="glass-card p-4 h-100 shadow-sm border border-light">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: var(--primary-color);">
                        @if($category === 'Billing')
                            <i class="bi bi-credit-card-fill text-success"></i>
                        @elseif($category === 'Academics')
                            <i class="bi bi-mortarboard-fill text-warning"></i>
                        @elseif($category === 'SMS & Notifications')
                            <i class="bi bi-chat-left-text-fill text-info"></i>
                        @else
                            <i class="bi bi-info-circle-fill text-primary"></i>
                        @endif
                        {{ $category }}
                    </h5>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2.5">
                        @foreach($categoryArticles as $art)
                            <li>
                                <a href="{{ route('school.help-center.show', $art->slug) }}" class="text-decoration-none d-flex align-items-start gap-2 py-1 text-secondary hover-text-primary">
                                    <i class="bi bi-file-text-fill text-muted mt-1 small"></i>
                                    <div>
                                        <span class="fw-semibold text-dark" style="font-size: 0.9rem;">{{ $art->title }}</span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="glass-card p-5 max-width-md mx-auto">
                    <i class="bi bi-search-heart text-muted display-4"></i>
                    <h5 class="fw-bold mt-3">No matching articles found</h5>
                    <p class="text-muted small">Try searching for other keywords, or clear filters to view all documentation guides.</p>
                    <a href="{{ route('school.help-center.index') }}" class="btn btn-outline-primary rounded-3 px-3 py-2 fw-semibold mt-2">Clear Search</a>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
.hover-text-primary {
    transition: color 0.15s ease-in-out;
}
.hover-text-primary:hover .fw-semibold {
    color: var(--primary-color) !important;
    text-decoration: underline;
}
</style>
@endsection
