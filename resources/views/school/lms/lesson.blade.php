@extends('layouts.app')

@section('title', $lesson->title . ' | Lesson player')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-4">
        <!-- Lessons Navigator Sidebar -->
        <div class="col-md-3">
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-list-task me-2 text-primary"></i>Curriculum Navigation</h5>
                <div class="list-group rounded-3">
                    @foreach($siblings as $sib)
                        <a href="{{ route('school.lms.lessons.show', $sib->id) }}" class="list-group-item list-group-item-action py-3 {{ $sib->id === $lesson->id ? 'active' : '' }}">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary bg-opacity-20 text-dark rounded-circle p-1" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">{{ $loop->iteration }}</span>
                                <span class="small fw-semibold">{{ $sib->title }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('school.lms.courses.show', $course->id) }}" class="btn btn-sm btn-outline-secondary w-100 rounded-3"><i class="bi bi-arrow-left"></i> Course Dashboard</a>
                </div>
            </div>
        </div>

        <!-- Lesson Reader Content -->
        <div class="col-md-9">
            <div class="glass-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <span class="text-primary small fw-semibold text-uppercase" style="font-size: 0.75rem;">Lesson player</span>
                        <h3 class="font-weight-bold mb-0 text-dark">{{ $lesson->title }}</h3>
                    </div>
                    <div>
                        @if($isCompleted)
                            <span class="badge bg-success py-2 px-3"><i class="bi bi-check-circle-fill me-1"></i> Completed</span>
                        @else
                            <form action="{{ route('school.lms.lessons.complete', $lesson->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success rounded-3"><i class="bi bi-check2-square"></i> Mark Completed</button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Lesson Text/Description -->
                <div class="text-dark mb-4 lh-lg" style="font-size: 1.05rem;">
                    {!! $lesson->content ?? '<p class="text-muted">No text contents compiled for this lesson.</p>' !!}
                </div>

                <!-- Resources attachments -->
                @if($lesson->resources->isNotEmpty())
                    <div class="p-3 bg-light rounded-4 border mt-5">
                        <h6 class="fw-bold mb-3 text-dark-theme"><i class="bi bi-paperclip me-1 text-primary"></i>Downloadable Lesson Resources</h6>
                        <div class="list-group rounded-3 shadow-xs">
                            @foreach($lesson->resources as $res)
                                @php
                                    $type = strtolower($res->resource_type);
                                    if ($type === 'pdf') {
                                        $icon = 'bi-file-earmark-pdf text-danger';
                                    } elseif (in_array($type, ['doc', 'docx'])) {
                                        $icon = 'bi-file-earmark-word text-primary';
                                    } elseif (in_array($type, ['png', 'jpg', 'jpeg', 'image'])) {
                                        $icon = 'bi-file-earmark-image text-success';
                                    } else {
                                        $icon = 'bi-file-earmark-arrow-down text-info';
                                    }
                                @endphp
                                <a href="{{ asset('storage/' . $res->file_path) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3 bg-transparent border-light-subtle">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi {{ $icon }} fs-5"></i>
                                        <span class="small fw-semibold text-dark-theme">{{ $res->title }}</span>
                                    </div>
                                    <span class="badge bg-secondary bg-opacity-10 text-dark-theme text-uppercase small" style="font-size: 0.65rem;">{{ $res->resource_type }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
