<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ config('app.name', 'EduLink') }} Ghana ERP is a next-generation school management system empowering administrators, teachers, parents, and students with smart automation.">
    <title>{{ config('app.name', 'EduLink') }} | Next-Gen School Management ERP</title>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --bg-dark: #090d16;
            --bg-dark-card: rgba(30, 41, 59, 0.45);
            --border-color: rgba(255, 255, 255, 0.08);
            --gold-primary: #FFD700;
            --gold-secondary: #e6c200;
            --gold-hover: #e6c200;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --font-family: 'Outfit', sans-serif;
            --font-body: 'Inter', sans-serif;
        }

        html, body {
            overflow-x: hidden;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-body);
            background: linear-gradient(135deg, var(--bg-dark) 0%, #0f172a 100%);
            color: var(--text-primary);
            min-height: 100vh;
            position: relative;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-family);
        }

        /* Decorative Grid Overlay */
        .grid-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.015) 1px, transparent 1px);
            background-size: 40px 40px;
            background-position: center top;
            z-index: 1;
            pointer-events: none;
        }

        /* Blur Glows */
        .glow-blur-1 {
            position: absolute;
            top: -150px;
            right: 5%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.06) 0%, transparent 70%);
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }

        .glow-blur-2 {
            position: absolute;
            top: 40%;
            left: -150px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.05) 0%, transparent 70%);
            filter: blur(100px);
            pointer-events: none;
            z-index: 0;
        }

        .glow-blur-3 {
            position: absolute;
            bottom: 10%;
            right: -150px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.04) 0%, transparent 70%);
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
        }

        /* Sticky Glass Navbar */
        .navbar {
            background: rgba(9, 13, 22, 0.7);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 0;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 1000;
        }

        .navbar.scrolled {
            padding: 0.85rem 0;
            background: rgba(9, 13, 22, 0.85);
            border-bottom-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.4);
        }

        .logo-icon-wrapper {
            width: 38px;
            height: 38px;
            background: rgba(255, 215, 0, 0.08);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.08);
        }

        .brand-text {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text-primary);
        }

        .brand-text span.text-gold {
            background: linear-gradient(135deg, var(--gold-primary) 0%, var(--gold-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 500;
            font-size: 0.95rem;
            position: relative;
            padding: 0.5rem 0.75rem;
            transition: color 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--text-primary) !important;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--gold-primary), var(--gold-secondary));
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 80%;
        }

        /* Buttons */
        .btn-nav-outline {
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.02);
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 10px;
            padding: 0.55rem 1.15rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-nav-outline:hover {
            border-color: rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-nav-primary {
            background: linear-gradient(135deg, var(--gold-primary) 0%, var(--gold-secondary) 100%);
            border: none;
            color: #04060c !important;
            font-weight: 700;
            font-size: 0.9rem;
            border-radius: 10px;
            padding: 0.55rem 1.25rem;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.2);
            transition: all 0.3s ease;
        }

        .btn-nav-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.35);
        }

        /* Hero styling */
        .hero-section {
            padding: 9.5rem 0 5rem 0;
            position: relative;
            z-index: 10;
        }

        .hero-badge-container {
            display: inline-block;
            padding: 1px;
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.3) 0%, rgba(255, 255, 255, 0.05) 50%, rgba(255, 215, 0, 0.05) 100%);
            border-radius: 50px;
            margin-bottom: 1.5rem;
        }

        .hero-badge {
            background: #080a14;
            padding: 0.4rem 1.15rem;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            background-color: var(--gold-primary);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--gold-primary);
            animation: badge-pulse 2s infinite;
        }

        @keyframes badge-pulse {
            0% { transform: scale(0.9); opacity: 0.6; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.6; }
        }

        .badge-text {
            color: var(--text-primary);
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .hero-title {
            font-size: clamp(2.4rem, 4.5vw, 3.8rem);
            font-weight: 900;
            line-height: 1.15;
            letter-spacing: -1.5px;
            margin-bottom: 1.5rem;
            background: linear-gradient(to bottom right, #ffffff 40%, #a1a1aa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-title span.text-gold-gradient {
            background: linear-gradient(135deg, var(--gold-primary) 0%, var(--gold-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-sub {
            color: var(--text-secondary);
            font-size: 1.15rem;
            line-height: 1.65;
            margin-bottom: 2.5rem;
        }

        .btn-hero-primary {
            background: linear-gradient(135deg, var(--gold-primary) 0%, var(--gold-secondary) 100%);
            border: none;
            color: #04060c !important;
            font-weight: 700;
            font-size: 1.05rem;
            border-radius: 12px;
            padding: 0.85rem 1.85rem;
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.2);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
        }

        .btn-hero-secondary {
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.02);
            font-weight: 600;
            font-size: 1.05rem;
            border-radius: 12px;
            padding: 0.85rem 1.85rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-hero-secondary:hover {
            border-color: rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.05);
            transform: translateY(-3px);
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.015);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.15rem 1.25rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .stat-value {
            font-size: 1.85rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, var(--gold-primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Mockup */
        .mockup-wrapper {
            perspective: 1000px;
        }

        .mockup-window {
            background: #080a14;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 20px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5), 0 0 40px rgba(255, 215, 0, 0.03);
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .mockup-window:hover {
            transform: translateY(-4px) rotateX(1deg) rotateY(-1deg);
            border-color: rgba(255, 215, 0, 0.15);
            box-shadow: 0 35px 70px rgba(0, 0, 0, 0.6), 0 0 50px rgba(255, 215, 0, 0.06);
        }

        .mockup-window-header {
            background: #0d1020;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 0.65rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .window-dots {
            display: flex;
            gap: 6px;
        }

        .window-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
        }

        .window-dot.red { background: #ef4444; }
        .window-dot.yellow { background: #f59e0b; }
        .window-dot.green { background: #10b981; }

        .window-address {
            background: #080a14;
            border: 1px solid rgba(255, 255, 255, 0.04);
            border-radius: 8px;
            padding: 0.2rem 1.75rem;
            font-size: 0.7rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .mock-status-pill {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.2rem 0.6rem;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .status-pulse-green {
            width: 5px;
            height: 5px;
            background-color: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 6px #10b981;
            animation: badge-pulse 1.5s infinite;
        }

        .mockup-window-body {
            display: flex;
            height: 360px;
        }

        .mock-sidebar {
            width: 125px;
            background: #0c101f;
            border-right: 1px solid rgba(255, 255, 255, 0.03);
            padding: 1rem 0.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            flex-shrink: 0;
        }

        @media (max-width: 575.98px) {
            .mock-sidebar {
                display: none;
            }
            .mockup-window-header {
                padding: 0.5rem 0.75rem;
            }
            .window-address {
                max-width: 130px;
                padding: 0.2rem 0.5rem;
            }
            .mock-status-pill {
                font-size: 0.55rem;
                padding: 0.15rem 0.4rem;
            }
        }

        .mock-sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0 0.5rem;
            font-size: 0.8rem;
            font-weight: 800;
            color: #ffffff;
        }

        .mock-sidebar-logo span span {
            color: var(--gold-primary);
        }

        .mock-sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .mock-menu-item {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.35rem 0.5rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 500;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .mock-menu-item:hover, .mock-menu-item.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.04);
        }

        .mock-menu-item.active {
            border-left: 2px solid var(--gold-primary);
            padding-left: calc(0.5rem - 2px);
            background: rgba(255, 215, 0, 0.04);
            color: var(--gold-primary);
        }

        .mock-main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background: #080a14;
            overflow-y: auto;
        }

        .mock-topbar {
            height: 44px;
            background: #0c101f;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            padding: 0 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .mock-search {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--text-secondary);
            font-size: 0.7rem;
            background: #080a14;
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 6px;
            padding: 0.2rem 0.65rem;
            width: 130px;
        }

        .mock-user {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .mock-noti {
            color: var(--text-secondary);
            font-size: 0.8rem;
            position: relative;
            cursor: pointer;
        }

        .noti-badge {
            position: absolute;
            top: 0;
            right: 0;
            width: 5px;
            height: 5px;
            background: #ef4444;
            border-radius: 50%;
        }

        .mock-avatar {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--gold-primary);
            color: #04060c;
            font-size: 0.65rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mock-content {
            padding: 0.85rem;
            overflow-y: auto;
        }

        .mock-section-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: #ffffff;
        }

        .mock-metric-card {
            background: #0c101f;
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            padding: 0.55rem 0.65rem;
        }

        .metric-title {
            display: block;
            font-size: 0.6rem;
            color: var(--text-secondary);
            margin-bottom: 0.15rem;
        }

        .metric-val {
            font-size: 0.95rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.1rem;
        }

        .metric-change {
            font-size: 0.55rem;
            font-weight: 600;
        }

        .metric-change.positive { color: #10b981; }
        .metric-change.neutral { color: var(--text-secondary); }

        .mock-chart-container {
            background: #0c101f;
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            padding: 0.65rem;
        }

        .mock-chart-title {
            font-size: 0.65rem;
            font-weight: 600;
            color: #ffffff;
        }

        .mock-chart-legend {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.55rem;
            color: var(--text-secondary);
        }

        .legend-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
        }

        .legend-dot.invoiced { background: var(--gold-primary); }
        .legend-dot.collected { background: #3b82f6; }

        .mock-chart {
            height: 60px;
            position: relative;
        }

        .mock-table-card {
            background: #0c101f;
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            padding: 0.65rem;
        }

        .table-header-title {
            font-size: 0.65rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 0.4rem;
        }

        .mock-table-wrapper {
            overflow-x: auto;
        }

        .mock-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.6rem;
            text-align: left;
        }

        .mock-table th {
            color: var(--text-secondary);
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 0.3rem 0.4rem;
        }

        .mock-table td {
            color: #ffffff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            padding: 0.35rem 0.4rem;
        }

        .status-pill {
            padding: 0.05rem 0.35rem;
            border-radius: 4px;
            font-size: 0.55rem;
            font-weight: 600;
        }

        .status-pill.completed {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        /* Trust section */
        .trust-section {
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            background: rgba(255, 255, 255, 0.015);
            position: relative;
            z-index: 10;
        }

        .trust-logo {
            color: var(--text-secondary);
            font-size: 0.95rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: color 0.3s ease;
        }

        .trust-logo:hover {
            color: var(--gold-primary);
        }

        /* Spacing utility override */
        

        /* Pillars & Features */
        .section-title {
            font-size: 2.3rem;
            font-weight: 800;
            letter-spacing: -0.75px;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #ffffff 40%, #a1a1aa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-sub {
            color: var(--text-secondary);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 3.5rem auto;
        }

        .feature-card {
            background: var(--bg-dark-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 2.25rem 2rem;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top left, rgba(255, 215, 0, 0.06) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            border-color: rgba(255, 215, 0, 0.25);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 25px rgba(255, 215, 0, 0.03);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon-wrapper {
            width: 56px;
            height: 56px;
            background: rgba(255, 215, 0, 0.05);
            border: 1px solid rgba(255, 215, 0, 0.15);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--gold-primary);
            margin-bottom: 1.5rem;
            transition: all 0.4s ease;
        }

        .feature-card:hover .feature-icon-wrapper {
            background: rgba(255, 215, 0, 0.1);
            border-color: rgba(255, 215, 0, 0.3);
            color: #ffffff;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.15);
            transform: scale(1.05);
        }

        /* Pricing Billing Toggle */
        .pricing-toggle-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .toggle-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-secondary);
            transition: color 0.3s ease;
        }

        .toggle-label.active {
            color: #ffffff;
        }

        .theme-switch {
            display: inline-block;
            height: 28px;
            position: relative;
            width: 50px;
        }

        .theme-switch input {
            display: none;
        }

        .slider {
            background-color: rgba(255, 255, 255, 0.1);
            bottom: 0;
            cursor: pointer;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            transition: .4s;
            border: 1px solid var(--border-color);
        }

        .slider:before {
            background-color: var(--gold-primary);
            bottom: 3px;
            content: "";
            height: 20px;
            left: 3px;
            position: absolute;
            transition: .4s;
            width: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        input:checked + .slider {
            background-color: rgba(255, 215, 0, 0.1);
            border-color: rgba(255, 215, 0, 0.3);
        }

        input:checked + .slider:before {
            transform: translateX(22px);
            background-color: var(--gold-primary);
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        /* Pricing Card */
        .pricing-card {
            background: var(--bg-dark-card);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 3rem 2rem;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .pricing-card.popular {
            border-color: var(--gold-primary);
            box-shadow: 0 15px 35px rgba(255, 215, 0, 0.05), 0 0 30px rgba(255, 215, 0, 0.05);
            background: linear-gradient(180deg, #0d1020 0%, #06080e 100%);
        }

        .pricing-card:hover {
            transform: translateY(-8px);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .pricing-card.popular:hover {
            border-color: var(--gold-hover);
            box-shadow: 0 20px 45px rgba(255, 215, 0, 0.08), 0 0 40px rgba(255, 215, 0, 0.08);
        }

        .popular-badge {
            position: absolute;
            top: 25px;
            right: -35px;
            background: linear-gradient(135deg, var(--gold-primary) 0%, var(--gold-secondary) 100%);
            color: #04060c;
            padding: 0.25rem 3rem;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            transform: rotate(45deg);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .pricing-price {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 1.5rem 0;
            color: #ffffff;
            display: flex;
            align-items: baseline;
        }

        .pricing-price span.price-duration {
            font-size: 0.95rem;
            color: var(--text-secondary);
            font-weight: 400;
            margin-left: 0.25rem;
        }

        .pricing-list {
            list-style: none;
            padding: 0;
            margin: 2rem 0;
            flex-grow: 1;
        }

        .pricing-list li {
            margin-bottom: 0.85rem;
            color: #cbd5e1;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .pricing-list li i {
            color: var(--gold-primary);
            font-size: 1rem;
            flex-shrink: 0;
        }

        .btn-price-outline {
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.02);
            font-weight: 600;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            display: block;
        }

        .btn-price-outline:hover {
            border-color: rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.05);
        }

        .btn-price-primary {
            background: linear-gradient(135deg, var(--gold-primary) 0%, var(--gold-secondary) 100%);
            border: none;
            color: #04060c !important;
            font-weight: 700;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.15);
            transition: all 0.3s ease;
            display: block;
        }

        .btn-price-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.3);
        }

        /* FAQ */
        .faq-accordion details {
            background: var(--bg-dark-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            margin-bottom: 1rem;
            padding: 1.25rem 1.5rem;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .faq-accordion details[open] {
            border-color: rgba(255, 215, 0, 0.25);
            background: rgba(13, 16, 32, 0.55);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .faq-accordion summary {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            cursor: pointer;
            list-style: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            outline: none;
        }

        .faq-accordion summary::-webkit-details-marker {
            display: none;
        }

        .faq-accordion summary::after {
            content: "\F282";
            font-family: "bootstrap-icons";
            font-size: 0.95rem;
            color: var(--text-secondary);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .faq-accordion details[open] summary::after {
            transform: rotate(180deg);
            color: var(--gold-primary);
        }

        .faq-content {
            margin-top: 1rem;
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.6;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 1rem;
        }

        /* Footer */
        footer {
            border-top: 1px solid var(--border-color);
            background: #020306;
            padding: 5rem 0 3rem 0;
            position: relative;
            z-index: 10;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 10px #10b981;
            animation: badge-pulse 2s infinite;
        }

        .footer-link {
            color: var(--text-secondary);
            transition: color 0.2s ease;
        }

        .footer-link:hover {
            color: var(--gold-primary);
        }

        @media (max-width: 991.98px) {
            .hero-title {
                font-size: 2.5rem;
                text-align: center;
            }
            .hero-section {
                padding: clamp(7.5rem, 12vh, 10.5rem) 0 clamp(4rem, 8vh, 6.5rem) 0;
            }
            .navbar-collapse {
                background: rgba(9, 13, 22, 0.95);
                border-radius: 12px;
                padding: 1.5rem;
                margin-top: 1rem;
                border: 1px solid var(--border-color);
            }
            .navbar-collapse .d-flex {
                flex-direction: column;
                width: 100%;
                gap: 0.75rem !important;
                align-items: stretch !important;
            }
            .navbar-collapse .d-flex .btn-nav-outline,
            .navbar-collapse .d-flex .btn-nav-primary {
                text-align: center;
                justify-content: center;
                width: 100%;
            }
        }

        @media (max-width: 767.98px) {
            .showcase-display-card {
                padding: 1.5rem 1rem;
                min-height: auto;
            }
        }


        /* Interactive Showcase Styles */
        .showcase-tab-btn {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            border-radius: 12px;
            padding: 0.85rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            cursor: pointer;
            width: 100%;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .showcase-tab-btn:hover {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.15);
            color: var(--text-primary);
        }

        .showcase-tab-btn.active {
            background: rgba(255, 215, 0, 0.06);
            border-color: var(--gold-primary);
            color: var(--gold-primary);
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.05);
        }

        .showcase-display-card {
            background: var(--bg-dark-card);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4), 0 0 40px rgba(255, 215, 0, 0.02);
            min-height: 400px;
            display: none;
            transition: all 0.4s ease;
        }

        .showcase-display-card.active {
            display: block;
            animation: fadeInShowcase 0.5s ease-in-out forwards;
        }

        @keyframes fadeInShowcase {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .showcase-illustration-window {
            background: #080a14;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 1.25rem;
            overflow: hidden;
            box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.6);
        }

        /* Spacing improvements */
        .py-6 {
            padding-top: clamp(4rem, 8vw, 7.5rem);
            padding-bottom: clamp(4rem, 8vw, 7.5rem);
        }

        .hero-section {
            padding: clamp(7.5rem, 12vh, 10.5rem) 0 clamp(4rem, 8vh, 6.5rem) 0;
            position: relative;
            z-index: 10;
        }

        /* FAQ details card adjustments to fix empty spaces */
        .faq-accordion details {
            margin-bottom: 0.75rem;
            padding: 1rem 1.25rem;
        }

        /* Trust Logos responsiveness */
        .trust-logos-container {
            opacity: 0.65;
            transition: all 0.3s ease;
        }
        .trust-logos-container:hover {
            opacity: 0.95;
            transform: scale(1.01);
        }

        /* Override Bootstrap's dark text utilities to make them light & readable on our dark background */
        .text-secondary,
        .text-muted {
            color: #94a3b8 !important;
        }

        .showcase-tab-btn.active .text-muted {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        /* Floating WhatsApp Button */
        .whatsapp-float {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background-color: #25d366;
            color: #fff !important;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @media (max-width: 767.98px) {
            .whatsapp-float {
                bottom: 20px;
                right: 20px;
                width: 48px;
                height: 48px;
                font-size: 24px;
            }
        }

        .whatsapp-float:hover {
            transform: scale(1.1) rotate(5deg);
            background-color: #20ba5a;
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.45);
        }

        .whatsapp-float i {
            display: inline-block;
            line-height: 0;
        }

        /* Pulse wave animation */
        .whatsapp-float::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: inherit;
            border-radius: inherit;
            z-index: -1;
            opacity: 0.4;
            animation: whatsappPulse 2s infinite;
        }

        @keyframes whatsappPulse {
            0% {
                transform: scale(1);
                opacity: 0.4;
            }
            100% {
                transform: scale(1.6);
                opacity: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Glow Backdrops -->
    <div class="glow-blur-1"></div>
    <div class="glow-blur-2"></div>
    <div class="glow-blur-3"></div>
    <div class="grid-overlay"></div>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <div class="logo-icon-wrapper me-2">
                    <i class="bi bi-globe-europe-africa text-warning"></i>
                </div>
                <span class="brand-text">{!! str_starts_with(strtolower(config('app.name', 'EduLink')), 'edu') && strlen(config('app.name', 'EduLink')) > 3 ? substr(config('app.name', 'EduLink'), 0, 3) . '<span class="text-gold">' . e(substr(config('app.name', 'EduLink'), 3)) . '</span>' : e(config('app.name', 'EduLink')) !!}</span>
            </a>
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-white"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Pricing Plans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQs</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('login') }}" class="btn-nav-outline text-decoration-none">
                        <i class="bi bi-person me-1.5"></i>Client Sign In
                    </a>
                    <a href="{{ route('register') }}" class="btn-nav-primary text-decoration-none">
                        Register School
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 text-center text-lg-start">
                    <div class="hero-badge-container d-flex justify-content-center justify-content-lg-start">
                        <div class="hero-badge">
                            <span class="badge-dot"></span>
                            <span class="badge-text"><i class="bi bi-stars text-warning me-1"></i> {{ \App\Models\SystemSetting::getVal('welcome_hero_badge', 'ERP Automation Solution') }}</span>
                        </div>
                    </div>
                    <h1 class="hero-title">
                        {!! str_ireplace('ERP', '<span class="text-gold-gradient">ERP</span>', e(\App\Models\SystemSetting::getVal('welcome_hero_title', 'The Intelligent Cloud ERP for Modern Institutions'))) !!}
                    </h1>
                    <p class="hero-sub">
                        {{ \App\Models\SystemSetting::getVal('welcome_hero_sub', 'Empower your school with a unified platform for academics, real-time fee tracking, automated terminal report cards, and seamless multi-portal communication. Built for institutions striving for excellence.') }}
                    </p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3">
                        <a href="{{ route('register') }}" class="btn-hero-primary text-decoration-none text-center d-inline-flex justify-content-center align-items-center">
                            Start 14-Day Free Trial
                        </a>
                        <a href="{{ route('login') }}" class="btn-hero-secondary text-decoration-none text-center d-inline-flex justify-content-center align-items-center">
                            Explore Client Login <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    
                    <div class="row mt-5 g-3 g-sm-4 justify-content-center">
                        <div class="col-sm-4 col-12">
                            <div class="stat-card text-center text-sm-start">
                                <div class="stat-value">{{ \App\Models\SystemSetting::getVal('welcome_stat1_value', '10k+') }}</div>
                                <div class="stat-label">{{ \App\Models\SystemSetting::getVal('welcome_stat1_label', 'Students Enrolled') }}</div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-12">
                            <div class="stat-card text-center text-sm-start">
                                <div class="stat-value">{{ \App\Models\SystemSetting::getVal('welcome_stat2_value', '99.9%') }}</div>
                                <div class="stat-label">{{ \App\Models\SystemSetting::getVal('welcome_stat2_label', 'Uptime SLA') }}</div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-12">
                            <div class="stat-card text-center text-sm-start">
                                <div class="stat-value">{{ \App\Models\SystemSetting::getVal('welcome_stat3_value', '15+') }}</div>
                                <div class="stat-label">{{ \App\Models\SystemSetting::getVal('welcome_stat3_label', 'Smart Modules') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mockup Graph -->
                <div class="col-lg-6">
                    <div class="mockup-wrapper">
                        <div class="mockup-window">
                            <!-- Mockup Window Header / Window controls -->
                            <div class="mockup-window-header">
                                <div class="window-dots">
                                    <span class="window-dot red"></span>
                                    <span class="window-dot yellow"></span>
                                    <span class="window-dot green"></span>
                                </div>
                                <div class="window-address">
                                    <i class="bi bi-shield-lock-fill text-success"></i>
                                    <span>edulink-erp.gh/admin/dashboard</span>
                                </div>
                                <div class="window-actions">
                                    <span class="mock-status-pill"><span class="status-pulse-green"></span> Live Analytics</span>
                                </div>
                            </div>
                            
                            <!-- Mockup Window Body (The App Layout) -->
                            <div class="mockup-window-body">
                                <!-- Simulated Sidebar Navigation -->
                                <div class="mock-sidebar">
                                    <div class="mock-sidebar-logo">
                                        <i class="bi bi-globe-europe-africa text-warning"></i>
                                        <span>Edu<span>Link</span></span>
                                    </div>
                                    <div class="mock-sidebar-menu">
                                        <div class="mock-menu-item active">
                                            <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
                                        </div>
                                        <div class="mock-menu-item">
                                            <i class="bi bi-people-fill"></i><span>Students</span>
                                        </div>
                                        <div class="mock-menu-item">
                                            <i class="bi bi-journal-text"></i><span>Academics</span>
                                        </div>
                                        <div class="mock-menu-item">
                                            <i class="bi bi-wallet2"></i><span>Finance</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Simulated Main App Panel -->
                                <div class="mock-main">
                                    <!-- Mock Topbar -->
                                    <div class="mock-topbar">
                                        <div class="mock-search">
                                            <i class="bi bi-search"></i>
                                            <span>Search...</span>
                                        </div>
                                        <div class="mock-user">
                                            <div class="mock-noti"><i class="bi bi-bell-fill"></i><span class="noti-badge"></span></div>
                                            <div class="mock-avatar">AD</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Mock Content Area -->
                                    <div class="mock-content">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h5 class="mock-section-title mb-0" style="font-size: 0.8rem;">Ghana International Academy</h5>
                                                <span class="mock-subtitle text-muted" style="font-size: 0.65rem;">Term 1 Summary</span>
                                            </div>
                                            <span class="badge bg-warning text-dark px-2 py-0.5 fw-semibold" style="font-size: 0.6rem; border-radius: 4px;">2026/27 Active</span>
                                        </div>
                                        
                                        <!-- Metrics Cards Grid -->
                                        <div class="row g-2 mb-3">
                                            <div class="col-4">
                                                <div class="mock-metric-card">
                                                    <span class="metric-title">Enrolled</span>
                                                    <div class="metric-val">1,248</div>
                                                    <span class="metric-change positive"><i class="bi bi-arrow-up-right"></i> +4.2%</span>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="mock-metric-card">
                                                    <span class="metric-title">Collected</span>
                                                    <div class="metric-val">89.2%</div>
                                                    <span class="metric-change positive"><i class="bi bi-shield-check"></i> Secure</span>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="mock-metric-card">
                                                    <span class="metric-title">Attendance</span>
                                                    <div class="metric-val">96.4%</div>
                                                    <span class="metric-change neutral">Target 95%</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Chart Section -->
                                        <div class="mock-chart-container mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="mock-chart-title">Monthly Collection Trend</span>
                                                <div class="mock-chart-legend">
                                                    <span class="legend-dot invoiced"></span><span>Invoiced</span>
                                                    <span class="legend-dot collected"></span><span>Paid</span>
                                                </div>
                                            </div>
                                            <div class="mock-chart">
                                                <svg width="100%" height="60" viewBox="0 0 400 60" preserveAspectRatio="none">
                                                    <defs>
                                                        <linearGradient id="chartGlowInvoiced" x1="0" y1="0" x2="0" y2="1">
                                                            <stop offset="0%" stop-color="#FFD700" stop-opacity="0.2"/>
                                                            <stop offset="100%" stop-color="#FFD700" stop-opacity="0"/>
                                                        </linearGradient>
                                                        <linearGradient id="chartGlowCollected" x1="0" y1="0" x2="0" y2="1">
                                                            <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.15"/>
                                                            <stop offset="100%" stop-color="#3b82f6" stop-opacity="0"/>
                                                        </linearGradient>
                                                    </defs>
                                                    <path d="M 0 60 Q 50 15 100 50 T 200 20 T 300 40 T 400 10 L 400 60 Z" fill="url(#chartGlowInvoiced)"></path>
                                                    <path d="M 0 60 Q 50 15 100 50 T 200 20 T 300 40 T 400 10" fill="none" stroke="#FFD700" stroke-width="2"></path>
                                                    <path d="M 0 60 Q 50 35 100 55 T 200 30 T 300 50 T 400 20 L 400 60 Z" fill="url(#chartGlowCollected)"></path>
                                                    <path d="M 0 60 Q 50 35 100 55 T 200 30 T 300 50 T 400 20" fill="none" stroke="#3b82f6" stroke-width="1.2" stroke-dasharray="2 2"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        
                                        <!-- Table Section -->
                                        <div class="mock-table-card">
                                            <div class="table-header-title">Recent MoMo Payments</div>
                                            <div class="mock-table-wrapper">
                                                <table class="mock-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Student</th>
                                                            <th>Class</th>
                                                            <th>Amount</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Kofi Mensah</td>
                                                            <td>Grade 6A</td>
                                                            <td>GHS 1,200</td>
                                                            <td><span class="status-pill completed">Paid</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Ama Serwaa</td>
                                                            <td>JHS 1</td>
                                                            <td>GHS 850</td>
                                                            <td><span class="status-pill completed">Paid</span></td>
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
            </div>
        </div>
    </header>

    <!-- Partner Schools Trust Section -->
    <section class="trust-section py-4">
        <div class="container text-center">
            <span class="text-secondary small text-uppercase tracking-wider mb-3 d-block" style="font-size: 0.75rem; letter-spacing: 1.5px; font-weight: 600;">Trusted by Excellent Educational Institutions</span>
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-md-5 gap-3 trust-logos-container">
                <span class="trust-logo"><i class="bi bi-mortarboard me-2"></i>Achimota School</span>
                <span class="trust-logo"><i class="bi bi-shield-check me-2"></i>Adisadel College</span>
                <span class="trust-logo"><i class="bi bi-book me-2"></i>Presbyterian Sec.</span>
                <span class="trust-logo"><i class="bi bi-award me-2"></i>Wesley Girls High</span>
            </div>
        </div>
    </section>

    <!-- Product Features Section -->
    <section class="py-6" id="features">
        <div class="container text-center">
            <h2 class="section-title">Core Management Pillars</h2>
            <p class="section-sub">
                Designed to cover every aspect of your educational ecosystem, bringing operations onto a unified platform.
            </p>
            
            <div class="row g-4 text-start">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi {{ \App\Models\SystemSetting::getVal('welcome_pillar1_icon', 'bi-wallet2') }}"></i>
                        </div>
                        <h4 class="fw-bold mb-3">{{ \App\Models\SystemSetting::getVal('welcome_pillar1_title', 'Fee & Billing Hub') }}</h4>
                        <p class="text-secondary small mb-0">
                            {{ \App\Models\SystemSetting::getVal('welcome_pillar1_desc', 'Automate student invoices, record payments dynamically via mobile money, track partial payment history, and generate digital financial reports.') }}
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi {{ \App\Models\SystemSetting::getVal('welcome_pillar2_icon', 'bi-journal-check') }}"></i>
                        </div>
                        <h4 class="fw-bold mb-3">{{ \App\Models\SystemSetting::getVal('welcome_pillar2_title', 'Academic Reports') }}</h4>
                        <p class="text-secondary small mb-0">
                            {{ \App\Models\SystemSetting::getVal('welcome_pillar2_desc', 'Compile terminal grades, calculate GPA averages automatically, customize teacher remarks, and generate beautiful, print-ready student report cards.') }}
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi {{ \App\Models\SystemSetting::getVal('welcome_pillar3_icon', 'bi-people-fill') }}"></i>
                        </div>
                        <h4 class="fw-bold mb-3">{{ \App\Models\SystemSetting::getVal('welcome_pillar3_title', 'Multi-Role Portals') }}</h4>
                        <p class="text-secondary small mb-0">
                            {{ \App\Models\SystemSetting::getVal('welcome_pillar3_desc', 'Dedicated dashboards tailored for administrators, teachers, parents, and students. Improve engagement with real-time access to assignments and performance.') }}
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi {{ \App\Models\SystemSetting::getVal('welcome_pillar4_icon', 'bi-calendar-event') }}"></i>
                        </div>
                        <h4 class="fw-bold mb-3">{{ \App\Models\SystemSetting::getVal('welcome_pillar4_title', 'Timetable Planner') }}</h4>
                        <p class="text-secondary small mb-0">
                            {{ \App\Models\SystemSetting::getVal('welcome_pillar4_desc', 'Generate clash-free timetables for classes, schedule subject allocations, assign teacher rooms, and organize academic calendars with ease.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Product Showcase Section -->
    <section class="py-6" style="background: rgba(255, 255, 255, 0.005); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Deep Dive Platform Experience</h2>
                <p class="section-sub">
                    Explore the robust capabilities of our intelligent system designed to streamline your administration's workflow.
                </p>
            </div>

            <div class="row g-4 align-items-center">
                <!-- Tabs Navigation -->
                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-3">
                        <button class="showcase-tab-btn active" data-target="showcase-billing">
                            <i class="bi bi-receipt-cutoff fs-5"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-white">Billing & Cashflow</h6>
                                <span class="small text-muted" style="font-size: 0.75rem;">Real-time automated school invoicing</span>
                            </div>
                        </button>

                        <button class="showcase-tab-btn" data-target="showcase-reports">
                            <i class="bi bi-file-bar-graph fs-5"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-white">Report Cards & SBA</h6>
                                <span class="small text-muted" style="font-size: 0.75rem;">GPA auto-scaling & remarks builder</span>
                            </div>
                        </button>

                        <button class="showcase-tab-btn" data-target="showcase-timetables">
                            <i class="bi bi-calendar3-range fs-5"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-white">Clash-free Timetables</h6>
                                <span class="small text-muted" style="font-size: 0.75rem;">Dynamic course planner & teachers load</span>
                            </div>
                        </button>

                        <button class="showcase-tab-btn" data-target="showcase-portals">
                            <i class="bi bi-phone-vibrate fs-5"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-white">Client Portals & ID Cards</h6>
                                <span class="small text-muted" style="font-size: 0.75rem;">Mobile login logs & digital ID badges</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Display Screen Mockups -->
                <div class="col-lg-8">
                    <!-- Tab 1: Billing -->
                    <div class="showcase-display-card active" id="showcase-billing">
                        <div class="row align-items-center g-4">
                            <div class="col-md-6 text-start">
                                <h4 class="fw-bold text-white mb-3">Finance & Fee Ledger</h4>
                                <p class="text-secondary small mb-4">
                                    Create flexible fees items, automatically bulk-generate terminal invoices for selected streams, and verify mobile money payments dynamically.
                                </p>
                                <ul class="list-unstyled small text-light mb-0 text-start" style="padding-left: 0;">
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Automated invoice compilation</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>MoMo (MTN/Telecel) payment api callbacks</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>General ledger reconciliation audits</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <div class="showcase-illustration-window">
                                    <div class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-2 mb-3" style="border-color: rgba(255,255,255,0.05) !important;">
                                        <span class="text-white small fw-bold"><i class="bi bi-cash-coin text-success me-1"></i> Fee Summary</span>
                                        <span class="badge bg-success text-white" style="font-size: 0.6rem;">Term 1</span>
                                    </div>
                                    <div class="p-2 bg-dark rounded border mb-2 text-center" style="border-color: rgba(255,255,255,0.05) !important; background-color: #0b0f19 !important;">
                                        <span class="text-secondary small d-block" style="font-size: 0.65rem;">Total Outstanding</span>
                                        <h4 class="fw-bold text-white mb-0">GHS 84,200</h4>
                                    </div>
                                    <div class="p-2 bg-dark rounded border text-center" style="border-color: rgba(255,255,255,0.05) !important; background-color: #0b0f19 !important;">
                                        <span class="text-secondary small d-block" style="font-size: 0.65rem;">Total Collection (Momo)</span>
                                        <h4 class="fw-bold text-success mb-0">GHS 126,500</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Reports -->
                    <div class="showcase-display-card" id="showcase-reports">
                        <div class="row align-items-center g-4">
                            <div class="col-md-6 text-start">
                                <h4 class="fw-bold text-white mb-3">Grading System & Report Hub</h4>
                                <p class="text-secondary small mb-4">
                                    Calculate student GPA scores based on raw class SBA tests (50%) and terminal exam totals (50%). Automatically populate class standings.
                                </p>
                                <ul class="list-unstyled small text-light mb-0 text-start" style="padding-left: 0;">
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Scalable SBA grading rules manager</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Bulk print-ready PDF reports compilation</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Dynamic teacher remarks autocomplete assistant</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <div class="showcase-illustration-window">
                                    <div class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-2 mb-3" style="border-color: rgba(255,255,255,0.05) !important;">
                                        <span class="text-white small fw-bold"><i class="bi bi-file-earmark-check-fill text-warning me-1"></i> SBA Broadsheet</span>
                                        <span class="badge bg-warning text-dark" style="font-size: 0.6rem;">JHS 3</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-sm table-borderless mb-0 text-white" style="font-size: 0.65rem; background: transparent !important;">
                                            <thead>
                                                <tr class="border-bottom border-secondary" style="border-color: rgba(255,255,255,0.05) !important;">
                                                    <th style="background: transparent !important;">Pupil</th>
                                                    <th style="background: transparent !important;">SBA(50)</th>
                                                    <th style="background: transparent !important;">Exam(50)</th>
                                                    <th style="background: transparent !important;">Grade</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="background: transparent !important;">Ama Osei</td>
                                                    <td style="background: transparent !important;">42.5</td>
                                                    <td style="background: transparent !important;">45.0</td>
                                                    <td style="background: transparent !important;"><span class="text-success">A1</span> (87.5%)</td>
                                                </tr>
                                                <tr>
                                                    <td style="background: transparent !important;">Kojo Larbi</td>
                                                    <td style="background: transparent !important;">35.0</td>
                                                    <td style="background: transparent !important;">38.5</td>
                                                    <td style="background: transparent !important;"><span class="text-warning">B2</span> (73.5%)</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Timetables -->
                    <div class="showcase-display-card" id="showcase-timetables">
                        <div class="row align-items-center g-4">
                            <div class="col-md-6 text-start">
                                <h4 class="fw-bold text-white mb-3">Clash-free Academic Planner</h4>
                                <p class="text-secondary small mb-4">
                                    Allocate subject course periods to class streams, assign classrooms, and organize weekly teacher allocations with an intelligent conflict warning detector.
                                </p>
                                <ul class="list-unstyled small text-light mb-0 text-start" style="padding-left: 0;">
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Drag-and-drop course assignments</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Room capacity limits inspector</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Teacher-wise schedules exporter</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <div class="showcase-illustration-window">
                                    <div class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-2 mb-3" style="border-color: rgba(255,255,255,0.05) !important;">
                                        <span class="text-white small fw-bold"><i class="bi bi-calendar-week text-primary me-1"></i> Course Grid</span>
                                        <span class="badge bg-primary text-white" style="font-size: 0.6rem;">Mon - Fri</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-dark table-sm text-center mb-0 text-white" style="font-size: 0.6rem; border-color: rgba(255,255,255,0.05) !important; background: transparent !important;">
                                            <thead>
                                                <tr>
                                                    <th style="background: transparent !important;">Period</th>
                                                    <th style="background: transparent !important;">JHS 1</th>
                                                    <th style="background: transparent !important;">JHS 2</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="background: transparent !important;">08:30</td>
                                                    <td style="background: rgba(13,110,253,0.15) !important; color: #58a6ff !important;" class="fw-bold">Maths</td>
                                                    <td style="background: rgba(25,135,84,0.15) !important; color: #10b981 !important;" class="fw-bold">Science</td>
                                                </tr>
                                                <tr>
                                                    <td style="background: transparent !important;">09:30</td>
                                                    <td style="background: rgba(13,202,240,0.15) !important; color: #0dcaf0 !important;" class="fw-bold">English</td>
                                                    <td style="background: rgba(13,110,253,0.15) !important; color: #58a6ff !important;" class="fw-bold">Maths</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Portals -->
                    <div class="showcase-display-card" id="showcase-portals">
                        <div class="row align-items-center g-4">
                            <div class="col-md-6 text-start">
                                <h4 class="fw-bold text-white mb-3">Digital ID Badges & Portals</h4>
                                <p class="text-secondary small mb-4">
                                    Dedicated parent login profiles to review fee status, student check-in alerts, assignment tasks, and automated QR-code reader checks.
                                </p>
                                <ul class="list-unstyled small text-light mb-0 text-start" style="padding-left: 0;">
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Live bus trackers & attendance notifications</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>QR code badge scanning for student verification</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Multi-role credentials dashboard</li>
                                </ul>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="showcase-illustration-window d-inline-block p-4" style="max-width: 240px; border-color: rgba(255, 215, 0, 0.15) !important; background-color: #0b0f19 !important;">
                                    <div class="text-center mb-3">
                                        <i class="bi bi-globe-europe-africa text-warning fs-3 mb-1 d-block"></i>
                                        <span class="fw-bold text-white small" style="font-size: 0.75rem;">EDULINK STUDENT ID</span>
                                    </div>
                                    <div class="bg-warning rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; color: #020617; font-weight: 800; font-size: 1.25rem;">
                                        KO
                                    </div>
                                    <h6 class="text-white mb-1 fw-bold" style="font-size: 0.8rem;">Kofi Osei</h6>
                                    <span class="text-secondary d-block mb-3" style="font-size: 0.65rem;">ID: EL-2026-9043</span>
                                    <div class="p-2 bg-white rounded d-inline-block">
                                        <i class="bi bi-qr-code text-dark" style="font-size: 2.2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>\n\n    <!-- Pricing Section -->
    <section class="py-6" id="pricing">
        <div class="container text-center">
            <h2 class="section-title">Transparent School Plans</h2>
            <p class="section-sub">
                No hidden setup fees. Choose the right size option for your school. Every package starts with our 14-day full free trial.
            </p>
            
            <!-- Pricing Billing Toggle -->
            <div class="pricing-toggle-container mb-5">
                <span class="toggle-label active" id="toggle-termly">Termly Billing</span>
                <label class="theme-switch" for="pricing-switch">
                    <input type="checkbox" id="pricing-switch" />
                    <span class="slider round"></span>
                </label>
                <span class="toggle-label" id="toggle-annual">
                    Annual Billing 
                    <span class="badge bg-success-subtle text-success ms-1" style="font-size: 0.75rem; border-radius: 4px;">Save 20%</span>
                </span>
            </div>
            
            <div class="row g-4 text-start justify-content-center">
                <!-- Basic Trial -->
                <div class="col-md-6 col-lg-4">
                    <div class="pricing-card">
                        <h4 class="fw-bold mb-1 text-white">{{ \App\Models\SystemSetting::getVal('welcome_price1_title', 'Starter Trial') }}</h4>
                        <p class="text-secondary small">{{ \App\Models\SystemSetting::getVal('welcome_price1_sub', 'Evaluate basic capabilities') }}</p>
                        @php
                            $priceText1 = \App\Models\SystemSetting::getVal('welcome_price1_price', 'GHS 0/14 days');
                            $priceVal1 = $priceText1;
                            $priceUnit1 = '';
                            if (strpos($priceText1, '/') !== false) {
                                list($priceVal1, $priceUnit1) = explode('/', $priceText1, 2);
                            }
                            $price1Features = explode("\n", trim(\App\Models\SystemSetting::getVal('welcome_price1_features', "Max 50 students\nBasic Student Register\nDaily Attendance logs\nSelf-managed onboarding")));
                        @endphp
                        <div class="pricing-price">
                            <span class="price-amount">{{ $priceVal1 }}</span><span class="price-duration">@if($priceUnit1)/{{ $priceUnit1 }}@endif</span>
                        </div>
                        <p class="small text-secondary mb-4">{{ \App\Models\SystemSetting::getVal('welcome_price1_desc', 'Great to test the software features with real data before choosing a subscription plan.') }}</p>
                        <ul class="pricing-list">
                            @foreach($price1Features as $feat)
                                @if(!empty(trim($feat)))
                                    <li><i class="bi bi-check-circle-fill text-warning"></i><span>{{ trim($feat) }}</span></li>
                                @endif
                            @endforeach
                        </ul>
                        <a href="{{ route('register') }}" class="btn-price-outline w-100 text-decoration-none text-center">
                            Start Free Trial
                        </a>
                    </div>
                </div>

                <!-- Standard Package (Popular) -->
                <div class="col-md-6 col-lg-4">
                    <div class="pricing-card popular">
                        <div class="popular-badge">MOST POPULAR</div>
                        <h4 class="fw-bold mb-1 text-white">{{ \App\Models\SystemSetting::getVal('welcome_price2_title', 'Standard School') }}</h4>
                        <p class="text-secondary small">{{ \App\Models\SystemSetting::getVal('welcome_price2_sub', 'For single campus primary/secondary') }}</p>
                        @php
                            $priceText2 = \App\Models\SystemSetting::getVal('welcome_price2_price', 'GHS 450/month');
                            $priceVal2 = $priceText2;
                            $priceUnit2 = '';
                            if (strpos($priceText2, '/') !== false) {
                                list($priceVal2, $priceUnit2) = explode('/', $priceText2, 2);
                            }
                            $price2Features = explode("\n", trim(\App\Models\SystemSetting::getVal('welcome_price2_features', "Up to 800 students\nSmart Accounting & Bills\nGrading System & Report Cards\nParent & Teacher Portals\nSMS Notifications support")));
                        @endphp
                        <div class="pricing-price">
                            <span class="price-amount">{{ $priceVal2 }}</span><span class="price-duration">@if($priceUnit2)/{{ $priceUnit2 }}@endif</span>
                        </div>
                        <p class="small text-secondary mb-4">{{ \App\Models\SystemSetting::getVal('welcome_price2_desc', 'Unlock automated grading and billing. Most chosen by growing private and model institutions.') }}</p>
                        <ul class="pricing-list">
                            @foreach($price2Features as $feat)
                                @if(!empty(trim($feat)))
                                    <li><i class="bi bi-check-circle-fill text-warning"></i><span>{{ trim($feat) }}</span></li>
                                @endif
                            @endforeach
                        </ul>
                        <a href="{{ route('register') }}" class="btn-price-primary w-100 text-decoration-none text-center">
                            Get Standard Plan
                        </a>
                    </div>
                </div>

                <!-- Enterprise Plan -->
                <div class="col-md-6 col-lg-4">
                    <div class="pricing-card">
                        <h4 class="fw-bold mb-1 text-white">{{ \App\Models\SystemSetting::getVal('welcome_price3_title', 'Institution Enterprise') }}</h4>
                        <p class="text-secondary small">{{ \App\Models\SystemSetting::getVal('welcome_price3_sub', 'Custom deployments') }}</p>
                        @php
                            $priceText3 = \App\Models\SystemSetting::getVal('welcome_price3_price', 'Custom/negotiated');
                            $priceVal3 = $priceText3;
                            $priceUnit3 = '';
                            if (strpos($priceText3, '/') !== false) {
                                list($priceVal3, $priceUnit3) = explode('/', $priceText3, 2);
                            }
                            $price3Features = explode("\n", trim(\App\Models\SystemSetting::getVal('welcome_price3_features', "Unlimited Students\nCustom Branding & Subdomain\nDedicated DB Instance\nPremium 24/7 SLA Support\nAPI Access & Integrations")));
                        @endphp
                        <div class="pricing-price">
                            <span class="price-amount">{{ $priceVal3 }}</span><span class="price-duration">@if($priceUnit3)/{{ $priceUnit3 }}@endif</span>
                        </div>
                        <p class="small text-secondary mb-4">{{ \App\Models\SystemSetting::getVal('welcome_price3_desc', 'For school groups with multiple branches, heavy resource operations, or dedicated servers.') }}</p>
                        <ul class="pricing-list">
                            @foreach($price3Features as $feat)
                                @if(!empty(trim($feat)))
                                    <li><i class="bi bi-check-circle-fill text-warning"></i><span>{{ trim($feat) }}</span></li>
                                @endif
                            @endforeach
                        </ul>
                        <a href="{{ route('register') }}" class="btn-price-outline w-100 text-decoration-none text-center">
                            Contact Sales
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-6" id="faq">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5 text-center text-lg-start">
                    <h2 class="section-title">Frequently Asked Questions</h2>
                    <p class="text-secondary mb-4">
                        Everything you need to know about setting up and running your institution workspace on the {{ config('app.name', 'EduLink') }} platform.
                    </p>
                    <a href="{{ route('register') }}" class="btn-hero-primary text-decoration-none">
                        Start Trial & Discover
                    </a>
                </div>
                
                <div class="col-lg-7">
                    <div class="faq-accordion">
                        <details>
                            <summary>{{ \App\Models\SystemSetting::getVal('welcome_faq1_q', 'How long does it take to onboard our school?') }}</summary>
                            <div class="faq-content">
                                {{ \App\Models\SystemSetting::getVal('welcome_faq1_a', 'You can register online instantly! Setup takes less than 10 minutes. Once registered, our setup assistant will guide you through adding classes, academic terms, assigning subjects to teachers, and uploading students.') }}
                            </div>
                        </details>

                        <details>
                            <summary>{{ \App\Models\SystemSetting::getVal('welcome_faq2_q', 'Are parent and student portal accounts free?') }}</summary>
                            <div class="faq-content">
                                {{ \App\Models\SystemSetting::getVal('welcome_faq2_a', 'Yes! Once a school subscribes to our platform, there are no extra charges for parents, students, or teacher accounts. All user portals are included in the flat monthly tenant package.') }}
                            </div>
                        </details>

                        <details>
                            <summary>{{ \App\Models\SystemSetting::getVal('welcome_faq3_q', 'What payment methods are integrated for fees?') }}</summary>
                            <div class="faq-content">
                                {{ \App\Models\SystemSetting::getVal('welcome_faq3_a', config('app.name', 'EduLink') . ' integrates natively with major mobile money providers in Ghana (MTN MoMo, Telecel Cash, AT Money) and credit/debit card processors. Parents can pay bills directly online, updating school accounts in real time.') }}
                            </div>
                        </details>

                        <details>
                            <summary>{{ \App\Models\SystemSetting::getVal('welcome_faq4_q', 'Is our institution\'s data safe and secure?') }}</summary>
                            <div class="faq-content">
                                {{ \App\Models\SystemSetting::getVal('welcome_faq4_a', 'Absolutely. We run on enterprise cloud services, utilizing daily automated database backups, multi-factor authentication (MFA) for user accounts, and end-to-end HTTPS encryption to ensure compliance and data safety.') }}
                            </div>
                        </details>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <a href="#" class="navbar-brand d-flex align-items-center mb-3 text-decoration-none">
                        <div class="logo-icon-wrapper me-2">
                            <i class="bi bi-globe-europe-africa text-warning"></i>
                        </div>
                        <span class="brand-text">{!! str_starts_with(strtolower(config('app.name', 'EduLink')), 'edu') && strlen(config('app.name', 'EduLink')) > 3 ? substr(config('app.name', 'EduLink'), 0, 3) . '<span class="text-gold">' . e(substr(config('app.name', 'EduLink'), 3)) . '</span>' : e(config('app.name', 'EduLink')) !!}</span>
                    </a>
                    <p class="text-secondary small">
                        {{ \App\Models\SystemSetting::getVal('welcome_footer_desc', 'Providing premium SaaS management systems for modern schools across Ghana and the West African sub-region.') }}
                    </p>
                    <div class="d-flex align-items-center gap-2 mt-4 text-secondary small">
                        <span class="status-dot"></span> All Systems Operational
                    </div>
                </div>
                
                <div class="col-6 col-lg-3 offset-lg-1">
                    <h6 class="fw-bold text-white mb-3">Product Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#features" class="footer-link text-decoration-none small">Features</a></li>
                        <li class="mb-2"><a href="#pricing" class="footer-link text-decoration-none small">Pricing Plans</a></li>
                        <li class="mb-2"><a href="#faq" class="footer-link text-decoration-none small">FAQs</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-4">
                    <h6 class="fw-bold text-white mb-3">Platform Governance</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2 text-secondary small"><i class="bi bi-shield-lock-fill me-2 text-warning"></i>MFA Protection Enabled</li>
                        <li class="mb-2 text-secondary small"><i class="bi bi-hdd-network-fill me-2 text-warning"></i>Daily Automated Cloud Backups</li>
                        <li class="mb-2 text-secondary small"><i class="bi bi-envelope me-2 text-warning"></i>{{ \App\Models\SystemSetting::getVal('welcome_support_email', 'support@' . strtolower(config('app.name', 'EduLink')) . '.gh') }}</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-top border-secondary pt-4 text-center text-secondary small" style="border-color: rgba(255,255,255,0.05) !important;">
                &copy; 2026 {{ config('app.name', 'EduLink') }} Ghana ERP. All rights reserved. Built with pride for better education.
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Pricing Switch Controller JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Showcase Tab Switcher
            const tabButtons = document.querySelectorAll('.showcase-tab-btn');
            const displayCards = document.querySelectorAll('.showcase-display-card');

            tabButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    
                    // Deactivate all buttons & cards
                    tabButtons.forEach(b => b.classList.remove('active'));
                    displayCards.forEach(c => c.classList.remove('active'));
                    
                    // Activate selected
                    this.classList.add('active');
                    const targetCard = document.getElementById(targetId);
                    if (targetCard) {
                        targetCard.classList.add('active');
                    }
                });
            });

            const pSwitch = document.getElementById('pricing-switch');
            const labelMonthly = document.getElementById('toggle-termly');
            const labelAnnual = document.getElementById('toggle-annual');
            
            // Select all pricing amount elements
            const priceCards = document.querySelectorAll('.pricing-card');
            
            const originalPrices = [];
            priceCards.forEach((card) => {
                const priceContainer = card.querySelector('.pricing-price');
                if (priceContainer) {
                    const amountEl = priceContainer.querySelector('.price-amount');
                    const durationEl = priceContainer.querySelector('.price-duration');
                    if (amountEl) {
                        originalPrices.push({
                            amountEl: amountEl,
                            durationEl: durationEl,
                            originalText: amountEl.textContent.trim(),
                            originalDuration: durationEl ? durationEl.textContent.trim() : ''
                        });
                    }
                }
            });

            pSwitch.addEventListener('change', function() {
                const isAnnual = this.checked;
                
                if (isAnnual) {
                    labelMonthly.classList.remove('active');
                    labelAnnual.classList.add('active');
                } else {
                    labelMonthly.classList.add('active');
                    labelAnnual.classList.remove('active');
                }
                
                originalPrices.forEach(item => {
                    const text = item.originalText;
                    const numericPart = text.replace(/[^0-9.]/g, '');
                    const nonNumericPart = text.replace(/[0-9.]/g, '').trim(); // e.g. "GHS "
                    
                    if (numericPart) {
                        const value = parseFloat(numericPart);
                        if (value > 0) {
                            if (isAnnual) {
                                const discountedRate = Math.round(value * 0.8 * 3);
                                item.amountEl.textContent = nonNumericPart + discountedRate;
                                if (item.durationEl) {
                                    item.durationEl.textContent = '/year, billed annually';
                                }
                            } else {
                                item.amountEl.textContent = item.originalText;
                                if (item.durationEl) {
                                    item.durationEl.textContent = item.originalDuration;
                                }
                            }
                        } else {
                            item.amountEl.textContent = item.originalText;
                            if (item.durationEl) {
                                item.durationEl.textContent = item.originalDuration;
                            }
                        }
                    } else {
                        item.amountEl.textContent = item.originalText;
                        if (item.durationEl) {
                            item.durationEl.textContent = item.originalDuration;
                        }
                    }
                });
            });

            // Navbar Scroll Action
            const navbar = document.getElementById('mainNavbar');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 30) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        });
    </script>

    @php
        $whatsappNumber = \App\Models\SystemSetting::getVal('welcome_whatsapp_number', '');
        $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsappNumber);
        $whatsappMsg = urlencode('Hi, admin, i am interested.');
    @endphp

    @if(!empty($cleanWhatsapp))
        <a href="https://wa.me/{{ $cleanWhatsapp }}?text={{ $whatsappMsg }}" class="whatsapp-float" target="_blank" rel="noopener noreferrer" title="Chat with us on WhatsApp">
            <i class="bi bi-whatsapp"></i>
        </a>
    @endif
</body>
</html>
