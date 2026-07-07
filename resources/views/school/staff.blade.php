@extends('layouts.app')

@section('title', 'Staff Directory | EduLink')
@section('header_title', 'Staff Account Management')

@section('content')
<style>
    .btn-filter {
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        color: var(--text-muted);
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    
    .btn-filter:hover {
        background-color: rgba(0, 51, 102, 0.05);
        color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-filter.active {
        background-color: var(--primary-color);
        color: #fff !important;
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(0, 51, 102, 0.25);
    }

    .staff-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .staff-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06) !important;
        border-color: rgba(0, 51, 102, 0.15) !important;
    }

    .avatar-circle {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.03);
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
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
        animation: pulse-group 3s infinite;
    }

    @keyframes pulse-group {
        0% { transform: scale(1); opacity: 0.8; }
        50% { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(1); opacity: 0.8; }
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

    @media (max-width: 575.98px) {
        .btn-responsive {
            padding: 0.5rem 0.85rem !important;
            font-size: 0.85rem !important;
            gap: 0.25rem !important;
            border-radius: 8px !important;
        }
        .actions-wrapper {
            flex-direction: column;
            align-items: stretch !important;
            width: 100%;
        }
        .actions-wrapper .btn,
        .actions-wrapper a {
            width: 100%;
            justify-content: center;
        }
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

    <!-- Actions Header Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 font-weight-bold" style="font-weight: 700; color: var(--primary-color);">Staff Account Directory</h4>
            <p class="text-muted small mb-0">Create, edit, assign roles, and activate/deactivate staff credentials.</p>
        </div>
        <div class="d-flex align-items-center gap-2 actions-wrapper">
            <a href="{{ route('school.staff.print-pdf') }}" target="_blank" class="btn btn-outline-secondary px-3 py-2.5 d-inline-flex align-items-center gap-2 btn-responsive" style="border-radius: 12px; font-weight: 600;">
                <i class="bi bi-file-pdf"></i> Print Staff List
            </a>
            <button class="btn btn-outline-primary px-3 py-2.5 d-inline-flex align-items-center gap-2 btn-responsive" data-bs-toggle="modal" data-bs-target="#importExportStaffModal" style="border-radius: 12px; font-weight: 600;">
                <i class="bi bi-arrow-down-up"></i> Import / Export
            </button>
            <button class="btn btn-primary px-4 py-2.5 d-inline-flex align-items-center gap-2 btn-responsive" data-bs-toggle="modal" data-bs-target="#addStaffModal" style="border-radius: 12px; background-color: var(--primary-color); border: none; font-weight: 600;">
                <i class="bi bi-person-plus-fill"></i> Register Staff Member
            </button>
        </div>
    </div>

    <!-- Quick Stats Cards Section -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(0, 51, 102, 0.1) !important;">
                <div>
                    <span class="text-muted small d-block">Registered Staff</span>
                    <span class="fs-3 fw-bold" style="color: var(--primary-color);">{{ count($staffMembers) }}</span>
                </div>
                <div class="fs-2 text-primary bg-primary bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(25, 135, 84, 0.1) !important;">
                <div>
                    <span class="text-muted small d-block">Active Logins</span>
                    <span class="fs-3 fw-bold text-success">{{ $staffMembers->filter(fn($s) => $s->user && $s->user->is_active)->count() }}</span>
                </div>
                <div class="fs-2 text-success bg-success bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-shield-check"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(255, 215, 0, 0.15) !important;">
                <div>
                    <span class="text-muted small d-block">Campus Branches</span>
                    <span class="fs-3 fw-bold text-warning" style="color: #b08d00 !important;">{{ $campuses->count() }}</span>
                </div>
                <div class="fs-2 text-warning bg-warning bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; color: #b08d00 !important;">
                    <i class="bi bi-buildings"></i>
                </div>
            </div>
        </div>
    </div>

    @if(count($staffMembers) > 0)
        <!-- Interactive Role Filter Pills -->
        <div class="d-flex flex-wrap gap-2 mb-4">
            <button class="btn btn-sm btn-filter active px-3 py-2 rounded-3 fw-bold" data-role="all">All Roles</button>
            @foreach($roles as $role)
                <button class="btn btn-sm btn-filter px-3 py-2 rounded-3 fw-bold" data-role="{{ $role->slug }}">{{ $role->name }}</button>
            @endforeach
        </div>

        <!-- Staff Cards Directory Grid -->
        <div class="row g-4 mb-5" id="staff-grid">
            @foreach($staffMembers as $staff)
                @php
                    $user = $staff->user;
                    $isActive = $user ? $user->is_active : false;
                    $roleSlug = $user && $user->role ? $user->role->slug : 'no-role';
                    $roleName = $user && $user->role ? $user->role->name : 'No Role';
                @endphp
                <div class="col-md-6 col-lg-4 staff-item-card" data-role="{{ $roleSlug }}">
                    <div class="glass-card staff-card p-4 h-100 d-flex flex-column position-relative overflow-hidden" style="border: 1px solid rgba(0,0,0,0.06); background: #ffffff;">
                        
                        <!-- Main Card Header Information -->
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="avatar-circle bg-primary bg-opacity-10 text-primary">
                                {{ strtoupper(substr($staff->user->name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="overflow-hidden" style="flex: 1;">
                                <h5 class="fw-bold mb-0 text-dark text-truncate" style="font-weight: 700; font-size: 1.1rem;" title="{{ $staff->user->name ?? 'Deleted User' }}">
                                    {{ $staff->user->name ?? 'Deleted User' }}
                                </h5>
                                <span class="text-muted small fw-semibold d-block">ID: {{ $staff->staff_number ?: 'N/A' }}</span>
                            </div>
                        </div>

                        <!-- Info tag pills group -->
                        <div class="d-flex flex-wrap gap-1.5 mb-3">
                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-2.5 py-1.5 font-weight-medium" style="border-radius: 8px; font-size: 0.75rem;">
                                <i class="bi bi-tag-fill me-1"></i>{{ $staff->designation }}
                            </span>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1.5 font-weight-medium" style="border-radius: 8px; font-size: 0.75rem;">
                                <i class="bi bi-shield-lock-fill me-1"></i>{{ $roleName }}
                            </span>
                            <span class="badge border border-secondary border-opacity-25 text-secondary px-2.5 py-1.5 font-weight-medium" style="border-radius: 8px; font-size: 0.75rem; background-color: #f8fafc;">
                                <i class="bi bi-geo-alt-fill me-1"></i>{{ $staff->campus->name ?? 'Global' }}
                            </span>
                            @php
                                $assignedStream = $allStreams->where('class_teacher_id', $staff->user->id)->first();
                            @endphp
                            @if($assignedStream)
                                <span class="badge bg-info bg-opacity-10 text-info px-2.5 py-1.5 font-weight-medium" style="border-radius: 8px; font-size: 0.75rem;">
                                    <i class="bi bi-mortarboard-fill me-1"></i>Class: {{ $assignedStream->class->name }} - {{ $assignedStream->name }}
                                </span>
                            @endif
                        </div>

                        <!-- Contact specifications -->
                        <div class="mb-4 flex-grow-1">
                            <div class="d-flex align-items-center gap-2 text-secondary mb-1.5" style="font-size: 0.82rem;">
                                <i class="bi bi-envelope-fill text-muted"></i>
                                <span class="text-truncate">{{ $staff->user->email ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-secondary" style="font-size: 0.82rem;">
                                <i class="bi bi-calendar-check-fill text-muted"></i>
                                <span>Status:</span>
                                @if($isActive)
                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 d-inline-flex align-items-center gap-1.5 py-1 px-2.5 rounded-3 fw-bold" style="font-size: 0.72rem;">
                                        <span class="pulse-dot"></span> Active Login
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 py-1 px-2.5 rounded-3 fw-bold" style="font-size: 0.72rem;">
                                        Deactivated
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Card action footer panel -->
                        <div class="border-top border-light pt-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1.5">
                                <div class="d-flex align-items-center gap-1.5">
                                    <!-- Toggle status button -->
                                    <form action="{{ route('school.staff.toggle', $staff->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning d-inline-flex align-items-center gap-1 px-2.5 py-1.5 rounded-3 shadow-xs fw-bold" style="font-size: 0.76rem; border-color: rgba(255,193,7,0.5);" title="Toggle account status">
                                            @if($isActive)
                                                <i class="bi bi-person-x-fill text-warning"></i> Suspend
                                            @else
                                                <i class="bi bi-person-check-fill text-warning"></i> Activate
                                            @endif
                                        </button>
                                    </form>

                                    <!-- HR details button -->
                                    <a href="{{ route('school.staff-hr.show', $staff->id) }}" class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1 px-2.5 py-1.5 rounded-3 shadow-xs fw-bold" style="font-size: 0.76rem; border-color: rgba(13,202,240,0.5);" title="HR profile & qualifications">
                                        <i class="bi bi-file-earmark-person"></i> HR Profile
                                    </a>
                                </div>
                                
                                <div class="d-flex align-items-center gap-1.5">
                                    <!-- Edit button modal trigger -->
                                    <button class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 px-2.5 py-1.5 rounded-3 shadow-xs fw-bold" style="font-size: 0.76rem;" data-bs-toggle="modal" data-bs-target="#editStaffModal{{ $staff->id }}" title="Edit credentials">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>

                                    <!-- Report button trigger -->
                                    <button class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center rounded-3 shadow-xs" style="width: 32px; height: 32px; padding: 0;" data-bs-toggle="modal" data-bs-target="#reportStaffModal{{ $staff->id }}" title="Report misconduct/issue">
                                        <i class="bi bi-flag-fill"></i>
                                    </button>

                                    <!-- Delete button -->
                                    <form action="{{ route('school.staff.destroy', $staff->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to permanently delete this staff member?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center rounded-3 shadow-xs" style="width: 32px; height: 32px; padding: 0;" title="Permanently delete profile">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- REPORT STAFF MODAL -->
                        <div class="modal fade" id="reportStaffModal{{ $staff->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                                    <div class="modal-header border-bottom-0 p-4 pb-0">
                                        <h5 class="modal-title font-weight-bold text-dark" style="font-weight: 700;"><i class="bi bi-flag-fill me-2 text-danger"></i>Report Staff Incident</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('school.staff.report', $staff->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body p-4" style="max-height: 60vh; overflow-y: auto;">
                                            <p class="small text-muted mb-3">Submit a formal report or flag misconduct concerning <strong>{{ $staff->user->name ?? 'this staff member' }}</strong>.</p>
                                            
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label fw-bold text-secondary small">Report Category</label>
                                                    <select class="form-select rounded-3 py-2" name="category" required>
                                                        <option value="">Select Category</option>
                                                        <option value="Misconduct / Behavior">Misconduct / Behavior</option>
                                                        <option value="Attendance / Punctuality">Attendance / Punctuality</option>
                                                        <option value="Performance Concern">Performance Concern</option>
                                                        <option value="Security / Safeguarding Flag">Security / Safeguarding Flag</option>
                                                        <option value="Other">Other</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold text-secondary small">Severity Level</label>
                                                    <select class="form-select rounded-3 py-2" name="severity" id="report-severity-{{ $staff->id }}" required>
                                                        <option value="Low">Low</option>
                                                        <option value="Medium">Medium</option>
                                                        <option value="High">High</option>
                                                        <option value="Critical">Critical (Immediate Attention)</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold text-secondary small">Incident / Misconduct Details</label>
                                                    <textarea class="form-control rounded-3" name="description" rows="4" placeholder="Please describe the incident, including dates and any relevant context..." required></textarea>
                                                </div>
                                                
                                                <div class="col-12" id="deactivate-account-wrapper-{{ $staff->id }}" style="display: none;">
                                                    <div class="form-check form-switch p-3 bg-danger-subtle rounded-3 border border-danger border-opacity-10">
                                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="deactivate_account" value="1" id="deactivateCheck{{ $staff->id }}">
                                                        <label class="form-check-label fw-bold text-danger small" for="deactivateCheck{{ $staff->id }}">
                                                            <i class="bi bi-shield-lock-fill me-1"></i>Deactivate staff portal login immediately
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 p-4 pt-0">
                                            <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                                            <button type="submit" class="btn btn-danger px-4" style="border-radius: 8px; border: none;">Submit Report</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Premium Redesigned Empty State -->
        <div class="glass-card p-5 text-center text-muted d-flex flex-column align-items-center justify-content-center" style="border-radius: 20px; border: 1px dashed rgba(0, 51, 102, 0.2); background: #ffffff;">
            <div class="empty-state-icon text-primary bg-primary bg-opacity-10 p-4 rounded-circle mb-4 d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                <i class="bi bi-people display-4"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">No Staff Members Registered</h4>
            <p class="mb-1 text-secondary">Start registering school administrators, teachers, registrars, and account officers.</p>
            <p class="small text-muted mb-4">Adding accounts allows users to securely log into school portals using GES-compliant controls.</p>
            <button class="btn btn-primary px-4 py-2.5 rounded-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addStaffModal" style="background-color: var(--primary-color); border: none;">
                <i class="bi bi-person-plus-fill me-2"></i> Register First Staff Member
            </button>
        </div>
    @endif

    <!-- ADD STAFF MODAL -->
    <div class="modal fade" id="addStaffModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold text-dark" style="font-weight: 700;"><i class="bi bi-person-plus-fill me-2 text-primary"></i>Register Staff Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('school.staff.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4" style="max-height: 60vh; overflow-y: auto;">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Full Name</label>
                                <input type="text" class="form-control rounded-3 py-2" name="name" placeholder="e.g. Samuel Osei-Kofi" required autocomplete="off">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Primary Email Address (For Login)</label>
                                <input type="email" class="form-control rounded-3 py-2" name="email" placeholder="e.g. samuel.osei@school.edu.gh" required autocomplete="off">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Initial Security Password</label>
                                <input type="password" class="form-control rounded-3 py-2" name="password" placeholder="Minimum 8 characters" required autocomplete="new-password">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">Staff ID Number (Optional)</label>
                                <input type="text" class="form-control rounded-3 py-2" name="staff_number" placeholder="e.g. STF-049" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">Designation / Title</label>
                                <input type="text" class="form-control rounded-3 py-2" name="designation" placeholder="e.g. Physics Tutor" required autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">System Role</label>
                                <select class="form-select rounded-3 py-2" name="role_id" id="add-staff-role" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" data-slug="{{ $role->slug }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12" id="add-staff-class-wrapper" style="display: none;">
                                <label class="form-label fw-bold text-secondary small">Assign Class & Stream</label>
                                <select class="form-select rounded-3 py-2" name="assigned_stream_id">
                                    <option value="">Select Class Stream</option>
                                    @foreach($classes as $cls)
                                        @foreach($cls->streams as $strm)
                                            <option value="{{ $strm->id }}">{{ $cls->name }} - Stream {{ $strm->name }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
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
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px; background-color: var(--primary-color); border: none;">Register Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT STAFF MODALS -->
    @foreach($staffMembers as $staff)
        @php
            $user = $staff->user;
            $isActive = $user ? $user->is_active : false;
        @endphp
        <div class="modal fade" id="editStaffModal{{ $staff->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    <div class="modal-header border-bottom-0 p-4 pb-0">
                        <h5 class="modal-title font-weight-bold text-dark" style="font-weight: 700;"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Staff Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('school.staff.update', $staff->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4" style="max-height: 60vh; overflow-y: auto;">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold text-secondary small">Full Name</label>
                                    <input type="text" class="form-control rounded-3 py-2" name="name" value="{{ $staff->user->name ?? '' }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Staff ID Number</label>
                                    <input type="text" class="form-control rounded-3 py-2" name="staff_number" value="{{ $staff->staff_number }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Designation / Title</label>
                                    <input type="text" class="form-control rounded-3 py-2" name="designation" value="{{ $staff->designation }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">System Role</label>
                                    <select class="form-select rounded-3 py-2 edit-staff-role" name="role_id" data-staff-id="{{ $staff->id }}" required>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" data-slug="{{ $role->slug }}" {{ ($staff->user->role_id ?? null) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @php
                                    $staffStream = $allStreams->where('class_teacher_id', $staff->user->id)->first();
                                    $staffRoleSlug = $staff->user->role ? $staff->user->role->slug : '';
                                @endphp
                                <div class="col-12 edit-staff-class-wrapper" id="edit-staff-class-wrapper-{{ $staff->id }}" style="display: {{ $staffRoleSlug === 'class-teacher' ? 'block' : 'none' }};">
                                    <label class="form-label fw-bold text-secondary small">Assign Class & Stream</label>
                                    <select class="form-select rounded-3 py-2" name="assigned_stream_id">
                                        <option value="">No Class Assignment</option>
                                        @foreach($classes as $cls)
                                            @foreach($cls->streams as $strm)
                                                <option value="{{ $strm->id }}" {{ $staffStream && $staffStream->id == $strm->id ? 'selected' : '' }}>{{ $cls->name }} - Stream {{ $strm->name }}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Campus Branch</label>
                                    <select class="form-select rounded-3 py-2" name="campus_id" required>
                                        @foreach($campuses as $campus)
                                            <option value="{{ $campus->id }}" {{ $staff->campus_id == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-secondary small">Status</label>
                                    <select class="form-select rounded-3 py-2" name="is_active">
                                        <option value="1" {{ $isActive ? 'selected' : '' }}>Active Account</option>
                                        <option value="0" {{ !$isActive ? 'selected' : '' }}>Deactivated Account</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 p-4 pt-0">
                            <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px; background-color: var(--primary-color); border: none;">Save Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

</div>

<!-- Vanilla JS client-side filter for sorting staff by role -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterButtons = document.querySelectorAll('.btn-filter');
        const staffCards = document.querySelectorAll('.staff-item-card');

        filterButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Remove active classes
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Make current class active
                this.classList.add('active');

                const targetRole = this.getAttribute('data-role');

                staffCards.forEach(card => {
                    const cardRole = card.getAttribute('data-role');
                    
                    if (targetRole === 'all') {
                        card.style.display = 'block';
                        setTimeout(() => card.style.opacity = '1', 50);
                    } else {
                        if (cardRole === targetRole) {
                            card.style.display = 'block';
                            setTimeout(() => card.style.opacity = '1', 50);
                        } else {
                            card.style.opacity = '0';
                            card.style.display = 'none';
                        }
                    }
                });
            });
        });

        // Reset ADD modal form fields on hide/show to ensure they are always empty
        const addModalEl = document.getElementById('addStaffModal');
        if (addModalEl) {
            addModalEl.addEventListener('hidden.bs.modal', function () {
                const form = addModalEl.querySelector('form');
                if (form) {
                    form.reset();
                }
                const addClassWrapper = document.getElementById('add-staff-class-wrapper');
                if (addClassWrapper) {
                    addClassWrapper.style.display = 'none';
                }
            });
        }

        // Dynamic Class Assignment fields in Add Modal
        const addRoleSelect = document.getElementById('add-staff-role');
        const addClassWrapper = document.getElementById('add-staff-class-wrapper');
        if (addRoleSelect && addClassWrapper) {
            addRoleSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const slug = selectedOption.getAttribute('data-slug');
                if (slug === 'class-teacher') {
                    addClassWrapper.style.display = 'block';
                } else {
                    addClassWrapper.style.display = 'none';
                    addClassWrapper.querySelector('select').value = '';
                }
            });
        }

        // Dynamic Class Assignment fields in Edit Modals
        const editRoleSelects = document.querySelectorAll('.edit-staff-role');
        editRoleSelects.forEach(select => {
            select.addEventListener('change', function() {
                const staffId = this.getAttribute('data-staff-id');
                const selectedOption = this.options[this.selectedIndex];
                const slug = selectedOption.getAttribute('data-slug');
                const editClassWrapper = document.getElementById('edit-staff-class-wrapper-' + staffId);
                if (editClassWrapper) {
                    if (slug === 'class-teacher') {
                        editClassWrapper.style.display = 'block';
                    } else {
                        editClassWrapper.style.display = 'none';
                        editClassWrapper.querySelector('select').value = '';
                    }
                }
            });
        });

        // Dynamic Deactivate account option in Report Modals
        const severitySelects = document.querySelectorAll('select[id^="report-severity-"]');
        severitySelects.forEach(select => {
            select.addEventListener('change', function() {
                const staffId = this.id.replace('report-severity-', '');
                const wrapper = document.getElementById('deactivate-account-wrapper-' + staffId);
                if (wrapper) {
                    if (this.value === 'Critical') {
                        wrapper.style.display = 'block';
                    } else {
                        wrapper.style.display = 'none';
                        const checkbox = wrapper.querySelector('input');
                        if (checkbox) checkbox.checked = false;
                    }
                }
            });
        });
    });

    function exportStaffToCSV() {
        const staff = @json($staffMembers->map(fn($s) => [
            'Staff_Number' => $s->staff_number,
            'Name' => $s->user->name ?? 'N/A',
            'Email' => $s->user->email ?? 'N/A',
            'Phone' => $s->user->phone ?? 'N/A',
            'Designation' => $s->designation,
            'Campus' => $s->campus->name ?? 'Global'
        ]));
        
        if (staff.length === 0) {
            alert("No staff records to export.");
            return;
        }
        
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += Object.keys(staff[0]).join(",") + "\n";
        
        staff.forEach(row => {
            csvContent += Object.values(row).map(v => `"${v}"`).join(",") + "\n";
        });
        
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "staff_directory_export.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

<!-- IMPORT / EXPORT MODAL -->
<div class="modal fade" id="importExportStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Import / Export Staff List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Export Section -->
                <div class="p-3 rounded-3 mb-4" style="background: rgba(0, 51, 102, 0.05); border: 1px solid rgba(0, 51, 102, 0.1);">
                    <h6 class="fw-bold mb-2"><i class="bi bi-download me-2 text-primary"></i>Export Staff Directory</h6>
                    <p class="text-muted small mb-3">Download a CSV file containing all registered staff profiles and credentials.</p>
                    <button type="button" onclick="exportStaffToCSV()" class="btn btn-primary btn-sm w-100 py-2 fw-semibold" style="border-radius: 8px;">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>Download CSV Export
                    </button>
                </div>

                <!-- Import Section -->
                <form action="#" onsubmit="alert('Staff CSV file uploaded and parsed successfully (simulation).'); bootstrap.Modal.getInstance(document.getElementById('importExportStaffModal')).hide(); return false;">
                    <div class="p-3 rounded-3" style="background: rgba(25, 135, 84, 0.05); border: 1px solid rgba(25, 135, 84, 0.1);">
                        <h6 class="fw-bold mb-2"><i class="bi bi-upload me-2 text-success"></i>Bulk Import Staff</h6>
                        <p class="text-muted small mb-3">Upload a CSV format file with staff data to register accounts in bulk.</p>
                        
                        <div class="mb-3">
                            <label class="form-label small text-secondary fw-semibold">Select CSV File</label>
                            <input type="file" class="form-control form-control-sm" accept=".csv" required style="border-radius: 8px;">
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <a href="data:text/csv;charset=utf-8,Staff_Number%2CName%2CEmail%2CPhone%2CDesignation%2CCampus%0A" download="staff_import_template.csv" class="btn btn-outline-secondary btn-sm py-2 px-3 fw-semibold w-100" style="border-radius: 8px; font-size: 0.8rem;">
                                <i class="bi bi-download me-1"></i>Template
                            </a>
                            <button type="submit" class="btn btn-success btn-sm py-2 px-3 fw-semibold w-100" style="border-radius: 8px; font-size: 0.8rem;">
                                <i class="bi bi-check-circle me-1"></i>Upload & Import
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 p-4 pt-0">
                <button type="button" class="btn btn-secondary px-4 w-100" data-bs-dismiss="modal" style="border-radius: 8px;">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
