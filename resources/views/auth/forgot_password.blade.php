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
        $appName = config('app.name', 'EduLink');
    @endphp

    <title>Reset Password | {{ $appName }} Ghana ERP</title>
    
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

        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            padding: 2.5rem 2.25rem;
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 440px;
            margin: 0 auto;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .form-label-custom {
            font-size: 0.84rem;
            font-weight: 600;
            color: var(--text-heading);
            margin-bottom: 0.35rem;
        }

        [data-bs-theme="dark"] .form-label-custom {
            color: #f8fafc;
        }

        .form-control-custom {
            background-color: var(--bg-canvas);
            border: 1.5px solid var(--border-subtle);
            color: var(--text-heading);
            border-radius: 10px;
            padding: 0.65rem 0.95rem;
            font-size: 0.92rem;
            transition: all 0.2s ease;
        }

        .form-control-custom::placeholder,
        input::placeholder {
            color: #94a3b8 !important;
            opacity: 0.45 !important;
            font-weight: 400;
        }

        .form-control-custom:focus {
            background-color: var(--bg-card);
            border-color: var(--primary-navy);
            box-shadow: 0 0 0 3.5px rgba(0, 51, 102, 0.12);
            color: var(--text-heading);
        }

        [data-bs-theme="dark"] .form-control-custom {
            background-color: #0c1427;
            border-color: rgba(255, 255, 255, 0.1);
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .form-control-custom::placeholder,
        [data-bs-theme="dark"] input::placeholder {
            color: #64748b !important;
            opacity: 0.4 !important;
        }

        .btn-brand-submit {
            background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-light) 100%);
            border: none;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 1rem;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 4px 14px rgba(0, 51, 102, 0.25);
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
        }

        .btn-brand-submit:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-navy) 100%);
            box-shadow: 0 6px 18px rgba(0, 51, 102, 0.35);
            transform: translateY(-2px);
        }

        .auth-footer {
            background: var(--bg-card);
            border-top: 1px solid var(--border-subtle);
            padding: 1.25rem 0;
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        /* Mobile Responsive Adjustments */
        @media (max-width: 576px) {
            .auth-top-header {
                padding: 0.65rem 0;
            }
            .brand-title {
                font-size: 1.15rem;
            }
            .logo-box {
                width: 32px;
                height: 32px;
                font-size: 1.05rem;
                border-radius: 8px;
            }
            .theme-toggle-btn {
                width: 32px;
                height: 32px;
                font-size: 0.9rem;
                border-radius: 8px;
            }
            main.py-5 {
                padding-top: 1.5rem !important;
                padding-bottom: 1.5rem !important;
            }
            .auth-card {
                padding: 1.65rem 1.25rem !important;
                border-radius: 16px !important;
                margin: 0 auto;
                box-shadow: var(--shadow-md) !important;
            }
            .auth-card .logo-box {
                width: 42px !important;
                height: 42px !important;
                font-size: 1.3rem !important;
                margin-bottom: 0.75rem !important;
            }
            .auth-card h3 {
                font-size: 1.25rem !important;
                line-height: 1.3 !important;
            }
            .auth-card p.text-muted {
                font-size: 0.82rem !important;
                line-height: 1.45 !important;
            }
            .form-label-custom {
                font-size: 0.82rem !important;
                margin-bottom: 0.25rem !important;
            }
            .form-control-custom {
                font-size: 0.88rem !important;
                padding: 0.58rem 0.85rem !important;
                border-radius: 8px !important;
            }
            .btn-brand-submit {
                font-size: 0.92rem !important;
                padding: 0.65rem 1rem !important;
                border-radius: 10px !important;
            }
            .auth-footer {
                padding: 0.85rem 0;
                font-size: 0.75rem;
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
                <div class="logo-box me-2">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <span class="brand-title">
                    {{ substr($appName, 0, 3) }}<span class="gold-text">{{ substr($appName, 3) }}</span>
                </span>
            </a>

            <div class="d-flex align-items-center gap-2">
                <button class="theme-toggle-btn" id="themeToggleBtn" type="button" aria-label="Toggle dark/light theme" title="Toggle theme">
                    <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                </button>
                <a href="{{ route('login') }}" class="text-decoration-none text-muted small fw-semibold d-inline-flex align-items-center">
                    <i class="bi bi-arrow-left me-1"></i>Sign In
                </a>
            </div>
        </div>
    </header>

    <!-- Main Card -->
    <main class="py-5 flex-grow-1 d-flex align-items-center">
        <div class="container">
            <div class="auth-card">
                
                <div class="text-center mb-4">
                    <div class="logo-box mx-auto mb-3" style="width: 48px; height: 48px; font-size: 1.5rem;">
                        <i class="bi bi-key-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="font-size: 1.4rem;">Reset Your Password</h3>
                    <p class="text-muted small mb-0">Enter your registered email address to receive password reset instructions</p>
                </div>

                @if(session('status'))
                    <div class="alert alert-success border-0 bg-success text-white mb-4 rounded-3 p-3" style="--bs-bg-opacity: 0.2;">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
                    </div>
                @endif

                <!-- Validation Errors Alert -->
                @if(isset($errors) && $errors->any())
                    <div class="alert alert-danger border-0 mb-4 rounded-3 p-3" style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626;">
                        <ul class="mb-0 ps-3 small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST" autocomplete="off">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="email" class="form-label-custom">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control form-control-custom" id="email" name="email" value="{{ old('email') }}" placeholder="admin@yourschool.edu.gh" required autocomplete="email" autofocus>
                    </div>

                    <button type="submit" class="btn-brand-submit mb-3">
                        <span>Send Password Reset Link</span>
                        <i class="bi bi-send-fill"></i>
                    </button>

                    <div class="text-center pt-2">
                        <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-bold small">
                            &larr; Back to Client Portal Sign In
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="auth-footer text-center">
        <div class="container">
            &copy; 2026 {{ $appName }} Ghana ERP. Built for Educational Excellence.
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme Toggle JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
        });
    </script>
</body>
</html>
