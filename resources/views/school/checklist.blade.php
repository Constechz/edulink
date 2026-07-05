@extends('layouts.app')

@section('title', 'Onboarding Checklist | EduLink')
@section('header_title', 'School Setup Assistant')

@section('styles')
<style>
    .progress-track-wrapper {
        background: rgba(255, 255, 255, 0.05);
        border: 1px dashed rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2.5rem;
    }
    .checklist-row {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.2s ease;
        margin-bottom: 1rem;
    }
    .checklist-row:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }
    .status-badge {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    .status-badge.success {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }
    .status-badge.pending {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">

    <!-- Progress overview card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4 text-white" style="background: linear-gradient(135deg, #0b1528 0%, #0f305f 100%); border: 1px solid rgba(255,255,255,0.05);">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 style="font-weight: 700; margin-bottom: 0.5rem;"><i class="bi bi-patch-check me-2 text-warning"></i>Setup Progress: {{ $progressPercent }}% Completed</h2>
                        <p class="mb-0 text-white-50" style="font-size: 0.95rem;">Follow the setup assistant checklist below to complete your tenant configurations and unlock all EduLink school operations.</p>
                    </div>
                    <div class="col-md-4 mt-3 mt-md-0">
                        <div class="d-flex align-items-center justify-content-md-end">
                            <span class="fs-4 fw-bold me-2">{{ $completedItems }}</span>
                            <span class="text-white-50">/ {{ $totalItems }} Tasks Done</span>
                        </div>
                        <div class="progress mt-2" style="height: 8px; background: rgba(255,255,255,0.1); border-radius: 4px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $progressPercent }}%; border-radius: 4px;" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Checklist Items -->
    <div class="row">
        <div class="col-lg-9 mx-auto">
            
            @foreach($checklist as $index => $item)
                @php
                    $isDone = isset($item['status_val']) ? $item['status_val'] : $item['status'];
                @endphp
                <div class="checklist-row d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <!-- Status indicator -->
                        <div class="status-badge {{ $isDone ? 'success' : 'pending' }} me-4">
                            @if($isDone)
                                <i class="bi bi-check-lg"></i>
                            @else
                                <i class="bi bi-exclamation-lg"></i>
                            @endif
                        </div>

                        <div>
                            <h5 class="mb-1 font-weight-bold" style="font-weight: 700; color: var(--primary-color);">
                                {{ $index + 1 }}. {{ $item['title'] }}
                            </h5>
                            <p class="text-muted mb-0 small">{{ $item['desc'] }}</p>
                        </div>
                    </div>

                    <div>
                        @if($isDone)
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-3" style="font-weight: 600;">
                                <i class="bi bi-check-circle-fill me-1"></i>Completed
                            </span>
                        @else
                            <a href="{{ $item['url'] }}" class="btn btn-warning btn-sm px-3 py-2 fw-semibold" style="border-radius: 8px;">
                                Setup <i class="bi bi-arrow-right-short ms-1"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach

        </div>
    </div>

</div>
@endsection
