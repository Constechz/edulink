@extends('layouts.app')

@section('title', 'Help, Training & Roadmap Center — EduLink')
@section('header_title', 'Help & Training Reference')

@section('content')
<div class="container-fluid">
    <!-- Header banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm text-white p-4" style="background: linear-gradient(135deg, #003366 0%, #002244 100%); border-radius: 16px;">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="bi bi-question-circle-fill fs-1 text-warning"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1">Help & Reference Hub</h2>
                        <p class="mb-0 text-white-50">Standard guidelines, continuous assessment formulas, role manuals, and milestones roadmap for EduLink Ghana ERP.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Navigation Tabs -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm glass-card p-3">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-guide-tab" data-bs-toggle="pill" data-bs-target="#v-pills-guide" type="button" role="tab" aria-selected="true">
                        <i class="bi bi-book-half me-2 text-warning font-weight-bold"></i> Admin User Guide
                    </button>
                    <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-manuals-tab" data-bs-toggle="pill" data-bs-target="#v-pills-manuals" type="button" role="tab" aria-selected="false">
                        <i class="bi bi-people me-2"></i> Role Manuals
                    </button>
                    <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-formulas-tab" data-bs-toggle="pill" data-bs-target="#v-pills-formulas" type="button" role="tab" aria-selected="false">
                        <i class="bi bi-calculator me-2"></i> Quick Reference
                    </button>
                    @if(Auth::user()->role && Auth::user()->role->slug === 'super-admin')
                    <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-roadmap-tab" data-bs-toggle="pill" data-bs-target="#v-pills-roadmap" type="button" role="tab" aria-selected="false">
                        <i class="bi bi-signpost-split me-2"></i> Milestone Roadmap
                    </button>
                    @endif
                    <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-training-tab" data-bs-toggle="pill" data-bs-target="#v-pills-training" type="button" role="tab" aria-selected="false">
                        <i class="bi bi-play-circle me-2"></i> Training Videos
                    </button>
                </div>
            </div>
        </div>
        <!-- Main Reference Documentation Content -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm glass-card p-4">
                <div class="tab-content" id="v-pills-tabContent">
                    
                    <!-- Tab: Admin User Guide -->
                    <div class="tab-pane fade show active" id="v-pills-guide" role="tabpanel">
                        <h4 class="fw-bold mb-1 text-primary">School Administrator User Guide</h4>
                        <p class="text-muted small mb-4">A complete, step-by-step system walkthrough to configure and manage your school ERP easily.</p>
                        
                        <div class="admin-guide-content text-dark pe-2" style="max-height: 650px; overflow-y: auto;">
                            <div class="card border-0 bg-light p-3 mb-4 rounded-3 shadow-none">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle-fill text-warning me-2"></i>1. Getting Started & Onboarding Checklist</h6>
                                <p class="mb-3 small">Upon your first login using the administrator credentials, you will be automatically routed to the <strong>Onboarding Checklist</strong>. It is critical to complete the checklist steps in the sequence shown below to ensure the ERP functions correctly:</p>
                                
                                <div class="table-responsive">
                                    <table class="table table-sm table-white table-bordered align-middle text-start small mb-0">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>Task</th>
                                                <th>System Path</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>1. School Profile</strong></td>
                                                <td><code>Settings -> Profile</code></td>
                                                <td>Upload logo, address, and headteacher signature.</td>
                                            </tr>
                                            <tr>
                                                <td><strong>2. Campuses</strong></td>
                                                <td><code>Campuses -> Add Campus</code></td>
                                                <td>Define campuses (e.g. Primary, JHS, SHS).</td>
                                            </tr>
                                            <tr>
                                                <td><strong>3. Academics</strong></td>
                                                <td><code>Academics -> Config</code></td>
                                                <td>Establish active Academic Years, Terms, and Classes.</td>
                                            </tr>
                                            <tr>
                                                <td><strong>4. Teachers</strong></td>
                                                <td><code>Staff -> Add Staff</code></td>
                                                <td>Register teachers and assign permissions.</td>
                                            </tr>
                                            <tr>
                                                <td><strong>5. Subjects Map</strong></td>
                                                <td><code>Subjects -> Allocate</code></td>
                                                <td>Link subjects to classes and assign teachers.</td>
                                            </tr>
                                            <tr>
                                                <td><strong>6. Students Registry</strong></td>
                                                <td><code>Students -> Add Student</code></td>
                                                <td>Import or enroll students to classes.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card border-0 bg-light p-3 mb-4 rounded-3 shadow-none">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-calendar-check text-primary me-2"></i>2. Academic Setup & Admissions</h6>
                                <p class="mb-2 small"><strong>Academics:</strong> Ensure that active academic years and terms are configured. Class streams (e.g., Stream A, B) must be mapped to classes before enrolling students.</p>
                                <p class="mb-0 small"><strong>Admissions CRM:</strong> Review online applications submitted by parents at the public link. Go to <code>Admissions -> Inquiries</code>, verify applicant info, approve, and click to auto-enroll them as active students.</p>
                            </div>

                            <div class="card border-0 bg-light p-3 mb-4 rounded-3 shadow-none">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-mortarboard text-purple me-2"></i>2b. Step-by-Step Guide: Configuring a KG to JHS School</h6>
                                <p class="mb-3 small">For a standard basic school spanning Nursery/KG to Junior High School (JHS), follow this implementation roadmap:</p>
                                <ol class="mb-0 ps-3 small" style="line-height: 1.6;">
                                    <li class="mb-2"><strong>Campus & Calendar:</strong> Add your primary campuses under <code>Campuses</code>. Setup the active academic year (e.g. 2026/2027) and terms (Term 1, 2, 3) in settings.</li>
                                    <li class="mb-2"><strong>Departments & Programmes:</strong> Under <code>Programmes</code>, create a general program (e.g. <em>Basic Education</em>) as a container. Create preschool, primary, and JHS departments to organize staff.</li>
                                    <li class="mb-2"><strong>Class Streams Setup:</strong> When creating classes in <code>Classes & Streams</code>, map them to correct level scopes:
                                        <ul class="mb-0 ps-3">
                                            <li><strong>KG 1 - KG 2:</strong> Set level scope to <code>KG</code>.</li>
                                            <li><strong>Class 1 - Class 6:</strong> Set level scope to <code>Primary</code>.</li>
                                            <li><strong>JHS 1 - JHS 3:</strong> Set level scope to <code>JHS</code>.</li>
                                        </ul>
                                    </li>
                                    <li class="mb-2"><strong>Subjects Roster:</strong> Register NaCCA-compliant subjects under <code>Subjects</code> (e.g. Numeracy for KG, OWOP for Primary, Integrated Science and Computing for JHS) and map them to their respective class rosters.</li>
                                    <li class="mb-2"><strong>Grading & Promotion Rules:</strong> The system automatically assigns standard GES scales (Standards-Based for KG/Primary, BECE 9-point scale for JHS) to each level. Configure promotions rules under <code>Student Promotions -> Promotion Rules</code>.</li>
                                </ol>
                            </div>

                            <div class="card border-0 bg-light p-3 mb-4 rounded-3 shadow-none">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-sliders text-success me-2"></i>3. Configuring the Dynamic Scoring Engine (Continuous Assessment)</h6>
                                <p class="mb-2 small">Each school independently configures continuous assessment formulas to match their internal policies or Ghana Education Service (GES) directives:</p>
                                <ul class="mb-2 ps-3 small">
                                    <li>Navigate to <code>Scoring Configuration</code> and create a template for a class level.</li>
                                    <li>Define the class work weight (e.g. 30%, 50%) and exam weight (e.g. 70%, 50%).</li>
                                    <li>Add individual components (Homework, tests, class exercises) and set their raw maximum marks. The system dynamically scales entries to match your selected weights.</li>
                                    <li><strong>Report Cards:</strong> Generate and publish reports under <code>Report Cards -> Compile</code>. Published reports are instantly visible on parent/student portal profiles.</li>
                                </ul>
                                <div class="alert alert-info py-2 px-3 small mb-0"><i class="bi bi-qr-code me-2"></i>Published report cards automatically feature a secure <strong>verification QR code</strong> for instant authentication.</div>
                            </div>

                            <div class="card border-0 bg-light p-3 mb-4 rounded-3 shadow-none">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-wallet2 text-info me-2"></i>4. Fees Billing & Cash Ledger</h6>
                                <p class="mb-2 small">Maintain transparent financial operations using the following steps:</p>
                                <ul class="mb-0 ps-3 small">
                                    <li><strong>Fee Structures:</strong> Define structural bills per class under <code>Finance -> Fee Structures</code> (e.g. JHS 1 Tuition, Computer Laboratory Fee).</li>
                                    <li><strong>Invoicing:</strong> Bulk invoice classes under <code>Finance -> Invoices</code>. This populates balances on all student ledgers.</li>
                                    <li><strong>Payments:</strong> Log cash or mobile money payments in real time at <code>Finance -> Payments -> Collect</code>. Receipts will be generated, and balance statements updated.</li>
                                </ul>
                            </div>

                            <div class="card border-0 bg-light p-3 mb-4 rounded-3 shadow-none">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-globe2 text-purple me-2"></i>5. Public School Website Builder</h6>
                                <p class="mb-2 small">Your school gets a dedicated subdomain/custom domain. Customize it without writing code:</p>
                                <ul class="mb-0 ps-3 small">
                                    <li>Navigate to <code>Settings -> Website Builder</code>.</li>
                                    <li>Click <strong>Open GrapesJS Builder</strong> to custom design web pages using interactive block widgets.</li>
                                    <li>Integrate direct database feeds: display school news, upcoming calendar events, photos gallery, and staff directories dynamically.</li>
                                    <li>Adjust site themes, school colors, branding logos, and click <strong>Publish</strong> to go live.</li>
                                </ul>
                            </div>

                            <div class="card border-0 bg-light p-3 mb-4 rounded-3 shadow-none">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-arrow-up-right-circle text-danger me-2"></i>6. End of Year Promotions (Dynamic Promotion Engine)</h6>
                                <p class="mb-2 small">When Term 3 closes, promotions are determined using cumulative averages across all three terms:</p>
                                <ul class="mb-3 ps-3 small">
                                    <li>Define promotion targets and thresholds under <code>Promotion Rules</code>.</li>
                                    <li>The system auto-calculates cumulative averages and suggests options: Promote, Repeat, or Review.</li>
                                    <li>HODs/Headteachers review the lists, submit overrides if necessary, and approve to publish the decisions.</li>
                                    <li><strong>Terminal Candidates:</strong> The engine automatically excludes Basic 9 (JHS 3) and SHS 3 students from internal promotions, routing them to BECE/WASSCE placement lists instead.</li>
                                </ul>
                                <div class="p-3 bg-white rounded-3 border">
                                    <span class="d-block fw-bold text-dark mb-2 small"><i class="bi bi-gear-fill me-1"></i> Admin Setup Guide: How to Configure Rules</span>
                                    <ol class="small text-muted mb-3 ps-3">
                                        <li class="mb-1">Navigate to <strong>Student Promotions -> Promotion Rules</strong> from the left sidebar.</li>
                                        <li class="mb-1">Click <strong>Add Promotion Rule</strong> (or edit an existing policy) and choose the class level scope.</li>
                                        <li class="mb-1">Choose a <strong>Method</strong>:
                                            <ul class="ps-3 my-1">
                                                <li><em>Annual Average</em>: Simple average weighted across terms.</li>
                                                <li><em>Best 2 of 3</em>: Average of highest 2 terms (Term 3 must count).</li>
                                                <li><em>Subject Pass Count</em>: Requires passing a min count of subjects at a per-subject threshold.</li>
                                            </ul>
                                        </li>
                                        <li class="mb-1">Define the <strong>Promotion Threshold (%)</strong> and optional <strong>Conditional Threshold (%)</strong>.</li>
                                        <li>Specify <strong>Term Weights</strong> (e.g. 1.0 : 1.0 : 2.0 to weigh Term 3 double) and click save. The promotions wizard will automatically apply this rule.</li>
                                    </ol>

                                    <!-- Term 3 Adoption Tip -->
                                    <div class="p-3 rounded-3" style="background-color: rgba(255, 193, 7, 0.08); border: 1px solid rgba(255, 193, 7, 0.25);">
                                        <span class="d-block fw-bold text-dark mb-1 small"><i class="bi bi-lightbulb-fill text-warning me-1"></i> Mid-Year Adoption (Term 3 Onboarding)</span>
                                        <p class="small text-muted mb-0">
                                            If your school starts using EduLink in <strong>Term 3</strong>, you will not have scores for Terms 1 & 2. To avoid penalizing students with 0% grades, go to your <strong>Promotion Rules</strong> config and set the <strong>Term Weights</strong> to: <strong>Term 1 = 0, Term 2 = 0, Term 3 = 1</strong>. This calculates promotion eligibility based 100% on Term 3 scores.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 1: Role Manuals -->
                    <div class="tab-pane fade" id="v-pills-manuals" role="tabpanel">
                        <h4 class="fw-bold mb-3 text-primary">Administrative & Staff Role Manuals</h4>
                        <p class="text-muted small">Select your designated system role to view specific workflows and continuous assessment operations guidelines.</p>
                        
                        <div class="accordion accordion-flush" id="roleManualsAccordion">
                            @foreach($manuals as $manual)
                                @if(!($manual['is_super_only'] ?? false) || (Auth::user()->role && Auth::user()->role->slug === 'super-admin'))
                                <div class="accordion-item border-bottom py-2 bg-transparent">
                                    <h2 class="accordion-header" id="heading{{ $manual['key'] }}">
                                        <button class="accordion-button collapsed bg-transparent px-0 py-3 shadow-none fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $manual['key'] }}" aria-expanded="false">
                                            <i class="bi {{ $manual['icon'] }} me-2"></i> {{ $manual['title'] }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $manual['key'] }}" class="accordion-collapse collapse" data-bs-parent="#roleManualsAccordion">
                                        <div class="accordion-body bg-light rounded-3 p-3">
                                            <p class="small text-muted mb-2">{{ $manual['description'] }}</p>
                                            <ul class="small text-muted ps-3 mb-0">
                                                @foreach($manual['items'] as $item)
                                                <li>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Tab 2: Quick Reference Cards -->
                    <div class="tab-pane fade" id="v-pills-formulas" role="tabpanel">
                        <h4 class="fw-bold mb-3 text-primary">Continuous Assessment (SBA) Formulas</h4>
                        <p class="text-muted small">EduLink scales raw Continuous Assessment (SBA) totals and exam inputs independently to calculate the overall percentage grade.</p>

                        <!-- Formulas Reference -->
                        <div class="bg-light p-3 rounded border mb-4">
                            <h6 class="fw-bold text-dark"><i class="bi bi-calculator-fill text-primary me-2"></i>Standard Scaling Formula</h6>
                            <div class="p-2 bg-white rounded border font-monospace small mb-2 text-center text-dark">
                                {{ $quickRefSba['formula_class'] ?? 'Scaled Class Score = (Raw Class Total ÷ Class Max) × Class Weight' }}
                            </div>
                            <div class="p-2 bg-white rounded border font-monospace small text-center text-dark">
                                {{ $quickRefSba['formula_exam'] ?? 'Scaled Exam Score = (Raw Exam Score ÷ Exam Max) × Exam Weight' }}
                            </div>
                            <p class="small text-muted mt-2 mb-0"><strong>Example:</strong> {{ $quickRefSba['example_text'] ?? '' }}</p>
                        </div>

                        <!-- GES Grading Table -->
                        <h5 class="fw-bold text-secondary mt-4">GES Standard Grading Scale (GES Standard)</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered align-middle text-center small">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Grade</th>
                                        <th>Marks Range</th>
                                        <th>Grade Point</th>
                                        <th>Interpretation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td>A1</td><td>80% - 100%</td><td>1.0</td><td>Excellent</td></tr>
                                    <tr><td>B2</td><td>70% - 79%</td><td>2.0</td><td>Very Good</td></tr>
                                    <tr><td>B3</td><td>60% - 69%</td><td>3.0</td><td>Good</td></tr>
                                    <tr><td>C4</td><td>55% - 59%</td><td>4.0</td><td>Credit</td></tr>
                                    <tr><td>C5</td><td>50% - 54%</td><td>5.0</td><td>Credit</td></tr>
                                    <tr><td>C6</td><td>45% - 49%</td><td>6.0</td><td>Credit</td></tr>
                                    <tr><td>D7</td><td>40% - 44%</td><td>7.0</td><td>Pass</td></tr>
                                    <tr><td>E8</td><td>35% - 39%</td><td>8.0</td><td>Pass</td></tr>
                                    <tr><td>F9</td><td>0% - 34%</td><td>9.0</td><td>Fail</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Keyboard Shortcuts -->
                        <h5 class="fw-bold text-secondary mt-4">Website Builder Keyboard Shortcuts</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle text-center small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Shortcut</th>
                                        <th>Action Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td><code>Ctrl + Z</code></td><td>Undo last action on builder canvas</td></tr>
                                    <tr><td><code>Ctrl + Y</code></td><td>Redo undone action</td></tr>
                                    <tr><td><code>Ctrl + S</code></td><td>Save changes as layout draft</td></tr>
                                    <tr><td><code>Ctrl + P</code></td><td>Toggle interactive preview mode</td></tr>
                                    <tr><td><code>Delete</code></td><td>Delete highlighted block component</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if(Auth::user()->role && Auth::user()->role->slug === 'super-admin')
                    <!-- Tab 3: Milestone Roadmap -->
                    <div class="tab-pane fade" id="v-pills-roadmap" role="tabpanel">
                        <h4 class="fw-bold mb-3 text-primary">Milestones Roadmap</h4>
                        <p class="text-muted small">EduLink's multi-year feature deployment schedule is detailed below.</p>
                        
                        <div class="position-relative mt-4 ps-4 border-start border-2 border-primary" style="margin-left: 20px;">
                            @foreach($roadmap as $milestone)
                            <div class="mb-4 position-relative">
                                <span class="position-absolute bg-{{ $milestone['color'] }} rounded-circle" style="width: 14px; height: 14px; left: -28px; top: 4px;"></span>
                                <h6 class="fw-bold text-dark">{{ $milestone['title'] }}</h6>
                                <p class="small text-muted mb-0">{{ $milestone['description'] }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Tab 4: Training Videos Scripts -->
                    <div class="tab-pane fade" id="v-pills-training" role="tabpanel">
                        <h4 class="fw-bold mb-3 text-primary">Videos Training Scripts</h4>
                        <p class="text-muted small">Overview of our guided tutorials for onboarding new school administrators and academic staff.</p>
                        
                        <div class="list-group">
                            @foreach($trainingVideos as $index => $video)
                            <div class="list-group-item bg-transparent py-3 border-bottom">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-play-btn-fill text-danger me-2"></i>{{ $video['title'] }}</h6>
                                        <p class="small text-muted mb-0">{{ $video['description'] }}</p>
                                    </div>
                                    @if(!empty($video['youtube_url']))
                                        @php
                                            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|[^/]+\?v=)|youtu\.be/)([^"&?/ ]{11})%i', $video['youtube_url'], $match);
                                            $videoId = $match[1] ?? null;
                                        @endphp
                                        @if($videoId)
                                        <div class="flex-shrink-0">
                                            <button class="btn btn-outline-danger btn-sm rounded-pill fw-bold py-1.5 px-3 d-flex align-items-center gap-1 shadow-xs" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#videoModal{{ $index }}">
                                                <i class="bi bi-play-circle-fill"></i> Watch Video
                                            </button>
                                        </div>

                                        <!-- Video Play Modal -->
                                        <div class="modal fade" id="videoModal{{ $index }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content border-0 bg-dark text-white rounded-4 overflow-hidden">
                                                    <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center p-3">
                                                        <span class="fw-bold small text-white-50"><i class="bi bi-camera-video me-1"></i>Tutorial: {{ $video['title'] }}</span>
                                                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close" onclick="stopVideo('{{ $index }}')"></button>
                                                    </div>
                                                    <div class="modal-body p-0">
                                                        <div class="ratio ratio-16x9">
                                                            <iframe id="ytPlayer{{ $index }}" src="https://www.youtube.com/embed/{{ $videoId }}?enablejsapi=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function stopVideo(index) {
        var iframe = document.getElementById('ytPlayer' + index);
        if (iframe) {
            var iframeSrc = iframe.src;
            iframe.src = iframeSrc; // Reload iframe to stop playing video
        }
    }
</script>
@endsection
