@extends('layouts.app')

@section('title', 'Platform Access Logs | ' . config('app.name', 'EduLink') . ' Admin')
@section('header_title', config('app.name', 'EduLink') . ' System Access & Security Logs')

@section('content')
<div class="container-fluid p-0">
    <!-- Executive Header Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 text-white rounded-4 shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #002244 0%, #003366 60%, #0f4c81 100%); border: 1px solid rgba(255, 215, 0, 0.15);">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <h2 class="fw-bold mb-2" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                            Platform Security &amp; <span style="color: var(--accent-color, #FFD700);">Audit Trail</span>
                        </h2>
                        <p class="text-white-50 mb-0" style="font-size: 0.9rem; max-width: 650px;">
                            Complete real-time ledger of multi-tenant administrative logins, privilege elevations, record modifications, and IP addresses across all schools in Ghana.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('super-admin.dashboard') }}" class="btn btn-outline-light rounded-3 px-3 py-2 fw-bold text-decoration-none">
                            <i class="bi bi-speedometer2 me-1"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Access Logs Directory Card -->
    <div class="row">
        <div class="col-12">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Security Audit Events</h5>
                    <span class="badge bg-primary-subtle text-primary fw-semibold">Continuous Ingestion</span>
                </div>
                
                @if($logs->isEmpty())
                    <div class="text-center py-5 text-muted border rounded-3 my-2">
                        <div class="p-3 rounded-circle bg-light d-inline-flex mb-2">
                            <i class="bi bi-clock-history fs-2 text-secondary"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">No System Audit Logs Found</h6>
                        <p class="small text-secondary mb-0">Security activities and login sessions will automatically appear here.</p>
                    </div>
                @else
                    <div class="table-responsive border rounded-3 mb-3">
                        <table class="table align-middle table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>School Tenant</th>
                                    <th>User Account</th>
                                    <th>Event Action</th>
                                    <th>Model Context</th>
                                    <th>Network IP Address</th>
                                    <th>Browser / User Agent</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    <tr>
                                        <td>
                                            @if($log->school)
                                                <span class="badge bg-primary-subtle text-primary border px-2 py-1">
                                                    <i class="bi bi-building me-1"></i>{{ $log->school->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning-emphasis border px-2 py-1">
                                                    <i class="bi bi-shield-lock me-1"></i>System Platform
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->user)
                                                <span class="fw-bold text-dark d-block" style="font-size: 0.88rem;">{{ $log->user->name }}</span>
                                                <span class="text-muted small">{{ $log->user->email }}</span>
                                            @else
                                                <span class="text-muted small">Unauthenticated / System</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark text-uppercase border px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($log->model_type)
                                                <span class="text-dark small d-block">
                                                    {{ class_basename($log->model_type) }} <code class="text-primary">#{{ $log->model_id }}</code>
                                                </span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <code class="text-primary small px-2 py-0.5 bg-light border rounded">{{ $log->ip_address ?: 'N/A' }}</code>
                                        </td>
                                        <td>
                                            <span class="text-muted small text-truncate d-inline-block" style="max-width: 190px;" title="{{ $log->user_agent }}">
                                                {{ $log->user_agent ?: 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="small text-secondary fw-semibold">{{ $log->created_at ? $log->created_at->format('M d, Y H:i:s') : 'N/A' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span class="text-muted small">Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} audit logs</span>
                        <div>{{ $logs->links() }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
