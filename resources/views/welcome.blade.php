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
        $seoTitle = \App\Models\SystemSetting::getVal('seo_meta_title', 'EduLink | Next-Gen School Management ERP');
        $seoDesc = \App\Models\SystemSetting::getVal('seo_meta_description', 'EduLink Ghana ERP is a next-generation school management system empowering administrators, teachers, parents, and students with smart automation.');
        $seoKeys = \App\Models\SystemSetting::getVal('seo_meta_keywords', 'school software, school management, Ghana ERP, report cards, GES SBA, MoMo fees');
        $googleAnalytics = \App\Models\SystemSetting::getVal('seo_google_analytics', '');
        $googleSearchConsole = \App\Models\SystemSetting::getVal('seo_search_console', '');
        $socialImage = file_exists(public_path('seo_social.png')) ? asset('seo_social.png') : 'https://placehold.co/1200x630/003366/FFF?text=EduLink+Ghana+ERP';
    @endphp

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDesc }}">
    @if(!empty($seoKeys))
        <meta name="keywords" content="{{ $seoKeys }}">
    @endif

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDesc }}">
    <meta property="og:image" content="{{ $socialImage }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $seoTitle }}">
    <meta property="twitter:description" content="{{ $seoDesc }}">
    <meta property="twitter:image" content="{{ $socialImage }}">

    @if(!empty($googleSearchConsole))
        <meta name="google-site-verification" content="{{ $googleSearchConsole }}">
    @endif

    @if(!empty($googleAnalytics))
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalytics }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $googleAnalytics }}');
        </script>
    @endif

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
            /* Brand Identity Colors */
            --primary-navy: #003366;       /* Official EduLink GES Navy Blue */
            --primary-dark: #002244;       /* Deep Navy */
            --primary-deep: #07182d;       /* Dark Banner/Footer Slate */
            --primary-light: #0f4c81;      /* Mid-Navy Accent */
            --primary-subtle: #eef4fb;     /* Soft Tint */
            
            --accent-gold: #FFD700;        /* Official Warm Gold */
            --accent-gold-dark: #d99b00;   /* Deep Gold/Amber */
            --accent-amber: #f59e0b;       /* Amber */
            
            --success-green: #10b981;      /* MoMo / Payment Emerald */
            --success-subtle: #ecfdf5;
            
            --bg-canvas: #f8fafc;          /* Crisp Professional Page Background */
            --bg-card: #ffffff;            /* Pure White Card */
            --bg-light-tint: #f1f5f9;      /* Subtle Neutral Light */
            
            --text-heading: #0f172a;       /* Slate 900 */
            --text-body: #334155;          /* Slate 700 */
            --text-muted: #64748b;         /* Slate 500 */
            
            --border-subtle: #e2e8f0;      /* Slate 200 */
            --border-card: rgba(0, 51, 102, 0.08);
            
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
            --shadow-md: 0 4px 14px -2px rgba(0, 51, 102, 0.08), 0 2px 6px -1px rgba(0, 0, 0, 0.04);
            --shadow-lg: 0 12px 30px -4px rgba(0, 51, 102, 0.12), 0 4px 12px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 40px -8px rgba(0, 51, 102, 0.16);

            --font-heading: 'Outfit', sans-serif;
            --font-body: 'Inter', sans-serif;
        }

        /* Dark Mode Theme Tokens */
        [data-bs-theme="dark"] {
            --bg-canvas: #090f1d;          /* Deep rich executive midnight navy */
            --bg-card: #111a2e;            /* Elevated dark card */
            --bg-light-tint: #0c1427;      /* Subtle dark tint section */
            
            --text-heading: #f8fafc;       /* Bright Crisp White */
            --text-body: #cbd5e1;          /* Slate 300 */
            --text-muted: #94a3b8;         /* Slate 400 */
            
            --border-subtle: rgba(255, 255, 255, 0.08);
            --border-card: rgba(255, 255, 255, 0.08);
            
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

        /* Navbar toggler and icons in Dark Mode */
        [data-bs-theme="dark"] .navbar-toggler i,
        [data-bs-theme="dark"] .navbar-toggler {
            color: #f8fafc !important;
        }

        /* Card, Container & Background Overrides */
        [data-bs-theme="dark"] .bg-white {
            background-color: #111a2e !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .bg-light {
            background-color: #0c1427 !important;
            color: #cbd5e1 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-bs-theme="dark"] .border {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        /* Tables in Dark Mode */
        [data-bs-theme="dark"] .table {
            color: #cbd5e1 !important;
            --bs-table-color: #cbd5e1;
            --bs-table-bg: transparent;
            --bs-table-border-color: rgba(255, 255, 255, 0.08);
        }

        [data-bs-theme="dark"] .table-light {
            background-color: #1a2744 !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .table th {
            color: #94a3b8 !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-bs-theme="dark"] .table td {
            color: #f1f5f9 !important;
            border-bottom-color: rgba(255, 255, 255, 0.04) !important;
        }

        /* Showcase Tab Item Inactive State in Dark Mode */
        [data-bs-theme="dark"] .showcase-tab-item {
            background: #111a2e !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-bs-theme="dark"] .showcase-tab-item h6 {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .showcase-tab-item .text-muted {
            color: #94a3b8 !important;
        }

        [data-bs-theme="dark"] .showcase-tab-item:hover {
            border-color: var(--accent-gold) !important;
            background: #141f38 !important;
        }

        /* Showcase Tab Active State in Dark Mode */
        [data-bs-theme="dark"] .showcase-tab-item.active {
            background: linear-gradient(135deg, #002244 0%, #003366 100%) !important;
            border-color: var(--accent-gold) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5) !important;
        }

        [data-bs-theme="dark"] .showcase-tab-item.active h6 {
            color: #ffffff !important;
        }

        [data-bs-theme="dark"] .showcase-tab-item.active .text-muted {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        /* Showcase Display Panels */
        [data-bs-theme="dark"] .showcase-content-panel {
            background: #111a2e !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-bs-theme="dark"] .showcase-content-panel h3,
        [data-bs-theme="dark"] .showcase-content-panel h4,
        [data-bs-theme="dark"] .showcase-content-panel h5,
        [data-bs-theme="dark"] .showcase-content-panel h6 {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .feature-check-list li {
            color: #cbd5e1 !important;
        }

        /* Badges Tonal Accents in Dark Mode */
        [data-bs-theme="dark"] .bg-primary-subtle {
            background-color: rgba(0, 51, 102, 0.45) !important;
            color: #58a6ff !important;
        }

        [data-bs-theme="dark"] .bg-warning-subtle {
            background-color: rgba(245, 158, 11, 0.18) !important;
            color: #fbbf24 !important;
        }

        [data-bs-theme="dark"] .bg-info-subtle {
            background-color: rgba(14, 165, 233, 0.18) !important;
            color: #38bdf8 !important;
        }

        [data-bs-theme="dark"] .bg-success-subtle {
            background-color: rgba(16, 185, 129, 0.18) !important;
            color: #34d399 !important;
        }

        /* Student ID Card Showcase in Dark Mode */
        [data-bs-theme="dark"] #showcase-portals .bg-white {
            background-color: #141f38 !important;
            border-color: rgba(255, 215, 0, 0.35) !important;
        }

        [data-bs-theme="dark"] #showcase-portals .p-1\.5.bg-light,
        [data-bs-theme="dark"] #showcase-portals .p-1\.5 {
            background-color: #ffffff !important;
        }

        [data-bs-theme="dark"] #showcase-portals .p-1\.5 i {
            color: #000000 !important;
        }

        /* Pillars & Feature Cards */
        [data-bs-theme="dark"] .pillar-card-pro {
            background: #111a2e !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-bs-theme="dark"] .pillar-card-pro h4 {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .pillar-card-pro p {
            color: #cbd5e1 !important;
        }

        /* Pricing Cards */
        [data-bs-theme="dark"] .pricing-card-pro {
            background: #111a2e !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-bs-theme="dark"] .pricing-card-pro.popular {
            border-color: var(--accent-gold) !important;
            background: #141f38 !important;
        }

        [data-bs-theme="dark"] .pricing-card-pro h4 {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .pricing-card-pro p,
        [data-bs-theme="dark"] .pricing-card-pro li span {
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .pricing-amount-row {
            color: #ffffff !important;
        }

        /* FAQ Accordion */
        [data-bs-theme="dark"] .faq-card-item {
            background: #111a2e !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-bs-theme="dark"] .faq-card-item summary {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .faq-answer-content {
            color: #cbd5e1 !important;
            border-top-color: rgba(255, 255, 255, 0.08) !important;
        }

        /* Trust Chips */
        [data-bs-theme="dark"] .trust-schools-section {
            background: #0c1427 !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-bs-theme="dark"] .trust-school-chip {
            background: #111a2e !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            color: #cbd5e1 !important;
        }

        /* ERP Mockup */
        [data-bs-theme="dark"] .erp-mockup-frame {
            border-color: rgba(255, 255, 255, 0.1) !important;
            background: #111a2e !important;
        }

        [data-bs-theme="dark"] .erp-mockup-topbar {
            background: #0d1424 !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-bs-theme="dark"] .browser-url-bar {
            background: #111a2e !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #94a3b8 !important;
        }

        [data-bs-theme="dark"] .erp-mockup-body,
        [data-bs-theme="dark"] .erp-mock-main {
            background: #090f1d !important;
        }

        [data-bs-theme="dark"] .erp-app-header,
        [data-bs-theme="dark"] .erp-kpi-card,
        [data-bs-theme="dark"] .erp-chart-box,
        [data-bs-theme="dark"] .erp-table-box {
            background: #111a2e !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-bs-theme="dark"] .erp-kpi-val {
            color: #ffffff !important;
        }

        [data-bs-theme="dark"] .erp-table-box span.fw-bold {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .erp-mini-table td {
            color: #cbd5e1 !important;
            border-bottom-color: rgba(255, 255, 255, 0.04) !important;
        }

        [data-bs-theme="dark"] .erp-mini-table td strong {
            color: #f8fafc !important;
        }

        html, body {
            overflow-x: hidden;
            width: 100%;
            margin: 0;
            padding: 0;
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--bg-canvas);
            color: var(--text-body);
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            color: var(--text-heading);
            font-weight: 700;
        }

        /* Top Notification Ribbon */
        .top-notification-bar {
            background: linear-gradient(90deg, var(--primary-dark) 0%, var(--primary-navy) 100%);
            color: #ffffff;
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.45rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .top-notification-bar a {
            color: var(--accent-gold);
            text-decoration: none;
            font-weight: 600;
        }

        .top-notification-bar a:hover {
            text-decoration: underline;
        }

        /* Sticky Glass Navbar */
        .navbar-main {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-subtle);
            padding: 0.9rem 0;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .navbar-main.scrolled {
            padding: 0.65rem 0;
            box-shadow: 0 4px 20px rgba(0, 51, 102, 0.08);
            background: rgba(255, 255, 255, 0.98);
        }

        [data-bs-theme="dark"] .navbar-main {
            background: rgba(9, 15, 29, 0.92);
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        [data-bs-theme="dark"] .navbar-main.scrolled {
            background: rgba(9, 15, 29, 0.98);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        .logo-box {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-light) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-gold);
            font-size: 1.3rem;
            box-shadow: 0 2px 8px rgba(0, 51, 102, 0.2);
            transition: transform 0.2s ease;
        }

        .logo-box:hover {
            transform: scale(1.05);
        }

        .brand-title {
            font-size: 1.4rem;
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

        .nav-link-custom {
            color: var(--text-body) !important;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.5rem 0.9rem !important;
            transition: color 0.2s ease;
            position: relative;
        }

        .nav-link-custom:hover {
            color: var(--primary-navy) !important;
        }

        .nav-link-custom::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--primary-navy);
            transition: all 0.25s ease;
            transform: translateX(-50%);
        }

        .nav-link-custom:hover::after {
            width: 70%;
        }

        [data-bs-theme="dark"] .nav-link-custom {
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .nav-link-custom:hover {
            color: #ffffff !important;
        }

        [data-bs-theme="dark"] .nav-link-custom::after {
            background: var(--accent-gold);
        }

        /* ==========================================================================
           PROFESSIONAL MOBILE NAVIGATION DRAWER & APP MENU
           ========================================================================== */
        @media (max-width: 991.98px) {
            .navbar-main {
                padding: 0.75rem 0 !important;
            }

            .navbar-collapse {
                background: var(--bg-card) !important;
                border: 1px solid var(--border-subtle);
                border-radius: 20px;
                padding: 1.15rem !important;
                margin-top: 0.85rem;
                box-shadow: 0 18px 40px -8px rgba(0, 51, 102, 0.18), 0 0 0 1px rgba(0, 51, 102, 0.04);
            }

            [data-bs-theme="dark"] .navbar-collapse {
                background: #111a2e !important;
                border-color: rgba(255, 255, 255, 0.08);
                box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.65);
            }

            .mobile-nav-list {
                display: flex;
                flex-direction: column;
                gap: 0.4rem;
            }

            .mobile-nav-item {
                display: flex;
                align-items: center;
                gap: 0.85rem;
                padding: 0.65rem 0.85rem;
                border-radius: 12px;
                background: transparent;
                transition: all 0.2s ease;
                color: var(--text-heading) !important;
            }

            .mobile-nav-item:hover,
            .mobile-nav-item:active {
                background: var(--bg-light-tint);
                color: var(--primary-navy) !important;
                transform: translateX(3px);
            }

            [data-bs-theme="dark"] .mobile-nav-item {
                color: #f8fafc !important;
            }

            [data-bs-theme="dark"] .mobile-nav-item:hover,
            [data-bs-theme="dark"] .mobile-nav-item:active {
                background: rgba(255, 255, 255, 0.06);
                color: var(--accent-gold) !important;
            }

            .mobile-nav-icon {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                background: var(--primary-subtle);
                color: var(--primary-navy);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.05rem;
                flex-shrink: 0;
                transition: all 0.2s ease;
            }

            [data-bs-theme="dark"] .mobile-nav-icon {
                background: rgba(255, 255, 255, 0.06);
                color: var(--accent-gold);
            }

            .mobile-nav-item:hover .mobile-nav-icon {
                background: var(--primary-navy);
                color: #ffffff;
            }

            [data-bs-theme="dark"] .mobile-nav-item:hover .mobile-nav-icon {
                background: var(--accent-gold);
                color: #002244;
            }

            .mobile-nav-text {
                display: flex;
                flex-direction: column;
                line-height: 1.25;
            }

            .mobile-nav-title {
                font-size: 0.92rem;
                font-weight: 700;
            }

            .mobile-nav-sub {
                font-size: 0.74rem;
                font-weight: 400;
                color: var(--text-muted);
                margin-top: 1px;
            }

            .mobile-nav-arrow {
                font-size: 0.78rem;
                color: var(--text-muted);
                margin-left: auto;
                transition: transform 0.2s ease;
            }

            .mobile-nav-item:hover .mobile-nav-arrow {
                transform: translateX(2px);
                color: var(--primary-navy);
            }

            [data-bs-theme="dark"] .mobile-nav-item:hover .mobile-nav-arrow {
                color: var(--accent-gold);
            }

            .btn-outline-brand-mobile {
                border: 1.5px solid var(--border-subtle);
                color: var(--primary-navy);
                background: var(--bg-card);
                font-size: 0.92rem;
                transition: all 0.2s ease;
            }

            .btn-outline-brand-mobile:hover {
                border-color: var(--primary-navy);
                background: var(--primary-subtle);
                color: var(--primary-dark);
            }

            [data-bs-theme="dark"] .btn-outline-brand-mobile {
                border-color: rgba(255, 255, 255, 0.15);
                background: rgba(255, 255, 255, 0.04);
                color: #f1f5f9;
            }

            [data-bs-theme="dark"] .btn-outline-brand-mobile:hover {
                border-color: var(--accent-gold);
                color: var(--accent-gold);
            }

            .btn-brand-primary-mobile {
                background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-light) 100%);
                color: #ffffff !important;
                border: none;
                font-size: 0.92rem;
                box-shadow: 0 4px 12px rgba(0, 51, 102, 0.25);
            }

            .btn-brand-primary-mobile:hover {
                background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-navy) 100%);
            }
        }

        /* Dark/Light Mode Switcher Button */
        .theme-toggle-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--bg-light-tint);
            border: 1px solid var(--border-subtle);
            color: var(--primary-navy);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .theme-toggle-btn:hover {
            transform: scale(1.08);
            background: var(--primary-subtle);
            color: var(--primary-dark);
        }

        [data-bs-theme="dark"] .theme-toggle-btn {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.15);
            color: var(--accent-gold);
        }

        [data-bs-theme="dark"] .theme-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }

        /* Buttons */
        .btn-brand-outline {
            border: 1.5px solid var(--primary-navy);
            color: var(--primary-navy);
            background: transparent;
            font-weight: 600;
            font-size: 0.92rem;
            border-radius: 10px;
            padding: 0.55rem 1.25rem;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-brand-outline:hover {
            background: var(--primary-subtle);
            color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
        }

        [data-bs-theme="dark"] .btn-brand-outline {
            border-color: rgba(255, 255, 255, 0.2);
            color: #f1f5f9;
        }

        [data-bs-theme="dark"] .btn-brand-outline:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.35);
            color: #ffffff;
        }

        .btn-brand-primary {
            background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-light) 100%);
            border: none;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 0.92rem;
            border-radius: 10px;
            padding: 0.6rem 1.35rem;
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.25);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-brand-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-navy) 100%);
            box-shadow: 0 6px 18px rgba(0, 51, 102, 0.35);
            transform: translateY(-2px);
        }

        .btn-brand-gold {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 1rem;
            border-radius: 12px;
            padding: 0.85rem 1.85rem;
            box-shadow: 0 4px 15px rgba(217, 119, 6, 0.3);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-brand-gold:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            box-shadow: 0 6px 20px rgba(217, 119, 6, 0.4);
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero-section-wrap {
            padding: 4.5rem 0 5rem 0;
            background: radial-gradient(circle at 80% 20%, rgba(0, 51, 102, 0.04) 0%, transparent 60%),
                        linear-gradient(180deg, #ffffff 0%, var(--bg-canvas) 100%);
            position: relative;
            border-bottom: 1px solid var(--border-subtle);
        }

        [data-bs-theme="dark"] .hero-section-wrap {
            background: radial-gradient(circle at 80% 20%, rgba(13, 71, 161, 0.12) 0%, transparent 60%),
                        linear-gradient(180deg, #090f1d 0%, #0c1427 100%);
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .hero-main-title {
            font-size: clamp(2.3rem, 4.2vw, 3.6rem);
            font-weight: 900;
            line-height: 1.15;
            color: var(--primary-dark);
            letter-spacing: -1.2px;
            margin-bottom: 1.25rem;
        }

        [data-bs-theme="dark"] .hero-main-title {
            color: #ffffff;
        }

        .hero-main-title span.text-gold-highlight {
            color: var(--accent-gold-dark);
            position: relative;
            display: inline-block;
        }

        [data-bs-theme="dark"] .hero-main-title span.text-gold-highlight {
            color: var(--accent-gold);
        }

        .hero-sub-desc {
            font-size: 1.125rem;
            color: var(--text-body);
            line-height: 1.7;
            margin-bottom: 2rem;
            max-width: 540px;
        }

        /* Hero Stats Cards */
        .hero-stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            padding: 1rem 1.15rem;
            box-shadow: var(--shadow-sm);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .hero-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: rgba(0, 51, 102, 0.2);
        }

        .hero-stat-number {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--primary-navy);
            line-height: 1.1;
            margin-bottom: 0.2rem;
        }

        [data-bs-theme="dark"] .hero-stat-number {
            color: #58a6ff;
        }

        .hero-stat-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* Real ERP Platform Showcase Window */
        .erp-mockup-frame {
            background: var(--bg-card);
            border: 1px solid rgba(0, 51, 102, 0.15);
            border-radius: 18px;
            box-shadow: 0 25px 50px -12px rgba(0, 51, 102, 0.22), 0 0 0 1px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        [data-bs-theme="dark"] .erp-mockup-frame {
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        }

        .erp-mockup-frame:hover {
            transform: translateY(-4px);
            box-shadow: 0 30px 60px -15px rgba(0, 51, 102, 0.28);
        }

        .erp-mockup-topbar {
            background: #f1f5f9;
            border-bottom: 1px solid var(--border-subtle);
            padding: 0.65rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        [data-bs-theme="dark"] .erp-mockup-topbar {
            background: #0d1424;
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .browser-buttons {
            display: flex;
            gap: 6px;
        }

        .browser-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .browser-dot.red { background: #ef4444; }
        .browser-dot.yellow { background: #f59e0b; }
        .browser-dot.green { background: #10b981; }

        .browser-url-bar {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            padding: 0.2rem 1.25rem;
            font-size: 0.72rem;
            color: var(--text-muted);
            font-family: monospace;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .erp-mockup-body {
            display: flex;
            height: 380px;
            background: var(--bg-canvas);
        }

        .erp-mock-sidebar {
            width: 130px;
            background: var(--primary-dark);
            color: #ffffff;
            padding: 1rem 0.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            flex-shrink: 0;
        }

        .erp-sidebar-logo {
            font-size: 0.82rem;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0 0.4rem;
        }

        .erp-sidebar-logo span span {
            color: var(--accent-gold);
        }

        .erp-sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .erp-nav-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.55rem;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 500;
            color: #94a3b8;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .erp-nav-item:hover, .erp-nav-item.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        .erp-nav-item.active {
            background: rgba(255, 215, 0, 0.15);
            color: var(--accent-gold);
            font-weight: 700;
        }

        .erp-mock-main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            background: var(--bg-canvas);
        }

        .erp-app-header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-subtle);
            padding: 0.6rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .erp-school-tag {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--primary-navy);
        }

        [data-bs-theme="dark"] .erp-school-tag {
            color: #58a6ff;
        }

        .erp-app-content {
            padding: 0.85rem;
            overflow-y: auto;
        }

        .erp-kpi-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 0.55rem 0.75rem;
            box-shadow: var(--shadow-sm);
        }

        .erp-kpi-label {
            font-size: 0.62rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
        }

        .erp-kpi-val {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin: 0.1rem 0;
        }

        [data-bs-theme="dark"] .erp-kpi-val {
            color: #ffffff;
        }

        .erp-kpi-sub {
            font-size: 0.6rem;
            font-weight: 600;
        }

        .erp-chart-box {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 0.65rem 0.85rem;
            box-shadow: var(--shadow-sm);
        }

        .erp-table-box {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 0.65rem;
            box-shadow: var(--shadow-sm);
        }

        .erp-mini-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.64rem;
        }

        .erp-mini-table th {
            color: var(--text-muted);
            font-weight: 600;
            padding: 0.25rem 0.35rem;
            border-bottom: 1px solid var(--border-subtle);
        }

        .erp-mini-table td {
            padding: 0.35rem;
            color: var(--text-heading);
            border-bottom: 1px solid var(--border-subtle);
        }

        .badge-momo-paid {
            background: #ecfdf5;
            color: #059669;
            font-weight: 700;
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            font-size: 0.58rem;
            border: 1px solid rgba(5, 150, 105, 0.2);
        }

        [data-bs-theme="dark"] .badge-momo-paid {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border-color: rgba(52, 211, 153, 0.3);
        }

        /* Trust / Institutional Accreditation Section */
        .trust-schools-section {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-subtle);
            padding: 2.25rem 0;
        }

        .trust-school-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--bg-canvas);
            border: 1px solid var(--border-subtle);
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-body);
            transition: all 0.2s ease;
        }

        .trust-school-chip:hover {
            border-color: var(--primary-navy);
            color: var(--primary-navy);
            background: var(--primary-subtle);
            transform: translateY(-2px);
        }

        [data-bs-theme="dark"] .trust-school-chip:hover {
            border-color: var(--accent-gold);
            color: var(--accent-gold);
            background: rgba(255, 215, 0, 0.08);
        }

        /* Section Headings */
        .section-tagline {
            color: var(--accent-gold-dark);
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            display: block;
        }

        [data-bs-theme="dark"] .section-tagline {
            color: var(--accent-gold);
        }

        .section-header-title {
            font-size: clamp(1.8rem, 3.2vw, 2.5rem);
            font-weight: 800;
            color: var(--primary-dark);
            letter-spacing: -0.75px;
            margin-bottom: 0.85rem;
        }

        [data-bs-theme="dark"] .section-header-title {
            color: #ffffff;
        }

        .section-header-desc {
            color: var(--text-body);
            font-size: 1.05rem;
            max-width: 620px;
            margin: 0 auto 3rem auto;
            line-height: 1.65;
        }

        /* Feature / Core Pillar Cards */
        .pillar-card-pro {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 18px;
            padding: 2.25rem 1.85rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .pillar-card-pro:hover {
            transform: translateY(-6px);
            border-color: rgba(0, 51, 102, 0.25);
            box-shadow: var(--shadow-lg);
        }

        [data-bs-theme="dark"] .pillar-card-pro:hover {
            border-color: rgba(255, 215, 0, 0.3);
            box-shadow: 0 16px 32px -4px rgba(0, 0, 0, 0.6);
        }

        .pillar-icon-box {
            width: 58px;
            height: 58px;
            background: var(--primary-subtle);
            border: 1px solid rgba(0, 51, 102, 0.12);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: var(--primary-navy);
            margin-bottom: 1.35rem;
            transition: all 0.3s ease;
        }

        [data-bs-theme="dark"] .pillar-icon-box {
            color: #58a6ff;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .pillar-card-pro:hover .pillar-icon-box {
            background: var(--primary-navy);
            color: var(--accent-gold);
            transform: scale(1.05);
        }

        /* Interactive Showcase Deep Dive Section */
        .showcase-tab-item {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            width: 100%;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: var(--shadow-sm);
        }

        .showcase-tab-item:hover {
            border-color: var(--primary-navy);
            transform: translateX(4px);
        }

        [data-bs-theme="dark"] .showcase-tab-item:hover {
            border-color: var(--accent-gold);
        }

        .showcase-tab-item.active {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-navy) 100%);
            border-color: var(--primary-dark);
            color: #ffffff;
            box-shadow: var(--shadow-md);
            transform: translateX(6px);
        }

        .showcase-tab-item.active h6 {
            color: #ffffff !important;
        }

        .showcase-tab-item.active span {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        .showcase-tab-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: var(--primary-subtle);
            color: var(--primary-navy);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        [data-bs-theme="dark"] .showcase-tab-icon {
            color: #58a6ff;
        }

        .showcase-tab-item.active .showcase-tab-icon {
            background: rgba(255, 215, 0, 0.2);
            color: var(--accent-gold);
        }

        .showcase-content-panel {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--shadow-lg);
            display: none;
        }

        .showcase-content-panel.active {
            display: block;
            animation: fadeInTab 0.35s ease-in-out forwards;
        }

        @keyframes fadeInTab {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .feature-check-list {
            list-style: none;
            padding: 0;
            margin: 1.5rem 0;
        }

        .feature-check-list li {
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 0.95rem;
            color: var(--text-body);
        }

        .feature-check-list li i {
            color: var(--success-green);
            font-size: 1.1rem;
        }

        /* Interactive ROI Efficiency Calculator */
        .calculator-card {
            background: linear-gradient(135deg, var(--primary-deep) 0%, var(--primary-dark) 100%);
            border-radius: 24px;
            color: #ffffff;
            padding: 3.5rem 2.5rem;
            box-shadow: var(--shadow-xl);
            position: relative;
            overflow: hidden;
        }

        .calculator-card h3 {
            color: #ffffff;
        }

        .calc-range-slider {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 10px;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.2);
            outline: none;
            margin: 1.5rem 0;
        }

        .calc-range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--accent-gold);
            cursor: pointer;
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.6);
        }

        .calc-result-box {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
        }

        .calc-result-val {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--accent-gold);
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        /* Pricing Billing Switch */
        .billing-toggle-wrapper {
            display: inline-flex;
            align-items: center;
            gap: 1rem;
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 50px;
            padding: 0.4rem 1.25rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 3rem;
        }

        .billing-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .billing-label.active {
            color: var(--primary-navy);
            font-weight: 700;
        }

        [data-bs-theme="dark"] .billing-label.active {
            color: #58a6ff;
        }

        .form-check-switch-custom .form-check-input {
            width: 44px;
            height: 24px;
            cursor: pointer;
            background-color: var(--border-subtle);
            border-color: var(--border-subtle);
        }

        .form-check-switch-custom .form-check-input:checked {
            background-color: var(--primary-navy);
            border-color: var(--primary-navy);
        }

        /* Pricing Cards */
        .pricing-card-pro {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 22px;
            padding: 2.75rem 2rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .pricing-card-pro:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
            border-color: rgba(0, 51, 102, 0.2);
        }

        .pricing-card-pro.popular {
            border: 2px solid var(--primary-navy);
            box-shadow: var(--shadow-lg);
        }

        [data-bs-theme="dark"] .pricing-card-pro.popular {
            border-color: var(--accent-gold);
            background: #141f38;
        }

        .popular-plan-badge {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--accent-gold-dark) 0%, #b45309 100%);
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 0.3rem 1.25rem;
            border-radius: 50px;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(180, 83, 9, 0.25);
        }

        .pricing-amount-row {
            font-size: 2.6rem;
            font-weight: 900;
            color: var(--primary-dark);
            margin: 1.25rem 0 0.5rem 0;
            display: flex;
            align-items: baseline;
            gap: 0.3rem;
        }

        [data-bs-theme="dark"] .pricing-amount-row {
            color: #ffffff;
        }

        .pricing-duration-text {
            font-size: 0.95rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .pricing-feature-list {
            list-style: none;
            padding: 0;
            margin: 2rem 0;
            flex-grow: 1;
        }

        .pricing-feature-list li {
            margin-bottom: 0.85rem;
            color: var(--text-body);
            font-size: 0.92rem;
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
        }

        .pricing-feature-list li i {
            color: var(--primary-navy);
            font-size: 1.05rem;
            margin-top: 0.15rem;
            flex-shrink: 0;
        }

        [data-bs-theme="dark"] .pricing-feature-list li i {
            color: #58a6ff;
        }

        /* FAQ Accordion */
        .faq-card-item {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            margin-bottom: 0.9rem;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: all 0.25s ease;
        }

        .faq-card-item details {
            padding: 1.25rem 1.5rem;
        }

        .faq-card-item details[open] {
            border-color: rgba(0, 51, 102, 0.2);
            background: var(--bg-card);
            box-shadow: var(--shadow-md);
        }

        .faq-card-item summary {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--primary-dark);
            cursor: pointer;
            list-style: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            outline: none;
        }

        [data-bs-theme="dark"] .faq-card-item summary {
            color: #ffffff;
        }

        .faq-card-item summary::-webkit-details-marker {
            display: none;
        }

        .faq-card-item summary::after {
            content: "\F282";
            font-family: "bootstrap-icons";
            font-size: 1rem;
            color: var(--primary-navy);
            transition: transform 0.25s ease;
        }

        [data-bs-theme="dark"] .faq-card-item summary::after {
            color: #58a6ff;
        }

        .faq-card-item details[open] summary::after {
            transform: rotate(180deg);
        }

        .faq-answer-content {
            margin-top: 1rem;
            color: var(--text-body);
            font-size: 0.95rem;
            line-height: 1.65;
            border-top: 1px solid var(--border-subtle);
            padding-top: 1rem;
        }

        /* CTA Banner */
        .cta-banner-section {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-navy) 100%);
            border-radius: 24px;
            padding: 4rem 3rem;
            color: #ffffff;
            box-shadow: var(--shadow-xl);
            position: relative;
            overflow: hidden;
        }

        .cta-banner-section h2 {
            color: #ffffff;
            font-size: clamp(2rem, 3.5vw, 2.75rem);
            font-weight: 800;
        }

        /* Footer */
        .site-footer {
            background: var(--primary-deep);
            color: #cbd5e1;
            padding: 5rem 0 2.5rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .site-footer h6 {
            color: #ffffff;
            font-weight: 700;
            margin-bottom: 1.25rem;
            font-size: 1rem;
        }

        .footer-nav-link {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s ease;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 0.65rem;
        }

        .footer-nav-link:hover {
            color: var(--accent-gold);
            transform: translateX(2px);
        }

        .system-status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            font-size: 0.78rem;
            color: #e2e8f0;
        }

        /* Floating WhatsApp Support Button */
        .whatsapp-float-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 58px;
            height: 58px;
            background-color: #25d366;
            color: #ffffff !important;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            box-shadow: 0 4px 18px rgba(37, 211, 102, 0.4);
            z-index: 9999;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .whatsapp-float-btn:hover {
            transform: scale(1.1) rotate(5deg);
            background-color: #20ba5a;
            box-shadow: 0 6px 22px rgba(37, 211, 102, 0.55);
        }

        @media (max-width: 991.98px) {
            .hero-section-wrap {
                padding: 2.75rem 0 3rem 0;
            }
            .erp-mockup-body {
                height: 320px;
            }
            .erp-mock-sidebar {
                display: none;
            }
            .calculator-card {
                padding: 2rem 1.25rem;
            }
            .cta-banner-section {
                padding: 2.5rem 1.25rem;
            }
        }

        /* ==========================================================================
           STANDARD MOBILE TYPOGRAPHY & RESPONSIVE SCALING (< 768px & < 576px)
           ========================================================================== */
        @media (max-width: 768px) {
            body {
                font-size: 0.9375rem; /* Standard 15px mobile body */
            }

            /* Main Headings */
            h1, .h1 {
                font-size: 1.85rem !important;
                line-height: 1.25 !important;
                letter-spacing: -0.5px !important;
            }
            h2, .h2 {
                font-size: 1.5rem !important;
                line-height: 1.3 !important;
                letter-spacing: -0.4px !important;
            }
            h3, .h3 {
                font-size: 1.25rem !important;
                line-height: 1.35 !important;
            }
            h4, .h4 {
                font-size: 1.1rem !important;
                line-height: 1.4 !important;
            }
            h5, .h5 {
                font-size: 0.98rem !important;
            }
            h6, .h6 {
                font-size: 0.88rem !important;
            }
            p, .lead {
                font-size: 0.92rem !important;
                line-height: 1.6 !important;
            }

            /* Hero Section Mobile Typography */
            .hero-section-wrap {
                padding: 2rem 0 2.5rem 0 !important;
            }
            .hero-main-title {
                font-size: 1.85rem !important;
                line-height: 1.25 !important;
                letter-spacing: -0.5px !important;
                margin-bottom: 0.85rem !important;
            }
            .hero-sub-desc {
                font-size: 0.95rem !important;
                line-height: 1.6 !important;
                margin-bottom: 1.5rem !important;
            }
            .hero-stat-card {
                padding: 0.75rem 0.85rem !important;
            }
            .hero-stat-number {
                font-size: 1.35rem !important;
            }
            .hero-stat-label {
                font-size: 0.68rem !important;
            }

            /* Section Headers */
            .section-tagline {
                font-size: 0.75rem !important;
                letter-spacing: 0.8px !important;
                margin-bottom: 0.35rem !important;
            }
            .section-header-title {
                font-size: 1.5rem !important;
                line-height: 1.3 !important;
                letter-spacing: -0.4px !important;
                margin-bottom: 0.6rem !important;
            }
            .section-header-desc {
                font-size: 0.92rem !important;
                line-height: 1.6 !important;
                margin-bottom: 2rem !important;
            }

            /* Buttons & Actions */
            .btn-brand-gold, .btn-brand-primary {
                font-size: 0.92rem !important;
                padding: 0.65rem 1.25rem !important;
                border-radius: 10px !important;
            }
            .btn-brand-outline {
                font-size: 0.88rem !important;
                padding: 0.5rem 1rem !important;
            }

            /* Pillar & Feature Cards */
            .pillar-card-pro {
                padding: 1.4rem 1.2rem !important;
                border-radius: 14px !important;
            }
            .pillar-icon-box {
                width: 48px !important;
                height: 48px !important;
                font-size: 1.3rem !important;
                margin-bottom: 1rem !important;
            }
            .pillar-card-pro h4 {
                font-size: 1.12rem !important;
            }
            .pillar-card-pro p {
                font-size: 0.88rem !important;
                line-height: 1.55 !important;
            }

            /* Pricing Cards */
            .pricing-card-pro {
                padding: 1.5rem 1.25rem !important;
                border-radius: 14px !important;
            }
            .pricing-card-pro h4 {
                font-size: 1.15rem !important;
            }
            .pricing-amount-row {
                font-size: 1.75rem !important;
            }
            .pricing-card-pro li span {
                font-size: 0.85rem !important;
            }

            /* Showcase Tabs & Content */
            .showcase-tab-item {
                padding: 0.75rem 0.85rem !important;
                border-radius: 10px !important;
            }
            .showcase-tab-item h6 {
                font-size: 0.88rem !important;
            }
            .showcase-tab-item .text-muted {
                font-size: 0.75rem !important;
            }
            .showcase-content-panel {
                padding: 1.25rem 1rem !important;
                border-radius: 14px !important;
            }

            /* Calculator & CTA Banners */
            .calculator-card {
                padding: 1.5rem 1.15rem !important;
                border-radius: 16px !important;
            }
            .calculator-card h3 {
                font-size: 1.3rem !important;
            }
            .calc-result-box {
                padding: 1.25rem 1rem !important;
            }
            .calc-result-box .display-5 {
                font-size: 1.6rem !important;
            }
            .cta-banner-section {
                padding: 2.25rem 1.25rem !important;
                border-radius: 16px !important;
            }
            .cta-banner-section h2 {
                font-size: 1.55rem !important;
                line-height: 1.3 !important;
            }

            /* FAQ Accordion */
            .faq-card-item summary {
                font-size: 0.92rem !important;
                padding: 0.85rem 1rem !important;
            }
            .faq-answer-content {
                font-size: 0.88rem !important;
                padding: 0.75rem 1rem !important;
            }

            /* Trust & School Badges */
            .trust-school-chip {
                font-size: 0.78rem !important;
                padding: 0.35rem 0.75rem !important;
            }

            /* Footer */
            .site-footer {
                padding: 3rem 0 2rem 0 !important;
            }
            .site-footer h6 {
                font-size: 0.92rem !important;
            }
            .footer-nav-link {
                font-size: 0.85rem !important;
            }
        }

        @media (max-width: 576px) {
            /* Extra Small Mobile Screen Fine-Tuning (e.g. 360px - 414px) */
            .hero-main-title {
                font-size: 1.65rem !important;
                line-height: 1.25 !important;
            }
            .hero-sub-desc {
                font-size: 0.9rem !important;
            }
            .section-header-title {
                font-size: 1.35rem !important;
            }
            .cta-banner-section h2 {
                font-size: 1.4rem !important;
            }
            .pricing-amount-row {
                font-size: 1.55rem !important;
            }
            .nav-link-custom {
                font-size: 0.9rem !important;
            }
        }
    </style>
</head>
<body>
    @include('partials.preloader')

    <!-- Top Announcement Ribbon -->
    <div class="top-notification-bar d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-patch-check-fill text-warning me-1.5"></i>
                <span>Ghana Education Service (GES) SBA Standard Compliant &bull; Automated MTN MoMo & Telecel Billing</span>
            </div>
            <div>
                <span class="me-3"><i class="bi bi-headset me-1"></i>Dedicated Local Support: <strong>{{ \App\Models\SystemSetting::getVal('welcome_support_email', 'support@' . strtolower(config('app.name', 'edulink')) . '.gh') }}</strong></span>
                <a href="{{ route('register') }}">Get Started Free &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg sticky-top navbar-main" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center text-decoration-none" href="{{ url('/') }}">
                <div class="logo-box me-2.5">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <span class="brand-title">
                    {{ substr(config('app.name', 'EduLink'), 0, 3) }}<span class="gold-text">{{ substr(config('app.name', 'EduLink'), 3) }}</span>
                </span>
            </a>
            
            <div class="d-flex align-items-center gap-2 d-lg-none">
                <!-- Mobile Theme Toggle -->
                <button class="theme-toggle-btn theme-toggle-action" type="button" aria-label="Toggle dark or light theme" title="Toggle theme">
                    <i class="bi bi-moon-stars-fill theme-icon-el"></i>
                </button>
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMainCollapse" aria-controls="navbarMainCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-2"></i>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="navbarMainCollapse">
                <!-- Desktop Navigation Links (Large Screens) -->
                <ul class="navbar-nav mx-auto mb-3 mb-lg-0 d-none d-lg-flex">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#pillars">Core Pillars</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#tour">Platform Tour</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#calculator">ROI Calculator</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#pricing">Pricing Plans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#faq">FAQs</a>
                    </li>
                </ul>

                <!-- Mobile App Navigation Drawer (Phones & Tablets) -->
                <div class="mobile-nav-panel d-lg-none w-100">
                    <div class="mobile-nav-list mb-3">
                        <a class="mobile-nav-item text-decoration-none" href="#pillars" onclick="closeMobileNav()">
                            <div class="mobile-nav-icon">
                                <i class="bi bi-grid-1x2-fill"></i>
                            </div>
                            <div class="mobile-nav-text">
                                <span class="mobile-nav-title">Core Pillars</span>
                                <span class="mobile-nav-sub">Modular ERP Architecture</span>
                            </div>
                            <i class="bi bi-chevron-right mobile-nav-arrow"></i>
                        </a>

                        <a class="mobile-nav-item text-decoration-none" href="#tour" onclick="closeMobileNav()">
                            <div class="mobile-nav-icon">
                                <i class="bi bi-laptop-fill"></i>
                            </div>
                            <div class="mobile-nav-text">
                                <span class="mobile-nav-title">Platform Tour</span>
                                <span class="mobile-nav-sub">Interactive System Walkthrough</span>
                            </div>
                            <i class="bi bi-chevron-right mobile-nav-arrow"></i>
                        </a>

                        <a class="mobile-nav-item text-decoration-none" href="#calculator" onclick="closeMobileNav()">
                            <div class="mobile-nav-icon">
                                <i class="bi bi-calculator-fill"></i>
                            </div>
                            <div class="mobile-nav-text">
                                <span class="mobile-nav-title">ROI Calculator</span>
                                <span class="mobile-nav-sub">Estimate School Cost Savings</span>
                            </div>
                            <i class="bi bi-chevron-right mobile-nav-arrow"></i>
                        </a>

                        <a class="mobile-nav-item text-decoration-none" href="#pricing" onclick="closeMobileNav()">
                            <div class="mobile-nav-icon">
                                <i class="bi bi-tags-fill"></i>
                            </div>
                            <div class="mobile-nav-text">
                                <span class="mobile-nav-title">Pricing Plans</span>
                                <span class="mobile-nav-sub">Transparent SaaS Subscription Tiers</span>
                            </div>
                            <i class="bi bi-chevron-right mobile-nav-arrow"></i>
                        </a>

                        <a class="mobile-nav-item text-decoration-none" href="#faq" onclick="closeMobileNav()">
                            <div class="mobile-nav-icon">
                                <i class="bi bi-question-circle-fill"></i>
                            </div>
                            <div class="mobile-nav-text">
                                <span class="mobile-nav-title">Help &amp; FAQs</span>
                                <span class="mobile-nav-sub">Common Questions &amp; Support</span>
                            </div>
                            <i class="bi bi-chevron-right mobile-nav-arrow"></i>
                        </a>
                    </div>

                    <!-- Mobile Action Buttons -->
                    <div class="mobile-auth-actions pt-3 border-top">
                        <a href="{{ route('login') }}" class="btn btn-outline-brand-mobile w-100 py-2.5 rounded-3 fw-semibold text-decoration-none d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-person-lock"></i>
                            <span>Client Sign In</span>
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-brand-primary-mobile w-100 py-2.5 rounded-3 fw-bold text-decoration-none d-flex align-items-center justify-content-center gap-2 shadow-sm">
                            <i class="bi bi-rocket-takeoff-fill"></i>
                            <span>Register School Free</span>
                        </a>
                        <div class="text-center mt-2 pt-2 border-top">
                            <span class="text-muted d-inline-flex align-items-center gap-1.5" style="font-size: 0.72rem;">
                                <i class="bi bi-patch-check-fill text-success"></i>
                                <span>GES SBA Standard Compliant &bull; Instant Activation</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Desktop Action Buttons (Large Screens) -->
                <div class="d-none d-lg-flex align-items-center gap-2.5">
                    <button class="theme-toggle-btn theme-toggle-action" type="button" aria-label="Toggle dark or light theme" title="Toggle theme">
                        <i class="bi bi-moon-stars-fill theme-icon-el"></i>
                    </button>
                    <a href="{{ route('login') }}" class="btn-brand-outline text-decoration-none justify-content-center">
                        <i class="bi bi-person-lock"></i>Client Sign In
                    </a>
                    <a href="{{ route('register') }}" class="btn-brand-primary text-decoration-none justify-content-center">
                        <i class="bi bi-rocket-takeoff-fill"></i>Register School Free
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section-wrap">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 text-center text-lg-start">

                    <h1 class="hero-main-title">
                        {!! str_ireplace('ERP', '<span class="text-gold-highlight">ERP</span>', e(\App\Models\SystemSetting::getVal('welcome_hero_title', 'The Intelligent Cloud ERP for Modern Institutions'))) !!}
                    </h1>

                    <p class="hero-sub-desc">
                        {{ \App\Models\SystemSetting::getVal('welcome_hero_sub', 'Empower your school with a unified platform for academics, real-time fee tracking, automated terminal report cards, and seamless multi-portal communication. Built for institutions striving for excellence.') }}
                    </p>

                    <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3 mb-5">
                        <a href="{{ route('register') }}" class="btn-brand-gold text-decoration-none justify-content-center">
                            <span>Start 14-Day Free Trial</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="{{ route('login') }}" class="btn-brand-outline text-decoration-none justify-content-center py-3 px-4">
                            <span>Explore Client Portals</span>
                        </a>
                    </div>

                    <!-- Live KPI Stat Badges -->
                    <div class="row g-3 justify-content-center justify-content-lg-start">
                        <div class="col-4">
                            <div class="hero-stat-card text-center text-lg-start">
                                <div class="hero-stat-number">{{ \App\Models\SystemSetting::getVal('welcome_stat1_value', '10k+') }}</div>
                                <div class="hero-stat-label">{{ \App\Models\SystemSetting::getVal('welcome_stat1_label', 'Students Enrolled') }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="hero-stat-card text-center text-lg-start">
                                <div class="hero-stat-number">{{ \App\Models\SystemSetting::getVal('welcome_stat2_value', '99.9%') }}</div>
                                <div class="hero-stat-label">{{ \App\Models\SystemSetting::getVal('welcome_stat2_label', 'Uptime SLA') }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="hero-stat-card text-center text-lg-start">
                                <div class="hero-stat-number">{{ \App\Models\SystemSetting::getVal('welcome_stat3_value', '15+') }}</div>
                                <div class="hero-stat-label">{{ \App\Models\SystemSetting::getVal('welcome_stat3_label', 'Smart Modules') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Realistic ERP Platform Mockup -->
                <div class="col-lg-6">
                    <div class="erp-mockup-frame">
                        <div class="erp-mockup-topbar">
                            <div class="browser-buttons">
                                <span class="browser-dot red"></span>
                                <span class="browser-dot yellow"></span>
                                <span class="browser-dot green"></span>
                            </div>
                            <div class="browser-url-bar">
                                <i class="bi bi-shield-check text-success"></i>
                                <span>https://portal.edulink.gh/admin/dashboard</span>
                            </div>
                            <span class="badge bg-success-subtle text-success px-2 py-1 fw-bold" style="font-size: 0.68rem;">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i>Active Session
                            </span>
                        </div>

                        <div class="erp-mockup-body">
                            <!-- Sidebar -->
                            <div class="erp-mock-sidebar">
                                <div class="erp-sidebar-logo">
                                    <i class="bi bi-mortarboard-fill text-warning"></i>
                                    <span>Edu<span>Link</span></span>
                                </div>
                                <div class="erp-sidebar-nav">
                                    <div class="erp-nav-item active">
                                        <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
                                    </div>
                                    <div class="erp-nav-item">
                                        <i class="bi bi-people-fill"></i><span>Students</span>
                                    </div>
                                    <div class="erp-nav-item">
                                        <i class="bi bi-journal-bookmark-fill"></i><span>SBA Reports</span>
                                    </div>
                                    <div class="erp-nav-item">
                                        <i class="bi bi-wallet2"></i><span>MoMo Billing</span>
                                    </div>
                                    <div class="erp-nav-item">
                                        <i class="bi bi-calendar3"></i><span>Timetables</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Main Dashboard Area -->
                            <div class="erp-mock-main">
                                <div class="erp-app-header">
                                    <div>
                                        <div class="erp-school-tag">Achimota Model Academy</div>
                                        <span class="text-muted" style="font-size: 0.65rem;">Academic Year 2026/2027 &bull; Term 1</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary text-white" style="font-size: 0.6rem;">Admin Workspace</span>
                                        <div class="rounded-circle bg-warning text-dark fw-bold d-flex align-items-center justify-content-center" style="width: 22px; height: 22px; font-size: 0.65rem;">
                                            GH
                                        </div>
                                    </div>
                                </div>

                                <div class="erp-app-content">
                                    <!-- Metric KPI Row -->
                                    <div class="row g-2 mb-2.5">
                                        <div class="col-4">
                                            <div class="erp-kpi-card">
                                                <div class="erp-kpi-label">Fees Collected</div>
                                                <div class="erp-kpi-val">GHS 124.5k</div>
                                                <div class="erp-kpi-sub text-success"><i class="bi bi-arrow-up-right"></i> 89.4% MoMo</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="erp-kpi-card">
                                                <div class="erp-kpi-label">Active Pupils</div>
                                                <div class="erp-kpi-val">1,280</div>
                                                <div class="erp-kpi-sub text-primary"><i class="bi bi-check-all"></i> Verified</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="erp-kpi-card">
                                                <div class="erp-kpi-label">Attendance</div>
                                                <div class="erp-kpi-val">96.8%</div>
                                                <div class="erp-kpi-sub text-success"><i class="bi bi-shield-check"></i> Normal</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Chart Container -->
                                    <div class="erp-chart-box mb-2.5">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-dark" style="font-size: 0.68rem;">Weekly Fee Collection Velocity</span>
                                            <span class="text-muted" style="font-size: 0.58rem;">MTN MoMo &bull; Telecel &bull; Cash</span>
                                        </div>
                                        <svg width="100%" height="45" viewBox="0 0 400 45" preserveAspectRatio="none">
                                            <defs>
                                                <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                                                    <stop offset="0%" stop-color="#003366" stop-opacity="0.3"/>
                                                    <stop offset="100%" stop-color="#003366" stop-opacity="0"/>
                                                </linearGradient>
                                            </defs>
                                            <path d="M 0 45 Q 60 10 120 28 T 240 12 T 320 22 T 400 5 L 400 45 Z" fill="url(#chartGrad)"></path>
                                            <path d="M 0 45 Q 60 10 120 28 T 240 12 T 320 22 T 400 5" fill="none" stroke="#003366" stroke-width="2.5"></path>
                                        </svg>
                                    </div>

                                    <!-- Table Container -->
                                    <div class="erp-table-box">
                                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                                            <span class="fw-bold text-dark" style="font-size: 0.68rem;">Recent Verified Terminal Fees</span>
                                            <span class="badge bg-light text-dark" style="font-size: 0.58rem;">Live Stream</span>
                                        </div>
                                        <table class="erp-mini-table">
                                            <thead>
                                                <tr>
                                                    <th>Student</th>
                                                    <th>Class</th>
                                                    <th>Channel</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><strong>Kwame Asante</strong></td>
                                                    <td>JHS 2 Green</td>
                                                    <td><i class="bi bi-phone text-warning me-1"></i>MTN MoMo</td>
                                                    <td>GHS 1,450</td>
                                                    <td><span class="badge-momo-paid">Confirmed</span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Akosua Mensah</strong></td>
                                                    <td>Basic 6A</td>
                                                    <td><i class="bi bi-phone text-danger me-1"></i>Telecel</td>
                                                    <td>GHS 980</td>
                                                    <td><span class="badge-momo-paid">Confirmed</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Accreditation & Trusted Schools Section -->
    <section class="trust-schools-section">
        <div class="container text-center">
            <span class="section-tagline mb-2">Institutional Trust</span>
            <h6 class="text-muted fw-semibold mb-3.5" style="font-size: 0.95rem;">Empowering leading Basic, JHS, and Senior High Schools nationwide</h6>
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-2.5 gap-md-4">
                <div class="trust-school-chip"><i class="bi bi-patch-check-fill text-primary"></i> Achimota Model School</div>
                <div class="trust-school-chip"><i class="bi bi-shield-fill-check text-primary"></i> Adisadel Collegiate</div>
                <div class="trust-school-chip"><i class="bi bi-mortarboard-fill text-primary"></i> Wesley Girls High Academy</div>
                <div class="trust-school-chip"><i class="bi bi-award-fill text-primary"></i> Presbyterian Senior Sec.</div>
                <div class="trust-school-chip"><i class="bi bi-building-check text-primary"></i> Prempeh Model Institute</div>
            </div>
        </div>
    </section>

    <!-- Core Management Pillars Section -->
    <section class="py-5 my-4" id="pillars">
        <div class="container text-center">
            <span class="section-tagline">Comprehensive Architecture</span>
            <h2 class="section-header-title">Built for Real School Workflows</h2>
            <p class="section-header-desc">
                Everything required to operate an exceptional institution smoothly, securely, and transparently.
            </p>

            <div class="row g-4 text-start">
                <!-- Pillar 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="pillar-card-pro">
                        <div class="pillar-icon-box">
                            <i class="bi {{ \App\Models\SystemSetting::getVal('welcome_pillar1_icon', 'bi-wallet2') }}"></i>
                        </div>
                        <h4 class="fw-bold fs-5 mb-2.5">{{ \App\Models\SystemSetting::getVal('welcome_pillar1_title', 'Fee & MoMo Billing Hub') }}</h4>
                        <p class="text-secondary small mb-0">
                            {{ \App\Models\SystemSetting::getVal('welcome_pillar1_desc', 'Automate student invoices, record payments dynamically via mobile money, track partial payment history, and generate digital financial reports.') }}
                        </p>
                    </div>
                </div>

                <!-- Pillar 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="pillar-card-pro">
                        <div class="pillar-icon-box">
                            <i class="bi {{ \App\Models\SystemSetting::getVal('welcome_pillar2_icon', 'bi-journal-check') }}"></i>
                        </div>
                        <h4 class="fw-bold fs-5 mb-2.5">{{ \App\Models\SystemSetting::getVal('welcome_pillar2_title', 'Academic Reports & SBA') }}</h4>
                        <p class="text-secondary small mb-0">
                            {{ \App\Models\SystemSetting::getVal('welcome_pillar2_desc', 'Compile terminal grades, calculate GPA averages automatically, customize teacher remarks, and generate beautiful, print-ready student report cards.') }}
                        </p>
                    </div>
                </div>

                <!-- Pillar 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="pillar-card-pro">
                        <div class="pillar-icon-box">
                            <i class="bi {{ \App\Models\SystemSetting::getVal('welcome_pillar3_icon', 'bi-people-fill') }}"></i>
                        </div>
                        <h4 class="fw-bold fs-5 mb-2.5">{{ \App\Models\SystemSetting::getVal('welcome_pillar3_title', 'Multi-Role Portals') }}</h4>
                        <p class="text-secondary small mb-0">
                            {{ \App\Models\SystemSetting::getVal('welcome_pillar3_desc', 'Dedicated dashboards tailored for administrators, teachers, parents, and students. Improve engagement with real-time access to assignments and performance.') }}
                        </p>
                    </div>
                </div>

                <!-- Pillar 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="pillar-card-pro">
                        <div class="pillar-icon-box">
                            <i class="bi {{ \App\Models\SystemSetting::getVal('welcome_pillar4_icon', 'bi-calendar-event') }}"></i>
                        </div>
                        <h4 class="fw-bold fs-5 mb-2.5">{{ \App\Models\SystemSetting::getVal('welcome_pillar4_title', 'Timetable Planner') }}</h4>
                        <p class="text-secondary small mb-0">
                            {{ \App\Models\SystemSetting::getVal('welcome_pillar4_desc', 'Generate clash-free timetables for classes, schedule subject allocations, assign teacher rooms, and organize academic calendars with ease.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Platform Tour (Tabbed Deep-Dive) -->
    <section class="py-5" id="tour" style="background-color: var(--bg-light-tint); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-tagline">Interactive Experience</span>
                <h2 class="section-header-title">Deep Dive Platform Capabilities</h2>
                <p class="section-header-desc">
                    Discover how each component of EduLink eliminates repetitive administrative friction.
                </p>
            </div>

            <div class="row g-4 align-items-center">
                <!-- Tabs Navigation -->
                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-3">
                        <div class="showcase-tab-item active" data-target="showcase-billing">
                            <div class="showcase-tab-icon">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Finance & MoMo Collections</h6>
                                <span class="text-muted small">Automated terminal billing & receipts</span>
                            </div>
                        </div>

                        <div class="showcase-tab-item" data-target="showcase-reports">
                            <div class="showcase-tab-icon">
                                <i class="bi bi-file-earmark-spreadsheet"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">GES SBA & Report Cards</h6>
                                <span class="text-muted small">50/50 weighting & batch PDF printer</span>
                            </div>
                        </div>

                        <div class="showcase-tab-item" data-target="showcase-timetables">
                            <div class="showcase-tab-icon">
                                <i class="bi bi-calendar3-range"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Clash-Free Academic Planner</h6>
                                <span class="text-muted small">Course allocations & teacher loads</span>
                            </div>
                        </div>

                        <div class="showcase-tab-item" data-target="showcase-portals">
                            <div class="showcase-tab-icon">
                                <i class="bi bi-qr-code-scan"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Parent Portal & QR ID Cards</h6>
                                <span class="text-muted small">SMS alerts & digital student badges</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Display Panels -->
                <div class="col-lg-8">
                    <!-- Tab 1: Billing -->
                    <div class="showcase-content-panel active" id="showcase-billing">
                        <div class="row align-items-center g-4">
                            <div class="col-md-7">
                                <h3 class="fw-bold text-dark mb-3">Zero Fee Leakage with Instant Mobile Money Callbacks</h3>
                                <p class="text-secondary small mb-3">
                                    Eliminate cash handling queues. EduLink automatically creates terminal bills, accepts MTN MoMo, Telecel Cash, and bank cards, and reconciles balances in real time.
                                </p>
                                <ul class="feature-check-list">
                                    <li><i class="bi bi-check-circle-fill"></i> Bulk terminal invoice generator for all classes</li>
                                    <li><i class="bi bi-check-circle-fill"></i> Instant SMS fee receipts dispatched to parents</li>
                                    <li><i class="bi bi-check-circle-fill"></i> Granular audit log and bank reconciliation exporter</li>
                                </ul>
                            </div>
                            <div class="col-md-5">
                                <div class="p-3 bg-light rounded-4 border">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fw-bold text-dark small"><i class="bi bi-wallet2 text-primary me-1"></i> Term Summary</span>
                                        <span class="badge bg-success text-white" style="font-size: 0.65rem;">Active</span>
                                    </div>
                                    <div class="p-2.5 bg-white rounded-3 border mb-2 text-center">
                                        <span class="text-muted small d-block" style="font-size: 0.7rem;">Total Invoiced</span>
                                        <h5 class="fw-bold text-dark mb-0">GHS 184,200</h5>
                                    </div>
                                    <div class="p-2.5 bg-white rounded-3 border text-center">
                                        <span class="text-muted small d-block" style="font-size: 0.7rem;">Collected via MoMo</span>
                                        <h5 class="fw-bold text-success mb-0">GHS 164,850</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Reports -->
                    <div class="showcase-content-panel" id="showcase-reports">
                        <div class="row align-items-center g-4">
                            <div class="col-md-7">
                                <h3 class="fw-bold text-dark mb-3">Automated Terminal Report Cards in Minutes</h3>
                                <p class="text-secondary small mb-3">
                                    Teachers enter continuous class SBA tests (50%) and terminal exam marks (50%). EduLink automatically scales scores, calculates GPAs, assigns class positions, and generates print-ready PDF broadsheets.
                                </p>
                                <ul class="feature-check-list">
                                    <li><i class="bi bi-check-circle-fill"></i> Standard GES continuous assessment grading scale</li>
                                    <li><i class="bi bi-check-circle-fill"></i> Automated smart teacher & headteacher remarks assistant</li>
                                    <li><i class="bi bi-check-circle-fill"></i> 1-click batch PDF export with custom school crest and signatures</li>
                                </ul>
                            </div>
                            <div class="col-md-5">
                                <div class="p-3 bg-light rounded-4 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold text-dark small"><i class="bi bi-award-fill text-warning me-1"></i> SBA Broadsheet</span>
                                        <span class="badge bg-primary text-white" style="font-size: 0.65rem;">JHS 3</span>
                                    </div>
                                    <table class="table table-sm table-borderless small mb-0 bg-transparent">
                                        <thead>
                                            <tr class="border-bottom text-muted" style="font-size: 0.7rem;">
                                                <th>Pupil</th>
                                                <th>SBA(50)</th>
                                                <th>Exam(50)</th>
                                                <th>Grade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Ama Osei</td>
                                                <td>44.0</td>
                                                <td>46.0</td>
                                                <td><span class="badge bg-success" style="font-size: 0.65rem;">A1 (90%)</span></td>
                                            </tr>
                                            <tr>
                                                <td>Kofi Antwi</td>
                                                <td>38.5</td>
                                                <td>41.0</td>
                                                <td><span class="badge bg-warning text-dark" style="font-size: 0.65rem;">B2 (79.5%)</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Timetables -->
                    <div class="showcase-content-panel" id="showcase-timetables">
                        <div class="row align-items-center g-4">
                            <div class="col-md-7">
                                <h3 class="fw-bold text-dark mb-3">Clash-Free Timetables and Room Allocations</h3>
                                <p class="text-secondary small mb-3">
                                    Easily configure subject periods, assign teachers to class streams, and inspect room capacities. Our built-in conflict detector guarantees zero double-booking.
                                </p>
                                <ul class="feature-check-list">
                                    <li><i class="bi bi-check-circle-fill"></i> Visual weekly grid schedule for all streams</li>
                                    <li><i class="bi bi-check-circle-fill"></i> Teacher workload balancing & lesson limits inspection</li>
                                    <li><i class="bi bi-check-circle-fill"></i> Printable master and individual teacher timetables</li>
                                </ul>
                            </div>
                            <div class="col-md-5">
                                <div class="p-3 bg-light rounded-4 border text-center">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold text-dark small"><i class="bi bi-calendar-check text-primary me-1"></i> Class Matrix</span>
                                        <span class="badge bg-info text-dark" style="font-size: 0.65rem;">Mon - Fri</span>
                                    </div>
                                    <table class="table table-bordered table-sm text-center small mb-0 bg-white" style="font-size: 0.68rem;">
                                        <thead>
                                            <tr class="table-light">
                                                <th>Period</th>
                                                <th>JHS 1</th>
                                                <th>JHS 2</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>08:30</td>
                                                <td class="bg-primary-subtle text-primary fw-bold">Maths</td>
                                                <td class="bg-success-subtle text-success fw-bold">Science</td>
                                            </tr>
                                            <tr>
                                                <td>09:30</td>
                                                <td class="bg-warning-subtle text-warning-emphasis fw-bold">English</td>
                                                <td class="bg-primary-subtle text-primary fw-bold">Maths</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Portals -->
                    <div class="showcase-content-panel" id="showcase-portals">
                        <div class="row align-items-center g-4">
                            <div class="col-md-7">
                                <h3 class="fw-bold text-dark mb-3">Multi-Role Portals & Digital Student ID Badges</h3>
                                <p class="text-secondary small mb-3">
                                    Keep parents informed with real-time fee balances, attendance alerts, homework assignments, and generate secure student ID badges with scannable QR verification.
                                </p>
                                <ul class="feature-check-list">
                                    <li><i class="bi bi-check-circle-fill"></i> Parent portal with direct mobile payment link</li>
                                    <li><i class="bi bi-check-circle-fill"></i> Downloadable student assignments and lesson materials</li>
                                    <li><i class="bi bi-check-circle-fill"></i> Printable student ID cards with encrypted verification QR</li>
                                </ul>
                            </div>
                            <div class="col-md-5 text-center">
                                <div class="p-3 bg-white rounded-4 border shadow-sm d-inline-block text-center" style="width: 220px; border-color: var(--primary-navy) !important;">
                                    <i class="bi bi-mortarboard-fill text-primary fs-3 mb-1 d-block"></i>
                                    <span class="fw-bold text-dark d-block mb-2" style="font-size: 0.72rem;">EDULINK STUDENT ID</span>
                                    <div class="bg-warning-subtle text-warning-emphasis fw-bold rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.1rem;">
                                        KO
                                    </div>
                                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.82rem;">Kofi Osei</h6>
                                    <span class="text-muted d-block mb-2" style="font-size: 0.65rem;">ID: EL-2026-9043</span>
                                    <div class="p-1.5 bg-light rounded d-inline-block border">
                                        <i class="bi bi-qr-code text-dark" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive School Efficiency Calculator -->
    <section class="py-5" id="calculator">
        <div class="container">
            <div class="calculator-card">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <h3 class="fw-bold mb-3" style="font-size: 2rem;">Calculate Your Institution's Time & Revenue Savings</h3>
                        <p class="text-white-50 mb-4">
                            Slide to match your school's total student enrollment and see the measurable boost in administrative velocity and fee collection rates with EduLink Ghana ERP.
                        </p>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Student Population:</span>
                                <span class="fw-bold text-warning fs-5" id="calc-student-display">500 Students</span>
                            </div>
                            <input type="range" class="calc-range-slider" id="studentSlider" min="50" max="2500" step="50" value="500">
                            <div class="d-flex justify-content-between text-white-50 small">
                                <span>50 Pupils</span>
                                <span>1,200 Pupils</span>
                                <span>2,500+ Pupils</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="calc-result-box">
                                    <div class="calc-result-val" id="calc-hours-saved">120+</div>
                                    <span class="text-white-50 small fw-semibold">Staff Hours Saved Per Term</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="calc-result-box">
                                    <div class="calc-result-val" id="calc-fee-recovery">+32%</div>
                                    <span class="text-white-50 small fw-semibold">Faster MoMo Fee Recovery</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="calc-result-box">
                                    <div class="calc-result-val" id="calc-report-time">10 Mins</div>
                                    <span class="text-white-50 small fw-semibold">Term Report Generation</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="calc-result-box">
                                    <div class="calc-result-val" id="calc-sms-speed">Instant</div>
                                    <span class="text-white-50 small fw-semibold">Parent Communication</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="py-5 my-4" id="pricing">
        <div class="container text-center">
            <span class="section-tagline">Transparent Investment</span>
            <h2 class="section-header-title">Simple, All-Inclusive School Packages</h2>
            <p class="section-header-desc">
                No setup surprises or hidden hardware fees. Every package starts with our full-featured 14-day free trial.
            </p>

            <!-- Billing Toggle -->
            <div class="billing-toggle-wrapper">
                <span class="billing-label active" id="label-termly">Termly Billing</span>
                <div class="form-check form-switch form-check-switch-custom mb-0 d-inline-block">
                    <input class="form-check-input" type="checkbox" role="switch" id="pricingBillingSwitch">
                </div>
                <span class="billing-label" id="label-annual">
                    Annual Billing 
                    <span class="badge bg-success text-white ms-1" style="font-size: 0.72rem;">Save 20%</span>
                </span>
            </div>

            <!-- Pricing Cards Grid -->
            <div class="row g-4 justify-content-center text-start">
                @php
                    try {
                        $plans = \App\Models\Plan::where('is_active', true)->orderBy('price_monthly', 'asc')->get();
                    } catch (\Exception $e) {
                        $plans = collect();
                    }
                @endphp

                @if($plans->isNotEmpty())
                    @foreach($plans as $index => $plan)
                        <div class="col-md-6 col-lg-4">
                            <div class="pricing-card-pro {{ $index === 1 ? 'popular' : '' }}">
                                @if($index === 1)
                                    <div class="popular-plan-badge">MOST POPULAR</div>
                                @endif
                                <h4 class="fw-bold mb-1 text-dark">{{ $plan->name }}</h4>
                                <p class="text-muted small">
                                    @if($plan->max_students == -1)
                                        Unlimited Students
                                    @else
                                        Up to {{ $plan->max_students }} students
                                    @endif
                                </p>
                                
                                <div class="pricing-amount-row">
                                    @if($plan->price_monthly == 0)
                                        <span class="plan-price-val" data-monthly="Free" data-yearly="Free">Free</span>
                                        <span class="pricing-duration-text" data-monthly="" data-yearly=""></span>
                                    @else
                                        <span class="plan-price-val" data-monthly="GHS {{ number_format($plan->price_monthly, 0) }}" data-yearly="GHS {{ number_format($plan->price_yearly, 0) }}">GHS {{ number_format($plan->price_monthly, 0) }}</span>
                                        <span class="pricing-duration-text" data-monthly="/term" data-yearly="/year, billed annually">/term</span>
                                    @endif
                                </div>
                                
                                <p class="small text-secondary mb-4">
                                    @if($plan->price_monthly == 0)
                                        Ideal to test all system features with live sample data before subscribing.
                                    @elseif($plan->max_students == -1)
                                        Designed for multi-branch institutions, large senior high schools, or custom servers.
                                    @else
                                        Complete automated SBA reports, mobile money billing, and parent SMS communications.
                                    @endif
                                </p>
                                
                                <ul class="pricing-feature-list">
                                    @if(is_array($plan->features))
                                        @foreach($plan->features as $feat)
                                            @if(!empty(trim($feat)))
                                                <li><i class="bi bi-check-circle-fill"></i><span>{{ ucwords(str_replace('_', ' ', trim($feat))) }}</span></li>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if($plan->sms_credits_monthly > 0)
                                        <li><i class="bi bi-check-circle-fill"></i><span>{{ number_format($plan->sms_credits_monthly) }} monthly parent SMS credits</span></li>
                                    @endif
                                </ul>
                                
                                <a href="{{ route('register') }}" class="btn-brand-{{ $index === 1 ? 'gold' : 'primary' }} w-100 text-decoration-none justify-content-center py-3">
                                    @if($plan->price_monthly == 0)
                                        Register Free Trial
                                    @elseif($plan->max_students == -1)
                                        Contact Enterprise Sales
                                    @else
                                        Get {{ $plan->name }} Plan
                                    @endif
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback: Static system settings pricing cards -->
                    <!-- Starter Plan -->
                    <div class="col-md-6 col-lg-4">
                        <div class="pricing-card-pro">
                            <h4 class="fw-bold mb-1 text-dark">{{ \App\Models\SystemSetting::getVal('welcome_price1_title', 'Starter Trial') }}</h4>
                            <p class="text-muted small">{{ \App\Models\SystemSetting::getVal('welcome_price1_sub', 'Evaluate basic capabilities') }}</p>
                            
                            @php
                                $priceText1 = \App\Models\SystemSetting::getVal('welcome_price1_price', 'GHS 0/14 days');
                                $priceVal1 = $priceText1;
                                $priceUnit1 = '';
                                if (strpos($priceText1, '/') !== false) {
                                    list($priceVal1, $priceUnit1) = explode('/', $priceText1, 2);
                                }
                                $price1Features = explode("\n", trim(\App\Models\SystemSetting::getVal('welcome_price1_features', "Max 50 students\nBasic Student Register\nDaily Attendance logs\nSelf-managed onboarding")));
                            @endphp
                            
                            <div class="pricing-amount-row">
                                <span class="plan-price-val">{{ $priceVal1 }}</span>
                                <span class="pricing-duration-text">@if($priceUnit1)/{{ $priceUnit1 }}@endif</span>
                            </div>
                            
                            <p class="small text-secondary mb-4">{{ \App\Models\SystemSetting::getVal('welcome_price1_desc', 'Great to test the software features with real data before choosing a subscription plan.') }}</p>
                            
                            <ul class="pricing-feature-list">
                                @foreach($price1Features as $feat)
                                    @if(!empty(trim($feat)))
                                        <li><i class="bi bi-check-circle-fill"></i><span>{{ trim($feat) }}</span></li>
                                    @endif
                                @endforeach
                            </ul>
                            
                            <a href="{{ route('register') }}" class="btn-brand-primary w-100 text-decoration-none justify-content-center py-3">
                                Register Free Trial
                            </a>
                        </div>
                    </div>

                    <!-- Standard Package (Most Popular) -->
                    <div class="col-md-6 col-lg-4">
                        <div class="pricing-card-pro popular">
                            <div class="popular-plan-badge">MOST POPULAR</div>
                            <h4 class="fw-bold mb-1 text-dark">{{ \App\Models\SystemSetting::getVal('welcome_price2_title', 'Standard School') }}</h4>
                            <p class="text-muted small">{{ \App\Models\SystemSetting::getVal('welcome_price2_sub', 'For single campus primary/secondary') }}</p>
                            
                            @php
                                $priceText2 = \App\Models\SystemSetting::getVal('welcome_price2_price', 'GHS 450/term');
                                $priceVal2 = $priceText2;
                                $priceUnit2 = '';
                                if (strpos($priceText2, '/') !== false) {
                                    list($priceVal2, $priceUnit2) = explode('/', $priceText2, 2);
                                }
                                $price2Features = explode("\n", trim(\App\Models\SystemSetting::getVal('welcome_price2_features', "Up to 800 students\nSmart Accounting & Bills\nGrading System & Report Cards\nParent & Teacher Portals\nSMS Notifications support")));
                            @endphp
                            
                            <div class="pricing-amount-row">
                                <span class="plan-price-val">{{ $priceVal2 }}</span>
                                <span class="pricing-duration-text">@if($priceUnit2)/{{ $priceUnit2 }}@endif</span>
                            </div>
                            
                            <p class="small text-secondary mb-4">{{ \App\Models\SystemSetting::getVal('welcome_price2_desc', 'Unlock automated grading and billing. Most chosen by growing private and model institutions.') }}</p>
                            
                            <ul class="pricing-feature-list">
                                @foreach($price2Features as $feat)
                                    @if(!empty(trim($feat)))
                                        <li><i class="bi bi-check-circle-fill"></i><span>{{ trim($feat) }}</span></li>
                                    @endif
                                @endforeach
                            </ul>
                            
                            <a href="{{ route('register') }}" class="btn-brand-gold w-100 text-decoration-none justify-content-center py-3">
                                Get Standard Plan
                            </a>
                        </div>
                    </div>

                    <!-- Enterprise Package -->
                    <div class="col-md-6 col-lg-4">
                        <div class="pricing-card-pro">
                            <h4 class="fw-bold mb-1 text-dark">{{ \App\Models\SystemSetting::getVal('welcome_price3_title', 'Institution Enterprise') }}</h4>
                            <p class="text-muted small">{{ \App\Models\SystemSetting::getVal('welcome_price3_sub', 'Custom deployments') }}</p>
                            
                            @php
                                $priceText3 = \App\Models\SystemSetting::getVal('welcome_price3_price', 'Custom/negotiated');
                                $priceVal3 = $priceText3;
                                $priceUnit3 = '';
                                if (strpos($priceText3, '/') !== false) {
                                    list($priceVal3, $priceUnit3) = explode('/', $priceText3, 2);
                                }
                                $price3Features = explode("\n", trim(\App\Models\SystemSetting::getVal('welcome_price3_features', "Unlimited Students\nCustom Branding & Subdomain\nDedicated DB Instance\nPremium 24/7 SLA Support\nAPI Access & Integrations")));
                            @endphp
                            
                            <div class="pricing-amount-row">
                                <span class="plan-price-val">{{ $priceVal3 }}</span>
                                <span class="pricing-duration-text">@if($priceUnit3)/{{ $priceUnit3 }}@endif</span>
                            </div>
                            
                            <p class="small text-secondary mb-4">{{ \App\Models\SystemSetting::getVal('welcome_price3_desc', 'For school groups with multiple branches, heavy resource operations, or dedicated servers.') }}</p>
                            
                            <ul class="pricing-feature-list">
                                @foreach($price3Features as $feat)
                                    @if(!empty(trim($feat)))
                                        <li><i class="bi bi-check-circle-fill"></i><span>{{ trim($feat) }}</span></li>
                                    @endif
                                @endforeach
                            </ul>
                            
                            <a href="{{ route('register') }}" class="btn-brand-primary w-100 text-decoration-none justify-content-center py-3">
                                Contact Sales
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5" id="faq" style="background-color: var(--bg-light-tint); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5 text-center text-lg-start">
                    <span class="section-tagline">Got Questions?</span>
                    <h2 class="section-header-title">Frequently Asked Questions</h2>
                    <p class="text-secondary mb-4">
                        Everything you need to know about setting up and running your institution workspace on the {{ config('app.name', 'EduLink') }} platform.
                    </p>
                    <a href="{{ route('register') }}" class="btn-brand-primary text-decoration-none">
                        <span>Get Started with EduLink</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="col-lg-7">
                    <div class="faq-accordion-group">
                        <div class="faq-card-item">
                            <details>
                                <summary>{{ \App\Models\SystemSetting::getVal('welcome_faq1_q', 'How long does it take to onboard our school?') }}</summary>
                                <div class="faq-answer-content">
                                    {{ \App\Models\SystemSetting::getVal('welcome_faq1_a', 'You can register online instantly! Setup takes less than 10 minutes. Once registered, our setup assistant will guide you through adding classes, academic terms, assigning subjects to teachers, and uploading students.') }}
                                </div>
                            </details>
                        </div>

                        <div class="faq-card-item">
                            <details>
                                <summary>{{ \App\Models\SystemSetting::getVal('welcome_faq2_q', 'Are parent and student portal accounts free?') }}</summary>
                                <div class="faq-answer-content">
                                    {{ \App\Models\SystemSetting::getVal('welcome_faq2_a', 'Yes! Once a school subscribes to our platform, there are no extra charges for parents, students, or teacher accounts. All user portals are included in the flat monthly tenant package.') }}
                                </div>
                            </details>
                        </div>

                        <div class="faq-card-item">
                            <details>
                                <summary>{{ \App\Models\SystemSetting::getVal('welcome_faq3_q', 'What payment methods are integrated for fees?') }}</summary>
                                <div class="faq-answer-content">
                                    {{ \App\Models\SystemSetting::getVal('welcome_faq3_a', config('app.name', 'EduLink') . ' integrates natively with major mobile money providers in Ghana (MTN MoMo, Telecel Cash, AT Money) and credit/debit card processors. Parents can pay bills directly online, updating school accounts in real time.') }}
                                </div>
                            </details>
                        </div>

                        <div class="faq-card-item">
                            <details>
                                <summary>{{ \App\Models\SystemSetting::getVal('welcome_faq4_q', 'Is our institution\'s data safe and secure?') }}</summary>
                                <div class="faq-answer-content">
                                    {{ \App\Models\SystemSetting::getVal('welcome_faq4_a', 'Absolutely. We run on enterprise cloud services, utilizing daily automated database backups, multi-factor authentication (MFA) for user accounts, and end-to-end HTTPS encryption to ensure compliance and data safety.') }}
                                </div>
                            </details>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom Conversion CTA Section -->
    <section class="py-5">
        <div class="container">
            <div class="cta-banner-section text-center text-lg-start">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <h2 class="mb-3">Ready to Elevate Your Institution's Academic Standards?</h2>
                        <p class="text-white-50 fs-6 mb-0" style="max-width: 580px;">
                            Join hundreds of modern basic, junior, and senior high schools running automated report cards, mobile money fees, and connected parent portals.
                        </p>
                    </div>
                    <div class="col-lg-4 text-center text-lg-end">
                        <a href="{{ route('register') }}" class="btn-brand-gold text-decoration-none py-3.5 px-4.5 fs-6">
                            <span>Register Your School Free</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Site Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <a href="{{ url('/') }}" class="d-flex align-items-center mb-3 text-decoration-none">
                        <div class="logo-box me-2.5">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <span class="brand-title text-white">
                            {{ substr(config('app.name', 'EduLink'), 0, 3) }}<span class="gold-text">{{ substr(config('app.name', 'EduLink'), 3) }}</span>
                        </span>
                    </a>
                    <p class="text-secondary small mb-3">
                        {{ \App\Models\SystemSetting::getVal('welcome_footer_desc', 'Providing premium SaaS management systems for modern schools across Ghana and the West African sub-region.') }}
                    </p>
                    <div class="system-status-indicator">
                        <span class="badge-live-pulse" style="width: 7px; height: 7px;"></span>
                        <span>All ERP Infrastructure Systems Operational</span>
                    </div>
                </div>

                <div class="col-6 col-lg-2 offset-lg-1">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#pillars" class="footer-nav-link">Core Pillars</a></li>
                        <li><a href="#tour" class="footer-nav-link">Platform Tour</a></li>
                        <li><a href="#calculator" class="footer-nav-link">ROI Calculator</a></li>
                        <li><a href="#pricing" class="footer-nav-link">Pricing Plans</a></li>
                        <li><a href="#faq" class="footer-nav-link">FAQs</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-2">
                    <h6>Access Portals</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('login') }}" class="footer-nav-link">School Admin Portal</a></li>
                        <li><a href="{{ route('login') }}" class="footer-nav-link">Teacher Gradebook</a></li>
                        <li><a href="{{ route('login') }}" class="footer-nav-link">Parent Mobile Portal</a></li>
                        <li><a href="{{ route('login') }}" class="footer-nav-link">Student Portal</a></li>
                        <li><a href="{{ route('register') }}" class="footer-nav-link">Register Institution</a></li>
                    </ul>
                </div>

                <div class="col-lg-3">
                    <h6>Platform Governance</h6>
                    <ul class="list-unstyled small text-secondary">
                        <li class="mb-2"><i class="bi bi-shield-lock-fill text-warning me-2"></i>MFA Two-Factor Authentication</li>
                        <li class="mb-2"><i class="bi bi-cloud-arrow-up-fill text-warning me-2"></i>Automated Daily Database Backups</li>
                        <li class="mb-2"><i class="bi bi-lock-fill text-warning me-2"></i>256-Bit SSL Encryption</li>
                        <li class="mb-2"><i class="bi bi-envelope-fill text-warning me-2"></i>{{ \App\Models\SystemSetting::getVal('welcome_support_email', 'support@' . strtolower(config('app.name', 'edulink')) . '.gh') }}</li>
                    </ul>
                </div>
            </div>

            <div class="border-top pt-4 text-center text-secondary small" style="border-color: rgba(255, 255, 255, 0.08) !important;">
                &copy; 2026 {{ config('app.name', 'EduLink') }} Ghana ERP. All rights reserved. Built with pride for Ghanaian Educational Excellence.
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Interactive Features & Calculations Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Theme Switcher Controller
            const themeToggleBtns = document.querySelectorAll('.theme-toggle-action');
            
            function updateThemeIcons(theme) {
                const iconClass = theme === 'dark' ? 'bi bi-sun-fill text-warning' : 'bi bi-moon-stars-fill';
                const buttonTitle = theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';
                
                document.querySelectorAll('.theme-icon-el').forEach(icon => {
                    icon.className = iconClass + ' theme-icon-el';
                });
                themeToggleBtns.forEach(btn => {
                    btn.setAttribute('title', buttonTitle);
                });
            }

            // Sync current theme icon state
            const currentActiveTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            updateThemeIcons(currentActiveTheme);

            // Bind click handler on toggle buttons
            themeToggleBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const activeTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                    const newTheme = activeTheme === 'dark' ? 'light' : 'dark';
                    
                    document.documentElement.setAttribute('data-bs-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    updateThemeIcons(newTheme);
                });
            });

            // Interactive Tab Switcher
            const tabButtons = document.querySelectorAll('.showcase-tab-item');
            const displayPanels = document.querySelectorAll('.showcase-content-panel');

            tabButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    
                    tabButtons.forEach(b => b.classList.remove('active'));
                    displayPanels.forEach(p => p.classList.remove('active'));
                    
                    this.classList.add('active');
                    const targetPanel = document.getElementById(targetId);
                    if (targetPanel) {
                        targetPanel.classList.add('active');
                    }
                });
            });

            // Interactive School Efficiency Calculator
            const slider = document.getElementById('studentSlider');
            const studentDisplay = document.getElementById('calc-student-display');
            const hoursSaved = document.getElementById('calc-hours-saved');
            const feeRecovery = document.getElementById('calc-fee-recovery');
            const reportTime = document.getElementById('calc-report-time');

            if (slider) {
                slider.addEventListener('input', function() {
                    const val = parseInt(this.value);
                    studentDisplay.textContent = val.toLocaleString() + ' Students';
                    
                    // Dynamic calculations
                    const hours = Math.round(val * 0.24);
                    hoursSaved.textContent = hours + '+ hrs';
                    
                    const recovery = Math.min(48, Math.round(20 + (val / 100)));
                    feeRecovery.textContent = '+' + recovery + '%';
                    
                    const mins = Math.max(5, Math.round(val * 0.02));
                    reportTime.textContent = mins + ' Mins';
                });
            }

            // Pricing Switch Controller (Termly vs. Annual)
            const billingSwitch = document.getElementById('pricingBillingSwitch');
            const labelTermly = document.getElementById('label-termly');
            const labelAnnual = document.getElementById('label-annual');
            const pricingCards = document.querySelectorAll('.pricing-card-pro');

            const priceStore = [];
            pricingCards.forEach(card => {
                const amountEl = card.querySelector('.plan-price-val');
                const durationEl = card.querySelector('.pricing-duration-text');
                if (amountEl) {
                    priceStore.push({
                        amountEl: amountEl,
                        durationEl: durationEl,
                        origText: amountEl.textContent.trim(),
                        origDuration: durationEl ? durationEl.textContent.trim() : ''
                    });
                }
            });

            if (billingSwitch) {
                billingSwitch.addEventListener('change', function() {
                    const isAnnual = this.checked;
                    
                    if (isAnnual) {
                        labelTermly.classList.remove('active');
                        labelAnnual.classList.add('active');
                    } else {
                        labelTermly.classList.add('active');
                        labelAnnual.classList.remove('active');
                    }

                    priceStore.forEach(item => {
                        if (item.amountEl.hasAttribute('data-monthly')) {
                            if (isAnnual) {
                                item.amountEl.textContent = item.amountEl.getAttribute('data-yearly');
                                if (item.durationEl) {
                                    item.durationEl.textContent = '/year, billed annually';
                                }
                            } else {
                                item.amountEl.textContent = item.amountEl.getAttribute('data-monthly');
                                if (item.durationEl) {
                                    item.durationEl.textContent = '/term';
                                }
                            }
                        } else {
                            const text = item.origText;
                            const numericPart = text.replace(/[^0-9.]/g, '');
                            const nonNumericPart = text.replace(/[0-9.]/g, '').trim();
                            
                            if (numericPart) {
                                const value = parseFloat(numericPart);
                                if (value > 0) {
                                    if (isAnnual) {
                                        const annualDiscounted = Math.round(value * 0.8 * 3);
                                        item.amountEl.textContent = nonNumericPart + ' ' + annualDiscounted.toLocaleString();
                                        if (item.durationEl) {
                                            item.durationEl.textContent = '/year, billed annually';
                                        }
                                    } else {
                                        item.amountEl.textContent = item.origText;
                                        if (item.durationEl) {
                                            item.durationEl.textContent = item.origDuration;
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            }

            // Navbar Scroll Class
            const nav = document.getElementById('mainNavbar');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 30) {
                    nav.classList.add('scrolled');
                } else {
                    nav.classList.remove('scrolled');
                }
            });
        });

        // Helper to smoothly collapse mobile menu on anchor tap
        function closeMobileNav() {
            const navbarCollapse = document.getElementById('navbarMainCollapse');
            if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse) || new bootstrap.Collapse(navbarCollapse);
                bsCollapse.hide();
            }
        }
    </script>

    @php
        $whatsappNumber = \App\Models\SystemSetting::getVal('welcome_whatsapp_number', '');
        $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsappNumber);
        $whatsappMsg = urlencode('Hello EduLink Team! I would like to inquire about registering our school on EduLink Ghana ERP.');
    @endphp

    @if(!empty($cleanWhatsapp))
        <a href="https://wa.me/{{ $cleanWhatsapp }}?text={{ $whatsappMsg }}" class="whatsapp-float-btn" target="_blank" rel="noopener noreferrer" title="Chat with us on WhatsApp">
            <i class="bi bi-whatsapp"></i>
        </a>
    @endif
</body>
</html>
