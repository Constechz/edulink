@extends('layouts.app')

@section('title', 'Landing Page Editor | EduLink Admin')
@section('header_title', 'SaaS Welcome Page Customizer')

@section('content')
<div class="container-fluid p-0">
    <!-- Session Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please fix the errors below:</h6>
            <ul class="mb-0 ps-3 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('super-admin.landing-page.update') }}" method="POST">
        @csrf

        <!-- Tab Navigations -->
        <div class="glass-card mb-4 shadow-sm p-3">
            <ul class="nav nav-pills nav-fill" id="landingPageTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold text-uppercase py-2.5 rounded-3" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" type="button" role="tab" aria-controls="hero" aria-selected="true">
                        <i class="bi bi-layout-wtf me-2"></i>Hero & Stats
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-uppercase py-2.5 rounded-3" id="pillars-tab" data-bs-toggle="tab" data-bs-target="#pillars" type="button" role="tab" aria-controls="pillars" aria-selected="false">
                        <i class="bi bi-grid-3x3-gap me-2"></i>Core Pillars
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-uppercase py-2.5 rounded-3" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing" type="button" role="tab" aria-controls="pricing" aria-selected="false">
                        <i class="bi bi-tags me-2"></i>Pricing Plans
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-uppercase py-2.5 rounded-3" id="faqs-tab" data-bs-toggle="tab" data-bs-target="#faqs" type="button" role="tab" aria-controls="faqs" aria-selected="false">
                        <i class="bi bi-question-circle me-2"></i>FAQs
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-uppercase py-2.5 rounded-3" id="footer-tab" data-bs-toggle="tab" data-bs-target="#footer" type="button" role="tab" aria-controls="footer" aria-selected="false">
                        <i class="bi bi-window-sidebar me-2"></i>Footer & General
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Contents -->
        <div class="tab-content" id="landingPageTabsContent">
            
            <!-- Hero & Stats Tab -->
            <div class="tab-pane fade show active" id="hero" role="tabpanel" aria-labelledby="hero-tab">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="glass-card p-4 shadow-sm mb-4 h-100">
                            <h5 class="fw-bold text-dark mb-4 border-bottom pb-2"><i class="bi bi-stars text-primary me-2"></i>Hero Content</h5>
                            
                            <div class="mb-3">
                                <label for="welcome_hero_badge" class="form-label fw-bold text-secondary small">Hero Section Badge</label>
                                <input type="text" class="form-control rounded-3 border-light shadow-sm" id="welcome_hero_badge" name="welcome_hero_badge" value="{{ $settings['welcome_hero_badge'] }}" required>
                                <div class="form-text text-muted">A small accent tag shown above the main title.</div>
                            </div>

                            <div class="mb-3">
                                <label for="welcome_hero_title" class="form-label fw-bold text-secondary small">Hero Main Title</label>
                                <input type="text" class="form-control rounded-3 border-light shadow-sm" id="welcome_hero_title" name="welcome_hero_title" value="{{ $settings['welcome_hero_title'] }}" required>
                                <div class="form-text text-muted">Use striking language. Example: 'The Intelligent Cloud ERP for Modern Institutions'.</div>
                            </div>

                            <div class="mb-3">
                                <label for="welcome_hero_sub" class="form-label fw-bold text-secondary small">Hero Subtitle Description</label>
                                <textarea class="form-control rounded-3 border-light shadow-sm" id="welcome_hero_sub" name="welcome_hero_sub" rows="4" required>{{ $settings['welcome_hero_sub'] }}</textarea>
                                <div class="form-text text-muted">A compelling summary paragraphs explaining SaaS platform value.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="glass-card p-4 shadow-sm mb-4 h-100">
                            <h5 class="fw-bold text-dark mb-4 border-bottom pb-2"><i class="bi bi-bar-chart-fill text-warning me-2"></i>Hero Metrics Stats</h5>
                            
                            <!-- Stat 1 -->
                            <div class="p-3 bg-light rounded-4 mb-3 border">
                                <h6 class="fw-bold text-primary mb-2">Metric Stat #1</h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Value (e.g. 10k+)</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_stat1_value" value="{{ $settings['welcome_stat1_value'] }}" required>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Label (e.g. Students Enrolled)</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_stat1_label" value="{{ $settings['welcome_stat1_label'] }}" required>
                                </div>
                            </div>

                            <!-- Stat 2 -->
                            <div class="p-3 bg-light rounded-4 mb-3 border">
                                <h6 class="fw-bold text-warning mb-2">Metric Stat #2</h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Value (e.g. 99.9%)</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_stat2_value" value="{{ $settings['welcome_stat2_value'] }}" required>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Label (e.g. Uptime SLA)</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_stat2_label" value="{{ $settings['welcome_stat2_label'] }}" required>
                                </div>
                            </div>

                            <!-- Stat 3 -->
                            <div class="p-3 bg-light rounded-4 border">
                                <h6 class="fw-bold text-success mb-2">Metric Stat #3</h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Value (e.g. 15+)</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_stat3_value" value="{{ $settings['welcome_stat3_value'] }}" required>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Label (e.g. Smart Modules)</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_stat3_label" value="{{ $settings['welcome_stat3_label'] }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Core Pillars Tab -->
            <div class="tab-pane fade" id="pillars" role="tabpanel" aria-labelledby="pillars-tab">
                <div class="glass-card p-4 shadow-sm mb-4">
                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2"><i class="bi bi-grid-3x3-gap text-success me-2"></i>Core Management Pillars (Features)</h5>
                    <p class="text-muted small">Update the 4 prominent feature boxes listed in the pillars section of the landing page.</p>

                    <div class="row g-4">
                        <!-- Pillar 1 -->
                        <div class="col-md-6 col-xl-3">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <h6 class="fw-bold text-dark border-bottom pb-1.5 mb-3"><span class="badge bg-primary me-2">Pillar 1</span></h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Bootstrap Icon Class</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_pillar1_icon" value="{{ $settings['welcome_pillar1_icon'] }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Title</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_pillar1_title" value="{{ $settings['welcome_pillar1_title'] }}" required>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Description</label>
                                    <textarea class="form-control form-control-sm rounded-2" name="welcome_pillar1_desc" rows="4" required>{{ $settings['welcome_pillar1_desc'] }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Pillar 2 -->
                        <div class="col-md-6 col-xl-3">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <h6 class="fw-bold text-dark border-bottom pb-1.5 mb-3"><span class="badge bg-warning text-dark me-2">Pillar 2</span></h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Bootstrap Icon Class</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_pillar2_icon" value="{{ $settings['welcome_pillar2_icon'] }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Title</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_pillar2_title" value="{{ $settings['welcome_pillar2_title'] }}" required>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Description</label>
                                    <textarea class="form-control form-control-sm rounded-2" name="welcome_pillar2_desc" rows="4" required>{{ $settings['welcome_pillar2_desc'] }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Pillar 3 -->
                        <div class="col-md-6 col-xl-3">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <h6 class="fw-bold text-dark border-bottom pb-1.5 mb-3"><span class="badge bg-info text-dark me-2">Pillar 3</span></h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Bootstrap Icon Class</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_pillar3_icon" value="{{ $settings['welcome_pillar3_icon'] }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Title</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_pillar3_title" value="{{ $settings['welcome_pillar3_title'] }}" required>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Description</label>
                                    <textarea class="form-control form-control-sm rounded-2" name="welcome_pillar3_desc" rows="4" required>{{ $settings['welcome_pillar3_desc'] }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Pillar 4 -->
                        <div class="col-md-6 col-xl-3">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <h6 class="fw-bold text-dark border-bottom pb-1.5 mb-3"><span class="badge bg-success me-2">Pillar 4</span></h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Bootstrap Icon Class</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_pillar4_icon" value="{{ $settings['welcome_pillar4_icon'] }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Title</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_pillar4_title" value="{{ $settings['welcome_pillar4_title'] }}" required>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Description</label>
                                    <textarea class="form-control form-control-sm rounded-2" name="welcome_pillar4_desc" rows="4" required>{{ $settings['welcome_pillar4_desc'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Plans Tab -->
            <div class="tab-pane fade" id="pricing" role="tabpanel" aria-labelledby="pricing-tab">
                <div class="glass-card p-4 shadow-sm mb-4">
                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2"><i class="bi bi-tags text-info me-2"></i>Pricing Options</h5>
                    
                    <div class="row g-4">
                        <!-- Plan 1 -->
                        <div class="col-lg-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <h6 class="fw-bold text-primary mb-3 border-bottom pb-1.5">Plan #1 (Starter)</h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Plan Title</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_price1_title" value="{{ $settings['welcome_price1_title'] }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Subtitle</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_price1_sub" value="{{ $settings['welcome_price1_sub'] }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Price Tag (GHS / frequency)</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_price1_price" value="{{ $settings['welcome_price1_price'] }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Description Paragraph</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_price1_desc" value="{{ $settings['welcome_price1_desc'] }}" required>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Features List (one per line)</label>
                                    <textarea class="form-control form-control-sm rounded-2 font-monospace" name="welcome_price1_features" rows="6" required>{{ $settings['welcome_price1_features'] }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Plan 2 -->
                        <div class="col-lg-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <h6 class="fw-bold text-warning mb-3 border-bottom pb-1.5">Plan #2 (Standard)</h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Plan Title</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_price2_title" value="{{ $settings['welcome_price2_title'] }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Subtitle</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_price2_sub" value="{{ $settings['welcome_price2_sub'] }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Price Tag (GHS / frequency)</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_price2_price" value="{{ $settings['welcome_price2_price'] }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Description Paragraph</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_price2_desc" value="{{ $settings['welcome_price2_desc'] }}" required>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Features List (one per line)</label>
                                    <textarea class="form-control form-control-sm rounded-2 font-monospace" name="welcome_price2_features" rows="6" required>{{ $settings['welcome_price2_features'] }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Plan 3 -->
                        <div class="col-lg-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <h6 class="fw-bold text-success mb-3 border-bottom pb-1.5">Plan #3 (Enterprise)</h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Plan Title</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_price3_title" value="{{ $settings['welcome_price3_title'] }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Subtitle</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_price3_sub" value="{{ $settings['welcome_price3_sub'] }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Price Tag (GHS / frequency)</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_price3_price" value="{{ $settings['welcome_price3_price'] }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Description Paragraph</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_price3_desc" value="{{ $settings['welcome_price3_desc'] }}" required>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Features List (one per line)</label>
                                    <textarea class="form-control form-control-sm rounded-2 font-monospace" name="welcome_price3_features" rows="6" required>{{ $settings['welcome_price3_features'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQs Tab -->
            <div class="tab-pane fade" id="faqs" role="tabpanel" aria-labelledby="faqs-tab">
                <div class="glass-card p-4 shadow-sm mb-4">
                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2"><i class="bi bi-question-circle text-danger me-2"></i>Frequently Asked Questions</h5>
                    
                    <div class="row g-4">
                        <!-- FAQ 1 -->
                        <div class="col-lg-6">
                            <div class="p-3 bg-light rounded-4 border">
                                <h6 class="fw-bold text-dark border-bottom pb-1 mb-2 text-uppercase small text-muted">FAQ Item 1</h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Question</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_faq1_q" value="{{ $settings['welcome_faq1_q'] }}" required>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Answer</label>
                                    <textarea class="form-control form-control-sm rounded-2" name="welcome_faq1_a" rows="4" required>{{ $settings['welcome_faq1_a'] }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="col-lg-6">
                            <div class="p-3 bg-light rounded-4 border">
                                <h6 class="fw-bold text-dark border-bottom pb-1 mb-2 text-uppercase small text-muted">FAQ Item 2</h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Question</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_faq2_q" value="{{ $settings['welcome_faq2_q'] }}" required>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Answer</label>
                                    <textarea class="form-control form-control-sm rounded-2" name="welcome_faq2_a" rows="4" required>{{ $settings['welcome_faq2_a'] }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="col-lg-6">
                            <div class="p-3 bg-light rounded-4 border">
                                <h6 class="fw-bold text-dark border-bottom pb-1 mb-2 text-uppercase small text-muted">FAQ Item 3</h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Question</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_faq3_q" value="{{ $settings['welcome_faq3_q'] }}" required>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Answer</label>
                                    <textarea class="form-control form-control-sm rounded-2" name="welcome_faq3_a" rows="4" required>{{ $settings['welcome_faq3_a'] }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="col-lg-6">
                            <div class="p-3 bg-light rounded-4 border">
                                <h6 class="fw-bold text-dark border-bottom pb-1 mb-2 text-uppercase small text-muted">FAQ Item 4</h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Question</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="welcome_faq4_q" value="{{ $settings['welcome_faq4_q'] }}" required>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Answer</label>
                                    <textarea class="form-control form-control-sm rounded-2" name="welcome_faq4_a" rows="4" required>{{ $settings['welcome_faq4_a'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer & General Tab -->
            <div class="tab-pane fade" id="footer" role="tabpanel" aria-labelledby="footer-tab">
                <div class="glass-card p-4 shadow-sm mb-4">
                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2"><i class="bi bi-window-sidebar text-primary me-2"></i>Footer & Global Settings</h5>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="welcome_support_email" class="form-label fw-bold text-secondary small">Platform Support Email</label>
                                <input type="email" class="form-control rounded-3 border-light shadow-sm" id="welcome_support_email" name="welcome_support_email" value="{{ $settings['welcome_support_email'] }}" required>
                                <div class="form-text text-muted">Email address listed under footer details.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="welcome_whatsapp_number" class="form-label fw-bold text-secondary small">WhatsApp Contact Number</label>
                                <input type="text" class="form-control rounded-3 border-light shadow-sm" id="welcome_whatsapp_number" name="welcome_whatsapp_number" value="{{ $settings['welcome_whatsapp_number'] ?? '' }}" placeholder="e.g. 233240000000">
                                <div class="form-text text-muted">Enter the phone number in international format without spaces or the '+' sign (e.g., 233240000000). Leave empty to hide the button.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="welcome_footer_desc" class="form-label fw-bold text-secondary small">Footer Platform Description</label>
                                <textarea class="form-control rounded-3 border-light shadow-sm" id="welcome_footer_desc" name="welcome_footer_desc" rows="4" required>{{ $settings['welcome_footer_desc'] }}</textarea>
                                <div class="form-text text-muted">Brief legal/operating statement displayed on bottom-left.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Form Save Action Bar -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="glass-card p-3 d-flex justify-content-between align-items-center shadow-xs">
                    <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Ensure to save changes in all tabs before exiting.</span>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold">
                        <i class="bi bi-save me-1"></i>Save Welcome Page Settings
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
