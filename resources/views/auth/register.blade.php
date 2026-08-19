<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Theme Initializer (Prevent Theme Flash) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    @php
        $currentHost = request()->getHost();
        $appName = config('app.name', 'EduLink');
        if ($currentHost === 'localhost' || $currentHost === '127.0.0.1') {
            $domainSuffix = '.' . strtolower($appName) . '.local';
        } else {
            $parts = explode('.', $currentHost);
            if (count($parts) > 2) {
                array_shift($parts);
                $domainSuffix = '.' . implode('.', $parts);
            } else {
                $domainSuffix = '.' . $currentHost;
            }
        }
    @endphp

    <title>Register Your School | {{ $appName }} Ghana ERP</title>
    
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            /* Brand Colors */
            --primary-navy: #003366;       /* Official EduLink GES Navy */
            --primary-dark: #002244;       /* Deep Navy */
            --primary-deep: #07182d;       /* Dark Canvas Slate */
            --primary-light: #0f4c81;      /* Mid-Navy */
            --primary-subtle: #eef4fb;     /* Soft Blue Tint */
            
            --accent-gold: #FFD700;        /* Warm Gold */
            --accent-gold-dark: #d99b00;   /* Deep Gold */
            --accent-amber: #f59e0b;       /* Amber */
            
            --success-green: #10b981;      /* MoMo Emerald */
            
            --bg-canvas: #f8fafc;          /* Light Canvas */
            --bg-card: #ffffff;            /* White Surface */
            --bg-light-tint: #f1f5f9;      /* Tint Section */
            
            --text-heading: #0f172a;       /* Dark Text */
            --text-body: #334155;          /* Body Text */
            --text-muted: #64748b;         /* Muted Text */
            
            --border-subtle: #e2e8f0;      /* Subtle Border */
            
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 14px -2px rgba(0, 51, 102, 0.08);
            --shadow-lg: 0 12px 30px -4px rgba(0, 51, 102, 0.12);
            --shadow-xl: 0 20px 40px -8px rgba(0, 51, 102, 0.16);

            --font-heading: 'Outfit', sans-serif;
            --font-body: 'Inter', sans-serif;
        }

        /* Dark Mode Theme Tokens */
        [data-bs-theme="dark"] {
            --bg-canvas: #090f1d;
            --bg-card: #111a2e;
            --bg-light-tint: #0c1427;
            
            --text-heading: #f8fafc;
            --text-body: #cbd5e1;
            --text-muted: #94a3b8;
            
            --border-subtle: rgba(255, 255, 255, 0.08);
            --primary-subtle: rgba(0, 51, 102, 0.45);
            
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 14px -2px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 12px 30px -4px rgba(0, 0, 0, 0.5);
            --shadow-xl: 0 20px 40px -8px rgba(0, 0, 0, 0.6);
        }

        /* ==========================================================================
           COMPREHENSIVE DARK MODE CONTRAST OVERRIDES ([data-bs-theme="dark"])
           ========================================================================== */
        [data-bs-theme="dark"] .text-dark,
        [data-bs-theme="dark"] .text-heading,
        [data-bs-theme="dark"] h1,
        [data-bs-theme="dark"] h2,
        [data-bs-theme="dark"] h3,
        [data-bs-theme="dark"] h4,
        [data-bs-theme="dark"] h5,
        [data-bs-theme="dark"] h6 {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .text-body,
        [data-bs-theme="dark"] .text-secondary {
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .text-muted {
            color: #94a3b8 !important;
        }

        [data-bs-theme="dark"] p {
            color: #cbd5e1;
        }

        [data-bs-theme="dark"] .benefit-checklist li {
            color: #cbd5e1 !important;
        }
        [data-bs-theme="dark"] .benefit-checklist li strong {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .quote-testimonial-card {
            background: #111a2e !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            border-left-color: var(--accent-gold) !important;
        }
        [data-bs-theme="dark"] .quote-testimonial-card p {
            color: #cbd5e1 !important;
        }
        [data-bs-theme="dark"] .quote-testimonial-card .text-dark {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .auth-top-header {
            background: #090f1d !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-bs-theme="dark"] .auth-footer {
            background: #090f1d !important;
            border-top-color: rgba(255, 255, 255, 0.08) !important;
            color: #94a3b8 !important;
        }

        [data-bs-theme="dark"] .register-form-card {
            background: #111a2e !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 20px 40px -8px rgba(0, 0, 0, 0.6) !important;
        }

        [data-bs-theme="dark"] .step-header-label {
            color: var(--accent-gold) !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-bs-theme="dark"] .form-label-custom {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .form-control-custom,
        [data-bs-theme="dark"] .form-select-custom {
            background-color: #0c1427 !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .form-control-custom::placeholder {
            color: #64748b !important;
            opacity: 0.8 !important;
        }

        [data-bs-theme="dark"] .form-control-custom:focus,
        [data-bs-theme="dark"] .form-select-custom:focus {
            background-color: #111a2e !important;
            border-color: var(--accent-gold) !important;
            box-shadow: 0 0 0 3.5px rgba(255, 215, 0, 0.18) !important;
            color: #ffffff !important;
        }

        [data-bs-theme="dark"] .form-select-custom option {
            background-color: #111a2e !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .input-group-text-custom {
            background-color: #141f38 !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .subdomain-helper-box {
            background: rgba(255, 255, 255, 0.05) !important;
            color: #94a3b8 !important;
        }

        [data-bs-theme="dark"] .subdomain-helper-box strong,
        [data-bs-theme="dark"] .subdomain-helper-box .text-primary {
            color: #58a6ff !important;
        }

        [data-bs-theme="dark"] .text-primary {
            color: #58a6ff !important;
        }

        [data-bs-theme="dark"] a.text-primary:hover {
            color: #93c5fd !important;
        }

        [data-bs-theme="dark"] .bg-success-subtle {
            background-color: rgba(16, 185, 129, 0.18) !important;
            color: #34d399 !important;
        }

        html, body {
            overflow-x: hidden;
            width: 100%;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--bg-canvas);
            color: var(--text-body);
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            color: var(--text-heading);
            font-weight: 700;
        }

        /* Global Header */
        .auth-top-header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-subtle);
            padding: 0.85rem 0;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .logo-box {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-light) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-gold);
            font-size: 1.25rem;
            box-shadow: 0 2px 8px rgba(0, 51, 102, 0.2);
        }

        .brand-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--primary-navy);
            letter-spacing: -0.5px;
        }

        .brand-title span.gold-text {
            color: var(--accent-gold-dark);
        }

        [data-bs-theme="dark"] .brand-title {
            color: #ffffff;
        }

        .theme-toggle-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--bg-light-tint);
            border: 1px solid var(--border-subtle);
            color: var(--primary-navy);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .theme-toggle-btn:hover {
            transform: scale(1.06);
            background: var(--primary-subtle);
            color: var(--primary-dark);
        }

        [data-bs-theme="dark"] .theme-toggle-btn {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.15);
            color: var(--accent-gold);
        }

        /* Value Proposition Panel (Left) */
        .register-value-panel {
            padding-right: 1.5rem;
        }

        .value-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--primary-subtle);
            color: var(--primary-navy);
            border: 1px solid rgba(0, 51, 102, 0.15);
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
        }

        [data-bs-theme="dark"] .value-badge {
            color: #58a6ff;
            border-color: rgba(88, 166, 255, 0.3);
        }

        .value-title {
            font-size: clamp(1.85rem, 3.2vw, 2.4rem);
            font-weight: 900;
            color: var(--primary-dark);
            letter-spacing: -0.8px;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        [data-bs-theme="dark"] .value-title {
            color: #ffffff;
        }

        .value-desc {
            font-size: 1rem;
            color: var(--text-body);
            line-height: 1.65;
            margin-bottom: 2rem;
        }

        .benefit-checklist {
            list-style: none;
            padding: 0;
            margin: 0 0 2.5rem 0;
        }

        .benefit-checklist li {
            margin-bottom: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.95rem;
            color: var(--text-body);
        }

        .benefit-icon-check {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #ecfdf5;
            color: var(--success-green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
            margin-top: 0.15rem;
        }

        [data-bs-theme="dark"] .benefit-icon-check {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }

        .quote-testimonial-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-left: 4px solid var(--accent-gold-dark);
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            box-shadow: var(--shadow-sm);
        }

        /* Registration Form Card (Right) */
        .register-form-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            padding: 2.5rem 2.25rem;
            box-shadow: var(--shadow-xl);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .step-header-label {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--primary-navy);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 1.5px solid var(--border-subtle);
            padding-bottom: 0.5rem;
            margin-bottom: 1.25rem;
        }

        .form-label-custom {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-heading);
            margin-bottom: 0.35rem;
        }

        .form-control-custom, .form-select-custom {
            background-color: var(--bg-canvas);
            border: 1.5px solid var(--border-subtle);
            color: var(--text-heading);
            border-radius: 10px;
            padding: 0.65rem 0.95rem;
            font-size: 0.92rem;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus, .form-select-custom:focus {
            background-color: var(--bg-card);
            border-color: var(--primary-navy);
            box-shadow: 0 0 0 3.5px rgba(0, 51, 102, 0.12);
            color: var(--text-heading);
        }

        .input-group-text-custom {
            background-color: var(--bg-light-tint);
            border: 1.5px solid var(--border-subtle);
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 0 10px 10px 0;
            padding: 0.65rem 0.85rem;
        }

        .subdomain-helper-box {
            font-size: 0.76rem;
            color: var(--text-muted);
            margin-top: 0.35rem;
            background: var(--bg-light-tint);
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            display: inline-block;
        }

        .btn-register-submit {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 1.02rem;
            border-radius: 12px;
            padding: 0.85rem 1.5rem;
            box-shadow: 0 4px 15px rgba(217, 119, 6, 0.3);
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
        }

        .btn-register-submit:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            box-shadow: 0 6px 20px rgba(217, 119, 6, 0.45);
            transform: translateY(-2px);
        }

        .btn-brand-outline-sm {
            border: 1.5px solid var(--primary-navy);
            color: var(--primary-navy);
            background: transparent;
            font-weight: 600;
            font-size: 0.85rem;
            border-radius: 8px;
            padding: 0.35rem 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: all 0.2s ease;
        }

        .btn-brand-outline-sm:hover {
            background: var(--primary-subtle);
            color: var(--primary-dark);
        }

        [data-bs-theme="dark"] .btn-brand-outline-sm {
            border-color: rgba(255, 255, 255, 0.25);
            color: #f1f5f9;
        }

        [data-bs-theme="dark"] .btn-brand-outline-sm:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        /* Password Show/Hide Toggle */
        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0;
            z-index: 10;
        }

        .password-toggle-btn:hover {
            color: var(--primary-navy);
        }

        [data-bs-theme="dark"] .password-toggle-btn:hover {
            color: var(--accent-gold);
        }

        /* Footer */
        .auth-footer {
            background: var(--bg-card);
            border-top: 1px solid var(--border-subtle);
            padding: 1.25rem 0;
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        @media (max-width: 991.98px) {
            .register-value-panel {
                padding-right: 0;
                margin-bottom: 2.5rem;
                text-align: center;
            }
            .benefit-checklist li {
                justify-content: center;
            }
            .register-form-card {
                padding: 1.75rem 1.25rem;
            }
        }

        @media (max-width: 575.98px) {
            .btn-register-submit {
                font-size: 0.95rem;
                padding: 0.8rem 1rem;
                gap: 0.4rem;
            }
        }
    </style>
</head>
<body>
    @include('partials.preloader')

    <!-- Top Navigation / Brand Header -->
    <header class="auth-top-header">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="d-flex align-items-center text-decoration-none" href="{{ url('/') }}">
                <div class="logo-box me-2.5">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <span class="brand-title">
                    {{ substr($appName, 0, 3) }}<span class="gold-text">{{ substr($appName, 3) }}</span>
                </span>
            </a>

            <div class="d-flex align-items-center gap-3">
                <button class="theme-toggle-btn" id="themeToggleBtn" type="button" aria-label="Toggle dark/light theme" title="Toggle theme">
                    <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                </button>
                <a href="{{ url('/') }}" class="text-decoration-none text-muted small fw-semibold d-none d-sm-inline-flex align-items-center">
                    <i class="bi bi-arrow-left me-1"></i>Home
                </a>
                <a href="{{ route('login') }}" class="btn-brand-outline-sm">
                    <i class="bi bi-person-lock"></i>Sign In
                </a>
            </div>
        </div>
    </header>

    <!-- Main Registration Section -->
    <main class="py-5 flex-grow-1">
        <div class="container">
            <div class="row align-items-center g-5">
                
                <!-- Left Column: Value Proposition & Assurance (Desktop Only) -->
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="register-value-panel">
                        <div class="value-badge">
                            <i class="bi bi-patch-check-fill text-warning me-1"></i>
                            <span>14-Day Full Free Trial &bull; No Card Required</span>
                        </div>

                        <h1 class="value-title">
                            Set Up Your School's Digital Campus in Minutes
                        </h1>

                        <p class="value-desc">
                            Join premier Basic, JHS, and Senior High Schools in Ghana automating terminal reports, mobile money billing, and parent communication.
                        </p>

                        <ul class="benefit-checklist">
                            <li>
                                <div class="benefit-icon-check"><i class="bi bi-check-lg"></i></div>
                                <div><strong>100% GES SBA Compliant</strong> — 50/50 grading scale, terminal remarks builder, and print-ready PDF reports.</div>
                            </li>
                            <li>
                                <div class="benefit-icon-check"><i class="bi bi-check-lg"></i></div>
                                <div><strong>Direct MTN MoMo & Telecel Billing</strong> — Instant parent receipts and automated account balance updates.</div>
                            </li>
                            <li>
                                <div class="benefit-icon-check"><i class="bi bi-check-lg"></i></div>
                                <div><strong>Multi-Portal Access</strong> — Tailored dashboards for Administrators, Teachers, Parents, and Students.</div>
                            </li>
                            <li>
                                <div class="benefit-icon-check"><i class="bi bi-check-lg"></i></div>
                                <div><strong>Local Support & Setup Assistant</strong> — Guided class and teacher onboarding with dedicated phone support.</div>
                            </li>
                        </ul>

                        <div class="quote-testimonial-card d-none d-lg-block">
                            <p class="small fst-italic text-secondary mb-2">
                                "EduLink transformed our terminal report processing. What took weeks of teacher manual calculations now compiles in under 20 minutes."
                            </p>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold small" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    AM
                                </div>
                                <div>
                                    <span class="d-block fw-bold small text-dark" style="font-size: 0.8rem;">Achimota Model Academy</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">Greater Accra Region</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Registration Form Card -->
                <div class="col-lg-7">
                    <div class="register-form-card">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h3 class="fw-bold mb-1" style="font-size: 1.4rem;">Register Your Institution</h3>
                                <span class="text-muted small">Create your master school workspace & administrator account</span>
                            </div>
                            <span class="badge bg-success-subtle text-success px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem;">
                                <i class="bi bi-shield-lock-fill me-1"></i>SSL Encrypted
                            </span>
                        </div>

                        <!-- Validation Errors Alert -->
                        @if(isset($errors) && $errors->any())
                            <div class="alert alert-danger border-0 mb-4 rounded-3 p-3" style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626;">
                                <h6 class="fw-bold mb-2 small"><i class="bi bi-exclamation-triangle-fill me-1.5"></i>Please correct the following:</h6>
                                <ul class="mb-0 ps-3 small">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('register') }}" method="POST" autocomplete="off">
                            @csrf
                            
                            <!-- Section 1: School Information -->
                            <div class="step-header-label">
                                <i class="bi bi-building"></i>
                                <span>1. School & Campus Information</span>
                            </div>

                            <div class="mb-3">
                                <label for="school_name" class="form-label-custom">School / Institution Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-custom" id="school_name" name="school_name" value="{{ old('school_name') }}" placeholder="e.g. Achimota Model Academy" required autofocus autocomplete="off">
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label for="subdomain" class="form-label-custom">Subdomain Workspace <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-custom rounded-end-0" id="subdomain" name="subdomain" value="{{ old('subdomain') }}" placeholder="e.g. achimotamodel" required autocomplete="off">
                                        <span class="input-group-text input-group-text-custom">{{ $domainSuffix }}</span>
                                    </div>
                                    <div class="subdomain-helper-box">
                                        Workspace URL: <strong class="text-primary" id="previewSub">achimotamodel{{ $domainSuffix }}</strong>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <label for="region" class="form-label-custom">Region <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-custom" id="region" name="region" required>
                                        <option value="" disabled {{ old('region') ? '' : 'selected' }}>Select Region</option>
                                        @foreach([
                                            'Greater Accra', 'Ashanti', 'Western', 'Eastern', 'Central', 
                                            'Northern', 'Upper East', 'Upper West', 'Volta', 'Savannah', 
                                            'North East', 'Bono', 'Bono East', 'Ahafo', 'Oti', 'Western North'
                                        ] as $reg)
                                            <option value="{{ $reg }}" {{ old('region') == $reg ? 'selected' : '' }}>{{ $reg }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Section 2: Administrator Information -->
                            <div class="step-header-label mt-4">
                                <i class="bi bi-person-badge"></i>
                                <span>2. Head Administrator Account</span>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="admin_name" class="form-label-custom">Admin Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-custom" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" placeholder="e.g. Dr. Kwame Mensah" required autocomplete="off">
                                </div>

                                <div class="col-md-6">
                                    <label for="admin_phone" class="form-label-custom">Admin Phone Number (MoMo / SMS) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-custom" id="admin_phone" name="admin_phone" value="{{ old('admin_phone') }}" placeholder="e.g. 0244123456" required autocomplete="off">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="admin_email" class="form-label-custom">Official Admin Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-custom" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" placeholder="admin@yourschool.edu.gh" required autocomplete="off">
                                <div class="text-muted" style="font-size: 0.74rem; margin-top: 0.25rem;">Used to sign in and receive security alerts.</div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="admin_password" class="form-label-custom">Master Password <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control form-control-custom pe-5" id="admin_password" name="admin_password" placeholder="••••••••" required autocomplete="new-password">
                                        <button type="button" class="password-toggle-btn" onclick="togglePass('admin_password', this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem; margin-top: 0.25rem;">Min. 8 characters</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="admin_password_confirmation" class="form-label-custom">Confirm Password <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control form-control-custom pe-5" id="admin_password_confirmation" name="admin_password_confirmation" placeholder="••••••••" required autocomplete="new-password">
                                        <button type="button" class="password-toggle-btn" onclick="togglePass('admin_password_confirmation', this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn-register-submit mb-3">
                                <span class="d-none d-sm-inline">Create School Workspace &bull; Start 14-Day Free Trial</span>
                                <span class="d-sm-none">Start 14-Day Free Trial</span>
                                <i class="bi bi-arrow-right"></i>
                            </button>

                            <div class="text-center text-muted small">
                                <span>Already registered your school?</span>
                                <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-bold ms-1">Sign in to Client Portal &rarr;</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="auth-footer text-center">
        <div class="container">
            &copy; 2026 {{ $appName }} Ghana ERP. Built with pride for Ghanaian Educational Excellence.
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Subdomain Live Formatter & Theme Toggle JS -->
    <script>
        // Password Visibility Toggle
        function togglePass(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Theme Switcher Controller
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const themeIcon = document.getElementById('themeIcon');
            
            function updateThemeIcon(theme) {
                if (themeIcon) {
                    if (theme === 'dark') {
                        themeIcon.className = 'bi bi-sun-fill text-warning';
                        themeToggleBtn.setAttribute('title', 'Switch to Light Mode');
                    } else {
                        themeIcon.className = 'bi bi-moon-stars-fill';
                        themeToggleBtn.setAttribute('title', 'Switch to Dark Mode');
                    }
                }
            }

            // Sync current theme state
            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            updateThemeIcon(currentTheme);

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    const activeTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                    const newTheme = activeTheme === 'dark' ? 'light' : 'dark';
                    
                    document.documentElement.setAttribute('data-bs-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    updateThemeIcon(newTheme);
                });
            }

            // Subdomain Live Preview & Sanitation
            const subdomainInput = document.getElementById('subdomain');
            const schoolNameInput = document.getElementById('school_name');
            const previewSub = document.getElementById('previewSub');
            const domainSuffix = "{{ $domainSuffix }}";

            function updatePreview() {
                const val = subdomainInput.value.trim().toLowerCase().replace(/[^a-z0-9\-]/g, '');
                previewSub.textContent = (val ? val : 'your-subdomain') + domainSuffix;
            }

            // Auto-suggest subdomain from school name if user hasn't typed a custom one
            let userEditedSubdomain = false;
            subdomainInput.addEventListener('input', function() {
                userEditedSubdomain = true;
                let cursorPosition = subdomainInput.selectionStart;
                let originalLength = subdomainInput.value.length;
                
                let cleanVal = subdomainInput.value.toLowerCase().replace(/[^a-z0-9\-]/g, '');
                subdomainInput.value = cleanVal;
                
                let difference = originalLength - cleanVal.length;
                subdomainInput.setSelectionRange(cursorPosition - difference, cursorPosition - difference);
                updatePreview();
            });

            schoolNameInput.addEventListener('input', function() {
                if (!userEditedSubdomain && !subdomainInput.value) {
                    let clean = schoolNameInput.value.toLowerCase().replace(/[^a-z0-9]/g, '');
                    subdomainInput.value = clean.substring(0, 20);
                    updatePreview();
                }
            });

            updatePreview();
        });
    </script>
</body>
</html>
