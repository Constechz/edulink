@extends('layouts.app')

@section('title', 'Student Promotions & Rollover | EduLink')
@section('header_title', 'Student Promotions')

@section('styles')
<style>
    .promotion-card {
        border-radius: 16px;
        border: 1px solid var(--border-color);
        background: var(--card-bg);
        transition: all 0.3s ease;
    }
    .status-btn-group input[type="radio"] {
        display: none;
    }
    .status-btn-group label {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid var(--border-color);
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 0;
    }
    .status-btn-group label:first-of-type {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }
    .status-btn-group label:last-of-type {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    
    /* Promote style (Active) */
    .status-btn-group input[value="promote"]:checked + label {
        background-color: #d1e7dd;
        color: #0f5132;
        border-color: #badbcc;
    }
    /* Repeat style (Active) */
    .status-btn-group input[value="repeat"]:checked + label {
        background-color: #fff3cd;
        color: #664d03;
        border-color: #ffecb5;
    }
    /* Graduate style (Active) */
    .status-btn-group input[value="graduate"]:checked + label {
        background-color: #cff4fc;
        color: #087990;
        border-color: #b6effb;
    }
    /* None style (Active) */
    .status-btn-group input[value="none"]:checked + label {
        background-color: #f8f9fa;
        color: #212529;
        border-color: #dee2e6;
    }

    /* Dark Mode specific buttons overrides */
    [data-bs-theme="dark"] .status-btn-group {
        border-color: var(--border-color) !important;
    }
    [data-bs-theme="dark"] .status-btn-group label {
        border-color: var(--border-color) !important;
        color: var(--text-muted);
    }
    [data-bs-theme="dark"] .status-btn-group label:hover {
        background-color: rgba(255, 255, 255, 0.05);
        color: var(--text-main);
    }
    [data-bs-theme="dark"] .status-btn-group input[value="none"]:checked + label {
        background-color: rgba(255, 255, 255, 0.05);
        color: #f1f5f9;
        border-color: var(--border-color);
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.students') }}" class="text-decoration-none">Students</a></li>
            <li class="breadcrumb-item active" aria-current="page">Student Promotions</li>
        </ol>
    </nav>

    <!-- Error/Success alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-4 border-0 shadow-xs mb-4" role="alert">
            <h5 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Promotion Processing Error</h5>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Alert / Notice Panel -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4 text-white rounded-4" style="background: linear-gradient(135deg, #0f2b48 0%, #1a4f8b 100%); border: 1px solid rgba(255,255,255,0.05);">
                <div class="d-flex align-items-start gap-3">
                    <div class="fs-1 text-warning"><i class="bi bi-exclamation-octagon"></i></div>
                    <div>
                        <h4 class="fw-bold mb-2">Academic Year Promotions & Rollover Wizard</h4>
                        <p class="mb-0 text-white-70 small">
                            Use this tool at the end of **Term 3** to transition your student rosters into the new academic year.
                            Promoted students will have new historical `Enrollment` logs generated and their active classroom placement updated.
                            Repeated students stay in the same class level but receive a new active enrollment record. History is fully preserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="promotion-card p-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-funnel text-primary me-2"></i>Select Source Class & Roster</h5>
                <form action="{{ route('school.students.promotion') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">Source Academic Year (Active Year)</label>
                            <select name="source_academic_year_id" class="form-select rounded-3 py-2" required>
                                <option value="">-- Choose Academic Year --</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ ($sourceYearId == $year->id || (!$sourceYearId && $year->is_current)) ? 'selected' : '' }}>
                                        {{ $year->name }} {{ $year->is_current ? '(Current Active)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">Source Class</label>
                            <select name="source_class_id" class="form-select rounded-3 py-2" required>
                                <option value="">-- Choose Class --</option>
                                @foreach($classes as $cls)
                                    <option value="{{ $cls->id }}" {{ $sourceClassId == $cls->id ? 'selected' : '' }}>
                                        {{ $cls->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary">Source Stream (Optional)</label>
                            <select name="source_stream_id" class="form-select rounded-3 py-2">
                                <option value="">-- All Streams --</option>
                                @foreach($streams as $strm)
                                    <option value="{{ $strm->id }}" {{ $sourceStreamId == $strm->id ? 'selected' : '' }}>
                                        {{ $strm->name }} ({{ $strm->class ? $strm->class->name : '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold">
                                <i class="bi bi-search"></i> Load
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Student Roster and Destination Selection -->
    @if($sourceClassId && $sourceYearId)
        @php
            $sourceClass = $classes->firstWhere('id', $sourceClassId);
            $sourceClassName = $sourceClass ? $sourceClass->name : '';
        @endphp
        <form action="{{ route('school.students.promotion.process') }}" method="POST">
            @csrf
            <input type="hidden" name="source_class_id" value="{{ $sourceClassId }}">
            <input type="hidden" name="source_academic_year_id" value="{{ $sourceYearId }}">

            <div class="row g-4">
                <!-- Roster Panel -->
                <div class="col-lg-8">
                    <div class="promotion-card p-4 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-people text-success me-2"></i>Class Student Roster ({{ $students->count() }} Students)</h5>
                            
                            <!-- Bulk Actions -->
                            @if($students->count() > 0)
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-3 fw-semibold" type="button" id="bulkActionsMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-check2-all me-1"></i> Bulk Roster Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="bulkActionsMenu">
                                        <li><a class="dropdown-item py-2" href="#" onclick="applyRecommendedBulk()"><i class="bi bi-magic text-primary me-2"></i>Reset to Recommendations</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item py-2" href="#" onclick="applyBulkAction('promote')"><i class="bi bi-arrow-up-circle text-success me-2"></i>Set All to Promote</a></li>
                                        <li><a class="dropdown-item py-2" href="#" onclick="applyBulkAction('repeat')"><i class="bi bi-arrow-counterclockwise text-warning me-2"></i>Set All to Repeat</a></li>
                                        <li><a class="dropdown-item py-2" href="#" onclick="applyBulkAction('graduate')"><i class="bi bi-award text-info me-2"></i>Set All to Graduate</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item py-2" href="#" onclick="applyBulkAction('none')"><i class="bi bi-x-circle text-secondary me-2"></i>Reset All Actions</a></li>
                                    </ul>
                                </div>
                            @endif
                        </div>

                        @if($students->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border-0">
                                    <thead>
                                        <tr class="bg-light">
                                            <th class="border-0 rounded-start text-secondary small fw-bold">Student Detail</th>
                                            <th class="border-0 text-secondary small fw-bold text-center">Term 1</th>
                                            <th class="border-0 text-secondary small fw-bold text-center">Term 2</th>
                                            <th class="border-0 text-secondary small fw-bold text-center">Term 3</th>
                                            <th class="border-0 text-secondary small fw-bold text-center">Cumulative Avg</th>
                                            <th class="border-0 text-secondary small fw-bold text-center">Recommended</th>
                                            <th class="border-0 rounded-end text-secondary small fw-bold text-center">Status / Placement Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $index => $student)
                                            <tr>
                                                <td class="border-bottom py-3">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="avatar-sm bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <span class="d-block fw-bold text-dark mb-0">{{ $student->first_name }} {{ $student->last_name }}</span>
                                                            <span class="text-muted small d-block">{{ $student->student_id_number }}</span>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="students[{{ $index }}][student_id]" value="{{ $student->id }}">
                                                </td>
                                                <td class="border-bottom text-center text-secondary small">{{ $student->term1_score !== null ? $student->term1_score . '%' : '—' }}</td>
                                                <td class="border-bottom text-center text-secondary small">{{ $student->term2_score !== null ? $student->term2_score . '%' : '—' }}</td>
                                                <td class="border-bottom text-center text-secondary small">{{ $student->term3_score !== null ? $student->term3_score . '%' : '—' }}</td>
                                                <td class="border-bottom text-center fw-semibold text-dark">{{ $student->computed_average }}%</td>
                                                <td class="border-bottom text-center">
                                                    @if($student->recommended_decision === 'promoted' || $student->recommended_decision === 'promote')
                                                        <span class="badge bg-success-subtle text-success py-1.5 px-2 rounded-2">Promote to <span class="target-class-label">(Select target class)</span></span>
                                                    @elseif($student->recommended_decision === 'conditional')
                                                        <span class="badge bg-warning-subtle text-warning py-1.5 px-2 rounded-2">Conditional to <span class="target-class-label">(Select target class)</span></span>
                                                    @elseif($student->recommended_decision === 'repeat')
                                                        <span class="badge bg-danger-subtle text-danger py-1.5 px-2 rounded-2">Repeat {{ $sourceClassName }}</span>
                                                    @elseif($student->recommended_decision === 'bece_candidate')
                                                        <span class="badge bg-secondary-subtle text-secondary py-1.5 px-2 rounded-2">BECE Candidate</span>
                                                    @elseif($student->recommended_decision === 'wassce_candidate')
                                                        <span class="badge bg-secondary-subtle text-secondary py-1.5 px-2 rounded-2">WASSCE Candidate</span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary py-1.5 px-2 rounded-2">Review</span>
                                                    @endif
                                                </td>
                                                <td class="border-bottom text-center">
                                                    <div class="status-btn-group d-inline-flex border rounded-3 overflow-hidden bg-body shadow-xs">
                                                        @php
                                                            $recVal = 'promote';
                                                            if ($student->recommended_decision === 'repeat') {
                                                                $recVal = 'repeat';
                                                            } elseif (in_array($student->recommended_decision, ['bece_candidate', 'wassce_candidate', 'graduate'])) {
                                                                $recVal = 'graduate';
                                                            }
                                                        @endphp
                                                        <input type="radio" name="students[{{ $index }}][status]" id="status_{{ $index }}_promote" value="promote" {{ $recVal === 'promote' ? 'checked' : '' }}>
                                                        <label for="status_{{ $index }}_promote"><i class="bi bi-arrow-up-circle me-1"></i>Promote <span class="target-class-label-short"></span></label>
 
                                                        <input type="radio" name="students[{{ $index }}][status]" id="status_{{ $index }}_repeat" value="repeat" {{ $recVal === 'repeat' ? 'checked' : '' }}>
                                                        <label for="status_{{ $index }}_repeat"><i class="bi bi-arrow-counterclockwise me-1"></i>Repeat {{ $sourceClassName }}</label>
 
                                                        <input type="radio" name="students[{{ $index }}][status]" id="status_{{ $index }}_graduate" value="graduate" {{ $recVal === 'graduate' ? 'checked' : '' }}>
                                                        <label for="status_{{ $index }}_graduate"><i class="bi bi-award me-1"></i>Graduate</label>
 
                                                        <input type="radio" name="students[{{ $index }}][status]" id="status_{{ $index }}_none" value="none">
                                                        <label for="status_{{ $index }}_none"><i class="bi bi-x-circle me-1"></i>Skip</label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-people-fill fs-1 d-block mb-3 opacity-30 text-secondary"></i>
                                <h6 class="fw-bold">No Active Students Found</h6>
                                <p class="small mb-0">No active student enrollment mappings exist for the selected source filters.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Destination Panel -->
                <div class="col-lg-4">
                    <div class="promotion-card p-4 shadow-sm h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="fw-bold text-dark mb-4"><i class="bi bi-box-arrow-in-right text-warning me-2"></i>Destination Target Placement</h5>
                            <p class="text-muted small mb-4">Specify the target academic year and class level where promoted students will be rolled over.</p>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">Target Academic Year (New Year)</label>
                                <select name="destination_academic_year_id" class="form-select rounded-3 py-2" required>
                                    <option value="">-- Select Target Year --</option>
                                    @foreach($academicYears as $year)
                                        <!-- Do not default target to source year -->
                                        @if($year->id != $sourceYearId)
                                            <option value="{{ $year->id }}">
                                                {{ $year->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">Target Class (For Promoted Students)</label>
                                <select name="destination_class_id" class="form-select rounded-3 py-2" id="destinationClassSelect">
                                    <option value="">-- Choose Class --</option>
                                    @foreach($classes as $cls)
                                        @if($cls->id != $sourceClassId)
                                            <option value="{{ $cls->id }}">
                                                {{ $cls->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if($students->count() > 0)
                            <div class="mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-success btn-lg w-100 py-3 rounded-3 fw-bold shadow-sm" onclick="return confirmPromotionSubmit()">
                                    <i class="bi bi-lightning-fill me-1"></i> Process Rollover & Promotions
                                </button>
                                <p class="text-center text-muted small mt-2 mb-0"><i class="bi bi-shield-lock me-1"></i>This action modifies records in database transactions.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    @else
        <!-- Filter Prompt Card -->
        <div class="row">
            <div class="col-12">
                <div class="promotion-card p-5 text-center shadow-sm">
                    <i class="bi bi-mortarboard fs-1 d-block mb-3 text-secondary opacity-30"></i>
                    <h5 class="fw-bold text-dark">Roster Filters Empty</h5>
                    <p class="text-muted small mx-auto" style="max-width: 480px;">Please configure your source academic year and class level above, then click **Load** to load the classroom student registry and configure promotions.</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var select = document.getElementById('destinationClassSelect');
        if (select) {
            function updateTargetLabels() {
                var text = select.options[select.selectedIndex].text;
                if (select.value === '') {
                    text = '(Select target class)';
                }
                
                document.querySelectorAll('.target-class-label').forEach(function(el) {
                    el.textContent = text;
                });
                
                var shortText = select.value === '' ? '' : 'to ' + text;
                document.querySelectorAll('.target-class-label-short').forEach(function(el) {
                    el.textContent = shortText;
                });
            }
            
            select.addEventListener('change', updateTargetLabels);
            updateTargetLabels(); // run once on load
        }
    });

    function applyRecommendedBulk() {
        @if(isset($students) && $students->count() > 0)
            @foreach($students as $index => $student)
                @php
                    $recVal = 'promote';
                    if ($student->recommended_decision === 'repeat') {
                        $recVal = 'repeat';
                    } elseif (in_array($student->recommended_decision, ['bece_candidate', 'wassce_candidate', 'graduate'])) {
                        $recVal = 'graduate';
                    }
                @endphp
                var el = document.getElementById('status_{{ $index }}_{{ $recVal }}');
                if (el) el.checked = true;
            @endforeach
        @endif
    }

    function applyBulkAction(action) {
        // Query all radio inputs with the selected action
        var radios = document.querySelectorAll('input[type="radio"][value="' + action + '"]');
        radios.forEach(function(radio) {
            radio.checked = true;
        });
    }

    function confirmPromotionSubmit() {
        var targetYear = document.querySelector('select[name="destination_academic_year_id"]').value;
        var targetClass = document.getElementById('destinationClassSelect').value;
        
        if (!targetYear) {
            alert('Please select the Target Academic Year.');
            return false;
        }

        // Count promoted students
        var promotedCount = document.querySelectorAll('input[type="radio"][value="promote"]:checked').length;
        if (promotedCount > 0 && !targetClass) {
            alert('Please select the Target Class level for promoted students.');
            return false;
        }

        return confirm('Are you sure you want to process the rollover? This will migrate student registers and create enrollment logs for the next academic year.');
    }
</script>
@endsection
