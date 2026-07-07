<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
    @php
        $sections = view()->getSections();
        $rawTitle = isset($sections['title']) ? trim(strip_tags($sections['title'])) : '';
        $platformBrand = config('app.name', 'EduLink');
        if ($rawTitle) {
            $cleanTitle = trim(preg_replace('/\|\s*(EduLink|EduLink)/i', '', $rawTitle));
            $fullTitle = $cleanTitle . ' | ' . $platformBrand;
        } else {
            $fullTitle = $platformBrand . ' Ghana ERP';
        }
    @endphp
    <title>{{ $fullTitle }}</title>
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Theme Styling -->
    <style>
        :root {
            --primary-color: #003366; /* GES Blue */
            --accent-color: #FFD700; /* Gold */
            --bg-color: #f8fafc;
            --card-bg: rgba(255, 255, 255, 0.85);
            --sidebar-width: 260px;
            --text-main: #334155;
            --text-muted: #64748b;
            --navbar-bg: rgba(255, 255, 255, 0.8);
            --border-color: rgba(0, 0, 0, 0.05);
            --theme-toggle-bg: #f1f5f9;
            --theme-toggle-color: #475569;
            --theme-toggle-bg-hover: #e2e8f0;
            --theme-toggle-color-hover: #0f172a;
            --card-border: rgba(255, 255, 255, 0.4);
        }

        [data-bs-theme="dark"] {
            --primary-color: #58a6ff; /* Lighter/vibrant primary blue in dark mode */
            --bg-color: #0b0f19;
            --card-bg: rgba(20, 26, 42, 0.85);
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --navbar-bg: rgba(20, 26, 42, 0.8);
            --border-color: rgba(255, 255, 255, 0.08);
            --theme-toggle-bg: #1e293b;
            --theme-toggle-color: #94a3b8;
            --theme-toggle-bg-hover: #334155;
            --theme-toggle-color-hover: #f1f5f9;
            --card-border: rgba(255, 255, 255, 0.08);
        }

        /* Override local high-contrast text color definitions in dark mode */
        [data-bs-theme="dark"] .text-dark {
            color: #f1f5f9 !important;
        }
        [data-bs-theme="dark"] .text-secondary {
            color: #cbd5e1 !important;
        }
        [data-bs-theme="dark"] .text-muted {
            color: #94a3b8 !important;
        }
        [data-bs-theme="dark"] .badge.text-secondary {
            background-color: rgba(255, 255, 255, 0.05) !important;
            color: #cbd5e1 !important;
        }

        /* Force glass-cards to use dynamic variable-based backgrounds instead of inline white backgrounds */
        [data-bs-theme="dark"] .glass-card {
            background: var(--card-bg) !important;
            border-color: var(--card-border) !important;
        }

        /* Override specific hardcoded white backgrounds on inputs and form controls in dark mode */
        [data-bs-theme="dark"] input.bg-white,
        [data-bs-theme="dark"] textarea.bg-white,
        [data-bs-theme="dark"] select.bg-white,
        [data-bs-theme="dark"] .form-control.bg-white,
        [data-bs-theme="dark"] .input-group-text.bg-white {
            background-color: rgba(255, 255, 255, 0.05) !important;
            color: #f1f5f9 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        /* Override general list items and slot cards that have bg-white class in dark mode */
        [data-bs-theme="dark"] .list-group.bg-white,
        [data-bs-theme="dark"] .list-group-item.bg-white {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }
        [data-bs-theme="dark"] .slot-card.bg-white {
            background-color: rgba(255, 255, 255, 0.04) !important;
            color: #f1f5f9 !important;
        }

        /* Force bg-light and border elements to adapt dynamically in dark mode */
        [data-bs-theme="dark"] .bg-light {
            background-color: rgba(255, 255, 255, 0.04) !important;
        }
        [data-bs-theme="dark"] .border,
        [data-bs-theme="dark"] .border-top,
        [data-bs-theme="dark"] .border-bottom,
        [data-bs-theme="dark"] .border-start,
        [data-bs-theme="dark"] .border-end {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        .theme-toggle-btn {
            background-color: var(--theme-toggle-bg);
            color: var(--theme-toggle-color);
        }
        .theme-toggle-btn:hover {
            background-color: var(--theme-toggle-bg-hover);
            color: var(--theme-toggle-color-hover);
            transform: scale(1.05);
        }

        /* Platform-wide Typography & Scaling Overrides */
        h1, .h1 { font-size: clamp(1.6rem, 3.5vw, 2.3rem) !important; font-weight: 700; letter-spacing: -0.02em; }
        h2, .h2 { font-size: clamp(1.4rem, 3.0vw, 1.85rem) !important; font-weight: 700; letter-spacing: -0.01em; }
        h3, .h3 { font-size: clamp(1.2rem, 2.5vw, 1.5rem) !important; font-weight: 600; }
        h4, .h4 { font-size: clamp(1.05rem, 2.0vw, 1.25rem) !important; font-weight: 600; }
        h5, .h5 { font-size: clamp(0.95rem, 1.8vw, 1.125rem) !important; font-weight: 600; }
        h6, .h6 { font-size: clamp(0.85rem, 1.5vw, 1.0rem) !important; font-weight: 600; }

        body {
            font-family: 'Outfit', 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Mobile specific typography and layout fixes */
        @media (max-width: 768px) {
            body {
                font-size: 0.95rem;
            }
            .form-label, label {
                font-size: 0.82rem !important;
            }
            .table th, .table td {
                font-size: 0.82rem !important;
                padding: 0.5rem 0.5rem !important;
            }
            .display-4, .display-5 {
                font-size: 1.6rem !important;
                font-weight: 800;
            }
            .glass-card, .card {
                padding: 1rem !important;
                border-radius: 12px !important;
            }
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, #002244 0%, #003366 100%);
            color: #ffffff;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.05);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }

        .sidebar-brand {
            padding: 1.5rem;
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand span {
            color: var(--accent-color);
        }

        .sidebar-menu {
            padding: 1rem 0;
            list-style: none;
            margin-bottom: 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            transition: all 0.2s ease;
            font-weight: 500;
            border-left: 4px solid transparent;
        }

        .sidebar-link:hover, .sidebar-link.active {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.07);
            border-left-color: var(--accent-color);
        }

        .sidebar-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        /* Main Content Styling */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .navbar-top {
            background-color: var(--navbar-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            padding: 0.8rem 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .content-container {
            padding: 2rem;
        }

        /* Glassmorphism Card Utility */
        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(8px);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.04);
        }

        .card-metric {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
        }

        .profile-img {
            width: 38px;
            height: 38px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid var(--accent-color);
        }

        /* Responsive Layout styles */
        @media (max-width: 991.98px) {
            .sidebar {
                left: calc(-1 * var(--sidebar-width));
            }
            
            body.sidebar-show .sidebar {
                left: 0;
            }

            .main-wrapper {
                margin-left: 0 !important;
            }

            .navbar-top {
                padding: 0.8rem 1.25rem;
            }
            
            .content-container {
                padding: 1.25rem;
            }
        }

        /* Sidebar Overlay Styling */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(2px);
            z-index: 998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        body.sidebar-show .sidebar-overlay {
            opacity: 1;
            visibility: visible;
        }

        /* Sidebar Close Button styles */
        #sidebarClose {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.12);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            outline: none;
            box-shadow: none;
        }

        #sidebarClose:hover {
            background-color: rgba(255, 255, 255, 0.24) !important;
            transform: scale(1.08);
        }

        #sidebarClose:active {
            transform: scale(0.92);
        }

        /* Custom Notification bell and dropdown styles */
        .notification-dropdown {
            width: 320px;
            max-height: 420px;
            overflow-y: auto;
            border-radius: 16px;
            padding: 0;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .notification-item {
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            transition: background-color 0.2s ease;
            cursor: pointer;
        }
        .notification-item:hover {
            background-color: #f8fafc;
        }
        .notification-item.unread {
            background-color: rgba(0, 51, 102, 0.02);
            border-left: 3px solid var(--primary-color);
        }
        .notification-item:last-child {
            border-bottom: none;
        }
        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            font-size: 0.65rem;
            padding: 0.25em 0.45em;
            border: 2px solid #ffffff;
        }
        .bell-icon-wrapper {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background-color: var(--theme-toggle-bg);
            color: var(--theme-toggle-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .bell-icon-wrapper:hover {
            background-color: var(--theme-toggle-bg-hover);
            color: var(--theme-toggle-color-hover);
            transform: scale(1.05);
        }
        .dropdown-toggle.no-caret::after {
            display: none !important;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-globe-europe-africa me-2"></i>{{ config('app.name', 'EduLink') }}
            </div>
            <button class="btn btn-link text-white d-lg-none" id="sidebarClose" aria-label="Close Sidebar" style="text-decoration: none;">
                <i class="bi bi-x-lg" style="font-size: 1rem; line-height: 1;"></i>
            </button>
        </div>
        <ul class="sidebar-menu">
            @if(Auth::user()->role && Auth::user()->role->slug === 'super-admin')
                <li>
                    <a href="{{ route('super-admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('super-admin.analytics') }}" class="sidebar-link {{ request()->routeIs('super-admin.analytics*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up-arrow"></i>Revenue & Analytics
                    </a>
                </li>
                <li>
                    <a href="{{ route('super-admin.plans.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.plans*') ? 'active' : '' }}">
                        <i class="bi bi-card-checklist"></i>SaaS Plans
                    </a>
                </li>
                <li>
                    <a href="{{ route('super-admin.sms-credits') }}" class="sidebar-link {{ request()->routeIs('super-admin.sms-credits*') ? 'active' : '' }}">
                        <i class="bi bi-chat-left-dots"></i>SMS Credits
                    </a>
                </li>
                <li>
                    <a href="{{ route('super-admin.access-logs') }}" class="sidebar-link {{ request()->routeIs('super-admin.access-logs*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock"></i>Access Logs
                    </a>
                </li>
                <li>
                    <a href="{{ route('super-admin.email-settings') }}" class="sidebar-link {{ request()->routeIs('super-admin.email-settings*') ? 'active' : '' }}">
                        <i class="bi bi-envelope"></i>Email Gateway & Logs
                    </a>
                </li>
                <li>
                    <a href="{{ route('super-admin.roles.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.roles*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge"></i>Roles & Permissions
                    </a>
                </li>
                <li>
                    <a href="{{ route('super-admin.landing-page.edit') }}" class="sidebar-link {{ request()->routeIs('super-admin.landing-page*') ? 'active' : '' }}">
                        <i class="bi bi-window-sidebar"></i>Landing Page Editor
                    </a>
                </li>
                <li>
                    <a href="{{ route('super-admin.settings') }}" class="sidebar-link {{ request()->routeIs('super-admin.settings*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i>Settings
                    </a>
                </li>
                <li>
                    <a href="{{ route('super-admin.env-settings') }}" class="sidebar-link {{ request()->routeIs('super-admin.env-settings*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-code"></i>Environment Editor (.env)
                    </a>
                </li>
                <li>
                    <a href="{{ route('super-admin.documentation.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.documentation*') ? 'active' : '' }}">
                        <i class="bi bi-book"></i>Knowledge Base Editor
                    </a>
                </li>
                <li>
                    <a href="{{ route('super-admin.help-settings.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.help-settings*') ? 'active' : '' }}">
                        <i class="bi bi-question-circle"></i>Help Center Config
                    </a>
                </li>
            @elseif(Auth::user()->role && Auth::user()->role->slug === 'student')
                <li>
                    <a href="{{ route('school.student-portal.dashboard') }}" class="sidebar-link {{ request()->routeIs('school.student-portal.dashboard*') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.student-portal.id-card') }}" class="sidebar-link {{ request()->routeIs('school.student-portal.id-card*') ? 'active' : '' }}">
                        <i class="bi bi-card-image"></i>Digital ID Card
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.student-portal.timetable') }}" class="sidebar-link {{ request()->routeIs('school.student-portal.timetable*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event"></i>Class Timetable
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.student-portal.assignments.index') }}" class="sidebar-link {{ request()->routeIs('school.student-portal.assignments*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i>Assignments Desk
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.student-portal.results.index') }}" class="sidebar-link {{ request()->routeIs('school.student-portal.results*') ? 'active' : '' }}">
                        <i class="bi bi-award"></i>Term Results
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.lms.courses.index') }}" class="sidebar-link {{ request()->routeIs('school.lms*') ? 'active' : '' }}">
                        <i class="bi bi-laptop"></i>LMS Catalog
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.student-portal.transport') }}" class="sidebar-link {{ request()->routeIs('school.student-portal.transport*') ? 'active' : '' }}">
                        <i class="bi bi-bus-front"></i>Bus Schedules
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.help-center.index') }}" class="sidebar-link {{ request()->routeIs('school.help-center*') ? 'active' : '' }}">
                        <i class="bi bi-question-circle"></i>Help & Guides
                    </a>
                </li>
            @elseif(Auth::user()->role && Auth::user()->role->slug === 'parent')
                <li>
                    <a href="{{ route('school.parent-portal.dashboard') }}" class="sidebar-link {{ request()->routeIs('school.parent-portal.dashboard*') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.parent-portal.attendance') }}" class="sidebar-link {{ request()->routeIs('school.parent-portal.attendance*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i>Child Attendance
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.parent-portal.fees') }}" class="sidebar-link {{ request()->routeIs('school.parent-portal.fees*') ? 'active' : '' }}">
                        <i class="bi bi-credit-card-2-front"></i>Billing Statements
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.parent-portal.reports') }}" class="sidebar-link {{ request()->routeIs('school.parent-portal.reports*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i>Report Cards
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.parent-portal.messages') }}" class="sidebar-link {{ request()->routeIs('school.parent-portal.messages*') ? 'active' : '' }}">
                        <i class="bi bi-chat-dots"></i>School Notices
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.parent-portal.transport') }}" class="sidebar-link {{ request()->routeIs('school.parent-portal.transport*') ? 'active' : '' }}">
                        <i class="bi bi-bus-front"></i>Bus Schedules
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.help-center.index') }}" class="sidebar-link {{ request()->routeIs('school.help-center*') ? 'active' : '' }}">
                        <i class="bi bi-question-circle"></i>Help & Guides
                    </a>
                </li>
            @else
                <!-- Staff Menu -->
                <li>
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>Dashboard
                    </a>
                </li>
                @if(Auth::user()->hasPermission('manage-campuses'))
                <li>
                    <a href="{{ route('school.campuses') }}" class="sidebar-link {{ request()->routeIs('school.campuses*') ? 'active' : '' }}">
                        <i class="bi bi-building"></i>Campuses
                    </a>
                </li>
                @endif

                @if(Auth::user()->hasPermission('manage-staff'))
                <li>
                    <a href="{{ route('school.staff') }}" class="sidebar-link {{ request()->routeIs('school.staff*') || request()->routeIs('school.staff-hr*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>Staff Accounts
                    </a>
                </li>
                @endif

                @if(Auth::user()->hasPermission('manage-academics'))
                <li>
                    <a href="{{ route('school.academics') }}" class="sidebar-link {{ request()->routeIs('school.academics*') ? 'active' : '' }}">
                        <i class="bi bi-calendar3"></i>Academics Setup
                    </a>
                </li>
                @endif

                @if(Auth::user()->hasPermission('manage-academics') || Auth::user()->hasPermission('enter-scores'))
                <li>
                    <a href="{{ route('school.timetable') }}" class="sidebar-link {{ request()->routeIs('school.timetable*') ? 'active' : '' }}">
                        <i class="bi bi-calendar2-week"></i>Timetable
                    </a>
                </li>
                @endif

                @if(Auth::user()->hasPermission('manage-academics'))
                <li>
                    <a href="{{ route('school.subjects') }}" class="sidebar-link {{ request()->routeIs('school.subjects*') ? 'active' : '' }}">
                        <i class="bi bi-book"></i>Subjects
                    </a>
                </li>
                @endif

                @if(Auth::user()->hasPermission('manage-enrollments'))
                <li>
                    <a href="{{ route('school.students') }}" class="sidebar-link {{ request()->routeIs('school.students') ? 'active' : '' }}">
                        <i class="bi bi-mortarboard"></i>Student Registry
                    </a>
                </li>
                @endif

                @if(Auth::user()->hasPermission('manage-enrollments') || Auth::user()->hasPermission('enter-scores'))
                <li>
                    <a href="{{ route('school.attendance') }}" class="sidebar-link {{ request()->routeIs('school.attendance*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i>Student Attendance
                    </a>
                </li>
                @endif

                @if(Auth::user()->hasPermission('configure-scoring'))
                <li>
                    <a href="{{ route('school.scoring-configs.index') }}" class="sidebar-link {{ request()->routeIs('school.scoring-configs*') ? 'active' : '' }}">
                        <i class="bi bi-sliders"></i>SBA Setup
                    </a>
                </li>
                @endif

                @if(Auth::user()->hasPermission('enter-scores'))
                <li>
                    <a href="{{ route('school.scores.enter') }}" class="sidebar-link {{ request()->routeIs('school.scores.enter*') ? 'active' : '' }}">
                        <i class="bi bi-table"></i>Enter Scores
                    </a>
                </li>
                @endif

                @if(Auth::user()->hasPermission('manage-enrollments') || (Auth::user()->role && Auth::user()->role->slug === 'class-teacher'))
                <li>
                    <a href="{{ route('school.students.promotion') }}" class="sidebar-link {{ request()->routeIs('school.students.promotion*') ? 'active' : '' }}">
                        <i class="bi bi-arrow-up-circle"></i>Student Promotions
                    </a>
                </li>
                @if(Auth::user()->hasPermission('manage-settings'))
                <li>
                    <a href="{{ route('school.settings.promotions.index') }}" class="sidebar-link {{ request()->routeIs('school.settings.promotions*') ? 'active' : '' }}">
                        <i class="bi bi-gear-fill"></i>Promotion Rules
                    </a>
                </li>
                @endif
                @endif

                @if(Auth::user()->hasPermission('enter-scores') || Auth::user()->hasPermission('verify-scores') || Auth::user()->hasPermission('approve-scores') || Auth::user()->hasPermission('publish-reports'))
                <li>
                    <a href="{{ route('school.reports.index') }}" class="sidebar-link {{ request()->routeIs('school.reports.index*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i>Report Hub
                    </a>
                </li>
                @endif

                @if(Auth::user()->hasPermission('manage-enrollments'))
                <li>
                    <a href="{{ route('school.admissions.index') }}" class="sidebar-link {{ request()->routeIs('school.admissions*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-person"></i>Admissions CRM
                    </a>
                </li>
                @endif

                <!-- LMS Link for staff -->
                <li>
                    <a href="{{ route('school.lms.courses.index') }}" class="sidebar-link {{ request()->routeIs('school.lms*') ? 'active' : '' }}">
                        <i class="bi bi-laptop"></i>LMS Catalog
                    </a>
                </li>
                
                <!-- Finance Module Section -->
                @if(Auth::user()->hasPermission('manage-fees') || Auth::user()->hasPermission('collect-payments') || Auth::user()->hasPermission('view-accounts'))
                <li class="sidebar-section-header text-uppercase text-white-50 px-4 pt-3 pb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Finance & Accounting</li>
                
                @if(Auth::user()->hasPermission('manage-fees'))
                <li>
                    <a href="{{ route('school.finance.fee-structures.index') }}" class="sidebar-link {{ request()->routeIs('school.finance.fee-structures*') ? 'active' : '' }}">
                        <i class="bi bi-sliders2"></i>Fee Items Setup
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.finance.invoices.index') }}" class="sidebar-link {{ request()->routeIs('school.finance.invoices*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i>Invoices & Billing
                    </a>
                </li>
                @endif

                @if(Auth::user()->hasPermission('collect-payments'))
                <li>
                    <a href="{{ route('school.finance.payments.index') }}" class="sidebar-link {{ request()->routeIs('school.finance.payments*') ? 'active' : '' }}">
                        <i class="bi bi-credit-card"></i>Payments Log
                    </a>
                </li>
                @endif

                @if(Auth::user()->hasPermission('view-accounts'))
                <li>
                    <a href="{{ route('school.finance.accounts.index') }}" class="sidebar-link {{ request()->routeIs('school.finance.accounts*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3"></i>Chart of Accounts
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.finance.journals.index') }}" class="sidebar-link {{ request()->routeIs('school.finance.journals*') ? 'active' : '' }}">
                        <i class="bi bi-journals"></i>General Ledger
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.finance.reports.index') }}" class="sidebar-link {{ request()->routeIs('school.finance.reports*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart"></i>Financial Reports
                    </a>
                </li>
                @endif
                @endif

                <!-- School Operations Section -->
                @if(Auth::user()->hasPermission('manage-settings') || Auth::user()->hasPermission('manage-academics'))
                <li class="sidebar-section-header text-uppercase text-white-50 px-4 pt-3 pb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">School Operations</li>
                <li>
                    <a href="{{ route('school.operations.dashboard') }}" class="sidebar-link {{ request()->routeIs('school.operations.dashboard*') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill"></i>Operations Center
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.operations.library.index') }}" class="sidebar-link {{ request()->routeIs('school.operations.library*') ? 'active' : '' }}">
                        <i class="bi bi-book-half"></i>Library
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.operations.inventory.index') }}" class="sidebar-link {{ request()->routeIs('school.operations.inventory*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i>Inventory
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.operations.hostel.index') }}" class="sidebar-link {{ request()->routeIs('school.operations.hostel*') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i>Hostels
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.operations.transport.index') }}" class="sidebar-link {{ request()->routeIs('school.operations.transport*') ? 'active' : '' }}">
                        <i class="bi bi-bus-front"></i>Transport
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.operations.hr.index') }}" class="sidebar-link {{ request()->routeIs('school.operations.hr*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge"></i>HR & Leave
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.operations.health-discipline.index') }}" class="sidebar-link {{ request()->routeIs('school.operations.health-discipline*') ? 'active' : '' }}">
                        <i class="bi bi-heart-pulse"></i>Health & Discipline
                    </a>
                </li>
                @endif

                <!-- Communication Section -->
                @if(Auth::user()->hasPermission('manage-settings') || Auth::user()->hasPermission('enter-scores'))
                <li class="sidebar-section-header text-uppercase text-white-50 px-4 pt-3 pb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Communication</li>
                <li>
                    <a href="{{ route('school.communication.index') }}" class="sidebar-link {{ request()->routeIs('school.communication.index*') ? 'active' : '' }}">
                        <i class="bi bi-broadcast"></i>Notice Blasts & SMS
                    </a>
                </li>
                @endif

                <!-- Website Builder Section -->
                @if(Auth::user()->hasPermission('manage-website'))
                <li class="sidebar-section-header text-uppercase text-white-50 px-4 pt-3 pb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">School Website</li>
                <li>
                    <a href="{{ route('school.website.pages.index') }}" class="sidebar-link {{ request()->routeIs('school.website.pages*') || request()->routeIs('school.website.builder*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-code"></i>Manage Pages
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.website.navigation.index') }}" class="sidebar-link {{ request()->routeIs('school.website.navigation*') ? 'active' : '' }}">
                        <i class="bi bi-list-nested"></i>Menu Builder
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.website.settings.index') }}" class="sidebar-link {{ request()->routeIs('school.website.settings*') ? 'active' : '' }}">
                        <i class="bi bi-palette"></i>Branding & Themes
                    </a>
                </li>
                @endif

                @if(Auth::user()->hasPermission('manage-settings'))
                <li>
                    <a href="{{ route('school.settings') }}" class="sidebar-link {{ request()->routeIs('school.settings*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i>Settings
                    </a>
                </li>
                @if(Auth::user()->school && Auth::user()->school->isFeatureEnabled('api_access', false))
                <li>
                    <a href="{{ route('school.api-keys.index') }}" class="sidebar-link {{ request()->routeIs('school.api-keys.index*') ? 'active' : '' }}">
                        <i class="bi bi-key-fill"></i>API Credentials
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('school.billing.index') }}" class="sidebar-link {{ request()->routeIs('school.billing.index*') ? 'active' : '' }}">
                        <i class="bi bi-credit-card-2-back-fill"></i>Billing & Plan
                    </a>
                </li>
                @endif
                
                @if(Auth::user()->role && Auth::user()->role->slug === 'super-admin')
                <li class="sidebar-section-header text-uppercase text-white-50 px-4 pt-3 pb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Systems & Docs</li>
                <li>
                    <a href="{{ route('school.docs.deployment') }}" class="sidebar-link {{ request()->routeIs('school.docs.deployment*') ? 'active' : '' }}">
                        <i class="bi bi-server"></i>Deployment Guide
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.docs.testing') }}" class="sidebar-link {{ request()->routeIs('school.docs.testing*') ? 'active' : '' }}">
                        <i class="bi bi-shield-check"></i>Test & Security Hub
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.docs.security') }}" class="sidebar-link {{ request()->routeIs('school.docs.security*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock"></i>Security Audit Center
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.docs.help') }}" class="sidebar-link {{ request()->routeIs('school.docs.help*') ? 'active' : '' }}">
                        <i class="bi bi-question-circle"></i>Help & Reference Hub
                    </a>
                </li>
                @elseif(Auth::user()->hasPermission('manage-settings'))
                <li class="sidebar-section-header text-uppercase text-white-50 px-4 pt-3 pb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Reference Hub</li>
                <li>
                    <a href="{{ route('school.docs.help') }}" class="sidebar-link {{ request()->routeIs('school.docs.help*') ? 'active' : '' }}">
                        <i class="bi bi-question-circle"></i>Help & Reference Hub
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('school.help-center.index') }}" class="sidebar-link {{ request()->routeIs('school.help-center*') ? 'active' : '' }}">
                        <i class="bi bi-question-circle"></i>Help Guides (Dynamic)
                    </a>
                </li>
            @endif
        </ul>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <nav class="navbar navbar-top">
            <div class="container-fluid p-0 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <!-- Sidebar Toggle Button (Mobile/Tablet only) -->
                    <button class="btn btn-link text-dark me-3 d-lg-none p-0 d-inline-flex align-items-center justify-content-center" id="sidebarToggle" aria-label="Toggle Sidebar" style="height: 38px; width: 38px; text-decoration: none;">
                        <i class="bi bi-list fs-3" style="line-height: 1;"></i>
                    </button>
                    <!-- Full Title (Desktop & Tablet) -->
                    <span class="navbar-brand font-weight-bold d-none d-sm-inline-block" style="font-weight: 700; font-size: 1.15rem; color: var(--primary-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 320px; line-height: 1.2;">
                        @yield('header_title', Auth::user()->role && Auth::user()->role->slug === 'super-admin' ? 'Super Admin Control Panel' : 'School Administration Portal')
                    </span>
                    <!-- Short App Name (Mobile) -->
                    <span class="navbar-brand font-weight-bold d-inline-block d-sm-none" style="font-weight: 700; font-size: 1.15rem; color: var(--primary-color); line-height: 1.2;">
                        {{ config('app.name', 'EduLink') }}
                    </span>
                </div>
                <div class="d-flex align-items-center ms-auto">
                    <!-- Light/Dark Theme Toggle -->
                    <button class="btn btn-link text-decoration-none me-3 theme-toggle-btn p-0 d-inline-flex align-items-center justify-content-center" id="themeToggleBtn" aria-label="Toggle Theme" style="height: 40px; width: 40px; border-radius: 10px; transition: all 0.2s;">
                        <i class="bi bi-sun fs-5" id="themeToggleIcon"></i>
                    </button>

                    <!-- Notification Bell Dropdown -->
                    <div class="dropdown me-3">
                        <a href="#" class="bell-icon-wrapper text-decoration-none dropdown-toggle no-caret" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell fs-5"></i>
                            @if(isset($unreadCount) && $unreadCount > 0)
                                <span class="position-absolute badge rounded-circle bg-danger notification-badge">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg notification-dropdown mt-2 border-0" aria-labelledby="notificationDropdown">
                            <li class="p-3 border-bottom d-flex align-items-center justify-content-between bg-light rounded-top-4">
                                <span class="fw-bold text-dark" style="font-size: 0.9rem;"><i class="bi bi-bell-fill me-1 text-primary"></i> Notifications</span>
                                @if(isset($unreadCount) && $unreadCount > 0)
                                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-link p-0 text-decoration-none small text-primary fw-semibold" style="font-size: 0.75rem;">
                                            Mark all read
                                        </button>
                                    </form>
                                @endif
                            </li>
                            <div class="notification-list-container" style="max-height: 320px; overflow-y: auto;">
                                @if(isset($notifications) && !$notifications->isEmpty())
                                    @foreach($notifications as $notif)
                                        <li class="p-3 notification-item {{ !$notif->is_read ? 'unread' : '' }}" onclick="markAsRead(this, '{{ $notif->id }}')">
                                            <div class="d-flex align-items-start gap-2">
                                                <div class="bg-primary-subtle text-primary rounded-circle p-1.5 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                                    <i class="bi bi-envelope"></i>
                                                </div>
                                                <div class="flex-grow-1 min-width-0">
                                                    <div class="d-flex align-items-center justify-content-between mb-0.5">
                                                        <span class="fw-bold text-dark text-truncate d-inline-block small" style="max-width: 160px;">{{ $notif->title }}</span>
                                                        <span class="text-muted" style="font-size: 0.7rem;">{{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}</span>
                                                    </div>
                                                    <p class="text-muted mb-0 text-truncate small" style="font-size: 0.8rem; max-width: 220px;" title="{{ $notif->body }}">
                                                        {{ $notif->body }}
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                @else
                                    <div class="text-center py-5 text-muted">
                                        <i class="bi bi-bell-slash fs-3 d-block mb-2 text-secondary opacity-50"></i>
                                        <span class="small d-block fw-semibold">No new notifications.</span>
                                    </div>
                                @endif
                            </div>
                        </ul>
                    </div>

                    <span class="me-3 d-none d-md-inline-block" style="font-weight: 500; font-size: 0.9rem;">
                        Hello, <strong>{{ Auth::user()->name }}</strong>
                    </span>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle no-caret" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ Auth::user()->profile_photo && (str_starts_with(Auth::user()->profile_photo, 'http') || \Illuminate\Support\Facades\Storage::disk('public')->exists(Auth::user()->profile_photo)) ? (str_starts_with(Auth::user()->profile_photo, 'http') ? Auth::user()->profile_photo : asset('storage/' . Auth::user()->profile_photo)) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=003366&color=fff' }}" alt="profile" class="profile-img">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item py-2" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('profile') }}#security"><i class="bi bi-shield-check me-2"></i>Security</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="content-container">
            @if(session()->has('impersonator_id'))
                <div class="alert alert-warning border-warning border-opacity-25 d-flex align-items-center justify-content-between p-3 mb-4 rounded-3 shadow-xs" style="background: rgba(255, 193, 7, 0.08); backdrop-filter: blur(10px);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                        <div>
                            <strong class="text-dark small d-block">Support Impersonation Mode Active</strong>
                            <span class="text-secondary small">You are logged in as <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->role ? Auth::user()->role->name : 'Staff' }}). All actions are audit logged.</span>
                        </div>
                    </div>
                    <form action="{{ route('super-admin.schools.impersonate.stop') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-xs fw-semibold px-3 py-1.5 rounded-2 d-flex align-items-center gap-1 shadow-xs border-0">
                            <i class="bi bi-box-arrow-left"></i>
                            Return to Super Admin
                        </button>
                    </form>
                </div>
            @endif
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registered successfully.', reg.scope))
                    .catch(err => console.error('Service Worker registration failed.', err));
            });
        }
    </script>

    <!-- Responsive Sidebar JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const body = document.body;

            function toggleSidebar() {
                body.classList.toggle('sidebar-show');
            }

            function closeSidebar() {
                body.classList.remove('sidebar-show');
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }

            if (sidebarClose) {
                sidebarClose.addEventListener('click', closeSidebar);
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeSidebar);
            }

            // Close sidebar when clicking on a sidebar link on mobile
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        closeSidebar();
                    }
                });
            });
        });
    </script>

    <!-- AJAX Notification Mark as Read Handler -->
    <script>
        function markAsRead(element, notifId) {
            // Check if notification is already read
            if (!element.classList.contains('unread')) {
                return;
            }

            // CSRF Token
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch(`/notifications/${notifId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    element.classList.remove('unread');
                    
                    // Decrease unread count badge
                    const badge = document.querySelector('.notification-badge');
                    if (badge) {
                        let currentCount = parseInt(badge.textContent.trim());
                        if (currentCount > 1) {
                            badge.textContent = currentCount - 1;
                        } else {
                            badge.remove();
                        }
                    }
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }
    </script>

    <!-- Theme Toggler JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const themeToggleIcon = document.getElementById('themeToggleIcon');

            function updateToggleIcon(theme) {
                if (theme === 'dark') {
                    themeToggleIcon.classList.remove('bi-sun');
                    themeToggleIcon.classList.add('bi-moon-stars');
                } else {
                    themeToggleIcon.classList.remove('bi-moon-stars');
                    themeToggleIcon.classList.add('bi-sun');
                }
            }

            // Set initial toggle icon state based on active theme
            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            updateToggleIcon(currentTheme);

            themeToggleBtn.addEventListener('click', function() {
                const activeTheme = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-bs-theme', activeTheme);
                localStorage.setItem('theme', activeTheme);
                updateToggleIcon(activeTheme);
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
