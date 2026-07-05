@extends('layouts.app')

@section('title', 'Score Entry Grid | EduLink')
@section('header_title', 'Student Score Sheet & Workflow')

@section('styles')
<style>
    .spreadsheet-table th {
        background-color: var(--primary-color) !important;
        color: white !important;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 0.75rem;
        border-color: rgba(255,255,255,0.08) !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .spreadsheet-table td {
        padding: 0.35rem 0.5rem;
        vertical-align: middle;
    }
    .grid-input {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 0.4rem 0.6rem;
        width: 100%;
        font-size: 0.9rem;
        font-weight: 600;
        font-family: monospace;
        transition: all 0.15s ease;
        text-align: center;
        background-color: #ffffff;
    }
    .grid-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.15);
        outline: none;
        background-color: #fffbeb;
    }
    .grid-input:disabled {
        background-color: #f8fafc;
        color: #64748b;
        border-color: #e2e8f0;
    }
    .metric-badge {
        font-size: 0.85rem;
        font-weight: 600;
        padding: 0.35rem 0.6rem;
        border-radius: 6px;
    }
    .saving-status {
        font-size: 0.8rem;
        font-weight: 700;
        transition: all 0.3s ease;
    }
    .status-banner {
        border-radius: 12px;
        font-weight: 600;
    }
    .key-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
    }

    /* Row Hover States */
    .spreadsheet-row {
        transition: background-color 0.25s ease;
    }
    .spreadsheet-row:hover {
        background-color: rgba(0, 51, 102, 0.02) !important;
    }

    /* Text Visibility and High-Contrast Overrides */
    .text-muted {
        color: #64748b !important;
    }
    .text-secondary {
        color: #475569 !important;
    }
    /* Dark Mode specific grid overrides */
    [data-bs-theme="dark"] .grid-input {
        background-color: var(--theme-toggle-bg) !important;
        color: var(--text-main) !important;
        border-color: var(--border-color) !important;
    }
    [data-bs-theme="dark"] .grid-input:focus {
        background-color: rgba(255, 255, 255, 0.05) !important;
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.4) !important;
    }
    [data-bs-theme="dark"] .grid-input:disabled {
        background-color: rgba(0, 0, 0, 0.2) !important;
        color: var(--text-muted) !important;
        border-color: rgba(255, 255, 255, 0.05) !important;
    }
    [data-bs-theme="dark"] .spreadsheet-table th {
        border-color: var(--border-color) !important;
    }
    [data-bs-theme="dark"] .spreadsheet-table td {
        border-color: var(--border-color) !important;
    }
    [data-bs-theme="dark"] .spreadsheet-row:hover {
        background-color: rgba(255, 255, 255, 0.02) !important;
    }
    [data-bs-theme="dark"] .btn-outline-dark {
        color: #f1f5f9;
        border-color: var(--border-color);
    }
    [data-bs-theme="dark"] .btn-outline-dark:hover {
        background-color: rgba(255, 255, 255, 0.05);
        color: #ffffff;
    }
    .btn-back-hub {
        background-color: #f1f5f9;
        color: #475569 !important;
        transition: all 0.2s ease;
    }
    .btn-back-hub:hover {
        background-color: #e2e8f0;
        color: #1e293b !important;
    }
    [data-bs-theme="dark"] .btn-back-hub {
        background-color: rgba(255, 255, 255, 0.05) !important;
        color: #f1f5f9 !important;
        border: 1px solid var(--border-color) !important;
    }
    [data-bs-theme="dark"] .btn-back-hub:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Selector Bar Form Card -->
    <div class="glass-card p-4 mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
        <h5 class="fw-bold mb-3 text-dark" style="font-weight: 700;"><i class="bi bi-funnel-fill text-primary me-2"></i>Select Score Sheet</h5>
        <form action="{{ route('school.scores.enter') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary small">Classroom Class</label>
                    <select class="form-select rounded-3 py-2" name="class_id" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary small">Academic Subject</label>
                    <select class="form-select rounded-3 py-2" name="subject_id" required>
                        <option value="">Select Subject</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" {{ $selectedSubjectId == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary small">Academic Term</label>
                    <select class="form-select rounded-3 py-2" name="term_id" required>
                        @foreach($terms as $t)
                            <option value="{{ $t->id }}" {{ $selectedTermId == $t->id ? 'selected' : '' }}>{{ $t->name }} {{ $t->is_current ? '(Current)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary small">Academic Year</label>
                    <select class="form-select rounded-3 py-2" name="academic_year_id" required>
                        @foreach($academicYears as $y)
                            <option value="{{ $y->id }}" {{ $selectedYearId == $y->id ? 'selected' : '' }}>{{ $y->name }} {{ $y->is_current ? '(Current)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold d-inline-flex align-items-center gap-2" style="border-radius: 10px; background-color: var(--primary-color); border: none;">
                        <i class="bi bi-search"></i> Load Score Sheet Grid
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($selectedClassId && $selectedSubjectId)
        @if(!$config)
            <!-- Config Missing Warning -->
            <div class="alert alert-warning border-0 p-4 mb-4" style="border-radius: 16px;">
                <h5 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>No Scoring Configuration Configured</h5>
                <p class="mb-2 text-secondary">There are no active scoring configurations found for this class level. You must configure component limits, weights, and rounding rules before entering scores.</p>
                <hr>
                <a href="{{ route('school.scoring-configs.create') }}" class="btn btn-warning fw-bold px-4 py-2" style="border-radius: 10px;"><i class="bi bi-magic me-1"></i>Launch Ruleset Wizard</a>
            </div>
        @else
            @php $isEditable = ($sheetStatus === 'draft') && Auth::user()->hasPermission('enter-scores'); @endphp
            
            <!-- Scoring config summary and workflow panel -->
            <div class="glass-card p-4 mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <a href="{{ route('school.scores.enter') }}" class="btn btn-sm btn-back-hub border-0 px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5 me-2" style="border-radius: 8px; font-size: 0.82rem;">
                                <i class="bi bi-arrow-left"></i>Back to Hub
                            </a>
                            <h4 class="mb-0 fw-bold text-dark" style="font-weight: 800; color: var(--primary-color);">
                                {{ $classes->find($selectedClassId)->name }} — {{ $subjects->find($selectedSubjectId)->name }}
                            </h4>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-2.5 py-1.5 fw-bold" style="border-radius: 8px;">
                                Ruleset: {{ $config->name }}
                            </span>
                        </div>
                        <p class="text-muted small mb-0">
                            Class Weight: <strong>{{ $config->class_score_weight }}%</strong> (Raw max {{ $config->class_score_max }}) |
                            Exam Weight: <strong>{{ $config->exam_score_weight }}%</strong> (Raw max {{ $config->exam_score_max }}) |
                            Rounding: <strong>{{ $config->rounding_method }}</strong>
                        </p>
                    </div>

                    <!-- Status Timeline Indicator -->
                    <div class="d-flex align-items-center gap-3">
                        <span class="saving-status text-success" id="autosaveLabel">
                            <i class="bi bi-cloud-check-fill fs-5 me-1"></i>All drafts saved
                        </span>
                        <div>
                            @if($sheetStatus === 'draft')
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 fw-bold" style="border-radius: 10px; font-size: 0.85rem;">
                                    <span class="key-indicator bg-secondary"></span>Draft Mode
                                </span>
                            @elseif($sheetStatus === 'submitted')
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 fw-bold" style="border-radius: 10px; font-size: 0.85rem;">
                                    <span class="key-indicator bg-primary"></span>Submitted to HOD
                                </span>
                            @elseif($sheetStatus === 'hod_verified')
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 fw-bold" style="border-radius: 10px; font-size: 0.85rem;">
                                    <span class="key-indicator bg-info"></span>HOD Verified
                                </span>
                            @elseif($sheetStatus === 'approved' || $sheetStatus === 'published')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 fw-bold" style="border-radius: 10px; font-size: 0.85rem;">
                                    <span class="key-indicator bg-success"></span>Approved & Published
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Workflow action buttons -->
                @if(count($students) > 0)
                    <hr class="my-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <div class="d-flex gap-2">
                                <a href="{{ route('school.scores.export', ['class_id' => $selectedClassId, 'subject_id' => $selectedSubjectId, 'term_id' => $selectedTermId, 'academic_year_id' => $selectedYearId]) }}" class="btn btn-outline-dark fw-bold" style="border-radius: 8px;">
                                    <i class="bi bi-file-earmark-arrow-down-fill me-1"></i>Export CSV
                                </a>
                                @if($isEditable)
                                    <button type="button" class="btn btn-outline-primary fw-bold" data-bs-toggle="modal" data-bs-target="#importScoresModal" style="border-radius: 8px;">
                                        <i class="bi bi-file-earmark-arrow-up-fill me-1"></i>Import CSV
                                    </button>
                                @endif
                            </div>
                            <span class="text-muted small d-none d-lg-inline">| Paste Hint: You can copy numeric data from Excel and paste (`Ctrl+V`) directly into cells.</span>
                        </div>
                        <div class="d-flex gap-2">
                            <!-- Teacher: Submit to HOD -->
                            @if($sheetStatus === 'draft' && Auth::user()->hasPermission('enter-scores'))
                                <form action="{{ route('school.scores.submit') }}" method="POST" onsubmit="return confirm('Are you sure you want to submit this complete score sheet to the HOD? This will lock it from further editing.')">
                                    @csrf
                                    <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                                    <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
                                    <input type="hidden" name="term_id" value="{{ $selectedTermId }}">
                                    <input type="hidden" name="academic_year_id" value="{{ $selectedYearId }}">
                                    <button type="submit" class="btn btn-primary fw-bold" style="border-radius: 8px; background-color: var(--primary-color); border: none;">
                                        <i class="bi bi-send-fill me-1"></i>Submit Scores to HOD
                                    </button>
                                </form>
                            @endif

                            <!-- HOD: Verify / Reject -->
                            @if($sheetStatus === 'submitted' && Auth::user()->hasPermission('verify-scores'))
                                <button type="button" class="btn btn-warning fw-bold" data-bs-toggle="collapse" data-bs-target="#hodActionPanel" style="border-radius: 8px;">
                                    <i class="bi bi-shield-check me-1"></i>HOD Review Panel
                                </button>
                            @endif

                            <!-- Headteacher: Approve / Reject -->
                            @if($sheetStatus === 'hod_verified' && Auth::user()->hasPermission('approve-scores'))
                                <button type="button" class="btn btn-success fw-bold" data-bs-toggle="collapse" data-bs-target="#headActionPanel" style="border-radius: 8px;">
                                    <i class="bi bi-patch-check-fill me-1"></i>HT Approval Panel
                                </button>
                            @endif

                            <!-- Headteacher/Admin: Unlock approved/published scores -->
                            @if(($sheetStatus === 'approved' || $sheetStatus === 'published') && Auth::user()->hasPermission('approve-scores'))
                                <form action="{{ route('school.scores.unlock') }}" method="POST" onsubmit="return confirm('Are you sure you want to unlock these scores and return them to draft? This will allow edits again.')" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                                    <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
                                    <input type="hidden" name="term_id" value="{{ $selectedTermId }}">
                                    <input type="hidden" name="academic_year_id" value="{{ $selectedYearId }}">
                                    <button type="submit" class="btn btn-danger fw-bold" style="border-radius: 8px;">
                                        <i class="bi bi-unlock-fill me-1"></i>Unlock Sheet (Return to Draft)
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- HOD Moderation Action Collapse Panel -->
                    @if($sheetStatus === 'submitted' && Auth::user()->hasPermission('verify-scores'))
                        <div class="collapse mt-3" id="hodActionPanel">
                            <div class="card card-body bg-light border-0 shadow-sm" style="border-radius: 12px;">
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-shield-fill-check me-1 text-warning"></i>HOD Review & Quality Verification</h6>
                                <form action="{{ route('school.scores.verify') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                                    <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
                                    <input type="hidden" name="term_id" value="{{ $selectedTermId }}">
                                    <input type="hidden" name="academic_year_id" value="{{ $selectedYearId }}">

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary">Verification Review Note (Optional)</label>
                                        <textarea class="form-control rounded-3" name="moderation_note" rows="2" placeholder="Describe quality review, adjustments, or comments."></textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" name="action" value="approve" class="btn btn-success px-4 fw-bold" style="border-radius: 8px;">
                                            <i class="bi bi-check-circle me-1"></i>Verify & Forward to Headteacher
                                        </button>
                                        <button type="submit" name="action" value="reject" class="btn btn-danger px-4 fw-bold" style="border-radius: 8px;" onclick="return confirm('Reject sheet and return to draft status?')">
                                            <i class="bi bi-x-circle me-1"></i>Reject & Send Back to Teacher
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Headteacher Moderation Action Collapse Panel -->
                    @if($sheetStatus === 'hod_verified' && Auth::user()->hasPermission('approve-scores'))
                        <div class="collapse mt-3" id="headActionPanel">
                            <div class="card card-body bg-light border-0 shadow-sm" style="border-radius: 12px;">
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-shield-lock-fill me-1 text-success"></i>Headteacher Final Verification & Publication</h6>
                                <form action="{{ route('school.scores.approve') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                                    <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
                                    <input type="hidden" name="term_id" value="{{ $selectedTermId }}">
                                    <input type="hidden" name="academic_year_id" value="{{ $selectedYearId }}">

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary">Final Approval Note (Optional)</label>
                                        <textarea class="form-control rounded-3" name="moderation_note" rows="2" placeholder="Write any comments regarding the approved terms scores."></textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" name="action" value="approve" class="btn btn-success px-4 fw-bold" style="border-radius: 8px;">
                                            <i class="bi bi-patch-check me-1"></i>Approve & Recalculate Class Ranks
                                        </button>
                                        <button type="submit" name="action" value="reject" class="btn btn-danger px-4 fw-bold" style="border-radius: 8px;" onclick="return confirm('Reject sheet and return to draft status?')">
                                            <i class="bi bi-x-circle me-1"></i>Reject & Send Back to Draft
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            @php
                $averageClassScore = 0;
                $passRate = 0;
                $highestScore = 0;
                $absentCount = 0;
                if (count($students) > 0 && count($scores) > 0) {
                    $allGrandTotals = $scores->pluck('grand_total');
                    if ($allGrandTotals->count() > 0) {
                        $averageClassScore = round($allGrandTotals->avg(), 1);
                        $highestScore = $allGrandTotals->max();
                        $passCount = $allGrandTotals->filter(fn($val) => $val >= 50)->count();
                        $passRate = round(($passCount / count($students)) * 100);
                    }
                    $absentCount = $scores->where('is_absent_exam', true)->count();
                }
            @endphp

            <!-- Dynamic Metrics Panel -->
            @if(count($students) > 0)
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(0, 51, 102, 0.1) !important;">
                            <div>
                                <span class="text-muted small d-block">Class Average</span>
                                <span class="fs-3 fw-bold text-primary" style="font-weight: 800; color: var(--primary-color) !important;">{{ $averageClassScore }}%</span>
                            </div>
                            <div class="fs-2 text-primary bg-primary bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-graph-up"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(25, 135, 84, 0.1) !important;">
                            <div>
                                <span class="text-muted small d-block">Pass Rate (>=50%)</span>
                                <span class="fs-3 fw-bold text-success" style="font-weight: 800;">{{ $passRate }}%</span>
                            </div>
                            <div class="fs-2 text-success bg-success bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-shield-check"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(255, 193, 7, 0.2) !important;">
                            <div>
                                <span class="text-muted small d-block">Highest Mark</span>
                                <span class="fs-3 fw-bold text-warning" style="font-weight: 800; color: #b08d00 !important;">{{ $highestScore }}%</span>
                            </div>
                            <div class="fs-2 text-warning bg-warning bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; color: #b08d00 !important;">
                                <i class="bi bi-trophy"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(220, 53, 69, 0.15) !important;">
                            <div>
                                <span class="text-muted small d-block">Exam Absentees</span>
                                <span class="fs-3 fw-bold text-danger" style="font-weight: 800;">{{ $absentCount }}</span>
                            </div>
                            <div class="fs-2 text-danger bg-danger bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-person-x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Spreadsheet Score Entry Grid -->
            <div class="glass-card p-4 mb-5" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                @if(count($students) > 0)
                    @if(!$isEditable)
                        <!-- Sheet Locked Notice -->
                        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4" style="border-radius: 10px; background-color: rgba(13, 202, 240, 0.1); color: #087990;">
                            <i class="bi bi-info-circle-fill fs-5 me-2"></i>
                            <div>
                                @if($sheetStatus !== 'draft')
                                    This score sheet is locked because its current status is <strong>{{ ucfirst($sheetStatus === 'published' ? 'approved & published' : $sheetStatus) }}</strong>. 
                                    @if(Auth::user()->hasPermission('approve-scores'))
                                        Click the <strong>Unlock Sheet (Return to Draft)</strong> button above to allow editing.
                                    @else
                                        An Administrator or Headteacher must unlock it to allow editing.
                                    @endif
                                @else
                                    You do not have the required permissions to enter/edit scores on this page.
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle spreadsheet-table" id="scoresGrid" style="font-size: 0.9rem;">
                            <thead>
                                <tr>
                                    <th style="width: 240px;" class="border-0 rounded-start">Student Name</th>
                                    @foreach($config->components as $comp)
                                        <th class="text-center border-0" style="width: 105px;">
                                            <div>{{ $comp->name }}</div>
                                            <small class="text-white-50">Max: {{ $comp->max_marks }}</small>
                                        </th>
                                    @endforeach
                                    <th class="text-center bg-dark border-0" style="width: 105px; background-color: #1e293b !important;">
                                        <div>SBA Total</div>
                                        <small class="text-white-50">Max: {{ $config->class_score_max }}</small>
                                    </th>
                                    <th class="text-center border-0" style="width: 105px;">
                                        <div>Exam Score</div>
                                        <small class="text-white-50">Max: {{ $config->exam_score_max }}</small>
                                    </th>
                                    <th class="text-center border-0" style="width: 80px;">Absent</th>
                                    <th style="width: 200px; min-width: 150px;" class="border-0">Teacher Remarks</th>
                                    <th class="text-center bg-dark border-0" style="width: 85px; background-color: #0f172a !important;">Grade</th>
                                    <th class="text-center bg-dark border-0 rounded-end" style="width: 95px; background-color: #0f172a !important;">Grand Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $rowIdx => $student)
                                    @php
                                        $scoreRecord = $scores->get($student->id);
                                        $componentScores = $scoreRecord ? $scoreRecord->component_scores : [];
                                    @endphp
                                    <tr data-student-id="{{ $student->id }}" id="row-{{ $student->id }}" class="spreadsheet-row">
                                        <td>
                                            <div class="fw-bold text-dark">{{ $student->first_name }} {{ $student->last_name }}</div>
                                            <span class="text-muted small fw-bold" style="font-family: monospace;">{{ $student->student_id_number }}</span>
                                        </td>
                                        
                                        <!-- SBA Components -->
                                        @foreach($config->components as $colIdx => $comp)
                                            <td>
                                                <input type="number" 
                                                       step="0.5" 
                                                       class="grid-input comp-score-input" 
                                                       data-comp-id="{{ $comp->id }}"
                                                       data-max="{{ $comp->max_marks }}"
                                                       data-row-index="{{ $rowIdx }}"
                                                       data-col-index="{{ $colIdx }}"
                                                       value="{{ $componentScores[$comp->id] ?? '' }}"
                                                       {{ !$isEditable ? 'disabled' : '' }}>
                                            </td>
                                        @endforeach

                                        <!-- Class SBA Raw Total -->
                                         <td class="text-center">
                                            <span class="fw-bold sba-total-cell text-primary" style="font-family: monospace; font-size: 0.95rem;">
                                                {{ $scoreRecord ? $scoreRecord->raw_class_total : '0.00' }}
                                            </span>
                                        </td>

                                        <!-- Exam Score -->
                                        <td>
                                            <input type="number" 
                                                   step="0.5" 
                                                   class="grid-input exam-score-input" 
                                                   data-max="{{ $config->exam_score_max }}"
                                                   data-row-index="{{ $rowIdx }}"
                                                   data-col-index="{{ count($config->components) }}"
                                                   value="{{ $scoreRecord ? $scoreRecord->raw_exam_score : '' }}"
                                                   {{ !$isEditable || ($scoreRecord && $scoreRecord->is_absent_exam) ? 'disabled' : '' }}>
                                        </td>

                                        <!-- Absent Checkbox -->
                                        <td class="text-center">
                                            <input type="checkbox" 
                                                   class="form-check-input absent-checkbox cursor-pointer" 
                                                   data-row-index="{{ $rowIdx }}"
                                                   value="1" 
                                                   {{ $scoreRecord && $scoreRecord->is_absent_exam ? 'checked' : '' }}
                                                   {{ !$isEditable ? 'disabled' : '' }}>
                                        </td>

                                        <!-- Remarks -->
                                        <td>
                                            <input type="text" 
                                                   class="grid-input text-start remarks-input" 
                                                   data-row-index="{{ $rowIdx }}"
                                                   value="{{ $scoreRecord ? $scoreRecord->remarks : '' }}"
                                                   placeholder="Remarks..."
                                                   {{ !$isEditable ? 'disabled' : '' }}
                                                   style="font-family: inherit; text-align: left;">
                                        </td>

                                        <!-- Grade & Final Total -->
                                        <td class="text-center bg-dark text-white">
                                            <span class="badge bg-warning text-dark px-2.5 py-1.5 fw-bold" style="border-radius: 6px; font-size: 0.76rem; font-family: monospace;">
                                                <span class="grade-cell">{{ $scoreRecord ? $scoreRecord->grade : '—' }}</span>
                                            </span>
                                        </td>
                                        <td class="text-center bg-dark text-white fw-bold">
                                            <span class="grand-total-cell" style="color: var(--accent-color); font-family: monospace; font-size: 0.95rem;">
                                                {{ $scoreRecord ? $scoreRecord->grand_total : '0.00' }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- Redesigned Empty State -->
                    <div class="text-center py-5 text-muted">
                        <div class="text-secondary bg-secondary bg-opacity-10 p-3 rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="bi bi-people display-5"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">No Students Allocated</h5>
                        <p class="mb-0">No students are currently registered in this class.</p>
                        <p class="small text-muted mb-0">Assign student profiles to this class level in the student registry first.</p>
                    </div>
                @endif
            </div>
        @endif
    @else
        <!-- Redesigned Quick Launch Grid -->
        <div class="glass-card p-4" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 16px;">
            <div class="text-center mb-4">
                <div class="text-primary bg-primary bg-opacity-10 p-3 rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-grid-3x3-gap display-6 text-primary"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1" style="font-weight: 800;">Quick Score Entry Hub</h4>
                <p class="text-secondary small mb-0">Select a class and click a subject below to start recording scores for the current term.</p>
            </div>

            <div class="row g-4">
                @foreach($classes as $c)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; background: var(--theme-toggle-bg); border: 1px solid var(--border-color) !important;">
                            <div class="card-header bg-transparent border-0 pt-3 pb-0 px-3 d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="bi bi-mortarboard-fill"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-0" style="font-weight: 700;">{{ $c->name }}</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="d-flex flex-wrap gap-2">
                                    @forelse($subjects as $s)
                                        <a href="{{ route('school.scores.enter', ['class_id' => $c->id, 'subject_id' => $s->id, 'term_id' => $selectedTermId, 'academic_year_id' => $selectedYearId]) }}" 
                                           class="btn btn-sm btn-outline-primary px-2.5 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5" 
                                           style="border-radius: 8px; font-size: 0.78rem; border-color: rgba(13, 110, 253, 0.25);">
                                            <i class="bi bi-book-half"></i>{{ $s->name }}
                                        </a>
                                    @empty
                                        <span class="text-muted small">No subjects configured.</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

<!-- CSV Import Modal (Rendered outside the conditional count loops to ensure DOM presence at all times) -->
@if($selectedClassId && $selectedSubjectId && $config)
    <div class="modal fade" id="importScoresModal" tabindex="-1" aria-labelledby="importScoresModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0 p-4">
                    <h5 class="modal-title fw-bold text-dark" id="importScoresModalLabel" style="font-weight: 700;">
                        <i class="bi bi-file-earmark-arrow-up-fill text-primary me-2"></i>Import Scores from CSV
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('school.scores.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                    <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
                    <input type="hidden" name="term_id" value="{{ $selectedTermId }}">
                    <input type="hidden" name="academic_year_id" value="{{ $selectedYearId }}">

                    <div class="modal-body p-4">
                        <div class="alert alert-warning border-0 small mb-4" style="border-radius: 12px; background-color: rgba(255, 193, 7, 0.1); color: #664d03;">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
                            <strong>Crucial Instructions:</strong>
                            <ol class="ps-3 mb-0 mt-2">
                                <li>First, click <strong>Export CSV</strong> to download your current class roster template.</li>
                                <li>Fill out the score columns using Microsoft Excel or Google Sheets.</li>
                                <li>Do <strong>NOT</strong> modify the "Student ID" or column header names.</li>
                                <li>Ensure entered values do not exceed the component maximum marks.</li>
                            </ol>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Select CSV File</label>
                            <input type="file" name="csv_file" class="form-control rounded-3" accept=".csv" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px; background-color: var(--primary-color); border: none;">
                            <i class="bi bi-cloud-upload-fill me-1"></i>Upload & Save Drafts
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
@if($selectedClassId && $selectedSubjectId && $config && count($students) > 0)
<script>
    const saveDraftUrl = "{{ route('school.scores.save-draft') }}";
    const configId = "{{ $config->id }}";
    const classId = "{{ $selectedClassId }}";
    const subjectId = "{{ $selectedSubjectId }}";
    const termId = "{{ $selectedTermId }}";
    const yearId = "{{ $selectedYearId }}";
    const decimals = parseInt("{{ $config->decimal_places }}");
    const roundingMethod = "{{ $config->rounding_method }}";
    
    // We map row indexes to student IDs
    const studentRows = Array.from(document.querySelectorAll('#scoresGrid tbody tr')).map(tr => tr.getAttribute('data-student-id'));

    document.addEventListener('DOMContentLoaded', function() {
        const grid = document.getElementById('scoresGrid');
        if (!grid) return;

        // Auto-save logic
        const pendingSaves = new Map();
        let autosaveTimer = null;

        // Cell Navigation and Pasting
        grid.addEventListener('keydown', handleKeyNavigation);
        grid.addEventListener('paste', handleExcelPaste);

        // Input and state change listeners
        grid.addEventListener('input', function(e) {
            if (e.target.classList.contains('comp-score-input') || e.target.classList.contains('exam-score-input')) {
                // Ensure value does not exceed max
                const max = parseFloat(e.target.getAttribute('data-max'));
                const val = parseFloat(e.target.value);
                if (val > max) {
                    e.target.value = max;
                }
                
                // Recompute locally
                const tr = e.target.closest('tr');
                calculateRowLocally(tr);
                
                // Queue for saving
                queueSave(tr);
            } else if (e.target.classList.contains('remarks-input')) {
                const tr = e.target.closest('tr');
                queueSave(tr);
            }
        });

        grid.addEventListener('change', function(e) {
            if (e.target.classList.contains('absent-checkbox')) {
                const tr = e.target.closest('tr');
                const examInput = tr.querySelector('.exam-score-input');
                if (e.target.checked) {
                    examInput.value = '';
                    examInput.disabled = true;
                } else {
                    examInput.disabled = false;
                }
                calculateRowLocally(tr);
                queueSave(tr);
            }
        });

        // Trigger immediate save on blur
        grid.addEventListener('focusout', function(e) {
            if (e.target.tagName === 'INPUT') {
                const tr = e.target.closest('tr');
                const studentId = tr.getAttribute('data-student-id');
                if (pendingSaves.has(studentId)) {
                    saveRowImmediately(tr);
                }
            }
        });

        function queueSave(tr) {
            const studentId = tr.getAttribute('data-student-id');
            showAutosaveStatus('Saving drafts...', 'text-warning');
            
            if (pendingSaves.has(studentId)) {
                clearTimeout(pendingSaves.get(studentId));
            }
            
            const timer = setTimeout(() => {
                saveRowImmediately(tr);
            }, 2500); // Wait 2.5s of typing inactivity
            
            pendingSaves.set(studentId, timer);
        }

        function saveRowImmediately(tr) {
            const studentId = tr.getAttribute('data-student-id');
            if (pendingSaves.has(studentId)) {
                clearTimeout(pendingSaves.get(studentId));
                pendingSaves.delete(studentId);
            }

            // Prepare Payload
            const componentScores = {};
            tr.querySelectorAll('.comp-score-input').forEach(input => {
                const compId = input.getAttribute('data-comp-id');
                if (input.value !== '') {
                    componentScores[compId] = parseFloat(input.value);
                }
            });

            const rawExam = tr.querySelector('.exam-score-input').value;
            const isAbsent = tr.querySelector('.absent-checkbox').checked ? 1 : 0;
            const remarks = tr.querySelector('.remarks-input').value;

            const payload = {
                _token: "{{ csrf_token() }}",
                student_id: studentId,
                class_id: classId,
                subject_id: subjectId,
                term_id: termId,
                academic_year_id: yearId,
                scoring_configuration_id: configId,
                component_scores: componentScores,
                raw_exam_score: rawExam !== '' ? parseFloat(rawExam) : null,
                is_absent_exam: isAbsent,
                remarks: remarks
            };

            fetch(saveDraftUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update computed fields from server calculations
                    tr.querySelector('.sba-total-cell').textContent = data.raw_class_total;
                    tr.querySelector('.grand-total-cell').textContent = data.grand_total + '%';
                    tr.querySelector('.grade-cell').textContent = data.grade || '—';
                    
                    if (pendingSaves.size === 0) {
                        showAutosaveStatus('All drafts saved', 'text-success');
                    }
                } else {
                    showAutosaveStatus('Failed to autosave: ' + (data.error || 'Server error'), 'text-danger');
                }
            })
            .catch(err => {
                console.error(err);
                showAutosaveStatus('Network error - saving paused', 'text-danger');
            });
        }

        function showAutosaveStatus(msg, className) {
            const label = document.getElementById('autosaveLabel');
            label.className = `saving-status ${className}`;
            label.innerHTML = `<i class="bi bi-cloud-arrow-up-fill me-1"></i>${msg}`;
            if (className === 'text-success') {
                label.innerHTML = `<i class="bi bi-cloud-check-fill me-1"></i>${msg}`;
            }
        }

        function calculateRowLocally(tr) {
            // Local Math simulation for latency-free updates before AJAX returns
            let rawSbaTotal = 0;
            tr.querySelectorAll('.comp-score-input').forEach(input => {
                rawSbaTotal += parseFloat(input.value) || 0;
            });
            tr.querySelector('.sba-total-cell').textContent = rawSbaTotal.toFixed(2);
        }

        function handleKeyNavigation(e) {
            if (e.target.tagName !== 'INPUT') return;
            
            const cell = e.target;
            const rowIdx = parseInt(cell.getAttribute('data-row-index'));
            const colIdx = parseInt(cell.getAttribute('data-col-index'));
            
            let nextRowIdx = rowIdx;
            let nextColIdx = colIdx;

            if (e.key === 'ArrowUp') {
                nextRowIdx--;
                e.preventDefault();
            } else if (e.key === 'ArrowDown' || e.key === 'Enter') {
                nextRowIdx++;
                e.preventDefault();
            } else if (e.key === 'ArrowLeft') {
                nextColIdx--;
            } else if (e.key === 'ArrowRight') {
                nextColIdx++;
            } else {
                return;
            }

            const nextInput = grid.querySelector(`input[data-row-index="${nextRowIdx}"][data-col-index="${nextColIdx}"]`);
            if (nextInput) {
                nextInput.focus();
                nextInput.select();
            }
        }

        function handleExcelPaste(e) {
            const activeEl = document.activeElement;
            if (!activeEl || activeEl.tagName !== 'INPUT') return;

            const clipboardData = e.clipboardData || window.clipboardData;
            const pastedText = clipboardData.getData('Text');

            // Check if it looks like Excel grid data (tabs and/or newlines)
            if (pastedText.includes('\t') || pastedText.includes('\n')) {
                e.preventDefault();

                const lines = pastedText.split(/\r?\n/).filter(line => line.trim() !== '');
                const startRow = parseInt(activeEl.getAttribute('data-row-index'));
                const startCol = parseInt(activeEl.getAttribute('data-col-index'));

                lines.forEach((line, rOffset) => {
                    const columns = line.split('\t');
                    const targetRowIdx = startRow + rOffset;

                    columns.forEach((val, cOffset) => {
                        const targetColIdx = startCol + cOffset;
                        const input = grid.querySelector(`input[data-row-index="${targetRowIdx}"][data-col-index="${targetColIdx}"]`);
                        
                        if (input && !input.disabled) {
                            const trimmedVal = val.trim();
                            if (trimmedVal !== '') {
                                const max = parseFloat(input.getAttribute('data-max'));
                                let floatVal = parseFloat(trimmedVal);
                                if (!isNaN(floatVal)) {
                                    if (floatVal > max) floatVal = max;
                                    input.value = floatVal;
                                    
                                    const tr = input.closest('tr');
                                    calculateRowLocally(tr);
                                    queueSave(tr);
                                }
                            }
                        }
                    });
                });
            }
        }
    });
</script>
@endif
@endsection
