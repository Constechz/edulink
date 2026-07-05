@extends('layouts.app')

@section('title', 'Class Broadsheet | EduLink')
@section('header_title', 'Class Broadsheet Grade Matrix')

@section('content')
<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('school.reports.index', ['class_id' => $class->id, 'term_id' => $term->id, 'academic_year_id' => $year->id]) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Report Hub
        </a>
        <a href="{{ route('school.reports.broadsheet', ['class_id' => $class->id, 'term_id' => $term->id, 'academic_year_id' => $year->id, 'format' => 'pdf']) }}" 
           class="btn btn-primary">
            <i class="bi bi-file-pdf me-1"></i> Download PDF Version
        </a>
    </div>

    <!-- Broadsheet Header Summary -->
    <div class="glass-card p-4 mb-4">
        <h4 class="font-weight-bold mb-1" style="font-weight: 700; color: var(--primary-color);">Class Broadsheet Summary</h4>
        <div class="row mt-3 text-dark">
            <div class="col-md-3">
                <span class="text-muted d-block small">Class Name</span>
                <strong>{{ $class->name }}</strong>
            </div>
            <div class="col-md-3">
                <span class="text-muted d-block small">Academic Term</span>
                <strong>{{ $term->name }}</strong>
            </div>
            <div class="col-md-3">
                <span class="text-muted d-block small">Academic Year</span>
                <strong>{{ $year->name }}</strong>
            </div>
            <div class="col-md-3">
                <span class="text-muted d-block small">Total Students Enrolled</span>
                <strong>{{ $students->count() }} students</strong>
            </div>
        </div>
    </div>

    <!-- Broadsheet Matrix Card -->
    <div class="glass-card p-4">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle text-center">
                <thead>
                    <tr class="table-dark">
                        <th class="text-start" style="min-width: 200px;">Student Name</th>
                        @foreach($subjects as $sub)
                            <th title="{{ $sub->name }} ({{ $sub->code }})">
                                {{ $sub->code }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td class="text-start fw-bold">
                                {{ $student->first_name }} {{ $student->last_name }}
                                <div class="text-muted small fw-normal">#{{ $student->admission_no }}</div>
                            </td>
                            @foreach($subjects as $sub)
                                @php
                                    $score = $scoresMap[$student->id][$sub->id] ?? null;
                                @endphp
                                <td>
                                    @if($score)
                                        <div class="fw-bold text-primary">{{ $score->grand_total }}%</div>
                                        <span class="badge bg-secondary bg-opacity-10 text-dark small" style="font-size: 0.75rem;">{{ $score->grade }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
