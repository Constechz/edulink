@extends('layouts.app')

@section('title', 'Website Branding & Themes')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Branding Settings</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">School Website Branding & Themes</h1>
                    <p class="text-muted mb-0 small">Customize your school colors, typography fonts, upload logos, contact metadata, and publish settings.</p>
                </div>
            </div>

            <!-- Validation/Success Feedback Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                        <div>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('school.website.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <!-- Left Column: Settings -->
                    <div class="col-lg-8">
                        <!-- Branding Parameters Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light border-0 py-3">
                                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-palette me-1"></i> Color Scheme & Fonts</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="primary_color" class="form-label fw-bold">Primary Color</label>
                                        <div class="input-group">
                                            <input type="color" name="primary_color" id="primary_color" class="form-control form-control-color w-25 border-end-0" value="{{ $settings->primary_color ?? '#003366' }}">
                                            <input type="text" class="form-control text-uppercase w-75 border-start-0" value="{{ $settings->primary_color ?? '#003366' }}" oninput="this.previousElementSibling.value = this.value">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="secondary_color" class="form-label fw-bold">Secondary Color</label>
                                        <div class="input-group">
                                            <input type="color" name="secondary_color" id="secondary_color" class="form-control form-control-color w-25 border-end-0" value="{{ $settings->secondary_color ?? '#FFD700' }}">
                                            <input type="text" class="form-control text-uppercase w-75 border-start-0" value="{{ $settings->secondary_color ?? '#FFD700' }}" oninput="this.previousElementSibling.value = this.value">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="accent_color" class="form-label fw-bold">Accent Color</label>
                                        <div class="input-group">
                                            <input type="color" name="accent_color" id="accent_color" class="form-control form-control-color w-25 border-end-0" value="{{ $settings->accent_color ?? '#FF6B35' }}">
                                            <input type="text" class="form-control text-uppercase w-75 border-start-0" value="{{ $settings->accent_color ?? '#FF6B35' }}" oninput="this.previousElementSibling.value = this.value">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label for="text_color" class="form-label fw-bold">Base Text Color</label>
                                        <input type="color" name="text_color" id="text_color" class="form-control form-control-color w-100" value="{{ $settings->text_color ?? '#333333' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="bg_color" class="form-label fw-bold">Base Background Color</label>
                                        <input type="color" name="bg_color" id="bg_color" class="form-control form-control-color w-100" value="{{ $settings->bg_color ?? '#FFFFFF' }}">
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <label for="heading_font" class="form-label fw-bold">Heading Font Family</label>
                                        <select name="heading_font" id="heading_font" class="form-select">
                                            @foreach(['Outfit', 'Inter', 'Montserrat', 'Playfair Display', 'Poppins', 'Roboto'] as $font)
                                                <option value="{{ $font }}" {{ ($settings->heading_font ?? 'Outfit') == $font ? 'selected' : '' }}>{{ $font }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label for="body_font" class="form-label fw-bold">Body Font Family</label>
                                        <select name="body_font" id="body_font" class="form-select">
                                            @foreach(['Inter', 'Outfit', 'Open Sans', 'Roboto', 'Lato', 'Montserrat'] as $font)
                                                <option value="{{ $font }}" {{ ($settings->body_font ?? 'Inter') == $font ? 'selected' : '' }}>{{ $font }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Site Identity Info -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light border-0 py-3">
                                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-globe me-1"></i> Site Identity</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="site_name" class="form-label fw-bold">Public School Name</label>
                                        <input type="text" name="site_name" id="site_name" class="form-control" value="{{ $settings->site_name ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="site_tagline" class="form-label fw-bold">School Tagline</label>
                                        <input type="text" name="site_tagline" id="site_tagline" class="form-control" value="{{ $settings->site_tagline ?? '' }}" placeholder="e.g. Excellence in Learning">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="logo" class="form-label fw-bold">School Logo Banner (PNG/SVG)</label>
                                        <input type="file" name="logo" id="logo" class="form-control">
                                        @if($settings->logo_path)
                                            <div class="mt-2 text-muted small"><i class="bi bi-file-earmark-image"></i> Current: <a href="{{ asset('storage/' . $settings->logo_path) }}" target="_blank">View Logo</a></div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="favicon" class="form-label fw-bold">Site Favicon (ICO/PNG)</label>
                                        <input type="file" name="favicon" id="favicon" class="form-control">
                                        @if($settings->favicon_path)
                                            <div class="mt-2 text-muted small"><i class="bi bi-file-earmark-image"></i> Current: <a href="{{ asset('storage/' . $settings->favicon_path) }}" target="_blank">View Favicon</a></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Metadata -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light border-0 py-3">
                                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-telephone-inbound me-1"></i> Contact Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="contact_phone" class="form-label fw-bold">Contact Phone</label>
                                        <input type="text" name="contact_phone" id="contact_phone" class="form-control" value="{{ $settings->contact_phone ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="contact_email" class="form-label fw-bold">Contact Email</label>
                                        <input type="email" name="contact_email" id="contact_email" class="form-control" value="{{ $settings->contact_email ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="contact_address" class="form-label fw-bold">Contact Address</label>
                                        <input type="text" name="contact_address" id="contact_address" class="form-control" value="{{ $settings->contact_address ?? '' }}" placeholder="e.g. Ring Road, Accra">
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="contact_map_embed" class="form-label fw-bold">Google Maps Embed Iframe Code (Optional)</label>
                                        <textarea name="contact_map_embed" id="contact_map_embed" rows="3" class="form-control" placeholder='e.g. <iframe src="https://www.google.com/maps/embed..."></iframe>'>{{ $settings->contact_map_embed ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Settings & Publish Status -->
                    <div class="col-lg-4">
                        <!-- Publish Status Card -->
                        <div class="card border-0 shadow-sm bg-gradient-light mb-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold text-dark mb-3">Publish Settings</h5>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="is_published" value="1" id="is_published" {{ $settings->is_published ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-dark" for="is_published">Publish Website Publicly</label>
                                </div>
                                <p class="text-muted small mb-4">When published, anyone can view your school homepage and links at your subdomain or custom domain without logging in.</p>
                                
                                @php
                                    $school = auth()->user()->school;
                                    $visitUrl = $school && $school->subdomain
                                        ? url('/' . $school->subdomain . '/home')
                                        : route('public.site.page', 'home') . '?school_id=' . $school->id;
                                @endphp
                                <div class="bg-light p-3 rounded border">
                                    <div class="small text-muted mb-1">Your School Subdomain Link:</div>
                                    <a href="{{ $visitUrl }}" target="_blank" class="fw-bold text-decoration-none text-primary">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Visit Public Portal Website
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Social Handles -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light border-0 py-3">
                                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-share me-1"></i> Social Network Handles</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="social_facebook" class="form-label fw-bold">Facebook URL</label>
                                    <input type="url" name="social_facebook" id="social_facebook" class="form-control form-control-sm" value="{{ $settings->social_facebook ?? '' }}" placeholder="https://facebook.com/yourschool">
                                </div>
                                <div class="mb-3">
                                    <label for="social_twitter" class="form-label fw-bold">Twitter/X URL</label>
                                    <input type="url" name="social_twitter" id="social_twitter" class="form-control form-control-sm" value="{{ $settings->social_twitter ?? '' }}" placeholder="https://x.com/yourschool">
                                </div>
                                <div class="mb-3">
                                    <label for="social_instagram" class="form-label fw-bold">Instagram URL</label>
                                    <input type="url" name="social_instagram" id="social_instagram" class="form-control form-control-sm" value="{{ $settings->social_instagram ?? '' }}" placeholder="https://instagram.com/yourschool">
                                </div>
                                <div class="mb-3">
                                    <label for="social_youtube" class="form-label fw-bold">YouTube Channel</label>
                                    <input type="url" name="social_youtube" id="social_youtube" class="form-control form-control-sm" value="{{ $settings->social_youtube ?? '' }}" placeholder="https://youtube.com/c/yourschool">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">Save Branding & Theme Settings</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
