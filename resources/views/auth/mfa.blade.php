<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MFA Verification | {{ config('app.name', 'EduLink') }} Ghana</title>
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-grad: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            --card-bg: rgba(30, 41, 59, 0.7);
            --border-color: rgba(255, 255, 255, 0.08);
            --accent-color: #ffd700;
            --text-glow: 0 0 10px rgba(255, 215, 0, 0.2);
        }

        body {
            font-family: 'Outfit', 'Inter', sans-serif;
            background: var(--primary-grad);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            margin: 0;
            padding: 20px;
        }

        .mfa-container {
            width: 100%;
            max-width: 460px;
        }

        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .brand-logo {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #ffffff;
            margin-bottom: 1.5rem;
        }

        .brand-logo span {
            color: var(--accent-color);
            text-shadow: var(--text-glow);
        }

        .instruction-text {
            color: #94a3b8;
            font-size: 0.95rem;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .otp-inputs {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 2rem;
        }

        .otp-field {
            width: 50px;
            height: 58px;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #ffffff;
            transition: all 0.2s ease;
        }

        .otp-field:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 12px rgba(255, 215, 0, 0.25);
            background: rgba(15, 23, 42, 0.8);
        }

        .btn-verify {
            background: linear-gradient(135deg, #ffd700 0%, #e0a900 100%);
            border: none;
            color: #0f172a;
            font-weight: 600;
            padding: 0.8rem 2rem;
            border-radius: 12px;
            width: 100%;
            transition: all 0.2s ease;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.15);
        }

        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.3);
            background: linear-gradient(135deg, #ffe033 0%, #f0b800 100%);
        }

        .btn-verify:active {
            transform: translateY(0);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            margin-top: 1.5rem;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: #ffffff;
        }

        .back-link i {
            margin-right: 5px;
        }

        .alert-custom {
            background: rgba(244, 63, 94, 0.15);
            border: 1px solid rgba(244, 63, 94, 0.3);
            color: #fda4af;
            border-radius: 12px;
            font-size: 0.875rem;
            padding: 0.8rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }
    </style>
</head>
<body>

<div class="mfa-container">
    <div class="glass-card">
        <div class="brand-logo">
            <i class="bi bi-globe-europe-africa me-2"></i>{!! str_starts_with(strtolower(config('app.name', 'EduLink')), 'edu') && strlen(config('app.name', 'EduLink')) > 3 ? substr(config('app.name', 'EduLink'), 0, 3) . '<span>' . e(substr(config('app.name', 'EduLink'), 3)) . '</span>' : e(config('app.name', 'EduLink')) !!}
        </div>
        
        <h4 class="mb-2" style="font-weight: 700; letter-spacing: -0.5px;">Verification Code</h4>
        <p class="instruction-text">We sent a dynamic 6-digit verification code to your registered email address. Enter the code below to complete your login.</p>

        @if ($errors->any())
            <div class="alert alert-custom d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('login.mfa.verify') }}" method="POST" id="mfaForm">
            @csrf
            
            <!-- Hidden input that actually carries the 6-digit value -->
            <input type="hidden" name="code" id="hiddenCode">

            <div class="otp-inputs">
                <input type="text" class="otp-field" maxlength="1" pattern="[0-9]*" inputmode="numeric" required autofocus>
                <input type="text" class="otp-field" maxlength="1" pattern="[0-9]*" inputmode="numeric" required>
                <input type="text" class="otp-field" maxlength="1" pattern="[0-9]*" inputmode="numeric" required>
                <input type="text" class="otp-field" maxlength="1" pattern="[0-9]*" inputmode="numeric" required>
                <input type="text" class="otp-field" maxlength="1" pattern="[0-9]*" inputmode="numeric" required>
                <input type="text" class="otp-field" maxlength="1" pattern="[0-9]*" inputmode="numeric" required>
            </div>

            <button type="submit" class="btn btn-verify">
                <i class="bi bi-shield-check me-2"></i>Verify & Authenticate
            </button>
        </form>

        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Back to sign in
        </a>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const fields = document.querySelectorAll(".otp-field");
        const hiddenInput = document.getElementById("hiddenCode");
        const form = document.getElementById("mfaForm");

        fields.forEach((field, index) => {
            // Focus next field on typing a number
            field.addEventListener("input", function (e) {
                const val = field.value;
                // Only allow digit
                if (!/^[0-9]$/.test(val)) {
                    field.value = "";
                    return;
                }
                if (index < fields.length - 1) {
                    fields[index + 1].focus();
                }
                updateHiddenCode();
            });

            // Handle backspace key
            field.addEventListener("keydown", function (e) {
                if (e.key === "Backspace") {
                    if (field.value === "") {
                        if (index > 0) {
                            fields[index - 1].focus();
                            fields[index - 1].value = "";
                        }
                    } else {
                        field.value = "";
                    }
                    updateHiddenCode();
                }
            });

            // Handle pasting standard 6-digit codes
            field.addEventListener("paste", function (e) {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData("text").trim();
                if (/^[0-9]{6}$/.test(text)) {
                    for (let i = 0; i < fields.length; i++) {
                        fields[i].value = text[i];
                    }
                    fields[fields.length - 1].focus();
                    updateHiddenCode();
                }
            });
        });

        function updateHiddenCode() {
            let code = "";
            fields.forEach(field => {
                code += field.value;
            });
            hiddenInput.value = code;
        }

        form.addEventListener("submit", function (e) {
            updateHiddenCode();
            if (hiddenInput.value.length !== 6) {
                e.preventDefault();
                alert("Please fill out all 6 digits of the verification code.");
            }
        });
    });
</script>

</body>
</html>
