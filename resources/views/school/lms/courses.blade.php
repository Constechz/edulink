@extends('layouts.app')

@section('title', 'LMS Course Catalog | EduLink')
@section('header_title', 'LMS Course Catalog')

@section('content')
<div class="container-fluid p-0">
    <div class="glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-laptop me-2 text-primary"></i>My Enrolled Digital Courses
            </h5>
            @if($isStaff)
                <button type="button" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                    <i class="bi bi-plus-lg me-1"></i> Create New Course
                </button>
            @endif
        </div>

        @if($courses->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-journal-x fs-1 mb-2 d-block"></i>
                <span>No digital courses are currently registered in the catalog.</span>
            </div>
        @else
            <div class="row g-4">
                @foreach($courses as $course)
                    <div class="col-md-4">
                        <div class="card h-100 border-0 rounded-4 shadow-sm bg-light">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" class="card-img-top rounded-top-4" style="height: 160px; object-fit: cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center bg-primary bg-gradient rounded-top-4 text-white" style="height: 160px;">
                                    <i class="bi bi-mortarboard fs-1"></i>
                                </div>
                            @endif
                            <div class="card-body p-4">
                                <span class="badge bg-secondary bg-opacity-10 text-dark mb-2">{{ $course->subject->name ?? 'N/A' }}</span>
                                <h5 class="fw-bold mb-2 text-dark">{{ $course->title }}</h5>
                                <p class="text-muted small mb-4">{{ Str::limit($course->description, 100) }}</p>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-1 small text-muted">
                                        <span>Course Progress</span>
                                        <span class="fw-bold text-primary">{{ $course->progress_percent }}%</span>
                                    </div>
                                    <div class="progress rounded-pill" style="height: 6px;">
                                        <div class="progress-bar rounded-pill" role="progressbar" style="width: {{ $course->progress_percent }}%" aria-valuenow="{{ $course->progress_percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top small text-muted">
                                    <span>Instructor: <strong>{{ $course->teacher->name ?? 'N/A' }}</strong></span>
                                    <a href="{{ route('school.lms.courses.show', $course->id) }}" class="btn btn-sm btn-primary rounded-3 px-3">Enter Course</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@if($isStaff)
    <!-- Modal for Creating a Course -->
    <div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg bg-glass">
                <form action="{{ route('school.lms.courses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom-0 p-4 pb-0">
                        <h5 class="modal-title fw-bold text-dark" id="createCourseModalLabel">Create New Course</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="title" class="form-label text-dark fw-semibold">Course Title</label>
                            <input type="text" class="form-control rounded-3" id="title" name="title" required placeholder="e.g. Grade 10 Mathematics">
                        </div>
                        <div class="mb-3">
                            <label for="subject_id" class="form-label text-dark fw-semibold">Subject</label>
                            <select class="form-select rounded-3" id="subject_id" name="subject_id" required>
                                <option value="" disabled selected>Select Subject</option>
                                @foreach($subjects as $subj)
                                    <option value="{{ $subj->id }}">{{ $subj->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="teacher_id" class="form-label text-dark fw-semibold">Assigned Teacher</label>
                            <select class="form-select rounded-3" id="teacher_id" name="teacher_id" required>
                                <option value="" disabled selected>Select Instructor</option>
                                @foreach($teachers as $teach)
                                    <option value="{{ $teach->id }}">{{ $teach->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label text-dark fw-semibold">Description</label>
                            <textarea class="form-control rounded-3" id="description" name="description" rows="3" placeholder="Describe the course curriculum..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="thumbnail" class="form-label text-dark fw-semibold">Cover Thumbnail (Optional)</label>
                            <input type="file" class="form-control rounded-3" id="thumbnail" name="thumbnail" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4">Create Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
