<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Verification | EduLink Credential Services</title>
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #003366;
            --accent-color: #FFD700;
            --success-color: #10b981;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
        }
        body {
            font-family: 'Outfit', 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: #334155;
            padding: 2rem 1rem;
        }
        .verification-container {
            max-width: 700px;
            margin: 0 auto;
        }
        .verification-card {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }
        .card-accent-header {
            background: linear-gradient(135deg, #002244 0%, #003366 100%);
            color: white;
            padding: 2.5rem;
            text-align: center;
        }
        .verified-badge {
            background-color: var(--success-color);
            color: white;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .meta-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 600;
        }
        .meta-value {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
        }
        .table th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
        }
        .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<div class="verification-container">
    
    <div class="text-center mb-4">
        <h4 class="fw-bold text-muted" style="letter-spacing: 0.5px;">
            <i class="bi bi-shield-fill-check text-primary me-2"></i>EDULINK VERIFICATION GATEWAY
        </h4>
    </div>

    <div class="verification-card">
        <!-- Accent Header -->
        <div class="card-accent-header">
            <div class="mb-3">
                <span class="verified-badge">
                    <i class="bi bi-patch-check-fill fs-5"></i>Verified Authentic
                </span>
            </div>
            <h3 class="fw-bold mb-1">{{ $school->name }}</h3>
            <p class="text-white-50 small mb-0">{{ $school->address ?? 'Accredited Academic Institution' }}</p>
        </div>

        <div class="p-4 p-md-5">
            <!-- Student metadata -->
            <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-person-badge-fill me-2"></i>Student Credentials</h5>
            
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <span class="meta-label">Student Name</span>
                    <div class="meta-value">{{ $student->first_name }} {{ $student->last_name }}</div>
                </div>
                <div class="col-md-6">
                    <span class="meta-label">Admission Number</span>
                    <div class="meta-value">#{{ $student->admission_no }}</div>
                </div>
                <div class="col-md-6">
                    <span class="meta-label">Academic Year / Term</span>
                    <div class="meta-value">{{ $year->name }} • {{ $term->name }}</div>
                </div>
                <div class="col-md-6">
                    <span class="meta-label">Enrolled Class</span>
                    <div class="meta-value">{{ $class->name }}</div>
                </div>
            </div>

            <!-- Certified Grades Matrix -->
            <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-journal-check me-2"></i>Verified Grades Record</h5>
            
            <div class="table-responsive mb-4">
                <table class="table table-hover border">
                    <thead>
                        <tr>
                            <th>Subject / Course</th>
                            <th class="text-center">SBA (Scaled)</th>
                            <th class="text-center">Exam (Scaled)</th>
                            <th class="text-center">Grand Total</th>
                            <th class="text-center">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scores as $score)
                            <tr>
                                <td class="fw-bold text-dark">{{ $score->subject ? $score->subject->name : 'Unknown Subject' }}</td>
                                <td class="text-center text-muted">{{ $score->scaled_class_score }}%</td>
                                <td class="text-center text-muted">
                                    @if($score->is_absent_exam)
                                        <span class="text-danger small fw-semibold">ABSENT</span>
                                    @else
                                        {{ $score->scaled_exam_score }}%
                                    @endif
                                </td>
                                <td class="text-center fw-bold text-primary">{{ $score->grand_total }}%</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary text-dark px-2.5 py-1.5" style="border-radius: 6px;">{{ $score->grade ?? '—' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No verified grades records released for this student term.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="alert alert-success border-0 d-flex align-items-center gap-3 p-3" style="border-radius: 12px; background-color: #ecfdf5;">
                <i class="bi bi-info-circle-fill text-success fs-4"></i>
                <div class="small text-success-emphasis">
                    This document was officially signed and locked in the school archives. For questions, contact school administration directly at <strong>{{ $school->email ?? 'admin@edulink.edu.gh' }}</strong>.
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>
