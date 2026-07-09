<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Admin Login | {{ config('app.name', 'EduLink') }}</title>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #020617 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: #f8fafc;
        }

        .login-card {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            width: 100%;
            max-width: 440px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
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
            background: rgba(2, 6, 17, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #ffffff;
            border-radius: 12px;
            padding: 0.75rem 1rem;
        }

        .form-control::placeholder {
            color: #94a3b8 !important;
            opacity: 0.7 !important;
        }

        .form-control:focus {
            background: rgba(2, 6, 17, 0.8);
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
            <i class="bi bi-globe-europe-africa me-2 text-warning"></i>{!! str_starts_with(strtolower(config('app.name', 'EduLink')), 'edu') && strlen(config('app.name', 'EduLink')) > 3 ? substr(config('app.name', 'EduLink'), 0, 3) . '<span>' . e(substr(config('app.name', 'EduLink'), 3)) . '</span>' : e(config('app.name', 'EduLink')) !!}
        </div>

        <h4 class="text-center mb-2" style="font-weight: 700; letter-spacing: -0.5px;">Platform Management</h4>
        <p class="text-center text-muted small mb-4">Administration Access Control Console</p>

        @if($errors->any())
            <div class="alert alert-danger border-0 bg-danger text-white mb-4" style="border-radius: 12px; --bs-bg-opacity: 0.2;">
                <ul class="mb-0 list-unstyled">
                    @foreach($errors->all() as $error)
                        <li><i class="bi bi-exclamation-circle-fill me-2"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.login') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label" style="font-size: 0.9rem; font-weight: 500;">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="admin{{ '@' . strtolower(config('app.name', 'EduLink')) }}.com" required autocomplete="username" autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label" style="font-size: 0.9rem; font-weight: 500;">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember" style="font-size: 0.9rem;">Remember session</label>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2.5">Authenticate Admin</button>
        </form>
    </div>

</body>
</html>
