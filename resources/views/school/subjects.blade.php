@extends('layouts.app')

@section('title', 'Subjects & Allocations | EduLink')
@section('header_title', 'Curriculum & Subject Allocations')

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

    .subject-row, .alloc-row {
        transition: background-color 0.2s ease;
    }

    .subject-row:hover, .alloc-row:hover {
        background-color: rgba(0, 51, 102, 0.02) !important;
    }

    .avatar-circle-sm {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
    }

    .code-badge {
        font-family: var(--font-sans), monospace;
        font-weight: 700;
        letter-spacing: 0.5px;
        font-size: 0.76rem;
        background-color: rgba(0, 51, 102, 0.08);
        color: var(--primary-color);
        border: 1px solid rgba(0, 51, 102, 0.1);
        border-radius: 6px;
        padding: 0.25rem 0.5rem;
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

    <!-- Actions Header Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 font-weight-bold" style="font-weight: 700; color: var(--primary-color);">Subject & Teacher Registry</h4>
            <p class="text-muted small mb-0">Register academic subjects and allocate subject tutors to classrooms.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary px-3 py-2.5 d-inline-flex align-items-center gap-2 fw-bold" data-bs-toggle="modal" data-bs-target="#addSubjectModal" style="border-radius: 12px; font-size: 0.85rem;">
                <i class="bi bi-plus-circle-fill"></i> Create New Subject
            </button>
            <button class="btn btn-primary px-4 py-2.5 d-inline-flex align-items-center gap-2 fw-bold" data-bs-toggle="modal" data-bs-target="#allocateTeacherModal" style="border-radius: 12px; background-color: var(--primary-color); border: none; font-size: 0.85rem;">
                <i class="bi bi-person-fill-add"></i> Allocate Teacher
            </button>
        </div>
    </div>

    <!-- Quick Stats Cards Section -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(0, 51, 102, 0.1) !important;">
                <div>
                    <span class="text-muted small d-block">Total Subjects</span>
                    <span class="fs-3 fw-bold" style="color: var(--primary-color);">{{ count($subjects) }}</span>
                </div>
                <div class="fs-2 text-primary bg-primary bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-book-half"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(25, 135, 84, 0.1) !important;">
                <div>
                    <span class="text-muted small d-block">Tutor Allocations</span>
                    <span class="fs-3 fw-bold text-success">{{ count($allocations) }}</span>
                </div>
                <div class="fs-2 text-success bg-success bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-person-workspace"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(255, 215, 0, 0.15) !important;">
                <div>
                    <span class="text-muted small d-block">Assigned Tutors</span>
                    <span class="fs-3 fw-bold text-warning" style="color: #b08d00 !important;">{{ $allocations->unique('teacher_id')->count() }}</span>
                </div>
                <div class="fs-2 text-warning bg-warning bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; color: #b08d00 !important;">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Panels Split -->
    <div class="row g-4 mb-5">
        
        <!-- PANEL 1: SUBJECT DIRECTORY (Left 5 Cols) -->
        <div class="col-lg-5">
            <div class="glass-card p-4 h-100 d-flex flex-column" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.05);">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h5 class="fw-bold mb-0 text-dark" style="font-weight: 700;"><i class="bi bi-journals text-primary me-2"></i>Subject Directory</h5>
                    
                    <!-- Subject live search filter input -->
                    <div class="position-relative" style="width: 180px;">
                        <span class="position-absolute top-50 start-0 translate-middle-y ps-2.5 text-muted small"><i class="bi bi-search"></i></span>
                        <input type="text" id="subject-search" class="form-control form-control-sm rounded-3 ps-5" placeholder="Search subjects..." style="font-size: 0.8rem;">
                    </div>
                </div>

                <div class="table-responsive flex-grow-1" style="max-height: 550px; overflow-y: auto;">
                    <table class="table table-hover align-middle" style="font-size: 0.88rem;">
                        <thead>
                            <tr class="table-light">
                                <th class="border-0 rounded-start">Code</th>
                                <th class="border-0">Subject Name</th>
                                <th class="border-0">Type</th>
                                <th class="border-0">Level</th>
                                <th class="border-0 rounded-end text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="subject-table-body">
                            @forelse($subjects as $subj)
                                <tr class="subject-row" data-name="{{ strtolower($subj->name) }}" data-code="{{ strtolower($subj->code) }}">
                                    <td><span class="code-badge">{{ $subj->code }}</span></td>
                                    <td class="fw-bold text-dark">{{ $subj->name }}</td>
                                    <td>
                                        @if($subj->is_core)
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1" style="border-radius: 6px; font-size: 0.72rem;">Core</span>
                                        @else
                                            <span class="badge bg-info bg-opacity-10 text-info px-2 py-1" style="border-radius: 6px; font-size: 0.72rem;">Elective</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1" style="border-radius: 6px; font-size: 0.72rem;">{{ $subj->level }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1.5">
                                            <button class="btn btn-sm btn-outline-primary edit-subject-btn p-1 d-inline-flex align-items-center justify-content-center rounded-3"
                                                style="width: 28px; height: 28px;"
                                                data-id="{{ $subj->id }}"
                                                data-name="{{ $subj->name }}"
                                                data-code="{{ $subj->code }}"
                                                data-level="{{ $subj->level }}"
                                                data-type="{{ $subj->is_core ? 'core' : 'elective' }}"
                                                data-department="{{ $subj->department_id }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editSubjectModal"
                                                title="Edit Subject">
                                                <i class="bi bi-pencil-square" style="font-size: 0.85rem;"></i>
                                            </button>
                                            <form action="{{ route('school.subjects.destroy', $subj->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger p-1 d-inline-flex align-items-center justify-content-center rounded-3" style="width: 28px; height: 28px;" title="Delete Subject">
                                                    <i class="bi bi-trash" style="font-size: 0.85rem;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-journal-x display-6 d-block mb-2 text-warning"></i>
                                        <p class="mb-0">No subjects registered yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PANEL 2: TEACHER ALLOCATIONS (Right 7 Cols) -->
        <div class="col-lg-7">
            <div class="glass-card p-4 h-100 d-flex flex-column" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.05);">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h5 class="fw-bold mb-0 text-dark" style="font-weight: 700;"><i class="bi bi-person-workspace text-success me-2"></i>Teacher Class Allocations</h5>
                    
                    <!-- Class filter dropdown list -->
                    <div style="width: 200px;">
                        <select id="class-filter" class="form-select form-select-sm rounded-3" style="font-size: 0.8rem;">
                            <option value="all">All Classrooms</option>
                            @foreach($classes as $cls)
                                <option value="class-{{ $cls->id }}">{{ $cls->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="table-responsive flex-grow-1" style="max-height: 550px; overflow-y: auto;">
                    <table class="table table-hover align-middle" style="font-size: 0.88rem;">
                        <thead>
                            <tr class="table-light">
                                <th class="border-0 rounded-start">Class & Stream</th>
                                <th class="border-0">Subject</th>
                                <th class="border-0">Assigned Teacher</th>
                                <th class="border-0">Periods/Wk</th>
                                <th class="border-0 rounded-end text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="allocation-table-body">
                            @forelse($allocations as $alloc)
                                <tr class="alloc-row" data-class="class-{{ $alloc->class_id }}">
                                    <td class="fw-bold text-dark">
                                        <i class="bi bi-folder2-open text-muted me-1.5"></i>
                                        {{ $alloc->class->name ?? 'N/A' }} 
                                        @if($alloc->stream)
                                            <span class="text-secondary small">({{ $alloc->stream->name }})</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold text-secondary">{{ $alloc->subject->name ?? 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle-sm bg-success bg-opacity-10 text-success">
                                                {{ substr($alloc->teacher->name ?? 'T', 0, 1) }}
                                            </div>
                                            <span class="fw-semibold text-dark">{{ $alloc->teacher->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-2.5 py-1.5 font-weight-medium" style="border-radius: 8px; font-size: 0.74rem;">
                                            <i class="bi bi-clock-history me-1"></i>{{ $alloc->periods_per_week }} Periods
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1.5">
                                            <button class="btn btn-sm btn-outline-primary edit-allocation-btn p-1 d-inline-flex align-items-center justify-content-center rounded-3"
                                                style="width: 28px; height: 28px;"
                                                data-id="{{ $alloc->id }}"
                                                data-class="{{ $alloc->class_id }}"
                                                data-stream="{{ $alloc->stream_id }}"
                                                data-subject="{{ $alloc->subject_id }}"
                                                data-teacher="{{ $alloc->teacher_id }}"
                                                data-periods="{{ $alloc->periods_per_week }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editAllocationModal"
                                                title="Edit Allocation">
                                                <i class="bi bi-pencil-square" style="font-size: 0.85rem;"></i>
                                            </button>
                                            <form action="{{ route('school.subjects.allocate.destroy', $alloc->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this teacher allocation?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger p-1 d-inline-flex align-items-center justify-content-center rounded-3" style="width: 28px; height: 28px;" title="Delete Allocation">
                                                    <i class="bi bi-trash" style="font-size: 0.85rem;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-person-x display-6 d-block mb-2 text-warning"></i>
                                        <p class="mb-0">No teacher allocations configured.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL 1: CREATE SUBJECT -->
    <div class="modal fade" id="addSubjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold text-dark" style="font-weight: 700;"><i class="bi bi-plus-circle-fill text-primary me-2"></i>Create Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('school.subjects.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Department Context</label>
                                <select class="form-select rounded-3 py-2" name="department_id">
                                    <option value="">None / Independent</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Subject Name</label>
                                <input type="text" class="form-control rounded-3 py-2" name="name" placeholder="e.g. Core Mathematics" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Subject Code</label>
                                <input type="text" class="form-control rounded-3 py-2" name="code" placeholder="e.g. C-MATH" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">Level Context</label>
                                <select class="form-select rounded-3 py-2" name="level" required>
                                    <option value="Nursery">Nursery</option>
                                    <option value="KG">KG</option>
                                    <option value="Primary">Primary</option>
                                    <option value="JHS">JHS</option>
                                    <option value="SHS" selected>SHS</option>
                                    <option value="TVET">TVET</option>
                                    <option value="Tertiary">Tertiary</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">Subject Category</label>
                                <select class="form-select rounded-3 py-2" name="type" required>
                                    <option value="core">Core Subject</option>
                                    <option value="elective">Elective Subject</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px; background-color: var(--primary-color); border: none;">Create Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: ALLOCATE TEACHER -->
    <div class="modal fade" id="allocateTeacherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold text-dark" style="font-weight: 700;"><i class="bi bi-person-fill-add text-primary me-2"></i>Allocate Subject Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('school.subjects.allocate') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Classroom Class</label>
                                <select class="form-select rounded-3 py-2" name="class_id" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $cls)
                                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Class Stream (Optional)</label>
                                <select class="form-select rounded-3 py-2" name="stream_id">
                                    <option value="">Allocate to Entire Class</option>
                                    @foreach($streams as $strm)
                                        <option value="{{ $strm->id }}">{{ $strm->name }} ({{ $strm->class->name ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Subject</label>
                                <select class="form-select rounded-3 py-2" name="subject_id" required>
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subj)
                                        <option value="{{ $subj->id }}">{{ $subj->name }} [{{ $subj->code }}]</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold text-secondary small">Teacher</label>
                                <select class="form-select rounded-3 py-2" name="teacher_id" required>
                                    <option value="">Select Teacher</option>
                                    @foreach($teachers as $teach)
                                        <option value="{{ $teach->id }}">{{ $teach->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small">Periods/Wk</label>
                                <input type="number" class="form-control rounded-3 py-2" name="periods_per_week" value="4" min="1" max="40" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px; background-color: var(--primary-color); border: none;">Allocate Teacher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 3: EDIT SUBJECT -->
    <div class="modal fade" id="editSubjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold text-dark" style="font-weight: 700;"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Subject Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editSubjectForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Department Context</label>
                                <select class="form-select rounded-3 py-2" name="department_id" id="edit_department_id">
                                    <option value="">None / Independent</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Subject Name</label>
                                <input type="text" class="form-control rounded-3 py-2" name="name" id="edit_name" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Subject Code</label>
                                <input type="text" class="form-control rounded-3 py-2" name="code" id="edit_code" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">Level Context</label>
                                <select class="form-select rounded-3 py-2" name="level" id="edit_level" required>
                                    <option value="Nursery">Nursery</option>
                                    <option value="KG">KG</option>
                                    <option value="Primary">Primary</option>
                                    <option value="JHS">JHS</option>
                                    <option value="SHS">SHS</option>
                                    <option value="TVET">TVET</option>
                                    <option value="Tertiary">Tertiary</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">Subject Category</label>
                                <select class="form-select rounded-3 py-2" name="type" id="edit_type" required>
                                    <option value="core">Core Subject</option>
                                    <option value="elective">Elective Subject</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px; background-color: var(--primary-color); border: none;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 4: EDIT ALLOCATION -->
    <div class="modal fade" id="editAllocationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold text-dark" style="font-weight: 700;"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Teacher Allocation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editAllocationForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Classroom Class</label>
                                <select class="form-select rounded-3 py-2" name="class_id" id="edit_alloc_class_id" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $cls)
                                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Class Stream (Optional)</label>
                                <select class="form-select rounded-3 py-2" name="stream_id" id="edit_alloc_stream_id">
                                    <option value="">Allocate to Entire Class</option>
                                    @foreach($streams as $strm)
                                        <option value="{{ $strm->id }}">{{ $strm->name }} ({{ $strm->class->name ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">Subject</label>
                                <select class="form-select rounded-3 py-2" name="subject_id" id="edit_alloc_subject_id" required>
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subj)
                                        <option value="{{ $subj->id }}">{{ $subj->name }} [{{ $subj->code }}]</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold text-secondary small">Teacher</label>
                                <select class="form-select rounded-3 py-2" name="teacher_id" id="edit_alloc_teacher_id" required>
                                    <option value="">Select Teacher</option>
                                    @foreach($teachers as $teach)
                                        <option value="{{ $teach->id }}">{{ $teach->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small">Periods/Wk</label>
                                <input type="number" class="form-control rounded-3 py-2" name="periods_per_week" id="edit_alloc_periods" min="1" max="40" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px; background-color: var(--primary-color); border: none;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Subject Live Search Filter
        const subjectSearch = document.getElementById('subject-search');
        const subjectRows = document.querySelectorAll('.subject-row');

        if (subjectSearch) {
            subjectSearch.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                
                subjectRows.forEach(row => {
                    const name = row.getAttribute('data-name');
                    const code = row.getAttribute('data-code');
                    
                    if (name.includes(query) || code.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Classroom Allocation Filter
        const classFilter = document.getElementById('class-filter');
        const allocRows = document.querySelectorAll('.alloc-row');

        if (classFilter) {
            classFilter.addEventListener('change', function() {
                const targetClass = this.value;
                
                allocRows.forEach(row => {
                    const rowClass = row.getAttribute('data-class');
                    
                    if (targetClass === 'all' || rowClass === targetClass) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Edit Subject Modal Trigger Form Setup
        const editButtons = document.querySelectorAll('.edit-subject-btn');
        const editForm = document.getElementById('editSubjectForm');
        
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const code = this.getAttribute('data-code');
                const level = this.getAttribute('data-level');
                const type = this.getAttribute('data-type');
                const deptId = this.getAttribute('data-department');
                
                // Update form action dynamically
                editForm.setAttribute('action', `/school/subjects/${id}`);
                
                // Set fields
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_code').value = code;
                document.getElementById('edit_level').value = level;
                document.getElementById('edit_type').value = type;
                document.getElementById('edit_department_id').value = deptId || '';
            });
        });

        // Edit Allocation Modal Trigger Form Setup
        const editAllocButtons = document.querySelectorAll('.edit-allocation-btn');
        const editAllocForm = document.getElementById('editAllocationForm');
        
        editAllocButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const classId = this.getAttribute('data-class');
                const streamId = this.getAttribute('data-stream');
                const subjectId = this.getAttribute('data-subject');
                const teacherId = this.getAttribute('data-teacher');
                const periods = this.getAttribute('data-periods');
                
                // Update form action
                editAllocForm.setAttribute('action', `/school/subjects/allocate/${id}`);
                
                // Set fields
                document.getElementById('edit_alloc_class_id').value = classId;
                document.getElementById('edit_alloc_stream_id').value = streamId || '';
                document.getElementById('edit_alloc_subject_id').value = subjectId;
                document.getElementById('edit_alloc_teacher_id').value = teacherId;
                document.getElementById('edit_alloc_periods').value = periods;
            });
        });
    });
</script>
@endsection
