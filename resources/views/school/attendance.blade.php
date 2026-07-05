@extends('layouts.app')

@section('title', 'Daily Attendance Register | EduLink')
@section('header_title', 'Student Daily Attendance')

@section('content')
<style>
    .btn-check:checked + .btn-outline-success {
        background-color: #198754 !important;
        color: #fff !important;
        box-shadow: 0 2px 6px rgba(25, 135, 84, 0.2);
    }
    .btn-check:checked + .btn-outline-danger {
        background-color: #dc3545 !important;
        color: #fff !important;
        box-shadow: 0 2px 6px rgba(220, 53, 69, 0.2);
    }
    .btn-check:checked + .btn-outline-warning {
        background-color: #ffc107 !important;
        color: #0f172a !important;
        box-shadow: 0 2px 6px rgba(255, 193, 7, 0.2);
    }
    .btn-check:checked + .btn-outline-info {
        background-color: #0dcaf0 !important;
        color: #0f172a !important;
        box-shadow: 0 2px 6px rgba(13, 202, 240, 0.2);
    }

    .attendance-row {
        transition: background-color 0.2s ease;
    }
    .attendance-row:hover {
        background-color: rgba(0, 51, 102, 0.02) !important;
    }

    .avatar-circle-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.82rem;
    }

    /* Text Visibility and High-Contrast Overrides */
    .text-muted {
        color: #64748b !important;
    }
    .text-secondary {
        color: #475569 !important;
    }
    .text-dark {
        color: #0f172a !important;
    }
</style>

<div class="container-fluid p-0">

    <!-- Offline Alert Banner -->
    <div id="offlineBanner" class="alert alert-warning border-0 shadow-sm mb-4 d-none" style="border-radius: 12px; background-color: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.2) !important;">
        <i class="bi bi-wifi-off me-2 text-warning fs-5"></i><strong class="text-dark">Offline Mode Active</strong>: Network connection lost. Attendance changes will be cached locally and synced automatically when you reconnect.
    </div>
    
    <div id="syncBanner" class="alert alert-success border-0 shadow-sm mb-4 d-none" style="border-radius: 12px; background-color: rgba(25, 135, 84, 0.1); border: 1px solid rgba(25, 135, 84, 0.2) !important;">
        <i class="bi bi-cloud-arrow-up-fill me-2 text-success fs-5"></i><strong class="text-dark">Reconnected!</strong> Successfully synced cached offline attendance records to the server database.
    </div>

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

    <!-- Selector Bar Form Card -->
    <div class="glass-card p-4 mb-4" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.05);">
        <form action="{{ route('school.attendance') }}" method="GET" class="row g-3 align-items-end" id="filterForm">
            <div class="col-md-3">
                <label class="form-label fw-bold text-secondary small"><i class="bi bi-calendar-event text-primary me-1"></i>Registry Date</label>
                <input type="date" class="form-control rounded-3 py-2" name="date" value="{{ $selectedDate }}" onchange="document.getElementById('filterForm').submit()">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary small"><i class="bi bi-layers-half text-primary me-1"></i>Select Classroom Class</label>
                <select class="form-select rounded-3 py-2" name="class_id" onchange="document.getElementById('filterForm').submit()" required>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}" {{ $selectedClassId == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold text-secondary small"><i class="bi bi-tags text-primary me-1"></i>Select Class Stream</label>
                <select class="form-select rounded-3 py-2" name="stream_id" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Entire Class</option>
                    @foreach($streams->where('class_id', $selectedClassId) as $strm)
                        <option value="{{ $strm->id }}" {{ $selectedStreamId == $strm->id ? 'selected' : '' }}>{{ $strm->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{ route('school.attendance.qr-kiosk') }}" class="btn btn-outline-primary w-100 py-2.5 d-inline-flex align-items-center justify-content-center gap-2 fw-bold" style="border-radius: 12px; font-size: 0.85rem;">
                    <i class="bi bi-qr-code-scan"></i> QR Kiosk
                </a>
            </div>
        </form>
    </div>

    @php
        $presentCount = 0;
        $absentCount = 0;
        $lateCount = 0;
        $excusedCount = 0;
        foreach($students as $s) {
            $status = $records[$s->id] ?? 'present';
            if ($status === 'present') $presentCount++;
            elseif ($status === 'absent') $absentCount++;
            elseif ($status === 'late') $lateCount++;
            elseif ($status === 'excused') $excusedCount++;
        }
    @endphp

    <!-- Dynamic Metrics Panel -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(0, 51, 102, 0.1) !important;">
                <div>
                    <span class="text-muted small d-block">Class Strength</span>
                    <span class="fs-3 fw-bold" style="color: var(--primary-color);">{{ count($students) }}</span>
                </div>
                <div class="fs-2 text-primary bg-primary bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(25, 135, 84, 0.1) !important;">
                <div>
                    <span class="text-muted small d-block">Present Pupils</span>
                    <span class="fs-3 fw-bold text-success">{{ $presentCount }}</span>
                </div>
                <div class="fs-2 text-success bg-success bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(220, 53, 69, 0.15) !important;">
                <div>
                    <span class="text-muted small d-block">Absent Pupils</span>
                    <span class="fs-3 fw-bold text-danger">{{ $absentCount }}</span>
                </div>
                <div class="fs-2 text-danger bg-danger bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(255, 193, 7, 0.2) !important;">
                <div>
                    <span class="text-muted small d-block">Late / Excused</span>
                    <span class="fs-3 fw-bold text-warning" style="color: #b08d00 !important;">{{ $lateCount + $excusedCount }}</span>
                </div>
                <div class="fs-2 text-warning bg-warning bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; color: #b08d00 !important;">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Entry Card Container -->
    <div class="glass-card p-4 mb-5" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.05);">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-1 text-dark" style="font-weight: 700;">Student Daily Checklist</h5>
                <p class="text-muted mb-0 small">Mark each student present, absent, late, or excused for <strong>{{ date('d F Y', strtotime($selectedDate)) }}</strong>.</p>
            </div>
        </div>

        @if(count($students) > 0)
            <form action="{{ route('school.attendance.store') }}" method="POST" id="attendanceForm">
                @csrf
                <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                <input type="hidden" name="stream_id" value="{{ $selectedStreamId }}">
                <input type="hidden" name="date" value="{{ $selectedDate }}">

                <div class="table-responsive">
                    <table class="table align-middle" style="font-size: 0.9rem;">
                        <thead>
                            <tr class="table-light">
                                <th class="border-0 rounded-start">Student ID Number</th>
                                <th class="border-0">Student Name</th>
                                <th class="border-0 rounded-end text-center" style="width: 420px;">Status Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php 
                                    $currentStatus = $records[$student->id] ?? 'present';
                                @endphp
                                <tr class="attendance-row">
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1.5 fw-bold" style="border-radius: 6px; font-family: monospace; font-size: 0.78rem;">
                                            {{ $student->student_id_number }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="avatar-circle-sm bg-primary bg-opacity-10 text-primary">
                                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                            </div>
                                            <span class="fw-bold text-dark">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <!-- Custom Styled Button Group Checks -->
                                        <div class="btn-group rounded-3 overflow-hidden p-1 bg-light border border-light-subtle" role="group" style="width: 100%; max-width: 380px;">
                                            
                                            <!-- Present -->
                                            <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" id="pres_{{ $student->id }}" value="present" {{ $currentStatus == 'present' ? 'checked' : '' }}>
                                            <label class="btn btn-sm btn-outline-success border-0 px-3 py-1.5 fw-bold" for="pres_{{ $student->id }}" style="font-size: 0.76rem; border-radius: 6px;">
                                                <i class="bi bi-check-circle me-1"></i>Present
                                            </label>

                                            <!-- Absent -->
                                            <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" id="abs_{{ $student->id }}" value="absent" {{ $currentStatus == 'absent' ? 'checked' : '' }}>
                                            <label class="btn btn-sm btn-outline-danger border-0 px-3 py-1.5 fw-bold" for="abs_{{ $student->id }}" style="font-size: 0.76rem; border-radius: 6px;">
                                                <i class="bi bi-x-circle me-1"></i>Absent
                                            </label>

                                            <!-- Late -->
                                            <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" id="late_{{ $student->id }}" value="late" {{ $currentStatus == 'late' ? 'checked' : '' }}>
                                            <label class="btn btn-sm btn-outline-warning border-0 px-3 py-1.5 fw-bold" for="late_{{ $student->id }}" style="font-size: 0.76rem; border-radius: 6px; color: #b08d00 !important;">
                                                <i class="bi bi-clock me-1"></i>Late
                                            </label>

                                            <!-- Excused -->
                                            <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" id="exc_{{ $student->id }}" value="excused" {{ $currentStatus == 'excused' ? 'checked' : '' }}>
                                            <label class="btn btn-sm btn-outline-info border-0 px-3 py-1.5 fw-bold" for="exc_{{ $student->id }}" style="font-size: 0.76rem; border-radius: 6px;">
                                                <i class="bi bi-file-earmark-medical me-1"></i>Excused
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-5 py-2.5 fw-bold d-inline-flex align-items-center gap-2" style="border-radius: 12px; background-color: var(--primary-color); border: none;">
                        <i class="bi bi-cloud-arrow-up-fill"></i> Save Attendance Sheet
                    </button>
                </div>
            </form>
        @else
            <!-- Redesigned Empty State -->
            <div class="text-center py-5 text-muted">
                <div class="text-warning bg-warning bg-opacity-10 p-3 rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px; color: #b08d00 !important;">
                    <i class="bi bi-people display-5"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">No Students Allocated</h5>
                <p class="mb-0">No students are currently assigned to this class/stream combination.</p>
                <p class="small text-muted mb-0">Select another classroom option or date from the filters above.</p>
            </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('attendanceForm');
        const offlineBanner = document.getElementById('offlineBanner');
        const syncBanner = document.getElementById('syncBanner');

        // Check initial connectivity status
        function updateNetworkStatus() {
            if (!navigator.onLine) {
                offlineBanner.classList.remove('d-none');
            } else {
                offlineBanner.classList.add('d-none');
            }
        }

        window.addEventListener('online', function() {
            updateNetworkStatus();
            syncOfflineData();
        });
        window.addEventListener('offline', updateNetworkStatus);

        updateNetworkStatus();

        // Handle offline save
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!navigator.onLine) {
                    e.preventDefault();
                    
                    // Collect Form Data
                    const formData = new FormData(form);
                    const attendanceData = {};
                    
                    for (let [key, value] of formData.entries()) {
                        if (key.startsWith('attendance[')) {
                            const studentId = key.substring(11, key.length - 1);
                            attendanceData[studentId] = value;
                        }
                    }

                    const offlineDraft = {
                        class_id: formData.get('class_id'),
                        stream_id: formData.get('stream_id'),
                        date: formData.get('date'),
                        attendance: attendanceData
                    };

                    localStorage.setItem('offline_attendance_draft', JSON.stringify(offlineDraft));
                    alert('Offline Mode: Network is down. Attendance draft cached locally. It will auto-sync on reconnect.');
                }
            });
        }

        // Automatic Sync
        function syncOfflineData() {
            const cached = localStorage.getItem('offline_attendance_draft');
            if (cached) {
                const draft = JSON.parse(cached);
                const csrfToken = document.querySelector('input[name="_token"]').value;

                fetch("{{ route('school.attendance.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(draft)
                })
                .then(response => {
                    if (response.ok) {
                        localStorage.removeItem('offline_attendance_draft');
                        syncBanner.classList.remove('d-none');
                        setTimeout(() => {
                            syncBanner.classList.add('d-none');
                            window.location.reload();
                        }, 3000);
                    }
                })
                .catch(err => console.error('Failed to sync cached attendance records', err));
            }
        }

        // Check for cached data to sync
        if (navigator.onLine) {
            syncOfflineData();
        }
    });
</script>
@endsection
