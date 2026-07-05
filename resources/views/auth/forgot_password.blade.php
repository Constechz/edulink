<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | {{ config('app.name', 'EduLink') }} Ghana ERP</title>
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

        .forgot-card {
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

        .text-muted {
            color: #94a3b8 !important;
        }
    </style>
</head>
<body>

    <div class="forgot-card">
        <div class="brand-logo">
            <i class="bi bi-globe-europe-africa me-2 text-warning"></i>{!! str_starts_with(strtolower(config('app.name', 'EduLink')), 'edu') && strlen(config('app.name', 'EduLink')) > 3 ? substr(config('app.name', 'EduLink'), 0, 3) . '<span>' . e(substr(config('app.name', 'EduLink'), 3)) . '</span>' : e(config('app.name', 'EduLink')) !!}
        </div>

        <h4 class="text-center mb-2" style="font-weight: 600;">Reset Password</h4>
        <p class="text-center text-muted small mb-4">Enter your email and we'll send you a password reset link.</p>

        @if(session('status'))
            <div class="alert alert-success border-0 bg-success text-white mb-4" style="border-radius: 12px; --bs-bg-opacity: 0.2;">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 bg-danger text-white mb-4" style="border-radius: 12px; --bs-bg-opacity: 0.2;">
                <ul class="mb-0 list-unstyled">
                    @foreach($errors->all() as $error)
                        <li><i class="bi bi-exclamation-circle-fill me-2"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="email" class="form-label" style="font-size: 0.9rem; font-weight: 500;">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required autocomplete="email" autofocus>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2.5 mb-3">Send Password Reset Link</button>

            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="text-warning text-decoration-none small fw-bold"><i class="bi bi-arrow-left me-1"></i>Back to Sign In</a>
            </div>
        </form>
    </div>

</body>
</html>
