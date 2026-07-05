@extends('layouts.app')

@section('title', 'Weekly Timetable | EduLink')
@section('header_title', 'School Timetable Builder')

@section('content')
<div class="container-fluid p-0">

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <ul class="mb-0 list-unstyled">
                @foreach($errors->all() as $error)
                    <li><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Selection Bar & Actions -->
    <div class="glass-card p-4 mb-4">
        <form action="{{ route('school.timetable') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small font-weight-bold">Select Class</label>
                <select class="form-select" name="class_id" onchange="this.form.submit()" required>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}" {{ $selectedClassId == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small font-weight-bold">Select Stream (Optional)</label>
                <select class="form-select" name="stream_id" onchange="this.form.submit()">
                    <option value="">Entire Class</option>
                    @foreach($streams->where('class_id', $selectedClassId) as $strm)
                        <option value="{{ $strm->id }}" {{ $selectedStreamId == $strm->id ? 'selected' : '' }}>{{ $strm->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-primary px-4 py-2 w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#scheduleSlotModal" style="border-radius: 10px;">
                    <i class="bi bi-calendar-plus me-2"></i>Schedule Lesson Slot
                </button>
            </div>
        </form>
    </div>

    <!-- Timetable Weekly Grid -->
    <div class="row g-4 mb-4">
        @foreach($days as $day)
            <div class="col-md-2.4 col-lg" style="min-width: 200px;">
                <div class="glass-card p-3 h-100">
                    <h6 class="font-weight-bold text-center border-bottom pb-2 text-primary mb-3">
                        <i class="bi bi-calendar-day me-1"></i>{{ $day }}
                    </h6>
                    
                    <div class="d-flex flex-column gap-3">
                        @forelse($slots->where('day_of_week', $day)->sortBy('start_time') as $slot)
                            <div class="p-2 border rounded bg-white shadow-sm position-relative slot-card" style="border-left: 4px solid var(--primary-color) !important;">
                                <div class="font-weight-bold small">{{ $slot->subject->name ?? 'N/A' }}</div>
                                <span class="text-muted d-block" style="font-size: 0.75rem;">
                                    <i class="bi bi-clock me-1"></i>{{ date('h:i A', strtotime($slot->start_time)) }} - {{ date('h:i A', strtotime($slot->end_time)) }}
                                </span>
                                <span class="text-muted d-block" style="font-size: 0.75rem;">
                                    <i class="bi bi-person me-1"></i>{{ $slot->teacher->name ?? 'Teacher' }}
                                </span>
                                @if($slot->room)
                                    <span class="badge bg-light text-dark mt-1" style="font-size: 0.65rem;">Room: {{ $slot->room }}</span>
                                @endif
                                
                                <form action="{{ route('school.timetable.destroy', $slot->id) }}" method="POST" class="position-absolute top-0 end-0 p-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 border-0" onclick="return confirm('Remove this schedule slot?')" style="font-size: 0.85rem;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small italic">No classes scheduled</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- SCHEDULE SLOT MODAL -->
    <div class="modal fade" id="scheduleSlotModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Schedule Lesson Slot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('school.timetable.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                    <input type="hidden" name="stream_id" value="{{ $selectedStreamId }}">
                    
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div>
                                <label class="form-label small font-weight-bold">Day of Week</label>
                                <select class="form-select" name="day_of_week" required>
                                    @foreach($days as $day)
                                        <option value="{{ $day }}">{{ $day }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small font-weight-bold">Start Time</label>
                                <input type="time" class="form-control" name="start_time" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small font-weight-bold">End Time</label>
                                <input type="time" class="form-control" name="end_time" required>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Subject</label>
                                <select class="form-select" name="subject_id" required>
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subj)
                                        <option value="{{ $subj->id }}">{{ $subj->name }} [{{ $subj->code }}]</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Subject Tutor</label>
                                <select class="form-select" name="teacher_id" required>
                                    <option value="">Select Teacher</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Room Name / Number (Optional)</label>
                                <input type="text" class="form-control" name="room" placeholder="e.g. Science Lab 1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Schedule Lesson</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
