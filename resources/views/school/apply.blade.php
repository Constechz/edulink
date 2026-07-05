<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Admission Portal | {{ $school->name }}</title>
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
            --primary-color: #003366; /* GES Blue */
            --accent-color: #FFD700; /* Gold */
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            --card-bg: rgba(255, 255, 255, 0.95);
            --text-main: #1e293b;
        }

        body {
            font-family: 'Outfit', 'Inter', sans-serif;
            background: var(--bg-gradient);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .apply-container {
            max-width: 850px;
            width: 100%;
        }

        .glass-form-card {
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-main);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .card-header-banner {
            background: linear-gradient(135deg, #002244 0%, #004488 100%);
            color: #ffffff;
            padding: 2.5rem;
            text-align: center;
            border-bottom: 4px solid var(--accent-color);
        }

        .form-section-title {
            font-weight: 700;
            color: var(--primary-color);
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            margin-top: 2rem;
        }

        .btn-submit {
            background: linear-gradient(135deg, #003366 0%, #002244 100%);
            color: #ffffff;
            border: none;
            font-weight: 600;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 51, 102, 0.4);
            color: #ffffff;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(0, 51, 102, 0.15);
        }
    </style>
</head>
<body>

<div class="apply-container">
    
    <div class="glass-form-card">
        <div class="card-header-banner">
            <i class="bi bi-globe-europe-africa display-4 text-warning mb-2 d-block"></i>
            <h3 class="mb-1 font-weight-bold" style="font-weight: 800;">{{ $school->name }}</h3>
            <p class="mb-0 text-white-50">Online Student Admission Registration Portal</p>
        </div>

        <div class="p-4 p-md-5">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm p-4 mb-4" style="border-radius: 12px;">
                    <h5 class="font-weight-bold"><i class="bi bi-check-circle-fill me-2 text-success"></i>Application Submitted!</h5>
                    <p class="mb-0 small text-muted">{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <ul class="mb-0 list-unstyled small">
                        @foreach($errors->all() as $error)
                            <li><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('school.admissions.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="school_id" value="{{ $school->id }}">

                <!-- Candidate Biodata -->
                <h5 class="form-section-title"><i class="bi bi-person-badge me-2"></i>Candidate Information</h5>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small font-weight-bold">First Name</label>
                        <input type="text" class="form-control" name="first_name" required placeholder="Candidate's first name">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small font-weight-bold">Middle Name</label>
                        <input type="text" class="form-control" name="middle_name" placeholder="Candidate's middle name (optional)">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small font-weight-bold">Last Name</label>
                        <input type="text" class="form-control" name="last_name" required placeholder="Candidate's last name">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small font-weight-bold">Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small font-weight-bold">Gender</label>
                        <select class="form-select" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small font-weight-bold">Candidate Email (if applicable)</label>
                        <input type="email" class="form-control" name="email" placeholder="e.g. candidate@email.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small font-weight-bold">Candidate Phone (if applicable)</label>
                        <input type="text" class="form-control" name="phone" placeholder="e.g. +233 ...">
                    </div>
                </div>

                <!-- Academic Allocation Request -->
                <h5 class="form-section-title"><i class="bi bi-building-add me-2"></i>Campus & Grade Placement Details</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small font-weight-bold">Desired Campus branch</label>
                        <select class="form-select" name="campus_id" required>
                            <option value="">Select Target Campus</option>
                            @foreach($campuses as $campus)
                                <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small font-weight-bold">Applying for Class/Grade Level</label>
                        <select class="form-select" name="class_id" required>
                            <option value="">Select Grade Level</option>
                            @foreach($classes as $cls)
                                <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Guardian details -->
                <h5 class="form-section-title"><i class="bi bi-people me-2"></i>Parent / Guardian Information</h5>
                
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small font-weight-bold">Primary Guardian Full Name</label>
                        <input type="text" class="form-control" name="guardian_name" required placeholder="e.g. Mr. Edward Appiah">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small font-weight-bold">Guardian Primary Phone Number</label>
                        <input type="text" class="form-control" name="guardian_phone" required placeholder="e.g. +233 24 123 4567">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small font-weight-bold">Guardian Primary Email Address</label>
                        <input type="email" class="form-control" name="guardian_email" required placeholder="e.g. parent@email.com">
                    </div>
                </div>

                <!-- Documents attachments -->
                <h5 class="form-section-title"><i class="bi bi-file-earmark-arrow-up me-2"></i>Document Attachments</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small font-weight-bold">Candidate Birth Certificate (PDF or Image)</label>
                        <input type="file" class="form-control" name="birth_certificate">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small font-weight-bold">Previous Term Academic Transcript (PDF or Image)</label>
                        <input type="file" class="form-control" name="transcript">
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <button type="submit" class="btn btn-submit px-5 py-2.5">
                        <i class="bi bi-send-check-fill me-2"></i>Submit Admission Application
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
