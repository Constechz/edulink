@extends('layouts.app')

@section('title', 'Help Center Customizer | ' . \App\Models\SystemSetting::getVal('platform_name', 'EduLink') . ' Admin')
@section('header_title', 'Help Center Customizer')

@section('content')
<div class="container-fluid p-0">

    <!-- Status Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm border-0 d-flex align-items-center" role="alert" style="background: rgba(16, 185, 129, 0.12); color: #059669; border-left: 4px solid #10b981 !important;">
            <i class="bi bi-check-circle-fill fs-5 me-2.5"></i>
            <div>{{ session('success') }}</div>
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
                            Help Center &amp; <span style="color: var(--accent-color, #FFD700);">SBA Manuals Customizer</span>
                        </h2>
                        <p class="text-white-50 mb-0" style="font-size: 0.9rem; max-width: 650px;">
                            Tailor role-specific manuals, GES continuous assessment calculation references, rollout roadmaps, and dynamic video walkthroughs displayed across school portals.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('super-admin.documentation.index') }}" class="btn btn-warning rounded-3 px-3 py-2 fw-bold text-decoration-none shadow-sm">
                            <i class="bi bi-journal-text me-1"></i>Knowledge Base
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('super-admin.help-settings.update') }}" method="POST" id="helpSettingsForm">
        @csrf
        <div class="row">
            <!-- Sidebar Navigation Tabs -->
            <div class="col-lg-3 mb-4">
                <div class="card border-0 shadow-sm glass-card p-3">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-manuals-tab" data-bs-toggle="pill" data-bs-target="#v-pills-manuals" type="button" role="tab" aria-selected="true">
                            <i class="bi bi-people me-2 text-primary font-weight-bold"></i> Role Manuals
                        </button>
                        <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-formulas-tab" data-bs-toggle="pill" data-bs-target="#v-pills-formulas" type="button" role="tab" aria-selected="false">
                            <i class="bi bi-calculator me-2 text-success font-weight-bold"></i> SBA Formulas
                        </button>
                        <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-roadmap-tab" data-bs-toggle="pill" data-bs-target="#v-pills-roadmap" type="button" role="tab" aria-selected="false">
                            <i class="bi bi-signpost-split me-2 text-warning font-weight-bold"></i> Roadmap Milestones
                        </button>
                        <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-training-tab" data-bs-toggle="pill" data-bs-target="#v-pills-training" type="button" role="tab" aria-selected="false">
                            <i class="bi bi-play-circle me-2 text-danger font-weight-bold"></i> Training Videos
                        </button>
                    </div>
                </div>

                <div class="card border-0 shadow-sm glass-card p-3 mt-4">
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold text-dark shadow-sm">
                        <i class="bi bi-save me-2"></i>Save Configuration
                    </button>
                </div>
            </div>

            <!-- Main Reference Customizer Content -->
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm glass-card p-4">
                    <div class="tab-content" id="v-pills-tabContent">
                        
                        <!-- Tab 1: Role Manuals -->
                        <div class="tab-pane fade show active" id="v-pills-manuals" role="tabpanel">
                            <h4 class="fw-bold mb-3 text-primary">Customize Role Manuals</h4>
                            <p class="text-muted small">Update manual descriptions, icons, and item checklists. Put each list item on a new line.</p>
                            
                            @foreach($manuals as $manual)
                            <div class="card border rounded-3 p-4 mb-4 bg-light shadow-none">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-bold small">Manual Title</label>
                                        <input type="text" name="manuals[{{ $manual['key'] }}][title]" class="form-control bg-white" value="{{ $manual['title'] }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-dark fw-bold small">Bootstrap Icon Class</label>
                                        <input type="text" name="manuals[{{ $manual['key'] }}][icon]" class="form-control bg-white" value="{{ $manual['icon'] }}" placeholder="bi-people" required>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="manuals[{{ $manual['key'] }}][is_super_only]" id="superOnly{{ $manual['key'] }}" {{ ($manual['is_super_only'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label text-danger small fw-bold" for="superOnly{{ $manual['key'] }}">
                                                Super Only
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-dark fw-bold small">Short Description</label>
                                        <input type="text" name="manuals[{{ $manual['key'] }}][description]" class="form-control bg-white" value="{{ $manual['description'] }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-dark fw-bold small">Checklist Items (One per line)</label>
                                        <textarea name="manuals[{{ $manual['key'] }}][items]" class="form-control bg-white" rows="4" required>{{ implode("\n", $manual['items']) }}</textarea>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Tab 2: SBA Formulas -->
                        <div class="tab-pane fade" id="v-pills-formulas" role="tabpanel">
                            <h4 class="fw-bold mb-3 text-primary">Customize Continuous Assessment Formulas</h4>
                            <p class="text-muted small">Update quick-reference scaling mathematical formulas and grading examples shown to teachers.</p>

                            <div class="card border rounded-3 p-4 bg-light shadow-none">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label text-dark fw-bold small">Class Score Scaling Formula</label>
                                        <input type="text" name="quick_ref[formula_class]" class="form-control bg-white" value="{{ $quickRefSba['formula_class'] ?? '' }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-dark fw-bold small">Exam Score Scaling Formula</label>
                                        <input type="text" name="quick_ref[formula_exam]" class="form-control bg-white" value="{{ $quickRefSba['formula_exam'] ?? '' }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-dark fw-bold small">Grading Example Paragraph</label>
                                        <textarea name="quick_ref[example_text]" class="form-control bg-white" rows="4" required>{{ $quickRefSba['example_text'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Roadmap Milestones -->
                        <div class="tab-pane fade" id="v-pills-roadmap" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h4 class="fw-bold mb-1 text-primary">Customize Milestone Roadmap</h4>
                                    <p class="text-muted small mb-0">Modify deployment roadmap years, labels, and timeline details.</p>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-3 fw-bold px-3" onclick="addMilestoneCard()">
                                    <i class="bi bi-plus-circle me-1"></i>Add Milestone
                                </button>
                            </div>

                            <div id="roadmapMilestonesContainer">
                                @forelse($roadmap as $i => $milestone)
                                <div class="card border rounded-3 p-4 mb-4 bg-light shadow-none milestone-card" data-index="{{ $i }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="badge bg-warning-subtle text-warning-emphasis fw-bold px-2.5 py-1">
                                            <i class="bi bi-flag-fill me-1"></i>Milestone #<span class="milestone-num">{{ $i + 1 }}</span>
                                        </span>
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-2 px-2.5 py-1" onclick="removeMilestoneCard(this)" title="Remove Milestone">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label text-dark fw-bold small">Milestone Title</label>
                                            <input type="text" name="roadmap[{{ $i }}][title]" class="form-control bg-white" value="{{ $milestone['title'] ?? '' }}" placeholder="Year {{ $i + 1 }} - Milestone name" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label text-dark fw-bold small">Timeline Node Color</label>
                                            <select name="roadmap[{{ $i }}][color]" class="form-select bg-white">
                                                <option value="primary" {{ ($milestone['color'] ?? '') === 'primary' ? 'selected' : '' }}>Blue (Primary)</option>
                                                <option value="secondary" {{ ($milestone['color'] ?? '') === 'secondary' ? 'selected' : '' }}>Gray (Secondary)</option>
                                                <option value="success" {{ ($milestone['color'] ?? '') === 'success' ? 'selected' : '' }}>Green (Success)</option>
                                                <option value="warning" {{ ($milestone['color'] ?? '') === 'warning' ? 'selected' : '' }}>Orange (Warning)</option>
                                                <option value="danger" {{ ($milestone['color'] ?? '') === 'danger' ? 'selected' : '' }}>Red (Danger)</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-dark fw-bold small">Detailed Description</label>
                                            <textarea name="roadmap[{{ $i }}][description]" class="form-control bg-white" rows="3" required>{{ $milestone['description'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div id="noMilestonesAlert" class="p-4 bg-light rounded-4 border text-center mb-4">
                                    <i class="bi bi-signpost-2 text-muted fs-2 d-block mb-2"></i>
                                    <p class="text-muted small mb-2">No roadmap milestones added yet.</p>
                                    <button type="button" class="btn btn-sm btn-primary rounded-3 px-3" onclick="addMilestoneCard()">
                                        <i class="bi bi-plus-lg me-1"></i>Add First Milestone
                                    </button>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Tab 4: Training Videos (DYNAMIC) -->
                        <div class="tab-pane fade" id="v-pills-training" role="tabpanel">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3 pb-2 border-bottom">
                                <div>
                                    <h4 class="fw-bold mb-1 text-primary">Customize Training Videos Details</h4>
                                    <p class="text-muted small mb-0">Add, reorder, or update video tutorials, durations, YouTube embed links, and walkthrough scripts.</p>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary btn-sm rounded-3 fw-bold px-3 py-2 text-dark shadow-sm d-inline-flex align-items-center" onclick="addVideoCard()">
                                        <i class="bi bi-plus-circle-fill me-1.5 fs-6"></i>Add Training Video
                                    </button>
                                </div>
                            </div>

                            <!-- Dynamic Videos Container -->
                            <div id="trainingVideosContainer">
                                @forelse($trainingVideos as $i => $video)
                                <div class="card border rounded-3 p-4 mb-4 bg-light shadow-none video-card" data-index="{{ $i }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="badge bg-danger-subtle text-danger fw-bold px-2.5 py-1 d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                            <i class="bi bi-play-circle-fill"></i>
                                            <span>Video #<span class="video-num">{{ $i + 1 }}</span></span>
                                        </span>
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-2 px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1" onclick="removeVideoCard(this)" title="Delete Video">
                                            <i class="bi bi-trash"></i>
                                            <span>Remove</span>
                                        </button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-7">
                                            <label class="form-label text-dark fw-bold small">Video Title &amp; Duration</label>
                                            <input type="text" name="videos[{{ $i }}][title]" class="form-control bg-white" value="{{ $video['title'] ?? '' }}" placeholder="e.g. 1. Platform Overview (10 mins)" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label text-dark fw-bold small">YouTube Video URL (Optional)</label>
                                            <input type="url" name="videos[{{ $i }}][youtube_url]" class="form-control bg-white" value="{{ $video['youtube_url'] ?? '' }}" placeholder="https://www.youtube.com/watch?v=...">
                                            <div class="form-text text-muted small" style="font-size: 0.72rem;">Supports full YouTube URLs or short links (youtu.be).</div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-dark fw-bold small">Tutorial Script Details</label>
                                            <textarea name="videos[{{ $i }}][description]" class="form-control bg-white" rows="3" placeholder="Brief summary of what this video covers..." required>{{ $video['description'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div id="noVideosAlert" class="p-5 bg-light rounded-4 border text-center mb-4">
                                    <i class="bi bi-camera-reels text-muted fs-1 d-block mb-2"></i>
                                    <h6 class="fw-bold text-dark mb-1">No Training Videos Added Yet</h6>
                                    <p class="text-muted small mb-3">Add video guides to assist school administrators and staff during onboarding.</p>
                                    <button type="button" class="btn btn-primary rounded-3 px-4 py-2 fw-bold text-dark" onclick="addVideoCard()">
                                        <i class="bi bi-plus-circle-fill me-1"></i>Add First Video
                                    </button>
                                </div>
                                @endforelse
                            </div>

                            <div class="d-flex justify-content-start mt-2">
                                <button type="button" class="btn btn-outline-primary rounded-3 fw-semibold px-3 py-2 d-inline-flex align-items-center gap-1.5" onclick="addVideoCard()">
                                    <i class="bi bi-plus-lg"></i>
                                    <span>Add Another Video</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Dynamic Video Card Template -->
<template id="videoCardTemplate">
    <div class="card border rounded-3 p-4 mb-4 bg-light shadow-none video-card" data-index="__INDEX__">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge bg-danger-subtle text-danger fw-bold px-2.5 py-1 d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                <i class="bi bi-play-circle-fill"></i>
                <span>Video #<span class="video-num">__NUM__</span></span>
            </span>
            <button type="button" class="btn btn-outline-danger btn-sm rounded-2 px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1" onclick="removeVideoCard(this)" title="Delete Video">
                <i class="bi bi-trash"></i>
                <span>Remove</span>
            </button>
        </div>
        <div class="row g-3">
            <div class="col-md-7">
                <label class="form-label text-dark fw-bold small">Video Title &amp; Duration</label>
                <input type="text" name="videos[__INDEX__][title]" class="form-control bg-white" placeholder="e.g. 1. Platform Overview (10 mins)" required>
            </div>
            <div class="col-md-5">
                <label class="form-label text-dark fw-bold small">YouTube Video URL (Optional)</label>
                <input type="url" name="videos[__INDEX__][youtube_url]" class="form-control bg-white" placeholder="https://www.youtube.com/watch?v=...">
                <div class="form-text text-muted small" style="font-size: 0.72rem;">Supports full YouTube URLs or short links (youtu.be).</div>
            </div>
            <div class="col-12">
                <label class="form-label text-dark fw-bold small">Tutorial Script Details</label>
                <textarea name="videos[__INDEX__][description]" class="form-control bg-white" rows="3" placeholder="Brief summary of what this video covers..." required></textarea>
            </div>
        </div>
    </div>
</template>

<!-- Dynamic Milestone Card Template -->
<template id="milestoneCardTemplate">
    <div class="card border rounded-3 p-4 mb-4 bg-light shadow-none milestone-card" data-index="__INDEX__">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge bg-warning-subtle text-warning-emphasis fw-bold px-2.5 py-1">
                <i class="bi bi-flag-fill me-1"></i>Milestone #<span class="milestone-num">__NUM__</span>
            </span>
            <button type="button" class="btn btn-outline-danger btn-sm rounded-2 px-2.5 py-1" onclick="removeMilestoneCard(this)" title="Remove Milestone">
                <i class="bi bi-trash me-1"></i>Delete
            </button>
        </div>
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label text-dark fw-bold small">Milestone Title</label>
                <input type="text" name="roadmap[__INDEX__][title]" class="form-control bg-white" placeholder="Year __NUM__ - Milestone name" required>
            </div>
            <div class="col-md-4">
                <label class="form-label text-dark fw-bold small">Timeline Node Color</label>
                <select name="roadmap[__INDEX__][color]" class="form-select bg-white">
                    <option value="primary" selected>Blue (Primary)</option>
                    <option value="secondary">Gray (Secondary)</option>
                    <option value="success">Green (Success)</option>
                    <option value="warning">Orange (Warning)</option>
                    <option value="danger">Red (Danger)</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label text-dark fw-bold small">Detailed Description</label>
                <textarea name="roadmap[__INDEX__][description]" class="form-control bg-white" rows="3" placeholder="Describe key deliverables for this milestone..." required></textarea>
            </div>
        </div>
    </div>
</template>

@endsection

@section('scripts')
<script>
    // ----------------------------------------------------
    // DYNAMIC TRAINING VIDEOS BUILDER
    // ----------------------------------------------------
    function addVideoCard() {
        const container = document.getElementById('trainingVideosContainer');
        const emptyAlert = document.getElementById('noVideosAlert');
        if (emptyAlert) {
            emptyAlert.remove();
        }

        const template = document.getElementById('videoCardTemplate').innerHTML;
        const currentCount = container.querySelectorAll('.video-card').length;
        const newIndex = Date.now(); // unique key to avoid collision
        const newNum = currentCount + 1;

        const renderedHtml = template
            .replace(/__INDEX__/g, newIndex)
            .replace(/__NUM__/g, newNum);

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = renderedHtml.trim();
        const newCard = tempDiv.firstChild;

        container.appendChild(newCard);
        reindexVideoCards();

        // Focus the title field of the new video card
        const titleInput = newCard.querySelector('input[type="text"]');
        if (titleInput) {
            titleInput.focus();
        }
    }

    function removeVideoCard(button) {
        if (!confirm('Are you sure you want to delete this training video?')) {
            return;
        }
        const card = button.closest('.video-card');
        if (card) {
            card.remove();
            reindexVideoCards();
        }
    }

    function reindexVideoCards() {
        const container = document.getElementById('trainingVideosContainer');
        const cards = container.querySelectorAll('.video-card');
        
        cards.forEach((card, idx) => {
            const numSpan = card.querySelector('.video-num');
            if (numSpan) {
                numSpan.textContent = idx + 1;
            }
        });

        if (cards.length === 0 && !document.getElementById('noVideosAlert')) {
            const emptyDiv = document.createElement('div');
            emptyDiv.id = 'noVideosAlert';
            emptyDiv.className = 'p-5 bg-light rounded-4 border text-center mb-4';
            emptyDiv.innerHTML = `
                <i class="bi bi-camera-reels text-muted fs-1 d-block mb-2"></i>
                <h6 class="fw-bold text-dark mb-1">No Training Videos Added Yet</h6>
                <p class="text-muted small mb-3">Add video guides to assist school administrators and staff during onboarding.</p>
                <button type="button" class="btn btn-primary rounded-3 px-4 py-2 fw-bold text-dark" onclick="addVideoCard()">
                    <i class="bi bi-plus-circle-fill me-1"></i>Add First Video
                </button>
            `;
            container.appendChild(emptyDiv);
        }
    }

    // ----------------------------------------------------
    // DYNAMIC ROADMAP MILESTONES BUILDER
    // ----------------------------------------------------
    function addMilestoneCard() {
        const container = document.getElementById('roadmapMilestonesContainer');
        const emptyAlert = document.getElementById('noMilestonesAlert');
        if (emptyAlert) {
            emptyAlert.remove();
        }

        const template = document.getElementById('milestoneCardTemplate').innerHTML;
        const currentCount = container.querySelectorAll('.milestone-card').length;
        const newIndex = Date.now();
        const newNum = currentCount + 1;

        const renderedHtml = template
            .replace(/__INDEX__/g, newIndex)
            .replace(/__NUM__/g, newNum);

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = renderedHtml.trim();
        const newCard = tempDiv.firstChild;

        container.appendChild(newCard);
        reindexMilestoneCards();

        const titleInput = newCard.querySelector('input[type="text"]');
        if (titleInput) {
            titleInput.focus();
        }
    }

    function removeMilestoneCard(button) {
        if (!confirm('Are you sure you want to delete this roadmap milestone?')) {
            return;
        }
        const card = button.closest('.milestone-card');
        if (card) {
            card.remove();
            reindexMilestoneCards();
        }
    }

    function reindexMilestoneCards() {
        const container = document.getElementById('roadmapMilestonesContainer');
        const cards = container.querySelectorAll('.milestone-card');
        
        cards.forEach((card, idx) => {
            const numSpan = card.querySelector('.milestone-num');
            if (numSpan) {
                numSpan.textContent = idx + 1;
            }
        });

        if (cards.length === 0 && !document.getElementById('noMilestonesAlert')) {
            const emptyDiv = document.createElement('div');
            emptyDiv.id = 'noMilestonesAlert';
            emptyDiv.className = 'p-4 bg-light rounded-4 border text-center mb-4';
            emptyDiv.innerHTML = `
                <i class="bi bi-signpost-2 text-muted fs-2 d-block mb-2"></i>
                <p class="text-muted small mb-2">No roadmap milestones added yet.</p>
                <button type="button" class="btn btn-sm btn-primary rounded-3 px-3" onclick="addMilestoneCard()">
                    <i class="bi bi-plus-lg me-1"></i>Add First Milestone
                </button>
            `;
            container.appendChild(emptyDiv);
        }
    }
</script>
@endsection
