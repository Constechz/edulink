@extends('layouts.app')

@section('title', 'Attendance Statistical Reports | EduLink')
@section('header_title', 'Attendance Reports & Analytics')

@section('content')
<div class="container-fluid p-0">

    <!-- Back to Register -->
    <div class="mb-4">
        <a href="{{ route('school.attendance') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-calendar-check me-1"></i> Back to Register Checklist
        </a>
    </div>

    <!-- Filter Control Bar -->
    <div class="glass-card p-4 mb-4">
        <form action="{{ route('school.attendance.reports') }}" method="GET" class="row g-3 align-items-end" id="reportFilterForm">
            <div class="col-md-3">
                <label class="form-label small font-weight-bold">Select Date</label>
                <input type="date" class="form-control" name="date" value="{{ $selectedDate }}" onchange="document.getElementById('reportFilterForm').submit()">
            </div>
            <div class="col-md-5">
                <label class="form-label small font-weight-bold">Select Class Context (Optional)</label>
                <select class="form-select" name="class_id" onchange="document.getElementById('reportFilterForm').submit()">
                    <option value="">All Academic Classes</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}" {{ $selectedClassId == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-outline-dark px-4 py-2" onclick="window.print()" style="border-radius: 10px;">
                    <i class="bi bi-printer me-2"></i> Print Report Sheet
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Statistics Dashboard Row -->
    <div class="row g-4 mb-4">
        <!-- Present Card -->
        <div class="col-xl-3 col-sm-6">
            <div class="glass-card p-4 border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase mb-1">Present Students</div>
                        <h4 class="card-metric mb-0 text-success" style="font-size: 2.2rem; font-weight: 800;">{{ $stats['present'] }}</h4>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                        <i class="bi bi-person-check-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Absent Card -->
        <div class="col-xl-3 col-sm-6">
            <div class="glass-card p-4 border-start border-4 border-danger">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase mb-1">Absent Students</div>
                        <h4 class="card-metric mb-0 text-danger" style="font-size: 2.2rem; font-weight: 800;">{{ $stats['absent'] }}</h4>
                    </div>
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger">
                        <i class="bi bi-person-x-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Late Card -->
        <div class="col-xl-3 col-sm-6">
            <div class="glass-card p-4 border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase mb-1">Late Check-ins</div>
                        <h4 class="card-metric mb-0 text-warning" style="font-size: 2.2rem; font-weight: 800;">{{ $stats['late'] }}</h4>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                        <i class="bi bi-clock-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Excused Card -->
        <div class="col-xl-3 col-sm-6">
            <div class="glass-card p-4 border-start border-4 border-info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase mb-1">Excused Absence</div>
                        <h4 class="card-metric mb-0 text-info" style="font-size: 2.2rem; font-weight: 800;">{{ $stats['excused'] }}</h4>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                        <i class="bi bi-journal-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Table Card -->
    <div class="glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-0 font-weight-bold" style="font-weight: 700;">Daily Attendance Register Summary</h5>
                <p class="text-muted mb-0 small">Active records logged on <strong>{{ date('d F Y', strtotime($selectedDate)) }}</strong></p>
            </div>
            <div>
                <input type="text" id="reportSearchInput" class="form-control" placeholder="Search report sheet..." style="width: 250px;">
            </div>
        </div>

        @if(count($records) > 0)
            <div class="table-responsive">
                <table class="table align-middle" id="reportTable">
                    <thead>
                        <tr>
                            <th>Student ID Number</th>
                            <th>Student Name</th>
                            <th>Class Context</th>
                            <th class="text-center">Status</th>
                            <th>Arrival Time</th>
                            <th class="text-center">Log Method</th>
                            <th>Late Penalty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $rec)
                            <tr>
                                <td class="font-weight-bold" style="font-weight: 600;">{{ $rec->student->student_id_number }}</td>
                                <td>{{ $rec->student->first_name }} {{ $rec->student->middle_name }} {{ $rec->student->last_name }}</td>
                                <td>
                                    {{ $rec->class->name }}
                                    @if($rec->stream)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary ms-1">{{ $rec->stream->name }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($rec->status === 'present')
                                        <span class="badge bg-success-subtle text-success border border-success border-opacity-20 px-3 py-1.5" style="border-radius: 8px;">Present</span>
                                    @elseif($rec->status === 'absent')
                                        <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-20 px-3 py-1.5" style="border-radius: 8px;">Absent</span>
                                    @elseif($rec->status === 'late')
                                        <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-20 px-3 py-1.5" style="border-radius: 8px;">Late</span>
                                    @elseif($rec->status === 'excused')
                                        <span class="badge bg-info-subtle text-info border border-info border-opacity-20 px-3 py-1.5" style="border-radius: 8px;">Excused</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $rec->arrival_time ? date('h:i A', strtotime($rec->arrival_time)) : 'N/A' }}
                                </td>
                                <td class="text-center">
                                    @if($rec->method === 'qr')
                                        <span class="badge bg-dark px-2.5 py-1.5" style="border-radius: 8px;"><i class="bi bi-qr-code-scan me-1"></i> QR Scan</span>
                                    @else
                                        <span class="badge bg-light text-dark border px-2.5 py-1.5" style="border-radius: 8px;"><i class="bi bi-pencil me-1"></i> Manual</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rec->late_minutes > 0)
                                        <span class="text-warning font-weight-bold" style="font-weight: 600;"><i class="bi bi-clock me-1"></i> +{{ $rec->late_minutes }}m late</span>
                                    @else
                                        <span class="text-muted small">None</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-calendar-x display-4 d-block mb-3"></i>
                <p>No attendance checklists have been recorded for the selected parameters on this date.</p>
            </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('reportSearchInput');
        const table = document.getElementById('reportTable');

        if (searchInput && table) {
            searchInput.addEventListener('keyup', function() {
                const query = searchInput.value.toLowerCase();
                const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

                for (let row of rows) {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }
    });
</script>
@endsection
