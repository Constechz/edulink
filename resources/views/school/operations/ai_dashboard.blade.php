@extends('layouts.app')

@section('title', 'AI Analytics Center | EduLink')
@section('header_title', 'AI Insights & Predictive Analytics')

@section('content')
<div class="container-fluid p-0">
    <!-- Session Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show glass-card p-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Top Action bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-4 glass-card" style="background: linear-gradient(135deg, rgba(0, 51, 102, 0.08) 0%, rgba(255, 215, 0, 0.08) 100%);">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Automated Risk Analysis</h4>
            <p class="text-muted small mb-0">Assess student academic, attendance, and financial risk profiles using rule-based predictive engines.</p>
        </div>
        <form action="{{ route('school.operations.ai.run') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary rounded-3 px-4 py-2fw-semibold">
                <i class="bi bi-cpu-fill me-2"></i>Run Risk Assessment
            </button>
        </form>
    </div>

    <div class="row g-4">
        <!-- Risk Flags list -->
        <div class="col-md-7">
            <div class="glass-card p-4 h-100">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-flag-fill text-danger me-2"></i>Active Student Risk Flags</h5>
                @if($flags->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-shield-check fs-1 mb-2 d-block text-success"></i>
                        <span>No active student risk triggers recorded. Run risk assessment to process data.</span>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Risk Type</th>
                                    <th>Severity</th>
                                    <th>Reason / Insight</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($flags as $flag)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $flag->student->first_name }} {{ $flag->student->last_name }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $flag->flagType->name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $flag->severity === 'high' ? 'bg-danger' : 'bg-warning text-dark' }} text-uppercase">
                                                {{ $flag->severity }}
                                            </span>
                                        </td>
                                        <td class="small text-muted">{{ $flag->trigger_reason }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recommendations Tracker -->
        <div class="col-md-5">
            <div class="glass-card p-4 h-100">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-patch-question-fill text-primary me-2"></i>System AI Recommendations</h5>
                @if($recommendations->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-lightbulb fs-1 mb-2 d-block text-primary"></i>
                        <span>No pending recommendations. Run an assessment to generate action plans.</span>
                    </div>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach($recommendations as $rec)
                            <div class="p-3 bg-light rounded-4 border-start border-4 border-primary shadow-xs">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold mb-0 text-dark">{{ $rec->student->first_name }} {{ $rec->student->last_name }}</h6>
                                    <span class="badge bg-info bg-opacity-10 text-info">AI System</span>
                                </div>
                                <p class="mb-0 text-muted small mt-1">{{ $rec->recommendation_text }}</p>
                                <span class="text-muted small" style="font-size: 0.7rem;">Generated: {{ $rec->created_at->format('M d, Y') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
