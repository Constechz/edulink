<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }} - {{ $settings->site_name }}</title>
    <meta name="description" content="{{ $page->meta_description ?? $settings->site_tagline }}">
    
    <!-- Google Fonts dynamically loaded from branding settings -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($settings->heading_font ?? 'Outfit') }}:wght@300;400;500;600;700;800&family={{ urlencode($settings->body_font ?? 'Inter') }}:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Dynamic Branding Stylesheet -->
    <link href="{{ route('school.website.branding-css', $school->id) }}" rel="stylesheet">

    <style>
        :root {
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: var(--body-font), sans-serif;
            color: var(--text-color);
            background-color: var(--bg-color);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6, .display-4 {
            font-family: var(--heading-font), sans-serif;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        /* Navbar Redesign - Modern and Glassy */
        .navbar-public {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.02);
            padding: 0.85rem 0;
            transition: var(--transition-smooth);
        }

        .navbar-brand-name {
            font-weight: 800;
            color: var(--primary-color);
            font-size: 1.45rem;
            letter-spacing: -0.03em;
        }

        /* Custom Underline Hover Animation for Nav Links */
        .nav-link-public {
            color: #4a5568 !important;
            font-weight: 500;
            font-size: 0.95rem;
            margin: 0 0.75rem;
            padding: 0.25rem 0 !important;
            position: relative;
            transition: color 0.25s ease;
        }

        .nav-link-public::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary-color);
            transition: width 0.25s ease;
        }

        .nav-link-public:hover::after, 
        .nav-link-public.active::after {
            width: 100%;
        }

        .nav-link-public:hover, 
        .nav-link-public.active {
            color: var(--primary-color) !important;
            font-weight: 600;
        }

        /* Footer Redesign - Premium Dark Theme */
        .footer-public {
            background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
            color: #9ca3af;
            padding: 5rem 0 2rem 0;
            margin-top: auto;
            border-top: 5px solid var(--primary-color);
            position: relative;
        }

        .footer-heading {
            color: #ffffff;
            font-weight: 700;
            font-size: 1.05rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1.75rem;
            position: relative;
            padding-bottom: 0.5rem;
            border-left: none;
            padding-left: 0;
        }

        .footer-heading::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 3px;
            background-color: var(--secondary-color);
            border-radius: 2px;
        }

        .footer-link {
            color: #9ca3af;
            text-decoration: none;
            transition: var(--transition-smooth);
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .footer-link i {
            font-size: 0.75rem;
            transition: transform 0.25s ease;
        }

        .footer-link:hover {
            color: #ffffff;
            padding-left: 5px;
        }

        .footer-link:hover i {
            transform: translateX(3px);
            color: var(--secondary-color);
        }

        .social-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            margin-right: 10px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: var(--transition-smooth);
        }

        .social-icon-btn:hover {
            background-color: var(--secondary-color);
            color: #111827;
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.2);
        }

        /* Dynamic Feed Items Styling */
        .news-card-premium {
            border: 0;
            border-radius: 16px;
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            transition: var(--transition-smooth);
            overflow: hidden;
        }

        .news-card-premium:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.085);
        }

        .event-card-premium {
            transition: var(--transition-smooth);
            border-radius: 16px !important;
            border: 1px solid rgba(0,0,0,0.05) !important;
        }

        .event-card-premium:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            background-color: #ffffff !important;
            border-color: rgba(0,0,0,0.08) !important;
        }

        .staff-card-premium {
            border: 0;
            border-radius: 16px;
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            transition: var(--transition-smooth);
            padding: 2rem 1.5rem !important;
        }

        .staff-card-premium:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.07);
        }

        .staff-avatar-premium {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: var(--transition-smooth);
        }

        .staff-card-premium:hover .staff-avatar-premium {
            transform: scale(1.05);
            border-color: var(--primary-color);
        }

        .gallery-item-premium {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: var(--transition-smooth);
        }

        .gallery-item-premium img {
            transition: var(--transition-smooth);
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-overlay-premium {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 1.5rem 1rem 1rem 1rem;
            background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.4) 60%, rgba(0,0,0,0) 100%);
            color: #ffffff;
            text-align: center;
            opacity: 0.9;
            transition: var(--transition-smooth);
        }

        .gallery-item-premium:hover img {
            transform: scale(1.08);
        }

        .gallery-item-premium:hover {
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
            transform: translateY(-3px);
        }

        /* Form Controls Overrides for GrapesJS contact block */
        .form-control {
            border: 1px solid rgba(0,0,0,0.1) !important;
            border-radius: 10px !important;
            padding: 0.75rem 1rem !important;
            transition: var(--transition-smooth) !important;
            background-color: #f9fafb !important;
        }

        .form-control:focus {
            background-color: #ffffff !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 4px rgba(242, 88, 34, 0.08) !important;
            outline: none !important;
        }

        /* Premium Buttons Overrides */
        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #ffffff !important;
            transition: var(--transition-smooth) !important;
        }
        .btn-primary:hover {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(242, 88, 34, 0.2);
        }
        .btn-outline-primary {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            background: transparent !important;
            transition: var(--transition-smooth) !important;
        }
        .btn-outline-primary:hover {
            color: #ffffff !important;
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            transform: translateY(-2px);
        }

        /* GrapesJS Custom Styles wrapper */
        {!! $revision->css_content !!}
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg navbar-public navbar-light sticky-top">
        <div class="container">
               @php
                   $isPathBased = request()->route()->hasParameter('school_subdomain');
                   $homeUrl = $isPathBased
                       ? url('/' . $school->subdomain . '/home')
                       : route('public.site.page', 'home') . '?school_id=' . $school->id;
               @endphp
               <a class="navbar-brand d-flex align-items-center text-decoration-none" href="{{ $homeUrl }}">
                   @if($settings->logo_path)
                       <img src="{{ str_starts_with($settings->logo_path, 'http') ? $settings->logo_path : asset('storage/' . $settings->logo_path) }}" alt="{{ $settings->site_name }} Logo" height="40" class="me-2">
                   @else
                       <i class="bi bi-globe-europe-africa me-2 text-primary fs-2" style="color: var(--primary-color) !important;"></i>
                   @endif
                   <div class="navbar-brand-name">{{ $settings->site_name }}</div>
               </a>
            
            <button class="navbar-collapse-toggler navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPublicContent" aria-controls="navbarPublicContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarPublicContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    @foreach($headerItems as $item)
                        @php
                            if ($item->page_id) {
                                $targetUrl = $isPathBased
                                    ? url('/' . $school->subdomain . '/' . $item->page->slug)
                                    : route('public.site.page', $item->page->slug) . '?school_id=' . $school->id;
                            } else {
                                $targetUrl = (strpos($item->url, 'http') === 0) ? $item->url : url($item->url);
                            }
                            $isActive = $item->page_id && $page->id == $item->page_id;
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link nav-link-public {{ $isActive ? 'active' : '' }}" href="{{ $targetUrl }}" {{ $item->open_new_tab ? 'target="_blank"' : '' }}>
                                {{ $item->label }}
                            </a>
                        </li>
                    @endforeach
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-bold">ERP Portal Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Canvas Layout -->
    <main class="flex-shrink-0">
        @if(session('success'))
            <div class="container mt-4">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        {!! $revision->html_content !!}
    </main>

    <!-- Footer Area -->
    <footer class="footer-public">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <h5 class="navbar-brand-name text-white mb-3">{{ $settings->site_name }}</h5>
                    <p class="small opacity-75 mb-4">{{ $settings->site_tagline ?? 'Holistic Education ERP.' }}</p>
                    <div class="d-flex">
                        @if($settings->social_facebook)
                            <a href="{{ $settings->social_facebook }}" class="social-icon-btn" target="_blank"><i class="bi bi-facebook"></i></a>
                        @endif
                        @if($settings->social_twitter)
                            <a href="{{ $settings->social_twitter }}" class="social-icon-btn" target="_blank"><i class="bi bi-twitter-x"></i></a>
                        @endif
                        @if($settings->social_instagram)
                            <a href="{{ $settings->social_instagram }}" class="social-icon-btn" target="_blank"><i class="bi bi-instagram"></i></a>
                        @endif
                        @if($settings->social_youtube)
                            <a href="{{ $settings->social_youtube }}" class="social-icon-btn" target="_blank"><i class="bi bi-youtube"></i></a>
                        @endif
                    </div>
                </div>

                <div class="col-md-4">
                    <h6 class="footer-heading">Quick Links</h6>
                    @foreach($footerItems as $item)
                        @php
                            if ($item->page_id) {
                                $targetUrl = $isPathBased
                                    ? url('/' . $school->subdomain . '/' . $item->page->slug)
                                    : route('public.site.page', $item->page->slug) . '?school_id=' . $school->id;
                            } else {
                                $targetUrl = (strpos($item->url, 'http') === 0) ? $item->url : url($item->url);
                            }
                        @endphp
                        <a href="{{ $targetUrl }}" class="footer-link small" {{ $item->open_new_tab ? 'target="_blank"' : '' }}>
                            <i class="bi bi-chevron-right me-1 small"></i> {{ $item->label }}
                        </a>
                    @endforeach
                </div>

                <div class="col-md-4">
                    <h6 class="footer-heading">Contact Information</h6>
                    <ul class="list-unstyled small opacity-75">
                        @if($settings->contact_address)
                            <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> {{ $settings->contact_address }}</li>
                        @endif
                        @if($settings->contact_phone)
                            <li class="mb-2"><i class="bi bi-telephone me-2"></i> {{ $settings->contact_phone }}</li>
                        @endif
                        @if($settings->contact_email)
                            <li class="mb-2"><i class="bi bi-envelope me-2"></i> {{ $settings->contact_email }}</li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="border-top border-secondary pt-3 text-center text-md-start d-md-flex justify-content-between align-items-center small opacity-75">
                <p class="mb-0">© {{ date('Y') }} {{ $settings->site_name }}. All rights reserved.</p>
                <p class="mb-0 mt-2 mt-md-0">Powered by <a href="#" class="text-white text-decoration-none">{{ config('app.name', 'EduLink') }} Ghana ERP</a></p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Dynamic Feeds Load Client Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load Dynamic News
            const newsContainer = document.getElementById('dynamic-news-container');
            if (newsContainer) {
                fetch('{{ url("/api/public/news") }}?school_id={{ $school->id }}')
                .then(res => res.json())
                .then(data => {
                    if (data.length === 0) {
                        newsContainer.innerHTML = '<div class="col-12 text-center text-muted py-4">No recent announcements posted.</div>';
                        return;
                    }
                    newsContainer.innerHTML = '';
                    data.forEach(item => {
                        const date = new Date(item.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        newsContainer.innerHTML += `
                            <div class="col-md-4">
                                <div class="card news-card-premium h-100">
                                    <div class="card-body p-4">
                                        <div class="text-primary small fw-bold mb-2"><i class="bi bi-clock me-1"></i> ${date}</div>
                                        <h5 class="fw-bold text-dark mb-3">${item.title}</h5>
                                        <p class="text-muted mb-0 small">${item.content.substring(0, 120)}...</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(err => {
                    newsContainer.innerHTML = '<div class="col-12 text-center text-danger py-4">Failed to load news feed.</div>';
                });
            }

            // Load Dynamic Events
            const eventsContainer = document.getElementById('dynamic-events-container');
            if (eventsContainer) {
                fetch('{{ url("/api/public/events") }}?school_id={{ $school->id }}')
                .then(res => res.json())
                .then(data => {
                    if (data.length === 0) {
                        eventsContainer.innerHTML = '<div class="col-12 text-center text-muted py-4">No upcoming events scheduled.</div>';
                        return;
                    }
                    eventsContainer.innerHTML = '';
                    data.forEach(item => {
                        const start = new Date(item.start_time).toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
                        eventsContainer.innerHTML += `
                            <div class="col-md-6">
                                <div class="d-flex align-items-start bg-light p-4 event-card-premium">
                                    <div class="bg-primary text-white p-3 rounded-3 text-center me-3" style="min-width: 60px;">
                                        <i class="bi bi-calendar2-event fs-3"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">${item.title}</h5>
                                        <div class="text-muted small mb-2"><i class="bi bi-clock me-1"></i> ${start}</div>
                                        <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i> ${item.location || 'Main Campus'}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(err => {
                    eventsContainer.innerHTML = '<div class="col-12 text-center text-danger py-4">Failed to load events list.</div>';
                });
            }

            // Load Dynamic Staff
            const staffContainer = document.getElementById('dynamic-staff-container');
            if (staffContainer) {
                fetch('{{ url("/api/public/staff") }}?school_id={{ $school->id }}')
                .then(res => res.json())
                .then(data => {
                    if (data.length === 0) {
                        staffContainer.innerHTML = '<div class="col-12 text-center text-muted py-4">Faculty directory is empty.</div>';
                        return;
                    }
                    staffContainer.innerHTML = '';
                    data.forEach(item => {
                        const name = item.user ? item.user.name : 'Teacher';
                        const cleanName = name.replace(/\s*\(.*?\)\s*/g, '').trim();
                        const spec = item.specialization || 'Educator';
                        const photo = item.user && item.user.profile_photo 
                            ? (item.user.profile_photo.startsWith('http') ? item.user.profile_photo : `{{ asset('storage') }}/${item.user.profile_photo}`) 
                            : `https://ui-avatars.com/api/?name=${encodeURIComponent(cleanName)}&background=003366&color=fff&size=200`;
                        staffContainer.innerHTML += `
                            <div class="col-md-3 text-center">
                                <div class="staff-card-premium h-100">
                                    <img src="${photo}" class="staff-avatar-premium mb-3" alt="${name}">
                                    <h5 class="fw-bold text-dark mb-1">${name}</h5>
                                    <div class="text-muted small mb-3">${item.designation}</div>
                                    <span class="badge bg-light text-primary border px-2 py-1 small fw-bold">${spec}</span>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(err => {
                    staffContainer.innerHTML = '<div class="col-12 text-center text-danger py-4">Failed to load faculty directory.</div>';
                });
            }

            // Load Dynamic Gallery
            const galleryContainer = document.getElementById('dynamic-gallery-container');
            if (galleryContainer) {
                fetch('{{ url("/api/public/gallery") }}?school_id={{ $school->id }}')
                .then(res => res.json())
                .then(data => {
                    if (data.length === 0) {
                        galleryContainer.innerHTML = '<div class="col-12 text-center text-muted py-4">No gallery pictures uploaded yet.</div>';
                        return;
                    }
                    galleryContainer.innerHTML = '';
                    data.forEach(item => {
                        const imgUrl = item.image_path.startsWith('http') ? item.image_path : `{{ asset('storage') }}/${item.image_path}`;
                        galleryContainer.innerHTML += `
                            <div class="col-md-3 col-6">
                                <div class="gallery-item-premium ratio ratio-1x1">
                                    <img src="${imgUrl}" alt="${item.title}">
                                    <div class="gallery-overlay-premium fw-semibold">${item.title}</div>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(err => {
                    galleryContainer.innerHTML = '<div class="col-12 text-center text-danger py-4">Failed to load gallery feed.</div>';
                });
            }
        });
    </script>
</body>
</html>
