<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | {{ config('app.name', 'EduLink') }} Ghana ERP</title>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: #f8fafc;
        }

        .login-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            width: 100%;
            max-width: 440px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .brand-logo {
            font-size: 2rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 2rem;
            color: #ffffff;
        }

        .brand-logo span {
            color: #FFD700;
        }

        .form-control {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-radius: 12px;
            padding: 0.75rem 1rem;
        }

        .form-control::placeholder {
            color: #94a3b8 !important;
            opacity: 0.7 !important;
        }

        .form-control:focus {
            background: rgba(15, 23, 42, 0.7);
            border-color: #FFD700;
            box-shadow: 0 0 0 0.25rem rgba(255, 215, 0, 0.15);
            color: #ffffff;
        }

        .btn-primary {
            background: #FFD700;
            border: none;
            color: #0f172a;
            font-weight: 700;
            border-radius: 12px;
            padding: 0.75rem;
            transition: all 0.2s ease;
        }

        .btn-primary:hover, .btn-primary:focus {
            background: #e6c200;
            transform: translateY(-1px);
            color: #0f172a;
        }

        .form-check-input:checked {
            background-color: #FFD700;
            border-color: #FFD700;
        }

        .form-check-label {
            color: #cbd5e1;
        }

        .text-muted {
            color: #94a3b8 !important;
        }
    </style>
</head>
<body>
    @include('partials.preloader')

    <div class="login-card">
        <div class="brand-logo">
            <i class="bi bi-globe-europe-africa me-2 text-warning"></i>{{ \App\Models\SystemSetting::getVal('platform_name', config('app.name', 'EduLink')) }}
        </div>

        <h4 class="text-center mb-4" style="font-weight: 600;">Welcome Back</h4>

        @if($errors->any())
            <div class="alert alert-danger border-0 bg-danger text-white mb-4" style="border-radius: 12px; --bs-bg-opacity: 0.2;">
                <ul class="mb-0 list-unstyled">
                    @foreach($errors->all() as $error)
                        <li><i class="bi bi-exclamation-circle-fill me-2"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label" style="font-size: 0.9rem; font-weight: 500;">Email Address or Student ID</label>
                <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com or STD-YYYY-XXXX" required autocomplete="username" autofocus>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <label for="password" class="form-label m-0" style="font-size: 0.9rem; font-weight: 500;">Password</label>
                    <a href="{{ route('password.request') }}" class="text-warning text-decoration-none" style="font-size: 0.85rem; font-weight: 500;">Forgot password?</a>
                </div>
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember" style="font-size: 0.9rem;">Remember me on this device</label>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2.5">Sign In</button>

            <div class="text-center mt-4">
                <span class="text-muted small">Are you a school administrator?</span>
                <a href="{{ route('register') }}" class="text-warning text-decoration-none small fw-bold ms-1">Register your school</a>
            </div>
        </form>
    </div>

</body>
</html>
