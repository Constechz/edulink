@extends('layouts.app')

@section('title', 'Academic Results | EduLink')
@section('header_title', 'Student Academic Results')

@section('content')
<div class="container-fluid p-0">
    <div class="glass-card p-4">
        <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-award me-2 text-primary"></i>My Published Term Grades</h5>

        @if($scores->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-journal-x fs-1 mb-2 d-block"></i>
                <span>No reports have been published for you yet.</span>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Academic Year</th>
                            <th>Term</th>
                            <th>Subject</th>
                            <th>Class Mark (/50)</th>
                            <th>Exam Mark (/50)</th>
                            <th>Grand Total (/100)</th>
                            <th>Grade</th>
                            <th>Position</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scores as $score)
                            <tr>
                                <td>{{ $score->academicYear->name ?? 'N/A' }}</td>
                                <td>{{ $score->term->name ?? 'N/A' }}</td>
                                <td>{{ $score->subject->name ?? 'N/A' }}</td>
                                <td>{{ $score->scaled_class_score ?? 'N/A' }}</td>
                                <td>{{ $score->scaled_exam_score ?? 'N/A' }}</td>
                                <td class="fw-bold text-primary">{{ $score->grand_total ?? 'N/A' }}%</td>
                                <td>
                                    @php
                                        $badgeClass = 'bg-secondary';
                                        if (in_array($score->grade, ['A1', 'B2', 'B3'])) {
                                            $badgeClass = 'bg-success';
                                        } elseif (in_array($score->grade, ['C4', 'C5', 'C6'])) {
                                            $badgeClass = 'bg-warning text-dark';
                                        } elseif (in_array($score->grade, ['D7', 'E8', 'F9'])) {
                                            $badgeClass = 'bg-danger';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $score->grade ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($score->subject_position)
                                        <span class="fw-semibold">{{ $score->subject_position }}</span>
                                        <span class="text-muted small">/ {{ $score->total_students ?? 'N/A' }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
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
