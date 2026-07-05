@extends('layouts.app')

@section('title', 'Report Cards | Parent Portal')
@section('header_title', 'Student Academic Report Cards')

@section('content')
<style>
    .avatar-circle-lg {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.4rem;
        box-shadow: 0 4px 12px rgba(0, 51, 102, 0.15);
    }

    .metric-card-green {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid rgba(25, 135, 84, 0.12) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card-green:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(25, 135, 84, 0.08);
    }

    .metric-card-blue {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid rgba(13, 110, 253, 0.12) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card-blue:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(13, 110, 253, 0.08);
    }

    .metric-card-amber {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border: 1px solid rgba(255, 193, 7, 0.25) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card-amber:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(255, 193, 7, 0.08);
    }

    .table-row-hover {
        transition: background-color 0.2s ease;
    }
    .table-row-hover:hover {
        background-color: rgba(0, 51, 102, 0.02) !important;
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
    .card-metric {
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: -0.5px;
    }
</style>

<div class="container-fluid p-0">
    <!-- Header Child Switcher Banner -->
    <div class="glass-card p-4 mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.05);">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar-circle-lg bg-primary text-white">
                {{ substr($activeChild->first_name, 0, 1) }}{{ substr($activeChild->last_name, 0, 1) }}
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Student academic reports</span>
                <h3 class="fw-bold mb-1 text-primary" style="font-weight: 800;">{{ $activeChild->first_name }} {{ $activeChild->middle_name }} {{ $activeChild->last_name }}</h3>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2.5 py-1.5 fw-bold" style="border-radius: 6px; font-family: monospace; font-size: 0.76rem;">
                        ID: {{ $activeChild->student_id_number }}
                    </span>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1.5 fw-bold" style="border-radius: 6px; font-size: 0.76rem;">
                        Class: {{ $activeChild->currentClass->name ?? 'N/A' }}
                    </span>
                    @if($activeChild->currentStream)
                        <span class="badge bg-info bg-opacity-10 text-info px-2.5 py-1.5 fw-bold" style="border-radius: 6px; font-size: 0.76rem;">
                            Stream: {{ $activeChild->currentStream->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle rounded-3 py-2 px-3 fw-bold d-inline-flex align-items-center gap-2" type="button" id="childSelector" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.85rem; border-radius: 10px !important;">
                    <i class="bi bi-person-fill-gear"></i> Switch Child Profile
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="childSelector" style="border-radius: 12px;">
                    @foreach($children as $child)
                        <li>
                            <a class="dropdown-item py-2.5 fw-bold text-dark {{ $child->id === $activeChild->id ? 'active bg-primary text-white' : '' }}" href="{{ route('school.parent-portal.select-child', $child->id) }}" style="font-size: 0.85rem;">
                                <i class="bi bi-person-fill me-2"></i>{{ $child->first_name }} {{ $child->last_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Consolidated PDF Report Card Download Banner -->
    <div class="glass-card p-4 mb-4 text-white d-flex flex-wrap justify-content-between align-items-center gap-3" style="background: linear-gradient(135deg, var(--primary-color) 0%, #1e40af 100%); border: none; border-radius: 16px;">
        <div class="d-flex align-items-center gap-3">
            <div class="fs-1 text-white opacity-90"><i class="bi bi-file-earmark-pdf-fill"></i></div>
            <div>
                <h5 class="fw-bold mb-1 text-white">Full Term Academic Report Card</h5>
                <p class="mb-0 text-white text-opacity-80 small">Download the officially compiled terminal report card file for {{ $activeChild->first_name }} in PDF format.</p>
            </div>
        </div>
        <a href="{{ route('school.reports.card', [$activeChild->id, 'term_id' => $currentTermId, 'academic_year_id' => $currentYearId]) }}" target="_blank" class="btn btn-light px-4 py-2.5 fw-bold text-primary d-inline-flex align-items-center gap-2" style="border-radius: 10px; font-size: 0.88rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <i class="bi bi-download"></i> Download PDF Report
        </a>
    </div>

    @php
        $uniqueSubjectsCount = $scores->unique('subject_id')->count();
        $averageScore = $scores->count() > 0 ? round($scores->avg('grand_total'), 1) : 0;
        $maxScore = $scores->count() > 0 ? $scores->max('grand_total') : 0;
        $bestGrade = 'N/A';
        if ($scores->count() > 0) {
            $bestScoreRecord = $scores->sortByDesc('grand_total')->first();
            $bestGrade = $bestScoreRecord->grade ?? 'N/A';
        }
    @endphp

    <!-- Dynamic Metrics Panel -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="glass-card p-4 text-center metric-card-green">
                <div class="fs-1 text-success mb-2"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="card-metric text-success">{{ $averageScore }}%</div>
                <div class="text-muted small fw-bold text-uppercase mt-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Average Score</div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="glass-card p-4 text-center metric-card-blue">
                <div class="fs-1 text-primary mb-2"><i class="bi bi-journals"></i></div>
                <div class="card-metric text-primary">{{ $uniqueSubjectsCount }}</div>
                <div class="text-muted small fw-bold text-uppercase mt-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Subjects Enrolled</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card p-4 text-center metric-card-amber">
                <div class="fs-1 text-warning mb-2" style="color: #b08d00 !important;"><i class="bi bi-trophy-fill"></i></div>
                <div class="card-metric text-warning" style="color: #b08d00 !important;">{{ $bestGrade }} <span style="font-size: 1.1rem; font-weight: 500;">({{ $maxScore }}%)</span></div>
                <div class="text-muted small fw-bold text-uppercase mt-1" style="font-size: 0.72rem; letter-spacing: 0.5px; color: #b08d00 !important;">Best Subject Grade</div>
            </div>
        </div>
    </div>

    <!-- Academic Score Sheets Ledger Card -->
    <div class="glass-card p-4 mb-5" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.05);">
        <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Term Subject Score Sheets Ledger</h5>

        @if($scores->isEmpty())
            <!-- Redesigned Empty State -->
            <div class="text-center py-5 text-muted">
                <div class="text-secondary bg-secondary bg-opacity-10 p-3 rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                    <i class="bi bi-journal-x display-5"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">No Scores Published</h5>
                <p class="mb-0">No subject scores or report cards have been officially published for this student profile yet.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle" style="font-size: 0.9rem;">
                    <thead>
                        <tr class="table-light">
                            <th class="border-0 rounded-start">Subject</th>
                            <th class="border-0">Term / Academic Year</th>
                            <th class="border-0 text-center">Class Assessment (/50)</th>
                            <th class="border-0 text-center">Terminal Exam (/50)</th>
                            <th class="border-0 text-center">Weighted Grand Total</th>
                            <th class="border-0 rounded-end text-center">Grade Awarded</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scores as $score)
                            <tr class="table-row-hover">
                                <td>
                                    <span class="fw-bold text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-book text-primary"></i> {{ $score->subject->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $score->term->name ?? 'N/A' }}</div>
                                    <div class="text-muted small">Calendar Year: {{ $score->academicYear->name ?? 'N/A' }}</div>
                                </td>
                                <td class="text-center fw-semibold text-secondary" style="font-family: monospace;">{{ $score->scaled_class_score ?? 'N/A' }}</td>
                                <td class="text-center fw-semibold text-secondary" style="font-family: monospace;">{{ $score->scaled_exam_score ?? 'N/A' }}</td>
                                <td class="text-center fw-bold text-primary" style="font-family: monospace; font-size: 0.98rem;">{{ $score->grand_total ?? 'N/A' }}%</td>
                                <td class="text-center">
                                    <span class="badge bg-success px-3 py-1.5 fw-bold" style="border-radius: 6px; font-size: 0.78rem;">
                                        {{ $score->grade ?? 'N/A' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
