@extends('layouts.app')

@section('title', 'School Notices | Parent Portal')
@section('header_title', 'School Announcements & Notices')

@section('content')
<div class="container-fluid p-0">
    <div class="glass-card p-4">
        <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-chat-dots me-2 text-primary"></i>Official Parent Announcements</h5>

        @if($announcements->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-chat-left-dots fs-1 mb-2 d-block"></i>
                <span>No announcements have been posted for parents.</span>
            </div>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach($announcements as $ann)
                    <div class="p-4 rounded bg-light border-start border-5 border-primary shadow-xs">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="fw-bold text-dark mb-0">{{ $ann->title }}</h5>
                            @if($ann->is_pinned)
                                <span class="badge bg-danger"><i class="bi bi-pin-angle-fill me-1"></i>Pinned</span>
                            @endif
                        </div>
                        <p class="text-muted mb-3">{{ $ann->content }}</p>
                        <div class="d-flex justify-content-between align-items-center text-muted small" style="font-size: 0.75rem;">
                            <span><i class="bi bi-clock me-1"></i>Posted on {{ date('M d, Y h:i A', strtotime($ann->created_at)) }}</span>
                            <span>Target: <span class="text-capitalize text-primary fw-semibold">{{ $ann->target_audience }}</span></span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
