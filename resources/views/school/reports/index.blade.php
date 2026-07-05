@extends('layouts.app')

@section('title', 'Report Cards Hub | EduLink')
@section('header_title', 'Student Performance Reports')

@section('content')
<div class="container-fluid p-0">

    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('school.reports.themes.index') }}" class="btn btn-primary rounded-3 px-3 py-2 fw-semibold shadow-xs">
            <i class="bi bi-palette-fill me-2"></i>Report Card Themes
        </a>
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

    <div class="glass-card p-4 mb-4">
        <h5 class="font-weight-bold mb-3" style="font-weight: 700;">Select Report Scope</h5>
        <form action="{{ route('school.reports.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted font-weight-bold">Class</label>
                    <select class="form-select" name="class_id" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted font-weight-bold">Term</label>
                    <select class="form-select" name="term_id" required>
                        @foreach($terms as $t)
                            <option value="{{ $t->id }}" {{ $selectedTermId == $t->id ? 'selected' : '' }}>{{ $t->name }} {{ $t->is_current ? '(Current)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted font-weight-bold">Academic Year</label>
                    <select class="form-select" name="academic_year_id" required>
                        @foreach($academicYears as $y)
                            <option value="{{ $y->id }}" {{ $selectedYearId == $y->id ? 'selected' : '' }}>{{ $y->name }} {{ $y->is_current ? '(Current)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-filter me-1"></i>Fetch Records
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($selectedClassId)
        <div class="row g-4 mb-4">
            <!-- Class Overview & Broadsheets Actions -->
            <div class="col-md-12">
                <div class="glass-card p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h5 class="fw-bold mb-1" style="font-weight: 700; color: var(--primary-color);">
                                Class Broadsheet & Summary
                            </h5>
                            <p class="text-muted small mb-0">
                                Generate the complete subject grade overview sheet for all students in <strong>{{ $classes->find($selectedClassId)->name }}</strong>.
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('school.reports.broadsheet', ['class_id' => $selectedClassId, 'term_id' => $selectedTermId, 'academic_year_id' => $selectedYearId, 'format' => 'html']) }}" 
                               class="btn btn-outline-primary px-3 py-2" style="border-radius: 8px;">
                                <i class="bi bi-eye me-1"></i>View Broadsheet Grid
                            </a>
                            <a href="{{ route('school.reports.broadsheet', ['class_id' => $selectedClassId, 'term_id' => $selectedTermId, 'academic_year_id' => $selectedYearId, 'format' => 'pdf']) }}" 
                               class="btn btn-primary px-3 py-2" style="border-radius: 8px;">
                                <i class="bi bi-file-pdf me-1"></i>Download Broadsheet PDF
                            </a>
                            @if(count($students) > 0)
                                <a href="{{ route('school.reports.bulk-print', ['class_id' => $selectedClassId, 'term_id' => $selectedTermId, 'academic_year_id' => $selectedYearId]) }}" 
                                   target="_blank" 
                                   class="btn btn-success px-3 py-2 text-white" style="border-radius: 8px;">
                                    <i class="bi bi-printer-fill me-1"></i>Bulk Print Report Cards
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Report Cards List -->
        <div class="glass-card p-4">
            <h5 class="fw-bold mb-4" style="font-weight: 700; color: var(--primary-color);">Student Report Cards</h5>
            @if(count($students) > 0)
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 150px;">Admission No.</th>
                                <th>Student Name</th>
                                <th>Gender</th>
                                <th class="text-end" style="width: 250px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td class="fw-bold text-muted">#{{ $student->student_id_number }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $student->first_name }} {{ $student->last_name }}</div>
                                    </td>
                                    <td>{{ $student->gender }}</td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary px-3" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editReportDetails{{ $student->id }}" 
                                                    style="border-radius: 6px;">
                                                <i class="bi bi-pencil-square me-1"></i>Edit Remarks
                                            </button>
                                            <a href="{{ route('school.reports.card', ['student' => $student->id, 'term_id' => $selectedTermId, 'academic_year_id' => $selectedYearId]) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-outline-success px-3" 
                                               style="border-radius: 6px;">
                                                <i class="bi bi-file-pdf me-1"></i>Stream Report
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-mortarboard display-4 d-block mb-3"></i>
                        <p class="mb-0">No students found in this class.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Report Details Modals (Placed at root scope to prevent container clipping) -->
        @foreach($students as $student)
            @php
                $detail = $reportDetails->get($student->id);
            @endphp
            <div class="modal fade" id="editReportDetails{{ $student->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                    <form action="{{ route('school.reports.details.store', $student->id) }}" method="POST" class="modal-content border-0 shadow-lg text-start" style="border-radius: 16px;">
                        @csrf
                        <input type="hidden" name="term_id" value="{{ $selectedTermId }}">
                        <input type="hidden" name="academic_year_id" value="{{ $selectedYearId }}">
                        
                        <div class="modal-header border-bottom p-4 pb-3">
                            <h5 class="modal-title fw-bold text-dark mb-0" style="font-weight: 700;">
                                <i class="bi bi-card-text text-primary me-2"></i>Edit Remarks & Report Details
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        <div class="modal-body p-4">
                            <div class="alert alert-info border-0 small mb-4 p-3 d-flex align-items-center gap-2" style="border-radius: 8px; background-color: rgba(13, 110, 253, 0.08); color: var(--primary-color);">
                                <i class="bi bi-person-fill fs-5"></i>
                                <div>
                                    <strong>Student Profile:</strong> {{ $student->first_name }} {{ $student->last_name }} (#{{ $student->student_id_number }})
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Conduct</label>
                                    <input type="text" class="form-control rounded-3 py-2 text-dark" name="conduct" value="{{ $detail ? $detail->conduct : 'Respectful' }}" placeholder="e.g. Respectful">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Attitude</label>
                                    <input type="text" class="form-control rounded-3 py-2 text-dark" name="attitude" value="{{ $detail ? $detail->attitude : 'Hardworking' }}" placeholder="e.g. Hardworking">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-secondary small">Interest</label>
                                    <input type="text" class="form-control rounded-3 py-2 text-dark" name="interest" value="{{ $detail ? $detail->interest : 'Reading' }}" placeholder="e.g. Reading & Writing">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-secondary small">Class Teacher's Remarks</label>
                                    <textarea class="form-control rounded-3 text-dark" name="remarks" rows="2" placeholder="e.g. Keep it up">{{ $detail ? $detail->remarks : 'Keep it up' }}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-secondary small">Reopening Date</label>
                                    <input type="date" class="form-control rounded-3 py-2 text-dark" name="reopening_date" value="{{ $detail && $detail->reopening_date ? $detail->reopening_date->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Attendance Present</label>
                                    <input type="number" class="form-control rounded-3 py-2 text-dark" name="attendance_present" value="{{ $detail ? $detail->attendance_present : '' }}" placeholder="e.g. 85">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Attendance Out Of</label>
                                    <input type="number" class="form-control rounded-3 py-2 text-dark" name="attendance_total" value="{{ $detail ? $detail->attendance_total : '' }}" placeholder="e.g. 90">
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer border-top p-3 bg-light d-flex justify-content-end gap-2" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px; background-color: var(--primary-color); border: none;">Save Details</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @else
        <div class="glass-card p-5 text-center text-muted">
            <i class="bi bi-file-earmark-bar-graph display-3 d-block mb-3 text-secondary"></i>
            <h5 class="fw-bold">No Records Loaded</h5>
            <p class="mb-0">Please select class filters above to display available student report card links.</p>
        </div>
    @endif

</div>
@endsection
