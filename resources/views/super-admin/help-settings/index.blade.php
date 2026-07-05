@extends('layouts.app')

@section('title', 'Help Center Customizer | ' . \App\Models\SystemSetting::getVal('platform_name', 'EduLink') . ' Admin')
@section('header_title', 'Help Center Customizer')

@section('content')
<div class="container-fluid p-0">

    <!-- Status Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show glass-card p-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="glass-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">Customize Help Reference Hub</h5>
                <p class="text-muted small mb-0">Modify role manuals, SBA formulas, milestone roadmaps, and training video descriptions displayed to your platform tenants.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('super-admin.help-settings.update') }}" method="POST">
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
                                        <textarea name="manuals[{{ $manual['key'] }}][items]" class="form-control bg-white rows-4" rows="4" required>{{ implode("\n", $manual['items']) }}</textarea>
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
                            <h4 class="fw-bold mb-3 text-primary">Customize Milestone Roadmap</h4>
                            <p class="text-muted small">Modify deployment roadmap years, labels, and timeline details.</p>

                            @for($i = 0; $i < 3; $i++)
                            @php $milestone = $roadmap[$i] ?? ['title' => '', 'color' => 'primary', 'description' => '']; @endphp
                            <div class="card border rounded-3 p-4 mb-4 bg-light shadow-none">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label text-dark fw-bold small">Milestone Title</label>
                                        <input type="text" name="roadmap[{{ $i }}][title]" class="form-control bg-white" value="{{ $milestone['title'] }}" placeholder="Year {{ $i+1 }} - Milestone name" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-dark fw-bold small">Timeline Node Color</label>
                                        <select name="roadmap[{{ $i }}][color]" class="form-select bg-white">
                                            <option value="primary" {{ $milestone['color'] === 'primary' ? 'selected' : '' }}>Blue (Primary)</option>
                                            <option value="secondary" {{ $milestone['color'] === 'secondary' ? 'selected' : '' }}>Gray (Secondary)</option>
                                            <option value="success" {{ $milestone['color'] === 'success' ? 'selected' : '' }}>Green (Success)</option>
                                            <option value="warning" {{ $milestone['color'] === 'warning' ? 'selected' : '' }}>Orange (Warning)</option>
                                            <option value="danger" {{ $milestone['color'] === 'danger' ? 'selected' : '' }}>Red (Danger)</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-dark fw-bold small">Detailed Description</label>
                                        <textarea name="roadmap[{{ $i }}][description]" class="form-control bg-white" rows="3" required>{{ $milestone['description'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>

                        <!-- Tab 4: Training Videos -->
                        <div class="tab-pane fade" id="v-pills-training" role="tabpanel">
                            <h4 class="fw-bold mb-3 text-primary">Customize Training Videos Details</h4>
                            <p class="text-muted small">Update video documentation tutorial names, lengths, YouTube links, and script descriptions.</p>

                            @for($i = 0; $i < 3; $i++)
                            @php $video = $trainingVideos[$i] ?? ['title' => '', 'description' => '', 'youtube_url' => '']; @endphp
                            <div class="card border rounded-3 p-4 mb-4 bg-light shadow-none">
                                <div class="row g-3">
                                    <div class="col-md-7">
                                        <label class="form-label text-dark fw-bold small">Video Title & Duration</label>
                                        <input type="text" name="videos[{{ $i }}][title]" class="form-control bg-white" value="{{ $video['title'] }}" placeholder="Video {{ $i+1 }} name" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label text-dark fw-bold small">YouTube Video URL (Optional)</label>
                                        <input type="url" name="videos[{{ $i }}][youtube_url]" class="form-control bg-white" value="{{ $video['youtube_url'] ?? '' }}" placeholder="https://www.youtube.com/watch?v=...">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-dark fw-bold small">Tutorial Script Details</label>
                                        <textarea name="videos[{{ $i }}][description]" class="form-control bg-white" rows="3" required>{{ $video['description'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
