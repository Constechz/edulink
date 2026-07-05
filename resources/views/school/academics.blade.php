@extends('layouts.app')

@section('title', 'Academic Structure | EduLink')
@section('header_title', 'Academic Calendar & Structures')

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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0 font-weight-bold" style="font-weight: 700;">Academic Structures Configurator</h5>
            <p class="text-muted mb-0 small">Define academic calendars, core curricula programmes, classrooms, and streams.</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="glass-card p-4">
        <ul class="nav nav-tabs nav-fill mb-4" id="academicTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active font-weight-bold" id="years-tab" data-bs-toggle="tab" data-bs-target="#years" type="button" role="tab" aria-controls="years" aria-selected="true">
                    <i class="bi bi-calendar-event me-2"></i>Academic Years
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link font-weight-bold" id="terms-tab" data-bs-toggle="tab" data-bs-target="#terms" type="button" role="tab" aria-controls="terms" aria-selected="false">
                    <i class="bi bi-calendar-range me-2"></i>Terms / Semesters
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link font-weight-bold" id="departments-tab" data-bs-toggle="tab" data-bs-target="#departments" type="button" role="tab" aria-controls="departments" aria-selected="false">
                    <i class="bi bi-briefcase me-2"></i>Departments
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link font-weight-bold" id="programmes-tab" data-bs-toggle="tab" data-bs-target="#programmes" type="button" role="tab" aria-controls="programmes" aria-selected="false">
                    <i class="bi bi-award me-2"></i>Programmes
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link font-weight-bold" id="classes-tab" data-bs-toggle="tab" data-bs-target="#classes" type="button" role="tab" aria-controls="classes" aria-selected="false">
                    <i class="bi bi-mortarboard me-2"></i>Classes
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link font-weight-bold" id="streams-tab" data-bs-toggle="tab" data-bs-target="#streams" type="button" role="tab" aria-controls="streams" aria-selected="false">
                    <i class="bi bi-grid-3x3-gap me-2"></i>Streams
                </button>
            </li>
        </ul>

        <div class="tab-content" id="academicTabsContent">
            
            <!-- TAB 1: ACADEMIC YEARS -->
            <div class="tab-pane fade show active" id="years" role="tabpanel" aria-labelledby="years-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold mb-0">Academic Years List</h6>
                    <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addYearModal" style="border-radius: 8px;">
                        <i class="bi bi-plus-circle me-1"></i>New Year
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Year Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($academicYears as $year)
                                <tr>
                                    <td class="font-weight-bold" style="font-weight: 600;">{{ $year->name }}</td>
                                    <td>{{ $year->start_date ? $year->start_date->format('d M Y') : 'N/A' }}</td>
                                    <td>{{ $year->end_date ? $year->end_date->format('d M Y') : 'N/A' }}</td>
                                    <td>
                                        @if($year->is_current)
                                            <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Current / Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary px-2.5 py-1.5 rounded-3 fw-bold" style="font-size: 0.76rem;" data-bs-toggle="modal" data-bs-target="#editYearModal{{ $year->id }}">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No academic years set up.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: TERMS -->
            <div class="tab-pane fade" id="terms" role="tabpanel" aria-labelledby="terms-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold mb-0">Configured Terms / Semesters</h6>
                    <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addTermModal" style="border-radius: 8px;">
                        <i class="bi bi-plus-circle me-1"></i>Configure Term
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Term Name</th>
                                <th>Academic Year</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Reopening</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($terms as $term)
                                <tr>
                                    <td class="font-weight-bold" style="font-weight: 600;">{{ $term->name }}</td>
                                    <td>{{ $term->academicYear->name ?? 'N/A' }}</td>
                                    <td>{{ $term->start_date ? $term->start_date->format('d M Y') : 'N/A' }}</td>
                                    <td>{{ $term->end_date ? $term->end_date->format('d M Y') : 'N/A' }}</td>
                                    <td>{{ $term->reopening_date ? $term->reopening_date->format('d M Y') : 'N/A' }}</td>
                                    <td>
                                        @if($term->is_current)
                                            <span class="badge bg-success">Active Term</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary px-2.5 py-1.5 rounded-3 fw-bold" style="font-size: 0.76rem;" data-bs-toggle="modal" data-bs-target="#editTermModal{{ $term->id }}">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">No terms configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB: DEPARTMENTS -->
            <div class="tab-pane fade" id="departments" role="tabpanel" aria-labelledby="departments-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold mb-0">Academic & Faculty Departments</h6>
                    <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addDepartmentModal" style="border-radius: 8px;">
                        <i class="bi bi-plus-circle me-1"></i>New Department
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Department Name</th>
                                <th>Code</th>
                                <th>Head of Department (HOD)</th>
                                <th>Programmes Count</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departments as $dept)
                                <tr>
                                    <td class="font-weight-bold" style="font-weight: 600;">{{ $dept->name }}</td>
                                    <td><code>{{ $dept->code ?: 'N/A' }}</code></td>
                                    <td>{{ $dept->hod->name ?? 'None Assigned' }}</td>
                                    <td>{{ $dept->programmes->count() }} Programme(s)</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary px-2.5 py-1.5 rounded-3 fw-bold" style="font-size: 0.76rem;" data-bs-toggle="modal" data-bs-target="#editDepartmentModal{{ $dept->id }}">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No departments configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: PROGRAMMES -->
            <div class="tab-pane fade" id="programmes" role="tabpanel" aria-labelledby="programmes-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold mb-0">Core Academic Programmes</h6>
                    <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addProgrammeModal" style="border-radius: 8px;">
                        <i class="bi bi-plus-circle me-1"></i>New Programme
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Programme Name</th>
                                <th>Code</th>
                                <th>Duration</th>
                                <th>Level</th>
                                <th>Department</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programmes as $prog)
                                <tr>
                                    <td class="font-weight-bold" style="font-weight: 600;">{{ $prog->name }}</td>
                                    <td><code>{{ $prog->code }}</code></td>
                                    <td>{{ $prog->duration_years }} Year(s)</td>
                                    <td>{{ $prog->level }}</td>
                                    <td>{{ $prog->department->name ?? 'None' }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary px-2.5 py-1.5 rounded-3 fw-bold" style="font-size: 0.76rem;" data-bs-toggle="modal" data-bs-target="#editProgrammeModal{{ $prog->id }}">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No programmes setup.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 4: CLASSES -->
            <div class="tab-pane fade" id="classes" role="tabpanel" aria-labelledby="classes-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold mb-0">Registered Classrooms</h6>
                    <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addClassModal" style="border-radius: 8px;">
                        <i class="bi bi-plus-circle me-1"></i>Create Class
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Class Name</th>
                                <th>Level</th>
                                <th>Programme</th>
                                <th>Campus</th>
                                <th>Capacity</th>
                                <th>Class Teacher</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes as $cls)
                                <tr>
                                    <td class="font-weight-bold" style="font-weight: 600;">{{ $cls->name }}</td>
                                    <td>Level {{ $cls->level }}</td>
                                    <td>{{ $cls->programme->name ?? 'N/A' }}</td>
                                    <td>{{ $cls->campus->name ?? 'N/A' }}</td>
                                    <td>{{ $cls->capacity }} Students</td>
                                    <td>{{ $cls->classTeacher->name ?? 'Unassigned' }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary px-2.5 py-1.5 rounded-3 fw-bold" style="font-size: 0.76rem;" data-bs-toggle="modal" data-bs-target="#editClassModal{{ $cls->id }}">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">No classrooms configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 5: STREAMS -->
            <div class="tab-pane fade" id="streams" role="tabpanel" aria-labelledby="streams-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold mb-0">Class Streams / Divisions</h6>
                    <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addStreamModal" style="border-radius: 8px;">
                        <i class="bi bi-plus-circle me-1"></i>Create Stream
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Stream Name</th>
                                <th>Parent Class</th>
                                <th>Capacity</th>
                                <th>Stream Teacher</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($streams as $strm)
                                <tr>
                                    <td class="font-weight-bold" style="font-weight: 600;">{{ $strm->name }}</td>
                                    <td>{{ $strm->class->name ?? 'N/A' }}</td>
                                    <td>{{ $strm->capacity }} Students</td>
                                    <td>{{ $strm->classTeacher->name ?? 'Unassigned' }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary px-2.5 py-1.5 rounded-3 fw-bold" style="font-size: 0.76rem;" data-bs-toggle="modal" data-bs-target="#editStreamModal{{ $strm->id }}">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No class streams configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL 1: ADD YEAR -->
    <div class="modal fade" id="addYearModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form action="{{ route('school.academics.years.store') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                @csrf
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Add Academic Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div>
                            <label class="form-label small font-weight-bold">Academic Year Label</label>
                            <input type="text" class="form-control" name="name" placeholder="e.g. 2026/2027" required>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Start Date</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">End Date</label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                        <div class="form-check form-switch ms-2 mt-3">
                            <input class="form-check-input" type="checkbox" name="is_current" value="1" checked id="yearSwitch">
                            <label class="form-check-label small font-weight-bold" for="yearSwitch">Set as Active Current Year</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Save Year</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: ADD TERM -->
    <div class="modal fade" id="addTermModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form action="{{ route('school.academics.terms.store') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                @csrf
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Configure Term / Semester</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div>
                            <label class="form-label small font-weight-bold">Academic Year Context</label>
                            <select class="form-select" name="academic_year_id" required>
                                <option value="">Select Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Term Name</label>
                            <input type="text" class="form-control" name="name" placeholder="e.g. Term 1 / Semester 2" required>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Start Date</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">End Date</label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Next Reopening Date</label>
                            <input type="date" class="form-control" name="reopening_date">
                        </div>
                        <div class="form-check form-switch ms-2 mt-3">
                            <input class="form-check-input" type="checkbox" name="is_current" value="1" checked id="termSwitch">
                            <label class="form-check-label small font-weight-bold" for="termSwitch">Set as Active Current Term</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Save Term</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: ADD DEPARTMENT -->
    <div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form action="{{ route('school.academics.departments.store') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                @csrf
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Create Academic Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div>
                            <label class="form-label small font-weight-bold">Department Name</label>
                            <input type="text" class="form-control" name="name" placeholder="e.g. Basic Education Department" required>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Code Identifier</label>
                            <input type="text" class="form-control" name="code" placeholder="e.g. BASIC-DEPT">
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Head of Department (HOD)</label>
                            <select class="form-select" name="hod_user_id">
                                <option value="">None / Assign Later</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Optional description..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Create Department</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: ADD PROGRAMME -->
    <div class="modal fade" id="addProgrammeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form action="{{ route('school.academics.programmes.store') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                @csrf
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Add Academic Programme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div>
                            <label class="form-label small font-weight-bold">Department Context</label>
                            <select class="form-select" name="department_id">
                                <option value="">None / Independent</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Programme Name</label>
                            <input type="text" class="form-control" name="name" placeholder="e.g. General Arts / Business Science" required>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Programme Code</label>
                            <input type="text" class="form-control" name="code" placeholder="e.g. G-ARTS" required>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Duration (Years)</label>
                            <input type="number" class="form-control" name="duration_years" value="3" min="1" required>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Education Level</label>
                            <select class="form-select" name="level" required>
                                <option value="Nursery">Nursery</option>
                                <option value="KG">KG</option>
                                <option value="Primary">Primary</option>
                                <option value="JHS">JHS</option>
                                <option value="SHS" selected>SHS</option>
                                <option value="TVET">TVET</option>
                                <option value="Tertiary">Tertiary</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Save Programme</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: ADD CLASS -->
    <div class="modal fade" id="addClassModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form action="{{ route('school.academics.classes.store') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                @csrf
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Create Classroom Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div>
                            <label class="form-label small font-weight-bold">Campus Branch</label>
                            <select class="form-select" name="campus_id">
                                <option value="">None / Main Campus</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Academic Year Context</label>
                            <select class="form-select" name="academic_year_id" required>
                                <option value="">Select Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Programme Context</label>
                            <select class="form-select" name="programme_id" required>
                                <option value="">Select Programme</option>
                                @foreach($programmes as $prog)
                                    <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Class Name</label>
                            <input type="text" class="form-control" name="name" placeholder="e.g. Form 1 Arts A" required>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Level Context</label>
                            <select class="form-select" name="level" required>
                                <option value="Nursery">Nursery</option>
                                <option value="KG">KG</option>
                                <option value="Primary">Primary</option>
                                <option value="JHS">JHS</option>
                                <option value="SHS" selected>SHS</option>
                                <option value="TVET">TVET</option>
                                <option value="Tertiary">Tertiary</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Class Teacher Assignment</label>
                            <select class="form-select" name="class_teacher_id">
                                <option value="">None / Assign Later</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Maximum Capacity</label>
                            <input type="number" class="form-control" name="capacity" value="40" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Create Class</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 5: ADD STREAM -->
    <div class="modal fade" id="addStreamModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form action="{{ route('school.academics.streams.store') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                @csrf
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Create Class Stream</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div>
                            <label class="form-label small font-weight-bold">Parent Class Context</label>
                            <select class="form-select" name="class_id" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $cls)
                                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Stream Name</label>
                            <input type="text" class="form-control" name="name" placeholder="e.g. Stream A / Red House" required>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Stream Teacher Assignment</label>
                            <select class="form-select" name="class_teacher_id">
                                <option value="">None / Assign Later</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Stream Capacity</label>
                            <input type="number" class="form-control" name="capacity" value="20" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Create Stream</button>
                </div>
            </form>
        </div>
    </div>

    @foreach($classes as $cls)
        <!-- MODAL: EDIT CLASS -->
        <div class="modal fade" id="editClassModal{{ $cls->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form action="{{ route('school.academics.classes.update', $cls->id) }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom-0 p-4 pb-0">
                        <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Edit Classroom Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div>
                                <label class="form-label small font-weight-bold">Campus Branch</label>
                                <select class="form-select" name="campus_id">
                                    <option value="">None / Main Campus</option>
                                    @foreach($campuses as $campus)
                                        <option value="{{ $campus->id }}" {{ $cls->campus_id == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Academic Year Context</label>
                                <select class="form-select" name="academic_year_id" required>
                                    <option value="">Select Year</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ $cls->academic_year_id == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Programme Context</label>
                                <select class="form-select" name="programme_id" required>
                                    <option value="">Select Programme</option>
                                    @foreach($programmes as $prog)
                                        <option value="{{ $prog->id }}" {{ $cls->programme_id == $prog->id ? 'selected' : '' }}>{{ $prog->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Class Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $cls->name }}" placeholder="e.g. Form 1 Arts A" required>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Level Context</label>
                                <select class="form-select" name="level" required>
                                    @foreach(['Nursery', 'KG', 'Primary', 'JHS', 'SHS', 'TVET', 'Tertiary'] as $lvl)
                                        <option value="{{ $lvl }}" {{ $cls->level == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Class Teacher Assignment</label>
                                <select class="form-select" name="class_teacher_id">
                                    <option value="">None / Assign Later</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ $cls->class_teacher_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Maximum Capacity</label>
                                <input type="number" class="form-control" name="capacity" value="{{ $cls->capacity }}" min="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4">
                        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Update Class</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    @foreach($academicYears as $year)
        <!-- MODAL: EDIT YEAR -->
        <div class="modal fade" id="editYearModal{{ $year->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form action="{{ route('school.academics.years.update', $year->id) }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom-0 p-4 pb-0">
                        <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Edit Academic Year</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div>
                                <label class="form-label small font-weight-bold">Academic Year Label / Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $year->name }}" placeholder="e.g. 2026/2027 Academic Year" required>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Start Date</label>
                                <input type="date" class="form-control" name="start_date" value="{{ $year->start_date ? $year->start_date->format('Y-m-d') : '' }}" required>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">End Date</label>
                                <input type="date" class="form-control" name="end_date" value="{{ $year->end_date ? $year->end_date->format('Y-m-d') : '' }}" required>
                            </div>
                            <div class="form-check form-switch ms-3 mt-2">
                                <input class="form-check-input" type="checkbox" name="is_current" id="edit_is_current{{ $year->id }}" value="1" {{ $year->is_current ? 'checked' : '' }}>
                                <label class="form-check-label small" for="edit_is_current{{ $year->id }}">Set as active / current academic year</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4">
                        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Update Year</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    @foreach($terms as $term)
        <!-- MODAL: EDIT TERM -->
        <div class="modal fade" id="editTermModal{{ $term->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form action="{{ route('school.academics.terms.update', $term->id) }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom-0 p-4 pb-0">
                        <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Edit Academic Term</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div>
                                <label class="form-label small font-weight-bold">Academic Year Context</label>
                                <select class="form-select" name="academic_year_id" required>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ $term->academic_year_id == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Term / Semester Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $term->name }}" placeholder="e.g. Term 1 / First Semester" required>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Start Date</label>
                                <input type="date" class="form-control" name="start_date" value="{{ $term->start_date ? $term->start_date->format('Y-m-d') : '' }}" required>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">End Date</label>
                                <input type="date" class="form-control" name="end_date" value="{{ $term->end_date ? $term->end_date->format('Y-m-d') : '' }}" required>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Next Reopening Date</label>
                                <input type="date" class="form-control" name="reopening_date" value="{{ $term->reopening_date ? $term->reopening_date->format('Y-m-d') : '' }}">
                            </div>
                            <div class="form-check form-switch ms-3 mt-2">
                                <input class="form-check-input" type="checkbox" name="is_current" id="edit_term_is_current{{ $term->id }}" value="1" {{ $term->is_current ? 'checked' : '' }}>
                                <label class="form-check-label small" for="edit_term_is_current{{ $term->id }}">Set as active term</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4">
                        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Update Term</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    @foreach($programmes as $prog)
        <!-- MODAL: EDIT PROGRAMME -->
        <div class="modal fade" id="editProgrammeModal{{ $prog->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form action="{{ route('school.academics.programmes.update', $prog->id) }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom-0 p-4 pb-0">
                        <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Edit Academic Programme</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div>
                                <label class="form-label small font-weight-bold">Department Context</label>
                                <select class="form-select" name="department_id">
                                    <option value="">None / Independent</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ $prog->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Programme Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $prog->name }}" placeholder="e.g. Primary School Programme" required>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Code Identifier</label>
                                <input type="text" class="form-control" name="code" value="{{ $prog->code }}" placeholder="e.g. BEM-PRI" required>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Duration (Years)</label>
                                <input type="number" class="form-control" name="duration_years" value="{{ $prog->duration_years }}" min="1" required>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Level Context</label>
                                <select class="form-select" name="level" required>
                                    @foreach(['Nursery', 'KG', 'Primary', 'JHS', 'SHS', 'TVET', 'Tertiary'] as $lvl)
                                        <option value="{{ $lvl }}" {{ $prog->level == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4">
                        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Update Programme</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    @foreach($streams as $strm)
        <!-- MODAL: EDIT STREAM -->
        <div class="modal fade" id="editStreamModal{{ $strm->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form action="{{ route('school.academics.streams.update', $strm->id) }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom-0 p-4 pb-0">
                        <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Edit Class Stream</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div>
                                <label class="form-label small font-weight-bold">Parent Class Context</label>
                                <select class="form-select" name="class_id" required>
                                    @foreach($classes as $cls)
                                        <option value="{{ $cls->id }}" {{ $strm->class_id == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Stream Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $strm->name }}" placeholder="e.g. Stream A" required>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Stream Teacher Assignment</label>
                                <select class="form-select" name="class_teacher_id">
                                    <option value="">None / Assign Later</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ $strm->class_teacher_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Stream Capacity</label>
                                <input type="number" class="form-control" name="capacity" value="{{ $strm->capacity }}" min="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4">
                        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Update Stream</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    @foreach($departments as $dept)
        <!-- MODAL: EDIT DEPARTMENT -->
        <div class="modal fade" id="editDepartmentModal{{ $dept->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form action="{{ route('school.academics.departments.update', $dept->id) }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom-0 p-4 pb-0">
                        <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Edit Academic Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div>
                                <label class="form-label small font-weight-bold">Department Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $dept->name }}" placeholder="e.g. Basic Education Department" required>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Code Identifier</label>
                                <input type="text" class="form-control" name="code" value="{{ $dept->code }}" placeholder="e.g. BASIC-DEPT">
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Head of Department (HOD)</label>
                                <select class="form-select" name="hod_user_id">
                                    <option value="">None / Assign Later</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ $dept->hod_user_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Description</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="Optional description...">{{ $dept->description }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4">
                        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Update Department</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

</div>
@endsection
