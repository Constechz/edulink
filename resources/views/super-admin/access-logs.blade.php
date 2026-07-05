@extends('layouts.app')

@section('title', 'Platform Access Logs | ' . config('app.name', 'EduLink') . ' Admin')
@section('header_title', config('app.name', 'EduLink') . ' System Access & Audit Logs')

@section('content')
<div class="container-fluid p-0">
    <div class="row">
        <!-- Access Logs Directory -->
        <div class="col-md-12">
            <div class="glass-card p-4">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Platform Security Audit Directory</h5>
                
                @if($logs->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary"></i>
                        <span>No system audit logs recorded.</span>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle table-hover small">
                            <thead>
                                <tr>
                                    <th>School Tenant</th>
                                    <th>User Info</th>
                                    <th>Event Action</th>
                                    <th>Model Context</th>
                                    <th>Network Address (IP)</th>
                                    <th>User Agent Metadata</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    <tr>
                                        <td>
                                            @if($log->school)
                                                <span class="fw-semibold text-dark">{{ $log->school->name }}</span>
                                            @else
                                                <span class="badge bg-secondary">System-Wide</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->user)
                                                <span class="fw-bold text-dark d-block">{{ $log->user->name }}</span>
                                                <span class="text-muted small">{{ $log->user->email }}</span>
                                            @else
                                                <span class="text-muted small">Unauthenticated / System</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark text-uppercase border">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($log->model_type)
                                                <span class="text-dark d-block">
                                                    {{ class_basename($log->model_type) }} (ID: {{ $log->model_id }})
                                                </span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <code class="text-secondary">{{ $log->ip_address ?: 'N/A' }}</code>
                                        </td>
                                        <td>
                                            <span class="text-muted text-truncate d-inline-block" style="max-width: 180px;" title="{{ $log->user_agent }}">
                                                {{ $log->user_agent ?: 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="text-muted">
                                            {{ $log->created_at ? $log->created_at->format('M d, Y H:i:s') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
