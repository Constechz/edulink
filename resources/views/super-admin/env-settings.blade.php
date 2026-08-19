@extends('layouts.app')

@section('title', 'Environment Configuration | ' . config('app.name', 'EduLink') . ' Admin')
@section('header_title', config('app.name', 'EduLink') . ' System Environment Editor')

@section('content')
<div class="container-fluid p-0">
    <!-- Session Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm border-0 d-flex align-items-center" role="alert" style="background: rgba(16, 185, 129, 0.12); color: #059669; border-left: 4px solid #10b981 !important;">
            <i class="bi bi-check-circle-fill fs-5 me-2.5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4 shadow-sm border-0" role="alert" style="background: rgba(239, 68, 68, 0.1); color: #dc2626; border-left: 4px solid #ef4444 !important;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Executive Header Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 text-white rounded-4 shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #002244 0%, #003366 60%, #0f4c81 100%); border: 1px solid rgba(255, 215, 0, 0.15);">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <h2 class="fw-bold mb-2" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                            Platform Environment &amp; <span style="color: var(--accent-color, #FFD700);">Secrets Editor</span>
                        </h2>
                        <p class="text-white-50 mb-0" style="font-size: 0.9rem; max-width: 650px;">
                            Direct low-level configuration of database drivers, mail gateways, redis caches, and third-party API credentials with automatic snapshot backups.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('super-admin.settings') }}" class="btn btn-warning rounded-3 px-3 py-2 fw-bold text-decoration-none shadow-sm">
                            <i class="bi bi-gear-fill me-1"></i>General Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Environment Editor Column -->
        <div class="col-lg-8">
            <div class="glass-card p-4 h-100 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1"><i class="bi bi-code-square text-primary me-2"></i>Environment Configuration File (.env)</h5>
                        <p class="text-muted small mb-0">Modify raw environment variables. Saving immediately flushes application cache.</p>
                    </div>
                </div>

                <form action="{{ route('super-admin.env-settings.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4 position-relative">
                        <label for="env_content" class="form-label d-none">Environment File Content</label>
                        <!-- Code-like environment textarea -->
                        <textarea class="form-control rounded-4 p-3 border shadow-xs" 
                                  id="env_content" 
                                  name="env_content" 
                                  rows="22" 
                                  style="font-family: 'Courier New', Courier, monospace; font-size: 14px; line-height: 1.5; background-color: #0b0f19; color: #38bdf8; resize: vertical; border: 1px solid rgba(255, 255, 255, 0.1);"
                                  required>{{ $envContent }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small"><i class="bi bi-info-circle me-1 text-primary"></i>Saving clears configuration and route caches instantly.</span>
                        <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold">
                            <i class="bi bi-save me-1"></i>Save Environment Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info & Backups Sidebar -->
        <div class="col-lg-4">
            <!-- Warnings Panel -->
            <div class="glass-card p-4 shadow-sm mb-4" style="border-left: 4px solid #f59e0b !important;">
                <h5 class="fw-bold text-warning mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i>Critical Warnings</h5>
                <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                    <li class="mb-2"><strong>Sensitive Credentials:</strong> Contains raw database keys, API secrets, and payment tokens. Protect this view.</li>
                    <li class="mb-2"><strong>Syntax Integrity:</strong> Ensure multi-word configurations are double-quoted (e.g. <code>APP_NAME="EduLink Ghana"</code>).</li>
                    <li class="mb-2"><strong>Encryption Key:</strong> Modifying <code>APP_KEY</code> will invalidate all active session tokens and encrypted records.</li>
                    <li><strong>Connection Limits:</strong> Ensure database connection credentials match your local host server.</li>
                </ul>
            </div>

            <!-- Backups Console -->
            <div class="glass-card p-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-shield-check text-success me-2"></i>Automated Snapshot Backup</h5>
                <p class="text-muted small mb-4">A backup file (<code>.env.bak</code>) is generated automatically before every single save transaction.</p>

                @if($backupExists)
                    <div class="p-3 bg-light rounded-4 border mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-semibold text-secondary">Last Backup Generated:</span>
                            <span class="badge bg-success-subtle text-success py-1 px-2 rounded-pill small fw-bold">Active Snapshot</span>
                        </div>
                        <code class="text-primary d-block small mt-1">{{ $backupTime }}</code>
                    </div>

                    <form action="{{ route('super-admin.env-settings.restore') }}" method="POST" onsubmit="return confirm('WARNING: Are you sure you want to revert the system to the backup configuration? Current settings will be overwritten.');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 rounded-3 py-2 fw-semibold">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Restore Snapshot (.env.bak)
                        </button>
                    </form>
                @else
                    <div class="p-4 bg-light rounded-4 border text-center">
                        <i class="bi bi-shield-slash text-muted fs-2 d-block mb-2"></i>
                        <span class="small text-muted d-block">No automatic snapshot created yet. Saving changes will automatically generate your first backup.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
