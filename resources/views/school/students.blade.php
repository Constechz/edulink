@extends('layouts.app')

@section('title', 'Student Registry | EduLink')
@section('header_title', 'Student Information Directory')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<style>
    .cropper-container {
        border-radius: 12px;
        overflow: hidden;
    }
</style>
@endsection

@section('content')
<style>
    .btn-filter {
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        color: var(--text-muted);
        transition: all 0.2s ease;
    }
    
    .btn-filter:hover {
        background-color: rgba(0, 51, 102, 0.05);
        color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .student-row {
        transition: background-color 0.2s ease;
    }

    .student-row:hover {
        background-color: rgba(0, 51, 102, 0.02) !important;
    }

    .avatar-circle-sm {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
    }

    .student-photo {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid var(--accent-color);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .pulse-dot {
        width: 7px;
        height: 7px;
        background-color: #198754;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 8px #198754;
        animation: pulse-green 2s infinite;
    }

    @keyframes pulse-green {
        0% { transform: scale(0.9); opacity: 0.8; }
        50% { transform: scale(1.1); opacity: 1; }
        100% { transform: scale(0.9); opacity: 0.8; }
    }

    .empty-state-icon {
        animation: pulse-student 3s infinite;
    }

    @keyframes pulse-student {
        0% { transform: scale(1); opacity: 0.8; }
        50% { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(1); opacity: 0.8; }
    }

    /* Bootstrap 5 Form in Scrollable Modal Fix */
    .modal-dialog-scrollable form {
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        margin: 0;
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

    <!-- Upgraded Metrics Cards Block -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(0, 51, 102, 0.1) !important;">
                <div>
                    <span class="text-muted small d-block">Total Students</span>
                    <span class="fs-3 fw-bold" style="color: var(--primary-color);">{{ $students->count() }}</span>
                </div>
                <div class="fs-2 text-primary bg-primary bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(0, 51, 102, 0.1) !important;">
                <div>
                    <span class="text-muted small d-block">Male Pupils</span>
                    <span class="fs-3 fw-bold text-primary">{{ $students->where('gender', 'Male')->count() }}</span>
                </div>
                <div class="fs-2 text-primary bg-primary bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-gender-male"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(219, 39, 119, 0.15) !important;">
                <div>
                    <span class="text-muted small d-block">Female Pupils</span>
                    <span class="fs-3 fw-bold text-pink" style="color: #db2777 !important;">{{ $students->where('gender', 'Female')->count() }}</span>
                </div>
                <div class="fs-2 text-pink bg-pink bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; color: #db2777 !important; background-color: rgba(219, 39, 119, 0.1) !important;">
                    <i class="bi bi-gender-female"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(25, 135, 84, 0.1) !important;">
                <div>
                    <span class="text-muted small d-block">Active Registry</span>
                    <span class="fs-3 fw-bold text-success">{{ $students->where('status', 'active')->count() }}</span>
                </div>
                <div class="fs-2 text-success bg-success bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Header Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 font-weight-bold" style="font-weight: 700; color: var(--primary-color);">Student Registry Directory</h4>
            <p class="text-muted small mb-0">Access student biodata, guardians, class/stream allocation, and medical records.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('school.students.print-pdf') }}" target="_blank" class="btn btn-outline-secondary px-3 py-2.5 d-inline-flex align-items-center gap-2" style="border-radius: 12px; font-weight: 600;">
                <i class="bi bi-file-pdf"></i> Print Student List
            </a>
            <button class="btn btn-primary px-4 py-2.5 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#registerStudentModal" style="border-radius: 12px; background-color: var(--primary-color); border: none; font-weight: 600;">
                <i class="bi bi-person-plus-fill"></i> Register Student Account
            </button>
        </div>
    </div>

    @if(count($students) > 0)
        <!-- Interactive Search & Class Filter Cards Directory -->
        <div class="glass-card p-4 mb-5" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.05);">
            
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                <div class="position-relative" style="width: 300px; max-width: 100%;">
                    <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="student-search" class="form-control rounded-3 ps-5 py-2" placeholder="Search by name, ID number...">
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small fw-bold text-nowrap"><i class="bi bi-filter"></i> Filter Class:</span>
                    <select id="student-class-filter" class="form-select rounded-3 py-2" style="width: 180px;">
                        <option value="all">All Classrooms</option>
                        @foreach($classes as $cls)
                            <option value="class-{{ $cls->id }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" style="font-size: 0.9rem;">
                    <thead>
                        <tr class="table-light">
                            <th class="border-0 rounded-start">ID Number</th>
                            <th class="border-0">Student Profile</th>
                            <th class="border-0">Class & Stream</th>
                            <th class="border-0">Campus</th>
                            <th class="border-0">Primary Guardian</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 rounded-end text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="student-table-body">
                        @foreach($students as $student)
                            <tr class="student-row animate-row" data-name="{{ strtolower($student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name) }}" data-id="{{ strtolower($student->student_id_number) }}" data-class="class-{{ $student->current_class_id }}">
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1.5 fw-bold" style="border-radius: 6px; font-family: monospace; font-size: 0.78rem;">
                                        {{ $student->student_id_number }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div>
                                            @if($student->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo))
                                                <img src="{{ asset('storage/' . $student->photo) }}" alt="photo" class="student-photo">
                                            @else
                                                <div class="avatar-circle-sm bg-primary bg-opacity-10 text-primary">
                                                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</div>
                                            <span class="text-muted small fw-semibold">{{ $student->gender }} @if($student->date_of_birth) | DOB: {{ $student->date_of_birth->format('d M Y') }} @endif</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $student->currentClass ? $student->currentClass->name : 'Unallocated' }}</div>
                                    <span class="text-muted small">Stream: {{ $student->currentStream ? $student->currentStream->name : 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge border border-secondary border-opacity-25 text-secondary px-2.5 py-1.5 font-weight-medium" style="border-radius: 8px; font-size: 0.75rem; background-color: #f8fafc;">
                                        <i class="bi bi-geo-alt-fill me-1"></i>{{ $student->campus ? $student->campus->name : 'Global' }}
                                    </span>
                                </td>
                                <td>
                                    @php $primary = $student->guardians->first(); @endphp
                                    @if($primary)
                                        <div class="fw-semibold text-dark">{{ $primary->first_name }} {{ $primary->last_name }}</div>
                                        <span class="text-muted small"><i class="bi bi-telephone-fill me-1 small"></i>{{ $primary->phone }}</span>
                                    @else
                                        <span class="text-muted small italic">No primary guardian</span>
                                    @endif
                                </td>
                                <td>
                                    @if($student->status === 'active')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 d-inline-flex align-items-center gap-1.5 py-1.5 px-2.5 rounded-3 fw-bold" style="font-size: 0.72rem;">
                                            <span class="pulse-dot"></span> Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 py-1.5 px-2.5 rounded-3 fw-bold" style="font-size: 0.72rem;">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1.5 align-items-center">
                                        <button class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1 px-2 py-1.5 rounded-3 shadow-xs fw-bold" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#admissionSlipModal{{ $student->id }}" title="Print Admission/Login Slip">
                                            <i class="bi bi-printer"></i> Slip
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 px-2 py-1.5 rounded-3 shadow-xs fw-bold" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#editStudentModal{{ $student->id }}">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                        <form action="{{ route('school.students.reset-password', $student->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to reset this student\'s password to their default date of birth (DDMMYYYY)?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning d-inline-flex align-items-center justify-content-center rounded-3 shadow-xs" style="width: 30px; height: 30px; padding: 0;" title="Reset Portal Password">
                                                <i class="bi bi-key-fill"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('school.students.destroy', $student->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this student record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center rounded-3 shadow-xs" style="width: 30px; height: 30px; padding: 0;" title="Delete Student Profile">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Premium Redesigned Empty State -->
        <div class="glass-card p-5 text-center text-muted d-flex flex-column align-items-center justify-content-center" style="border-radius: 20px; border: 1px dashed rgba(0, 51, 102, 0.2); background: #ffffff;">
            <div class="empty-state-icon text-primary bg-primary bg-opacity-10 p-4 rounded-circle mb-4 d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                <i class="bi bi-person-badge display-4"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">No Students Registered</h4>
            <p class="mb-1 text-secondary">Start registers students, map parent logins, and allocate classes/streams.</p>
            <p class="small text-muted mb-4">Adding accounts allows automatic grading mapping, reports card calculations, and billing generation.</p>
            <button class="btn btn-primary px-4 py-2.5 rounded-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#registerStudentModal" style="background-color: var(--primary-color); border: none;">
                <i class="bi bi-person-plus-fill me-2"></i> Register First Student
            </button>
        </div>
    @endif

    <!-- REGISTER STUDENT MODAL -->
    <div class="modal fade" id="registerStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold text-dark" style="font-weight: 700;"><i class="bi bi-person-plus-fill me-2 text-primary"></i>Register Student Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('school.students.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <!-- Student Section -->
                            <div class="col-12 border-bottom pb-2">
                                <h6 class="font-weight-bold text-primary mb-0"><i class="bi bi-person-badge-fill me-1"></i>Student Personal Information</h6>
                            </div>

                            @php
                                $schoolObj = auth()->user()->school;
                                $prefixVal = $schoolObj->settings['student_id_prefix'] ?? 'STD';
                                $formatVal = $schoolObj->settings['student_id_format'] ?? '{PREFIX}-{YEAR}-{SEQUENCE}';
                                $nextSeqVal = $schoolObj->settings['student_id_next_sequence'] ?? 1;
                                $previewIdVal = str_replace(
                                    ['{PREFIX}', '{YEAR}', '{SEQUENCE}'],
                                    [$prefixVal, date('Y'), sprintf('%04d', $nextSeqVal)],
                                    $formatVal
                                );
                            @endphp

                            <div class="col-12">
                                <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="manual_student_id" name="manual_student_id" value="1">
                                        <label class="form-check-label small fw-bold text-secondary ms-1" for="manual_student_id">Assign ID Number Manually</label>
                                    </div>
                                    <div style="width: 250px; max-width: 100%;">
                                        <input type="text" class="form-control form-control-sm rounded-3" id="student_id_number_input" name="student_id_number" placeholder="e.g. {{ $previewIdVal }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small">First Name</label>
                                <input type="text" class="form-control rounded-3 py-2" name="first_name" required placeholder="e.g. Kofi">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small">Middle Name</label>
                                <input type="text" class="form-control rounded-3 py-2" name="middle_name" placeholder="e.g. Mensah">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small">Last Name</label>
                                <input type="text" class="form-control rounded-3 py-2" name="last_name" required placeholder="e.g. Osei">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small">Date of Birth</label>
                                <input type="date" class="form-control rounded-3 py-2" name="date_of_birth" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small">Gender</label>
                                <select class="form-select rounded-3 py-2" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small">Nationality</label>
                                <input type="text" class="form-control rounded-3 py-2" name="nationality" value="Ghanaian" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small">NHIS Number / Ghana Card</label>
                                <input type="text" class="form-control rounded-3 py-2" name="nhis_number" placeholder="Ghana Card / NHIS ID">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small">Blood Group</label>
                                <select class="form-select rounded-3 py-2" name="blood_group">
                                    <option value="">Unknown</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small">Religion</label>
                                <input type="text" class="form-control rounded-3 py-2" name="religion" placeholder="e.g. Christian / Muslim">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Residential Address</label>
                                <input type="text" class="form-control rounded-3 py-2" name="address" placeholder="Residential location/GPS code">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">Region</label>
                                <input type="text" class="form-control rounded-3 py-2" name="region" placeholder="e.g. Greater Accra">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">District</label>
                                <input type="text" class="form-control rounded-3 py-2" name="district" placeholder="e.g. Accra Metropolitan">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Student Photo (Avatar)</label>
                                <input type="file" class="form-control rounded-3" name="photo" accept="image/*">
                                <div class="form-text small text-muted">Supports JPG, PNG, GIF. Max 2MB.</div>
                            </div>

                            <!-- Academic Setup -->
                            <div class="col-12 border-bottom pb-2 mt-4">
                                <h6 class="font-weight-bold text-primary mb-0"><i class="bi bi-book-fill me-1"></i>Campus & Classroom Settings</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">Campus Branch</label>
                                <select class="form-select rounded-3 py-2" name="campus_id" required>
                                    <option value="">Select Campus</option>
                                    @foreach($campuses as $campus)
                                        <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-secondary small">Current Class</label>
                                <select class="form-select rounded-3 py-2" name="current_class_id" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $cls)
                                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-secondary small">Current Stream</label>
                                <select class="form-select rounded-3 py-2" name="current_stream_id">
                                    <option value="">None / Unassigned</option>
                                    @foreach($streams as $stream)
                                        <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold text-secondary small">Enrollment Date</label>
                                <input type="date" class="form-control rounded-3 py-2" name="enrollment_date" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <!-- Guardian Settings -->
                            <div class="col-12 border-bottom pb-2 mt-4">
                                <h6 class="font-weight-bold text-primary mb-0"><i class="bi bi-people-fill me-1"></i>Primary Parent / Guardian Portal Details</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">Guardian First Name</label>
                                <input type="text" class="form-control rounded-3 py-2" name="guardian_first_name" required placeholder="e.g. John">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">Guardian Last Name</label>
                                <input type="text" class="form-control rounded-3 py-2" name="guardian_last_name" required placeholder="e.g. Appiah">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small">Guardian Phone Number</label>
                                <input type="text" class="form-control rounded-3 py-2" name="guardian_phone" required placeholder="e.g. +233 24 000 0000">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small">Guardian Email Address</label>
                                <input type="email" class="form-control rounded-3 py-2" name="guardian_email" placeholder="e.g. parent@email.com">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small">Relationship to Student</label>
                                <select class="form-select rounded-3 py-2" name="guardian_relationship" required>
                                    <option value="Father">Father</option>
                                    <option value="Mother">Mother</option>
                                    <option value="Uncle">Uncle</option>
                                    <option value="Aunt">Aunt</option>
                                    <option value="Guardian">Other Guardian</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px; background-color: var(--primary-color); border: none;">Register & Onboard</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT STUDENT MODALS -->
    @foreach($students as $student)
        <div class="modal fade" id="editStudentModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    <div class="modal-header border-bottom-0 p-4 pb-0">
                        <h5 class="modal-title font-weight-bold text-dark" style="font-weight: 700;"><i class="bi bi-pencil-square me-2 text-primary"></i>Update Student Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('school.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-12 border-bottom pb-2">
                                    <h6 class="font-weight-bold text-primary mb-0"><i class="bi bi-person-badge-fill me-1"></i>Student Personal Details</h6>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-secondary small">Student ID Number</label>
                                    <input type="text" class="form-control rounded-3 py-2" name="student_id_number" value="{{ $student->student_id_number }}" required>
                                    <div class="form-text small text-muted">Warning: Modifying the Student ID will automatically update their portal credentials to preserve login access.</div>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-secondary small">First Name</label>
                                    <input type="text" class="form-control rounded-3 py-2" name="first_name" value="{{ $student->first_name }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Middle Name</label>
                                    <input type="text" class="form-control rounded-3 py-2" name="middle_name" value="{{ $student->middle_name }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Last Name</label>
                                    <input type="text" class="form-control rounded-3 py-2" name="last_name" value="{{ $student->last_name }}" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Date of Birth</label>
                                    <input type="date" class="form-control rounded-3 py-2" name="date_of_birth" value="{{ $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '' }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Gender</label>
                                    <select class="form-select rounded-3 py-2" name="gender" required>
                                        <option value="Male" {{ $student->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ $student->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ $student->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Nationality</label>
                                    <input type="text" class="form-control rounded-3 py-2" name="nationality" value="{{ $student->nationality }}" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-secondary small">NHIS Number / Ghana Card</label>
                                    <input type="text" class="form-control rounded-3 py-2" name="nhis_number" value="{{ $student->nhis_number }}" placeholder="Ghana Card / NHIS ID">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Blood Group</label>
                                    <select class="form-select rounded-3 py-2" name="blood_group">
                                        <option value="">Unknown</option>
                                        <option value="A+" {{ $student->blood_group == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ $student->blood_group == 'A-' ? 'selected' : '' }}>A-</option>
                                        <option value="B+" {{ $student->blood_group == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ $student->blood_group == 'B-' ? 'selected' : '' }}>B-</option>
                                        <option value="O+" {{ $student->blood_group == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ $student->blood_group == 'O-' ? 'selected' : '' }}>O-</option>
                                        <option value="AB+" {{ $student->blood_group == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ $student->blood_group == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Religion</label>
                                    <input type="text" class="form-control rounded-3 py-2" name="religion" value="{{ $student->religion }}" placeholder="e.g. Christian / Muslim">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-secondary small">Residential Address</label>
                                    <input type="text" class="form-control rounded-3 py-2" name="address" value="{{ $student->address }}" placeholder="Residential location/GPS code">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Region</label>
                                    <input type="text" class="form-control rounded-3 py-2" name="region" value="{{ $student->region }}" placeholder="e.g. Greater Accra">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">District</label>
                                    <input type="text" class="form-control rounded-3 py-2" name="district" value="{{ $student->district }}" placeholder="e.g. Accra Metropolitan">
                                </div>

                                <!-- Academic Setup -->
                                <div class="col-12 border-bottom pb-2 mt-4">
                                    <h6 class="font-weight-bold text-primary mb-0"><i class="bi bi-book-fill me-1"></i>Campus & Classroom Allocation</h6>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Campus Allocation</label>
                                    <select class="form-select rounded-3 py-2" name="campus_id" required>
                                        @foreach($campuses as $campus)
                                            <option value="{{ $campus->id }}" {{ $student->campus_id == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary small">Current Class</label>
                                    <select class="form-select rounded-3 py-2" name="current_class_id" required>
                                        @foreach($classes as $cls)
                                            <option value="{{ $cls->id }}" {{ $student->current_class_id == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary small">Current Stream</label>
                                    <select class="form-select rounded-3 py-2" name="current_stream_id">
                                        <option value="">None</option>
                                        @foreach($streams as $stream)
                                            <option value="{{ $stream->id }}" {{ $student->current_stream_id == $stream->id ? 'selected' : '' }}>{{ $stream->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Enrollment Status</label>
                                    <select class="form-select rounded-3 py-2" name="status" required>
                                        <option value="active" {{ $student->status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="graduated" {{ $student->status == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                        <option value="transferred" {{ $student->status == 'transferred' ? 'selected' : '' }}>Transferred</option>
                                        <option value="withdrawn" {{ $student->status == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                                        <option value="deceased" {{ $student->status == 'deceased' ? 'selected' : '' }}>Deceased</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Student Photo (Avatar)</label>
                                    <input type="file" class="form-control rounded-3" name="photo" accept="image/*">
                                    <div class="form-text small text-muted">Upload to replace the current student avatar photo.</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 p-4 pt-0">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px; background-color: var(--primary-color); border: none;">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ADMISSION SLIP MODAL -->
        <div class="modal fade" id="admissionSlipModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <!-- Modal Header with standard Exit Button -->
                    <div class="modal-header border-0 pb-0 d-flex justify-content-end p-3">
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body px-4 pb-4 pt-0 text-center" 
                         id="printableSlip{{ $student->id }}"
                         data-school="{{ Auth::user()->school ? Auth::user()->school->name : \App\Models\SystemSetting::getVal('platform_name', config('app.name', 'EduLink')) . ' ERP' }}"
                         data-name="{{ $student->first_name }} {{ $student->last_name }}"
                         data-class="{{ $student->currentClass ? $student->currentClass->name : 'Not Assigned' }}"
                         data-id="{{ $student->student_id_number }}"
                         data-password="{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('dmY') : 'DDMMYYYY' }}"
                         data-url="{{ url('/login') }}">
                        <div class="mb-4">
                            <i class="bi bi-globe-europe-africa text-warning display-4"></i>
                            <h4 class="fw-bold mt-2 text-dark">{{ Auth::user()->school ? Auth::user()->school->name : \App\Models\SystemSetting::getVal('platform_name', config('app.name', 'EduLink')) . ' ERP' }}</h4>
                            <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small">Official Admission & Login Slip</span>
                        </div>
                        
                        <div class="border rounded-4 p-4 mb-4 text-start bg-light">
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span class="text-muted small">Student Name:</span>
                                <span class="fw-bold text-dark">{{ $student->first_name }} {{ $student->last_name }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span class="text-muted small">Class Assigned:</span>
                                <span class="fw-bold text-dark">{{ $student->currentClass ? $student->currentClass->name : 'Not Assigned' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span class="text-muted small">Student ID Number:</span>
                                <span class="fw-bold text-success">{{ $student->student_id_number }}</span>
                            </div>
                            
                            <hr class="my-3">
                            
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-shield-lock text-primary me-2"></i>Portal Login Credentials</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Username / Student ID</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-person text-muted"></i></span>
                                    <input type="text" class="form-control bg-white" value="{{ $student->student_id_number }}" readonly>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label text-muted small mb-1">Default Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-lock text-muted"></i></span>
                                    <input type="text" class="form-control bg-white" value="{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('dmY') : 'DDMMYYYY' }}" readonly>
                                </div>
                                <span class="text-muted small d-block mt-1" style="font-size: 0.75rem;"><i class="bi bi-info-circle me-1"></i>Format: Date of Birth in DDMMYYYY (e.g. 15082012)</span>
                            </div>
                        </div>
                        
                        <div class="text-muted small mb-4">
                            <i class="bi bi-link-45deg me-1"></i>Portal URL: <strong>{{ url('/login') }}</strong>
                        </div>
                        
                        <div class="d-flex gap-3">
                            <button type="button" class="btn btn-outline-secondary w-100 py-2.5 rounded-3 fw-bold" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold" onclick="printSlip({{ $student->id }})" style="background-color: var(--primary-color); border: none;">
                                <i class="bi bi-printer me-1"></i> Print Slip
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</div>

<script>
    function printSlip(studentId) {
        var element = document.getElementById('printableSlip' + studentId);
        var school = element.getAttribute('data-school');
        var name = element.getAttribute('data-name');
        var classVal = element.getAttribute('data-class');
        var idVal = element.getAttribute('data-id');
        var passwordVal = element.getAttribute('data-password');
        var urlVal = element.getAttribute('data-url');

        var printWindow = window.open('', '_blank', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Admission & Login Slip</title>');
        printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">');
        printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">');
        printWindow.document.write('<style>');
        printWindow.document.write('body { font-family: "Segoe UI", system-ui, sans-serif; padding: 40px; background: white; color: black; }');
        printWindow.document.write('.slip-card { border: 2px dashed #334155; border-radius: 16px; padding: 25px; margin-top: 20px; background-color: #f8fafc !important; }');
        printWindow.document.write('.title-header { text-align: center; margin-bottom: 20px; }');
        printWindow.document.write('.title-header h4 { font-weight: 800; font-size: 1.5rem; text-transform: uppercase; margin-bottom: 2px; color: #1e293b; }');
        printWindow.document.write('.slip-label { text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; font-weight: bold; background-color: #3b82f6; color: #fff; padding: 4px 12px; border-radius: 50px; display: inline-block; margin-top: 5px; }');
        printWindow.document.write('.info-row { display: flex; justify-content: space-between; border-bottom: 1px dashed #cbd5e1; padding: 12px 0; font-size: 1.05rem; }');
        printWindow.document.write('.info-row:last-child { border-bottom: none; }');
        printWindow.document.write('.info-label { color: #64748b; font-weight: 500; }');
        printWindow.document.write('.info-value { font-weight: 700; color: #0f172a; }');
        printWindow.document.write('.credential-section { margin-top: 25px; border-top: 2px solid #334155; padding-top: 20px; }');
        printWindow.document.write('.credential-title { font-weight: 700; font-size: 1.1rem; text-transform: uppercase; margin-bottom: 15px; display: flex; align-items: center; color: #1e293b; }');
        printWindow.document.write('.credential-box { background-color: #ffffff !important; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px 15px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; }');
        printWindow.document.write('.cred-label { font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; }');
        printWindow.document.write('.cred-value { font-family: monospace; font-size: 1.1rem; font-weight: bold; color: #0f766e; }');
        printWindow.document.write('.footer-note { text-align: center; font-size: 0.8rem; color: #64748b; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 15px; }');
        printWindow.document.write('</style>');
        printWindow.document.write('</head><body>');

        var slipHtml = `
            <div class="title-header">
                <i class="bi bi-globe-europe-africa" style="font-size: 2.5rem; color: #3b82f6;"></i>
                <h4>${school}</h4>
                <div class="slip-label">Official Admission & Login Slip</div>
            </div>
            
            <div class="slip-card">
                <div class="info-row">
                    <span class="info-label">Student Name:</span>
                    <span class="info-value">${name}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Class Assigned:</span>
                    <span class="info-value">${classVal}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Student ID Number:</span>
                    <span class="info-value" style="color: #0f766e;">${idVal}</span>
                </div>
                
                <div class="credential-section">
                    <div class="credential-title">
                        <i class="bi bi-shield-lock me-2" style="color: #3b82f6;"></i> Portal Login Credentials
                    </div>
                    <div class="credential-box">
                        <span class="cred-label">Login Username / ID:</span>
                        <span class="cred-value">${idVal}</span>
                    </div>
                    <div class="credential-box">
                        <span class="cred-label">Default Password:</span>
                        <span class="cred-value">${passwordVal}</span>
                    </div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 5px; text-align: center;">
                        <i class="bi bi-info-circle me-1"></i>Format is Date of Birth in DDMMYYYY format (e.g. 15082012).
                    </div>
                </div>
            </div>
            
            <div class="footer-note">
                <div>Access Portal URL: <strong>${urlVal}</strong></div>
                <div style="margin-top: 5px; font-size: 0.75rem;">This is an official system slip. Please keep your login credentials private.</div>
                <div style="margin-top: 5px; font-size: 0.75rem;">&copy; 2026 EduLink Ghana ERP. All rights reserved.</div>
            </div>
        `;

        printWindow.document.write(slipHtml);
        printWindow.document.write('</body></html>');
        printWindow.document.close();

        setTimeout(function() {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }, 300);
    }
</script>

<!-- Vanilla JS client-side filter and search for student registry -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const studentSearch = document.getElementById('student-search');
        const classFilter = document.getElementById('student-class-filter');
        const studentRows = document.querySelectorAll('.student-row');

        function filterStudents() {
            const query = studentSearch ? studentSearch.value.trim().toLowerCase() : '';
            const targetClass = classFilter ? classFilter.value : 'all';

            studentRows.forEach(row => {
                const name = row.getAttribute('data-name');
                const id = row.getAttribute('data-id');
                const rowClass = row.getAttribute('data-class');

                const matchesQuery = name.includes(query) || id.includes(query);
                const matchesClass = targetClass === 'all' || rowClass === targetClass;

                if (matchesQuery && matchesClass) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        if (studentSearch) {
            studentSearch.addEventListener('input', filterStudents);
        }

        if (classFilter) {
            classFilter.addEventListener('change', filterStudents);
        }
        }
    });
</script>

    <!-- CROP IMAGE MODAL -->
    <div class="modal fade" id="cropImageModal" tabindex="-1" aria-labelledby="cropImageModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="cropImageModalLabel"><i class="bi bi-crop me-2 text-primary"></i>Crop Student Photo</h5>
                    <button type="button" class="btn-close" id="btnCancelCropX" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="img-container" style="max-height: 320px; overflow: hidden; display: flex; justify-content: center; background-color: #f8fafc; border-radius: 12px; border: 1px solid rgba(0,0,0,0.05);">
                        <img id="imageToCrop" src="" style="max-width: 100%; display: block;">
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-secondary px-3" id="btnCancelCrop" style="border-radius: 8px;">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" id="btnSaveCrop" style="border-radius: 8px; background-color: var(--primary-color); border: none;">Crop & Apply</button>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let cropper = null;
        let activeInput = null;
        const cropModalEl = document.getElementById('cropImageModal');
        const cropModal = new bootstrap.Modal(cropModalEl);
        const imageToCrop = document.getElementById('imageToCrop');
        
        const photoInputs = document.querySelectorAll('input[type="file"][name="photo"]');
        
        photoInputs.forEach(input => {
            input.addEventListener('change', function (e) {
                const files = e.target.files;
                if (files && files.length > 0) {
                    activeInput = e.target;
                    const file = files[0];
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        imageToCrop.src = event.target.result;
                        cropModal.show();
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        cropModalEl.addEventListener('shown.bs.modal', function () {
            cropper = new Cropper(imageToCrop, {
                aspectRatio: 1,
                viewMode: 1,
                background: false,
                autoCropArea: 0.8
            });
        });

        cropModalEl.addEventListener('hidden.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            imageToCrop.src = '';
        });

        document.getElementById('btnSaveCrop').addEventListener('click', function () {
            if (cropper && activeInput) {
                const canvas = cropper.getCroppedCanvas({
                    width: 250,
                    height: 250
                });
                
                canvas.toBlob(function (blob) {
                    const file = new File([blob], 'cropped-student-photo.jpg', { type: 'image/jpeg' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    activeInput.files = dataTransfer.files;
                    
                    cropModal.hide();
                }, 'image/jpeg');
            }
        });

        const cancelAction = function () {
            if (activeInput) {
                activeInput.value = '';
            }
            cropModal.hide();
        };
        document.getElementById('btnCancelCrop').addEventListener('click', cancelAction);
        document.getElementById('btnCancelCropX').addEventListener('click', cancelAction);

        // Manual Student ID Switch Toggler
        const manualSwitch = document.getElementById('manual_student_id');
        const idInput = document.getElementById('student_id_number_input');
        if (manualSwitch && idInput) {
            manualSwitch.addEventListener('change', function() {
                if (this.checked) {
                    idInput.removeAttribute('readonly');
                    idInput.focus();
                    idInput.required = true;
                    idInput.placeholder = 'Enter custom ID number';
                } else {
                    idInput.setAttribute('readonly', 'readonly');
                    idInput.required = false;
                    idInput.value = '';
                    idInput.placeholder = 'e.g. {{ $previewIdVal }}';
                }
            });
        }
    });
</script>
@endsection
@endsection
