@extends('layouts.app')

@section('title', 'Scoring Configurations | EduLink')
@section('header_title', 'SBA Scoring Systems & Component Config')

@section('content')
<style>
    .btn-filter {
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        color: var(--text-muted);
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    
    .btn-filter:hover {
        background-color: rgba(0, 51, 102, 0.05);
        color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-filter.active {
        background-color: var(--primary-color);
        color: #fff !important;
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(0, 51, 102, 0.25);
    }

    .config-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .config-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06) !important;
        border-color: rgba(0, 51, 102, 0.15) !important;
    }

    .empty-state-icon {
        animation: pulse-gear 3s infinite;
    }

    @keyframes pulse-gear {
        0% { transform: scale(1); opacity: 0.8; }
        50% { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(1); opacity: 0.8; }
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
</style>

<div class="container-fluid p-0">

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Dashboard Header Banner -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 font-weight-bold" style="font-weight: 700; color: var(--primary-color);">Continuous Assessment Rulesets</h4>
            <p class="text-muted small mb-0">Configure raw component marks, target weight scaling, and rounding per school level.</p>
        </div>
        <a href="{{ route('school.scoring-configs.create') }}" class="btn btn-primary px-4 py-2.5 d-inline-flex align-items-center gap-2" style="border-radius: 12px; background-color: var(--primary-color); border: none; font-weight: 600;">
            <i class="bi bi-plus-circle-fill"></i> New Ruleset Wizard
        </a>
    </div>

    <!-- Quick Stats Cards Section -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(0, 51, 102, 0.1) !important;">
                <div>
                    <span class="text-muted small d-block">Active Rulesets</span>
                    <span class="fs-3 fw-bold" style="color: var(--primary-color);">{{ count($configs) }}</span>
                </div>
                <div class="fs-2 text-primary bg-primary bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-sliders"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(25, 135, 84, 0.1) !important;">
                <div>
                    <span class="text-muted small d-block">Default Systems</span>
                    <span class="fs-3 fw-bold text-success">{{ $configs->where('is_default', true)->count() }}</span>
                </div>
                <div class="fs-2 text-success bg-success bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(255, 215, 0, 0.15) !important;">
                <div>
                    <span class="text-muted small d-block">Avg. Class Weight</span>
                    <span class="fs-3 fw-bold text-warning" style="color: #b08d00 !important;">{{ number_format($configs->avg('class_score_weight') ?? 0, 0) }}%</span>
                </div>
                <div class="fs-2 text-warning bg-warning bg-opacity-10 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; color: #b08d00 !important;">
                    <i class="bi bi-percent"></i>
                </div>
            </div>
        </div>
    </div>

    @if(count($configs) > 0)
        <!-- Interactive Level Filter Pills -->
        <div class="d-flex flex-wrap gap-2 mb-4">
            <button class="btn btn-sm btn-filter active px-3 py-2 rounded-3 fw-bold" data-level="all">All Levels</button>
            <button class="btn btn-sm btn-filter px-3 py-2 rounded-3 fw-bold" data-level="Primary">Primary</button>
            <button class="btn btn-sm btn-filter px-3 py-2 rounded-3 fw-bold" data-level="JHS">JHS</button>
            <button class="btn btn-sm btn-filter px-3 py-2 rounded-3 fw-bold" data-level="SHS">SHS</button>
            <button class="btn btn-sm btn-filter px-3 py-2 rounded-3 fw-bold" data-level="Nursery">Nursery & KG</button>
            <button class="btn btn-sm btn-filter px-3 py-2 rounded-3 fw-bold" data-level="Tertiary">Tertiary</button>
        </div>

        <!-- Configurations Cards Grid Layout -->
        <div class="row g-4 mb-5" id="configs-grid">
            @foreach($configs as $cfg)
                <div class="col-md-6 col-lg-4 config-item-card" data-level="{{ $cfg->level }}">
                    <div class="glass-card config-card p-4 h-100 d-flex flex-column position-relative overflow-hidden" style="border: 1px solid rgba(0,0,0,0.06); background: #ffffff;">
                        
                        <!-- Default ruleset golden stamp -->
                        @if($cfg->is_default)
                            <div class="position-absolute top-0 right-0 p-3 text-warning" title="Default ruleset for this level" style="right: 5px; top: 5px; z-index: 10;">
                                <i class="bi bi-patch-check-fill fs-4" style="color: #FFD700;"></i>
                            </div>
                        @endif

                        <div class="mb-3">
                            <h5 class="fw-bold mb-1 text-dark" style="font-weight: 700; width: 85%; font-size: 1.15rem; line-height: 1.3;">{{ $cfg->name }}</h5>
                            <span class="text-muted small fw-semibold">ID: #{{ $cfg->id }}</span>
                        </div>

                        <!-- Scope metadata badges -->
                        <div class="d-flex flex-wrap gap-1.5 mb-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1.5 font-weight-medium" style="border-radius: 8px; font-size: 0.75rem;">
                                <i class="bi bi-layers-half me-1"></i>Level: {{ $cfg->level }}
                            </span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-2.5 py-1.5 font-weight-medium" style="border-radius: 8px; font-size: 0.75rem;">
                                <i class="bi bi-journal-text me-1"></i>{{ $cfg->subject ? $cfg->subject->name : 'All Subjects' }}
                            </span>
                        </div>

                        <!-- Class Continuous Assessment Component Lists Preview -->
                        <div class="mb-4 flex-grow-1">
                            <div class="small text-secondary mb-2 fw-bold" style="font-size: 0.8rem;"><i class="bi bi-tags me-1"></i> Class Assessment Components ({{ $cfg->components->count() }}):</div>
                            <div class="d-flex flex-wrap gap-1.5">
                                @foreach($cfg->components as $component)
                                    <span class="badge border border-secondary border-opacity-25 text-secondary px-2 py-1 fw-normal" style="font-size: 0.75rem; border-radius: 6px; background-color: #f8fafc;">
                                        {{ $component->name }} <strong class="text-dark">({{ $component->max_marks }}m)</strong>
                                    </span>
                                @endforeach
                                @if($cfg->components->isEmpty())
                                    <span class="text-muted small italic">No components configured</span>
                                @endif
                            </div>
                        </div>

                        <!-- Weight ratio progress bar indicator (Class vs Exam split) -->
                        <div class="mb-4 mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-1.5" style="font-size: 0.82rem;">
                                <span class="text-primary fw-bold"><i class="bi bi-book-half me-1"></i>Class Assessment: {{ $cfg->class_score_weight }}%</span>
                                <span class="text-warning fw-bold" style="color: #b08d00 !important;"><i class="bi bi-pencil-square me-1"></i>Terminal Exam: {{ $cfg->exam_score_weight }}%</span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 6px; overflow: hidden; background-color: rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.02);">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $cfg->class_score_weight }}%" aria-valuenow="{{ $cfg->class_score_weight }}" aria-valuemin="0" aria-valuemax="100" title="Class weight"></div>
                                <div class="progress-bar" role="progressbar" style="width: {{ $cfg->exam_score_weight }}%; background-color: #FFD700 !important;" aria-valuenow="{{ $cfg->exam_score_weight }}" aria-valuemin="0" aria-valuemax="100" title="Exam weight"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted small mt-2" style="font-size: 0.73rem;">
                                <span>Raw Max: <strong>{{ $cfg->class_score_max }}m</strong></span>
                                <span>Rounding: <strong>{{ $cfg->rounding_method }} ({{ $cfg->decimal_places }}dp)</strong></span>
                                <span>Raw Max: <strong>{{ $cfg->exam_score_max }}m</strong></span>
                            </div>
                        </div>

                        <!-- Card Action Footer Buttons -->
                        <div class="border-top border-light pt-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <a href="{{ route('school.scoring-configs.show', $cfg->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5 px-3 py-2 rounded-3 shadow-xs fw-bold" style="font-size: 0.78rem;">
                                    <i class="bi bi-eye"></i> View details
                                </a>
                                <div class="d-flex align-items-center gap-1.5">
                                    <a href="{{ route('school.scoring-configs.edit', $cfg->id) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5 px-2.5 py-2 rounded-3 shadow-xs fw-bold" style="font-size: 0.78rem;" title="Edit config">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="{{ route('school.scoring-configs.destroy', $cfg->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this scoring configuration? This might fail if scores depend on it.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center rounded-3 shadow-xs" style="width: 32px; height: 32px; padding: 0;" title="Delete ruleset">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Premium Redesigned Empty State -->
        <div class="glass-card p-5 text-center text-muted d-flex flex-column align-items-center justify-content-center" style="border-radius: 20px; border: 1px dashed rgba(0, 51, 102, 0.2); background: #ffffff;">
            <div class="empty-state-icon text-warning bg-warning bg-opacity-10 p-4 rounded-circle mb-4 d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; color: #b08d00 !important;">
                <i class="bi bi-sliders display-4"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">No Scoring Configurations Found</h4>
            <p class="mb-1 text-secondary">You need to set up scoring configurations and rulesets before entering continuous assessment grades.</p>
            <p class="small text-muted mb-4">Configurations allow you to allocate homework, tests, classwork weights, and final terminal exams scaling.</p>
            <a href="{{ route('school.scoring-configs.create') }}" class="btn btn-primary px-4 py-2.5 rounded-3 fw-bold shadow-sm" style="background-color: var(--primary-color); border: none;">
                <i class="bi bi-magic me-2"></i> Launch Setup Wizard
            </a>
        </div>
    @endif

</div>

<!-- Vanilla JS level sorting filter -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterButtons = document.querySelectorAll('.btn-filter');
        const configCards = document.querySelectorAll('.config-item-card');

        filterButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Remove active classes
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Make current class active
                this.classList.add('active');

                const targetLevel = this.getAttribute('data-level');

                configCards.forEach(card => {
                    const cardLevel = card.getAttribute('data-level');
                    
                    if (targetLevel === 'all') {
                        card.style.display = 'block';
                        setTimeout(() => card.style.opacity = '1', 50);
                    } else if (targetLevel === 'Nursery') {
                        // KG and Nursery match Nursery filter
                        if (cardLevel === 'Nursery' || cardLevel === 'KG') {
                            card.style.display = 'block';
                            setTimeout(() => card.style.opacity = '1', 50);
                        } else {
                            card.style.opacity = '0';
                            card.style.display = 'none';
                        }
                    } else {
                        if (cardLevel === targetLevel) {
                            card.style.display = 'block';
                            setTimeout(() => card.style.opacity = '1', 50);
                        } else {
                            card.style.opacity = '0';
                            card.style.display = 'none';
                        }
                    }
                });
            });
        });
    });
</script>
@endsection
