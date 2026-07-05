@extends('layouts.app')

@section('title', 'Scoring Configuration Details | EduLink')
@section('header_title', 'SBA Ruleset Summary')

@section('content')
<div class="container-fluid p-0">

    <div class="mb-4">
        <a href="{{ route('school.scoring-configs.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Rulesets
        </a>
    </div>

    <div class="row g-4">
        <!-- Configuration Summary Card -->
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h4 class="mb-1 font-weight-bold" style="font-weight: 700; color: var(--primary-color);">{{ $scoringConfig->name }}</h4>
                        <span class="text-muted small">Ruleset ID: #{{ $scoringConfig->id }}</span>
                    </div>
                    @if($scoringConfig->is_default)
                        <span class="badge bg-success px-2.5 py-1.5" style="border-radius: 8px;" title="Default Fallback Ruleset">
                            <i class="bi bi-patch-check-fill me-1"></i>Default
                        </span>
                    @endif
                </div>

                <hr class="my-4">

                <div class="mb-3">
                    <span class="text-muted d-block small">Academic Level Scope</span>
                    <strong class="fs-5 text-dark">{{ $scoringConfig->level }}</strong>
                </div>

                <div class="mb-3">
                    <span class="text-muted d-block small">Subject Scope</span>
                    <strong class="text-dark">{{ $scoringConfig->subject ? $scoringConfig->subject->name : 'All Subjects' }}</strong>
                </div>

                <div class="mb-3">
                    <span class="text-muted d-block small">Academic Year Scope</span>
                    <strong class="text-dark">{{ $scoringConfig->academicYear ? $scoringConfig->academicYear->name : 'All Academic Years' }}</strong>
                </div>

                <div class="mb-3">
                    <span class="text-muted d-block small">Status</span>
                    @if($scoringConfig->is_active)
                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1" style="border-radius: 6px;">Active</span>
                    @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1" style="border-radius: 6px;">Inactive</span>
                    @endif
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <form action="{{ route('school.scoring-configs.destroy', $scoringConfig->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this scoring configuration? This might fail if scores depend on it.')" class="w-100">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100 py-2.5" style="border-radius: 10px;">
                            <i class="bi bi-trash me-2"></i>Delete Configuration
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Formula and Components Configuration Details -->
        <div class="col-lg-8">
            <div class="glass-card p-4 mb-4">
                <h5 class="font-weight-bold mb-4" style="font-weight: 700;">Mathematical Weight Scaling</h5>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <span class="text-muted d-block small mb-1">Class SBA Score</span>
                            <h3 class="font-weight-bold text-primary mb-1" style="font-weight: 800;">{{ $scoringConfig->class_score_weight }}%</h3>
                            <span class="text-muted small">Raw Maximum: {{ $scoringConfig->class_score_max }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <span class="text-muted d-block small mb-1">Exam Score</span>
                            <h3 class="font-weight-bold text-primary mb-1" style="font-weight: 800;">{{ $scoringConfig->exam_score_weight }}%</h3>
                            <span class="text-muted small">Raw Maximum: {{ $scoringConfig->exam_score_max }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-primary bg-opacity-10 rounded-3 text-center">
                            <span class="text-primary d-block small mb-1" style="font-weight: 600;">Report Card Total</span>
                            <h3 class="font-weight-bold text-primary mb-1" style="font-weight: 800;">{{ $scoringConfig->grand_total }}%</h3>
                            <span class="text-primary small" style="font-weight: 500;">
                                {{ $scoringConfig->rounding_method }} ({{ $scoringConfig->decimal_places }}dp)
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="font-weight-bold mb-0" style="font-weight: 700;">Class SBA Score Components</h5>
                    <span class="badge bg-dark px-2.5 py-1.5" style="border-radius: 8px;">
                        {{ $scoringConfig->components->count() }} Components Defined
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 80px;" class="text-center">Order</th>
                                <th>Component Name</th>
                                <th class="text-center" style="width: 150px;">Max Marks</th>
                                <th class="text-center" style="width: 150px;">Required</th>
                                <th class="text-center" style="width: 120px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $componentsSum = 0; @endphp
                            @foreach($scoringConfig->components->sortBy('display_order') as $component)
                                @php $componentsSum += $component->max_marks; @endphp
                                <tr>
                                    <td class="text-center fw-bold text-muted">#{{ $component->display_order }}</td>
                                    <td>
                                        <div class="fw-bold" style="color: var(--primary-color);">{{ $component->name }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold">{{ $component->max_marks }}</span> marks
                                    </td>
                                    <td class="text-center">
                                        @if($component->is_required)
                                            <span class="badge bg-danger bg-opacity-10 text-danger px-2.5 py-1" style="border-radius: 6px;">Required</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-2.5 py-1" style="border-radius: 6px;">Optional</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($component->is_active)
                                            <span class="text-success"><i class="bi bi-check-circle-fill"></i> Active</span>
                                        @else
                                            <span class="text-muted"><i class="bi bi-x-circle"></i> Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td></td>
                                <td>Total Components Sum</td>
                                <td class="text-center">
                                    <span class="text-success fs-5">{{ $componentsSum }}</span> / {{ $scoringConfig->class_score_max }}
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
