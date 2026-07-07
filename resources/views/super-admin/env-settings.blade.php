@extends('layouts.app')

@section('title', 'Environment Configuration | ' . config('app.name', 'EduLink') . ' Admin')
@section('header_title', config('app.name', 'EduLink') . ' System Environment Editor')

@section('content')
<div class="container-fluid p-0">
    <!-- Session Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Environment Editor Column -->
        <div class="col-lg-8">
            <div class="glass-card p-4 h-100 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1"><i class="bi bi-file-earmark-code text-primary me-2"></i>Environment Configuration File (.env)</h5>
                        <p class="text-muted small mb-0">Modify raw environment configuration variables. Each save automatically backs up the current state.</p>
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
                                  style="font-family: 'Courier New', Courier, monospace; font-size: 14px; line-height: 1.5; background-color: #1e1e1e; color: #d4d4d4; resize: vertical; border: 1px solid #333;"
                                  required>{{ $envContent }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Saving clears configuration caches immediately.</span>
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
            <div class="glass-card p-4 shadow-sm mb-4 border border-warning" style="border-width: 2px !important;">
                <h5 class="fw-bold text-warning mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i>Critical Warnings</h5>
                <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                    <li class="mb-2"><strong>Sensitive Credentials:</strong> Do not share screenshots or allow unauthorized users access to this screen. This file contains raw database keys, API gateway secrets, and payment credentials.</li>
                    <li class="mb-2"><strong>Syntax Integrity:</strong> Ensure variables do not contain unquoted spaces. Use quotes for multi-word configurations (e.g. <code>APP_NAME="EduLink Ghana"</code>).</li>
                    <li class="mb-2"><strong>Encryption Key:</strong> Modifying the <code>APP_KEY</code> will render existing user passwords and cookies un-decryptable, effectively locking out all sessions.</li>
                    <li><strong>Connection Limits:</strong> Ensure database settings match your server credentials, otherwise the platform will experience offline outages.</li>
                </ul>
            </div>

            <!-- Backups Console -->
            <div class="glass-card p-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-shield-check text-success me-2"></i>Backup Console</h5>
                <p class="text-muted small mb-4">A backup file (<code>.env.bak</code>) is created automatically in the root folder before any updates are saved.</p>

                @if($backupExists)
                    <div class="p-3 bg-light rounded-4 border mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-semibold text-secondary">Last Backup Created:</span>
                            <span class="badge bg-success bg-opacity-10 text-success py-1 px-2 rounded-pill small">Active Backup</span>
                        </div>
                        <span class="fw-mono text-dark d-block small">{{ $backupTime }}</span>
                    </div>

                    <form action="{{ route('super-admin.env-settings.restore') }}" method="POST" onsubmit="return confirm('WARNING: Are you sure you want to revert the system to the backup configuration? Current settings will be overwritten.');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 rounded-3 py-2 fw-semibold">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Restore Backup (.env.bak)
                        </button>
                    </form>
                @else
                    <div class="p-4 bg-light rounded-4 border text-center">
                        <i class="bi bi-shield-slash text-muted fs-2 d-block mb-2"></i>
                        <span class="small text-muted d-block">No automatic backup found yet. Saving changes will trigger your first backup.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
