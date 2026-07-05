@extends('layouts.app')

@section('title', 'Quiz Results | LMS')
@section('header_title', 'Assessment Grading')

@section('content')
<div class="container d-flex justify-content-center py-5">
    <div class="glass-card p-5 text-center" style="max-width: 500px; width: 100%;">
        @if($isPassed)
            <div class="fs-1 text-success mb-3"><i class="bi bi-patch-check-fill" style="font-size: 4.5rem;"></i></div>
            <h3 class="fw-bold text-success mb-2">Congratulations!</h3>
            <h5 class="fw-bold text-dark mb-4">You passed the quiz!</h5>
        @else
            <div class="fs-1 text-danger mb-3"><i class="bi bi-x-circle-fill" style="font-size: 4.5rem;"></i></div>
            <h3 class="fw-bold text-danger mb-2">Quiz Failed</h3>
            <h5 class="fw-bold text-dark mb-4">You did not clear the passing score.</h5>
        @endif

        <div class="p-4 bg-light rounded-4 mb-4 border">
            <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Your Grade Details</span>
            <h2 class="fw-bold text-primary mb-1">{{ number_format($percentage, 1) }}%</h2>
            <span class="text-muted small">Passing Threshold: {{ $quiz->passing_percentage }}%</span>
            <div class="mt-3 pt-3 border-top small text-muted">
                Points: <strong>{{ $earnedPoints }}</strong> out of {{ $totalPoints }}
            </div>
        </div>

        <div class="d-grid gap-2">
            <a href="{{ route('school.lms.courses.show', $quiz->course_id) }}" class="btn btn-primary rounded-3 py-2 fw-semibold">Back to Course Curriculum</a>
            <a href="{{ route('school.lms.courses.index') }}" class="btn btn-outline-secondary rounded-3 py-2">View Course Catalog</a>
        </div>
    </div>
</div>
@endsection
