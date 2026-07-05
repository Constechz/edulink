@extends('layouts.app')

@section('title', 'Assignments Desk | EduLink')
@section('header_title', 'Student Assignments Desk')

@section('content')
<div class="container-fluid p-0">
    <!-- Success/Error Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show glass-card p-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="glass-card p-4">
        <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-journal-text me-2 text-primary"></i>My Course Assignments</h5>

        @if($assignments->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-folder-x fs-1 mb-2 d-block"></i>
                <span>No assignments registered for your class.</span>
            </div>
        @else
            <div class="row g-4">
                @foreach($assignments as $assignment)
                    @php
                        $sub = $submissions->get($assignment->id);
                        $isSubmitted = !empty($sub);
                        $isGraded = $isSubmitted && $sub->status === 'graded';
                    @endphp
                    <div class="col-md-6">
                        <div class="card border-0 rounded-4 shadow-sm bg-light">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <span class="badge bg-primary bg-opacity-10 text-primary mb-2">{{ $assignment->subject->name ?? 'N/A' }}</span>
                                        <h5 class="fw-bold mb-0 text-dark">{{ $assignment->title }}</h5>
                                    </div>
                                    @if($isGraded)
                                        <span class="badge bg-success">Graded: {{ $sub->marks_obtained }} / {{ $assignment->max_marks }}</span>
                                    @elseif($isSubmitted)
                                        <span class="badge bg-warning text-dark">Submitted</span>
                                    @else
                                        <span class="badge bg-danger">Pending</span>
                                    @endif
                                </div>

                                <p class="text-muted small mb-3">{{ $assignment->description ?? 'No description provided.' }}</p>

                                <div class="row g-2 mb-4 small text-muted">
                                    <div class="col-6"><i class="bi bi-person me-1"></i>Teacher: {{ $assignment->teacher->name ?? 'N/A' }}</div>
                                    <div class="col-6"><i class="bi bi-calendar-event me-1"></i>Due: {{ date('M d, Y h:i A', strtotime($assignment->due_date)) }}</div>
                                </div>

                                @if(!$isSubmitted)
                                    <!-- File Submission Form -->
                                    <form action="{{ route('school.student-portal.assignments.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="input-group">
                                            <input type="file" name="file" class="form-control rounded-start-3" required>
                                            <button type="submit" class="btn btn-primary rounded-end-3"><i class="bi bi-upload"></i> Submit</button>
                                        </div>
                                        <span class="text-muted small mt-1 d-block" style="font-size: 0.7rem;">Allowed formats: PDF, DOC, PNG, JPG (max 10MB)</span>
                                    </form>
                                @else
                                    <div class="p-3 bg-white rounded-3 border">
                                        <span class="text-muted small d-block mb-1"><i class="bi bi-check-lg text-success me-1"></i>Submitted on {{ date('M d, Y', strtotime($sub->submitted_at)) }}</span>
                                        <!-- Feedback -->
                                        @php
                                            $feedback = DB::table('assignment_feedback')->where('submission_id', $sub->id)->first();
                                        @endphp
                                        @if($feedback)
                                            <div class="mt-2 pt-2 border-top">
                                                <strong class="small d-block text-dark">Teacher Comments:</strong>
                                                <p class="mb-0 text-muted small italic">"{{ $feedback->comments }}"</p>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
