<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Offline | {{ $school->name }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary: #003366;
            --accent: #FFD700;
            --font-heading: 'Outfit', sans-serif;
            --font-body: 'Inter', sans-serif;
        }

        body {
            font-family: var(--font-body);
            background: radial-gradient(circle at 10% 20%, rgba(243, 246, 249, 1) 0%, rgba(230, 237, 245, 1) 90%);
            color: #2d3748;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .offline-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            width: 100%;
            padding: 50px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .offline-card:hover {
            transform: translateY(-5px);
        }

        .offline-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
        }

        .logo-placeholder {
            width: 80px;
            height: 80px;
            background: rgba(0, 51, 102, 0.05);
            color: var(--primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 30px auto;
            position: relative;
            animation: pulse 2s infinite;
        }

        .title {
            font-family: var(--font-heading);
            font-weight: 800;
            color: var(--primary);
            font-size: 2.25rem;
            letter-spacing: -0.03em;
            margin-bottom: 15px;
        }

        .subtitle {
            font-size: 1.1rem;
            color: #718096;
            line-height: 1.6;
            margin-bottom: 35px;
        }

        .btn-portal {
            background-color: var(--primary);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0, 51, 102, 0.15);
        }

        .btn-portal:hover {
            background-color: #002244;
            color: #fff;
            box-shadow: 0 6px 20px rgba(0, 51, 102, 0.25);
            transform: translateY(-2px);
        }

        .badge-status {
            display: inline-block;
            background: rgba(255, 107, 53, 0.1);
            color: #FF6B35;
            padding: 6px 16px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            margin-bottom: 25px;
            letter-spacing: 0.05em;
        }

        .footer-text {
            margin-top: 40px;
            font-size: 0.85rem;
            color: #a0aec0;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 51, 102, 0.2);
            }
            70% {
                box-shadow: 0 0 0 15px rgba(0, 51, 102, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0, 51, 102, 0);
            }
        }
    </style>
</head>
<body>

    <div class="offline-card">
        <div class="logo-placeholder">
            <i class="bi bi-globe-americas"></i>
        </div>
        
        <span class="badge-status">Under Construction</span>
        
        <h1 class="title">{{ $school->name }}</h1>
        <p class="subtitle">
            Welcome! Our custom public website is currently under development. Please check back soon as we are preparing an updated, feature-rich experience for parents, students, and visitors.
        </p>

        <div>
            <a href="{{ route('login') }}" class="btn-portal">
                <i class="bi bi-shield-lock-fill"></i> Access Portal Login
            </a>
        </div>

        <p class="footer-text">
            Powered by {{ config('app.name', 'EduLink') }} Ghana ERP &copy; {{ date('Y') }}
        </p>
    </div>

</body>
</html>
