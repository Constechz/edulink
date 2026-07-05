@extends('layouts.app')

@section('title', 'Security Audit & Compliance Center — EduLink')
@section('header_title', 'Security Audit & Compliance')

@section('content')
<div class="container-fluid">
    <!-- Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm text-white p-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-bottom: 3px solid var(--accent-color); border-radius: 16px;">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="bi bi-shield-lock-fill fs-1 text-warning"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1">Security Audit & Compliance Center</h2>
                        <p class="mb-0 text-white-50">Auditing environment settings, session configurations, and access logs dynamically to assure hosting compliance.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Diagnostic Summary Left Panel -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm glass-card p-4 text-center h-100">
                <h5 class="fw-bold text-dark mb-4">Overall Compliance Rating</h5>
                
                <!-- Circular Score Indicator -->
                <div class="position-relative d-inline-flex align-items-center justify-content-center mb-4" style="width: 180px; height: 180px;">
                    <svg class="position-absolute w-100 h-100" style="transform: rotate(-90deg);">
                        <circle cx="90" cy="90" r="75" stroke="#f1f5f9" stroke-width="12" fill="transparent" />
                        <circle cx="90" cy="90" r="75" stroke="{{ $complianceScore >= 80 ? '#10b981' : ($complianceScore >= 50 ? '#f59e0b' : '#ef4444') }}" stroke-width="12" fill="transparent" 
                                stroke-dasharray="471.2" stroke-dashoffset="{{ 471.2 - (471.2 * $complianceScore / 100) }}" style="transition: stroke-dashoffset 1s ease-in-out;" />
                    </svg>
                    <div class="text-center">
                        <span class="fs-1 fw-extrabold text-slate-800 d-block" style="font-weight: 800; font-size: 2.75rem;">{{ $complianceScore }}%</span>
                        <span class="text-muted small text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Compliant</span>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 mb-4">
                    <h6 class="fw-bold text-dark mb-2">Audit Log Volumes</h6>
                    <div class="row text-start g-2">
                        <div class="col-6 border-end">
                            <span class="small text-muted d-block">System Audit Logs</span>
                            <span class="fs-5 fw-bold text-dark">{{ $auditLogsCount }}</span>
                        </div>
                        <div class="col-6 ps-3">
                            <span class="small text-muted d-block">Safeguarding Logs</span>
                            <span class="fs-5 fw-bold text-dark">{{ $safeguardingLogsCount }}</span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('school.docs.security') }}" class="btn btn-dark w-100 py-2.5 rounded-3 fw-bold shadow-sm">
                    <i class="bi bi-arrow-clockwise me-2"></i>Run Diagnostic Checks
                </a>
            </div>
        </div>

        <!-- Diagnostic Items Details Right Panel -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm glass-card p-4">
                <h5 class="fw-bold text-dark mb-4">Active Compliance Diagnostics</h5>
                
                <div class="accordion accordion-flush" id="accordionDiagnostics">
                    
                    @foreach($diagnostics as $key => $check)
                        <div class="accordion-item border-bottom py-2 bg-transparent">
                            <h2 class="accordion-header" id="heading-{{ $key }}">
                                <button class="accordion-button collapsed bg-transparent px-0 py-3 shadow-none d-flex align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $key }}" aria-expanded="false" aria-controls="collapse-{{ $key }}">
                                    <div class="d-flex align-items-center text-start">
                                        <!-- Status Indicator -->
                                        @if($check['status'] === 'passed')
                                            <span class="badge bg-success bg-opacity-10 text-success p-2 rounded-circle me-3">
                                                <i class="bi bi-patch-check-fill fs-5"></i>
                                            </span>
                                        @elseif($check['status'] === 'warning')
                                            <span class="badge bg-warning bg-opacity-10 text-warning p-2 rounded-circle me-3">
                                                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger p-2 rounded-circle me-3">
                                                <i class="bi bi-x-circle-fill fs-5"></i>
                                            </span>
                                        @endif
                                        
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $check['title'] }}</span>
                                            <span class="small text-muted font-monospace">{{ $check['value'] }}</span>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            
                            <div id="collapse-{{ $key }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $key }}" data-bs-parent="#accordionDiagnostics">
                                <div class="accordion-body bg-light rounded-3 p-3 mt-2">
                                    <p class="small text-muted mb-3">{{ $check['description'] }}</p>
                                    <div class="border-top pt-2">
                                        <h6 class="small fw-bold text-dark"><i class="bi bi-wrench me-1 text-primary"></i>Recommended Correction:</h6>
                                        <code class="small text-danger bg-danger bg-opacity-10 p-2 d-block rounded border-start border-3 border-danger font-monospace mt-1">
                                            {{ $check['recommendation'] }}
                                        </code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
