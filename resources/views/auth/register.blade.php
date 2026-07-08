<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        .register-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            width: 100%;
            max-width: 520px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .brand-logo {
            font-size: 2rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 1.5rem;
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

        .form-select option {
            background: #1e293b;
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

        .input-group-text {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
            border-radius: 12px;
        }

        .text-muted {
            color: #94a3b8 !important;
        }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="brand-logo">
            <i class="bi bi-globe-europe-africa me-2 text-warning"></i>{{ \App\Models\SystemSetting::getVal('platform_name', $appName) }}
        </div>

        <h4 class="text-center mb-1" style="font-weight: 600;">Register Your School</h4>
        <p class="text-center text-muted small mb-4">Start your school's 14-day free trial on the {{ $appName }} platform.</p>

        @if($errors->any())
            <div class="alert alert-danger border-0 bg-danger text-white mb-4" style="border-radius: 12px; --bs-bg-opacity: 0.2;">
                <ul class="mb-0 list-unstyled">
                    @foreach($errors->all() as $error)
                        <li><i class="bi bi-exclamation-circle-fill me-2"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" autocomplete="off">
            @csrf
            
            <h5 class="border-bottom border-secondary pb-2 mb-3" style="font-size: 0.95rem; font-weight: 600; color: #FFD700;">1. School Details</h5>

            <div class="mb-3">
                <label for="school_name" class="form-label" style="font-size: 0.85rem; font-weight: 500;">School Name</label>
                <input type="text" class="form-control" id="school_name" name="school_name" value="{{ old('school_name') }}" placeholder="e.g. Green Valley International School" required autofocus autocomplete="off">
            </div>

            <div class="mb-3">
                <label for="subdomain" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Subdomain Workspace</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="subdomain" name="subdomain" value="{{ old('subdomain') }}" placeholder="e.g. greenvalley" required autocomplete="off">
                    <span class="input-group-text">{{ $domainSuffix }}</span>
                </div>
                <div id="subdomainHelp" class="form-text text-muted" style="font-size: 0.75rem;">Your workspace: <strong class="text-light" id="previewSub">your-subdomain{{ $domainSuffix }}</strong></div>
            </div>

            <div class="mb-3">
                <label for="region" class="form-label" style="font-size: 0.85rem; font-weight: 500;">School Region</label>
                <select class="form-select form-control" id="region" name="region" required>
                    <option value="" disabled {{ old('region') ? '' : 'selected' }}>Select School Region</option>
                    @foreach([
                        'Greater Accra', 'Ashanti', 'Western', 'Eastern', 'Central', 
                        'Northern', 'Upper East', 'Upper West', 'Volta', 'Savannah', 
                        'North East', 'Bono', 'Bono East', 'Ahafo', 'Oti', 'Western North'
                    ] as $reg)
                        <option value="{{ $reg }}" {{ old('region') == $reg ? 'selected' : '' }}>{{ $reg }}</option>
                    @endforeach
                </select>
            </div>

            <h5 class="border-bottom border-secondary pb-2 mt-4 mb-3" style="font-size: 0.95rem; font-weight: 600; color: #FFD700;">2. Administrator Account</h5>

            <div class="mb-3">
                <label for="admin_name" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Admin Name</label>
                <input type="text" class="form-control" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" placeholder="e.g. Dr. Kofi Annan" required autocomplete="off">
            </div>

            <div class="mb-3">
                <label for="admin_email" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Admin Email Address</label>
                <input type="email" class="form-control" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" placeholder="admin@yourschool.edu.gh" required autocomplete="off">
            </div>

            <div class="mb-3">
                <label for="admin_phone" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Admin Phone Number</label>
                <input type="text" class="form-control" id="admin_phone" name="admin_phone" value="{{ old('admin_phone') }}" placeholder="e.g. +233240000000" required autocomplete="off">
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="admin_password" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Password</label>
                    <input type="password" class="form-control" id="admin_password" name="admin_password" placeholder="••••••••" required autocomplete="new-password">
                </div>
                <div class="col-md-6">
                    <label for="admin_password_confirmation" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Confirm Password</label>
                    <input type="password" class="form-control" id="admin_password_confirmation" name="admin_password_confirmation" placeholder="••••••••" required autocomplete="new-password">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2.5 mb-3">Register School & Admin</button>

            <div class="text-center">
                <span class="text-muted small">Already registered?</span>
                <a href="{{ route('login') }}" class="text-warning text-decoration-none small fw-bold ms-1">Sign In</a>
            </div>
        </form>
    </div>

    <!-- Live Subdomain Preview JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const subdomainInput = document.getElementById('subdomain');
            const previewSub = document.getElementById('previewSub');

            const domainSuffix = "{{ $domainSuffix }}";
            function updatePreview() {
                const val = subdomainInput.value.trim().toLowerCase().replace(/[^a-z0-9\-]/g, '');
                previewSub.textContent = val ? val + domainSuffix : 'your-subdomain' + domainSuffix;
            }

            subdomainInput.addEventListener('input', function() {
                // Filter out invalid characters on the fly
                let cursorPosition = subdomainInput.selectionStart;
                let originalLength = subdomainInput.value.length;
                
                let cleanVal = subdomainInput.value.toLowerCase().replace(/[^a-z0-9\-]/g, '');
                subdomainInput.value = cleanVal;
                
                // Adjust cursor position if characters were stripped
                let difference = originalLength - cleanVal.length;
                subdomainInput.setSelectionRange(cursorPosition - difference, cursorPosition - difference);
                
                updatePreview();
            });

            // Initial trigger
            updatePreview();
        });
    </script>

</body>
</html>
