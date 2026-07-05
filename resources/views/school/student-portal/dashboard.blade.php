@extends('layouts.app')

@section('title', 'Student Dashboard | EduLink')
@section('header_title', 'Student Dashboard')

@section('content')
<div class="container-fluid p-0">
    @if(isset($error))
        <div class="alert alert-danger glass-card p-4">
            <h5 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Error</h5>
            <p class="mb-0">{{ $error }}</p>
        </div>
    @else
        <!-- Greeting Section -->
        <div class="glass-card p-4 mb-4" style="background: linear-gradient(135deg, rgba(0, 51, 102, 0.08) 0%, rgba(255, 215, 0, 0.08) 100%);">
            <h3 class="font-weight-bold mb-1" style="font-weight: 700; color: var(--primary-color);">Hello, {{ $student->first_name }}!</h3>
            <p class="text-muted mb-0">Welcome back to Green Valley. Here is your academic overview for today.</p>
        </div>

        <!-- Metric Summaries -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="glass-card p-4 text-center">
                    <div class="fs-1 text-primary mb-2"><i class="bi bi-journal-check"></i></div>
                    <div class="card-metric">{{ $assignmentsCount }}</div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Assignments</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-4 text-center">
                    <div class="fs-1 text-success mb-2"><i class="bi bi-file-earmark-arrow-up"></i></div>
                    <div class="card-metric">{{ $submittedCount }}</div>
                    <div class="text-muted small fw-semibold text-uppercase">Submitted Tasks</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-4 text-center">
                    <div class="fs-1 text-warning mb-2"><i class="bi bi-star"></i></div>
                    <div class="card-metric">{{ number_format($gpa, 1) }}%</div>
                    <div class="text-muted small fw-semibold text-uppercase">Published Average</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-4 text-center">
                    <div class="fs-1 text-info mb-2"><i class="bi bi-award"></i></div>
                    <div class="card-metric">{{ $student->currentClass->name ?? 'N/A' }}</div>
                    <div class="text-muted small fw-semibold text-uppercase">Current Class</div>
                </div>
            </div>
        </div>

        @if($hostelAllocation)
            <!-- Hostel & Accommodation Assignment Details -->
            <div class="glass-card p-4 mb-4" style="background: linear-gradient(135deg, rgba(25, 135, 84, 0.05) 0%, rgba(0, 51, 102, 0.05) 100%); border-left: 5px solid #198754;">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="fw-bold text-dark mb-1"><i class="bi bi-house-door-fill text-success me-2"></i>My Hostel Accommodation</h5>
                        <p class="text-secondary small mb-0">You have been allocated an active bed in <strong>{{ $hostelAllocation->bed->room->dormitory->name ?? 'Nelson Mandela Block' }}</strong>.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-3 fs-7">
                            Room: {{ $hostelAllocation->bed->room->room_number ?? 'RM-101' }} / Bed: {{ $hostelAllocation->bed->bed_number }}
                        </span>
                        <div class="small text-muted mt-1" style="font-size: 0.75rem;">Checked In: {{ date('M d, Y', strtotime($hostelAllocation->allocated_date)) }}</div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-4">
            <!-- Today's Timetable -->
            <div class="col-md-7">
                <div class="glass-card p-4 h-100">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-calendar2-week me-2 text-primary"></i>Today's Class Schedule</h5>
                    @if($timetable->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 mb-2 d-block"></i>
                            <span>No classes scheduled for today.</span>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Subject</th>
                                        <th>Teacher</th>
                                        <th>Room</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($timetable as $slot)
                                        <tr>
                                            <td class="fw-semibold text-primary">{{ date('h:i A', strtotime($slot->start_time)) }} - {{ date('h:i A', strtotime($slot->end_time)) }}</td>
                                            <td>{{ $slot->subject->name ?? 'N/A' }}</td>
                                            <td>{{ $slot->teacher->name ?? 'N/A' }}</td>
                                            <td><span class="badge bg-secondary bg-opacity-10 text-dark">{{ $slot->room ?? 'Main Hall' }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Bulletins & Announcements -->
            <div class="col-md-5">
                <div class="glass-card p-4 h-100">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-megaphone me-2 text-warning"></i>Announcements</h5>
                    @if($announcements->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-bell-slash fs-1 mb-2 d-block"></i>
                            <span>No recent announcements.</span>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-3">
                            @foreach($announcements as $ann)
                                <div class="p-3 rounded bg-light border-start border-4 border-warning">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="fw-bold mb-0 text-dark">{{ $ann->title }}</h6>
                                        @if($ann->is_pinned)
                                            <span class="badge bg-danger"><i class="bi bi-pin-angle-fill"></i> Pinned</span>
                                        @endif
                                    </div>
                                    <p class="mb-0 text-muted small">{{ Str::limit($ann->content, 120) }}</p>
                                    <span class="text-muted small" style="font-size: 0.75rem;">{{ date('M d, Y', strtotime($ann->created_at)) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
