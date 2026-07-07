<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Onboarding Wizard | {{ config('app.name', 'EduLink') }} Ghana</title>
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
            --primary-grad: linear-gradient(135deg, #0b1528 0%, #0f2444 50%, #0b1528 100%);
            --card-bg: rgba(22, 38, 70, 0.7);
            --border-color: rgba(255, 255, 255, 0.08);
            --accent-color: #ffd700;
            --success-color: #10b981;
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
            padding: 40px 20px;
        }

        .wizard-container {
            width: 100%;
            max-width: 800px;
        }

        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
            padding: 3rem;
            transition: all 0.3s ease;
        }

        .brand-logo {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #ffffff;
            margin-bottom: 2rem;
            text-align: center;
        }

        .brand-logo span {
            color: var(--accent-color);
        }

        /* Progress Steps */
        .steps-nav {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 3.5rem;
            z-index: 1;
        }

        .steps-nav::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.1);
            z-index: -1;
        }

        .steps-nav-progress {
            position: absolute;
            top: 20px;
            left: 0;
            height: 3px;
            background: var(--accent-color);
            z-index: -1;
            transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            width: 0%;
        }

        .step-indicator {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: default;
            width: 80px;
        }

        .step-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #111e38;
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }

        .step-indicator.active .step-circle {
            background: var(--accent-color);
            border-color: var(--accent-color);
            color: #0b1528;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.4);
            transform: scale(1.15);
        }

        .step-indicator.completed .step-circle {
            background: var(--success-color);
            border-color: var(--success-color);
            color: white;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);
        }

        .step-label {
            margin-top: 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            text-align: center;
            transition: color 0.3s ease;
        }

        .step-indicator.active .step-label {
            color: #ffffff;
        }

        .step-indicator.completed .step-label {
            color: var(--success-color);
        }

        /* Form Wizard Content */
        .wizard-step {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .wizard-step.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #cbd5e1;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #ffffff;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(15, 23, 42, 0.7);
            border-color: var(--accent-color);
            box-shadow: 0 0 12px rgba(255, 215, 0, 0.2);
            color: #ffffff;
        }

        .form-select option {
            background: #0f172a;
            color: #ffffff;
        }

        .color-preview-box {
            width: 100%;
            height: 48px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 10px;
            transition: background-color 0.2s ease;
        }

        /* Navigation Buttons */
        .wizard-actions {
            margin-top: 3rem;
            display: flex;
            justify-content: space-between;
        }

        .btn-wizard {
            padding: 0.75rem 1.75rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .btn-next {
            background: linear-gradient(135deg, #ffd700 0%, #e0a900 100%);
            border: none;
            color: #0b1528;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.15);
        }

        .btn-next:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.3);
            background: linear-gradient(135deg, #ffe033 0%, #f0b800 100%);
        }

        .alert-error {
            background: rgba(244, 63, 94, 0.15);
            border: 1px solid rgba(244, 63, 94, 0.3);
            color: #fda4af;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
            display: none;
        }

        /* Responsive styling for mobile devices */
        @media (max-width: 767.98px) {
            body {
                padding: 15px 10px;
            }
            .glass-card {
                padding: 1.75rem 1.25rem !important;
                border-radius: 16px !important;
            }
            .brand-logo {
                font-size: 1.5rem;
                margin-bottom: 1.5rem;
            }
            .wizard-actions {
                margin-top: 2rem;
            }
            .btn-wizard {
                padding: 0.65rem 1.25rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 575.98px) {
            .step-label {
                display: none !important;
            }
            .steps-nav {
                margin-bottom: 2.25rem;
            }
            .step-indicator {
                width: auto !important;
            }
            .step-circle {
                width: 34px !important;
                height: 34px !important;
                font-size: 0.85rem !important;
            }
            .steps-nav::before, .steps-nav-progress {
                top: 17px !important;
            }
        }

        /* Text readability contrast overrides */
        .text-muted {
            color: #94a3b8 !important;
        }
    </style>
</head>
<body>

<div class="wizard-container">
    <div class="glass-card">
        <div class="brand-logo">
            <i class="bi bi-globe-europe-africa me-2"></i>{!! str_starts_with(strtolower(config('app.name', 'EduLink')), 'edu') && strlen(config('app.name', 'EduLink')) > 3 ? substr(config('app.name', 'EduLink'), 0, 3) . '<span>' . e(substr(config('app.name', 'EduLink'), 3)) . '</span>' : e(config('app.name', 'EduLink')) !!} Ghana
        </div>

        <!-- Horizontal Steps Navigation -->
        <div class="steps-nav">
            <div class="steps-nav-progress" id="wizardProgress"></div>
            <div class="step-indicator active" data-step="1">
                <div class="step-circle">1</div>
                <div class="step-label">Branding</div>
            </div>
            <div class="step-indicator" data-step="2">
                <div class="step-circle">2</div>
                <div class="step-label">Calendar</div>
            </div>
            <div class="step-indicator" data-step="3">
                <div class="step-circle">3</div>
                <div class="step-label">Admin</div>
            </div>
            <div class="step-indicator" data-step="4">
                <div class="step-circle">4</div>
                <div class="step-label">Grading</div>
            </div>
            <div class="step-indicator" data-step="5">
                <div class="step-circle">5</div>
                <div class="step-label">Homepage</div>
            </div>
        </div>

        <div class="alert alert-error" id="wizardError"></div>

        <!-- Wizard Forms -->
        <form id="onboardingForm" novalidate>
            @csrf

            <!-- STEP 1: School Branding -->
            <div class="wizard-step active" data-step="1">
                <h4 class="mb-3" style="font-weight: 700;"><i class="bi bi-palette me-2 text-warning"></i>1. School Branding Parameters</h4>
                <p class="text-muted mb-4">Set your school's unique identity colors and base font family used across student portals and reporting pages.</p>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label" for="primary_color">Primary Brand Color</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color w-25 border-end-0" id="primary_color_picker" value="#000000">
                            <input type="text" class="form-control" name="primary_color" id="primary_color" value="" placeholder="#003366" maxlength="7">
                        </div>
                        <div class="color-preview-box" id="primaryPreview" style="background-color: transparent;"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="accent_color">Accent / Secondary Color</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color w-25 border-end-0" id="accent_color_picker" value="#000000">
                            <input type="text" class="form-control" name="accent_color" id="accent_color" value="" placeholder="#ffd700" maxlength="7">
                        </div>
                        <div class="color-preview-box" id="accentPreview" style="background-color: transparent;"></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="font_family">Portal Font Family</label>
                        <select class="form-select" name="font_family" id="font_family">
                            <option value="" selected disabled>Select font family...</option>
                            <option value="Outfit">Outfit (Modern Premium)</option>
                            <option value="Inter">Inter (Sleek Professional)</option>
                            <option value="Roboto">Roboto (Classic Clean)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="logo">School Logo</label>
                        <input type="file" class="form-control" name="logo" id="logo" accept="image/*">
                        <span class="text-muted small d-block mt-1" style="font-size: 0.75rem;">Upload a PNG or JPG logo for report card headers.</span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="headteacher_signature">Headteacher Signature</label>
                        <input type="file" class="form-control" name="headteacher_signature" id="headteacher_signature" accept="image/*">
                        <span class="text-muted small d-block mt-1" style="font-size: 0.75rem;">Upload the Headteacher's signature image to stamp reports.</span>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Academic Calendar Setup -->
            <div class="wizard-step" data-step="2">
                <h4 class="mb-3" style="font-weight: 700;"><i class="bi bi-calendar-event me-2 text-warning"></i>2. Academic Calendar Setup</h4>
                <p class="text-muted mb-4">Create your school's initial active Academic Year and the current active academic Term details.</p>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label" for="academic_year">Academic Year Name</label>
                        <input type="text" class="form-control" name="academic_year" id="academic_year" placeholder="e.g. 2026/2027" value="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="term_name">Active Term Name</label>
                        <input type="text" class="form-control" name="term_name" id="term_name" placeholder="e.g. Term 1" value="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="start_date">Term Starts On</label>
                        <input type="date" class="form-control" name="start_date" id="start_date" value="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="end_date">Term Ends On</label>
                        <input type="date" class="form-control" name="end_date" id="end_date" value="">
                    </div>
                </div>
            </div>

            <!-- STEP 3: Primary Administrator User -->
            <div class="wizard-step" data-step="3">
                <h4 class="mb-3" style="font-weight: 700;"><i class="bi bi-person-badge me-2 text-warning"></i>3. Primary School Administrator</h4>
                <p class="text-muted mb-4">Initialize the default school administrator profile credentials. This account will have full access to manage students, staff, and billing.</p>
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label" for="admin_name">Administrator Name</label>
                        <input type="text" class="form-control" name="admin_name" id="admin_name" placeholder="e.g. Mr. Benjamin Mensah" value="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="admin_email">Primary Email Address</label>
                        <input type="email" class="form-control" name="admin_email" id="admin_email" placeholder="e.g. admin@school.edu.gh" value="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="admin_password">Security Password</label>
                        <input type="password" class="form-control" name="admin_password" id="admin_password" placeholder="Minimum 8 characters" value="">
                    </div>
                </div>
            </div>

            <!-- STEP 4: Default Score Configurations -->
            <div class="wizard-step" data-step="4">
                <h4 class="mb-3" style="font-weight: 700;"><i class="bi bi-percent me-2 text-warning"></i>4. Default Scoring Configuration</h4>
                <p class="text-muted mb-4">Set up standard GES continuous assessment weights. The sum of SBA (Class score) and Terminal Exams must equal 100%.</p>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label" for="class_weight">Class / SBA Weight (%)</label>
                        <input type="number" class="form-control" name="class_weight" id="class_weight" min="0" max="100" value="">
                        <div class="form-text text-muted">Ghana GES standard is 30% Continuous Assessment.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="exam_weight">Terminal Examination Weight (%)</label>
                        <input type="number" class="form-control" name="exam_weight" id="exam_weight" min="0" max="100" value="">
                        <div class="form-text text-muted">Ghana GES standard is 70% Terminal Exams.</div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.05); border: 1px dashed rgba(255,255,255,0.1)">
                            <div class="d-flex justify-content-between font-weight-bold">
                                <span>Total Evaluation Weight:</span>
                                <span id="weightTotal" class="text-success fw-bold">100%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 5: Homepage Setup -->
            <div class="wizard-step" data-step="5">
                <h4 class="mb-3" style="font-weight: 700;"><i class="bi bi-window-sidebar me-2 text-warning"></i>5. Public School Website Builder</h4>
                <p class="text-muted mb-4">Click below to initialize your school's default public homepage landing page. This will set up a placeholder page in the drag-and-drop builder.</p>
                <div class="p-4 text-center rounded-4 mb-3" style="background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255,255,255,0.05);">
                    <i class="bi bi-window-plus text-warning display-4 mb-3 d-block"></i>
                    <h5 class="fw-bold mb-2">Initialize Landing Page Template</h5>
                    <p class="text-muted-50 small mb-0">Loads custom layout blocks, branding colors CSS, and contact forms inside the database page registry.</p>
                </div>
            </div>

            <!-- Navigation Actions -->
            <div class="wizard-actions">
                <button type="button" class="btn btn-wizard btn-back" id="btnPrev" style="display: none;">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn btn-wizard btn-next ms-auto" id="btnNext">
                    Next <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let currentStep = 1;
        const totalSteps = 5;

        // Branding colors event sync
        const primaryPicker = document.getElementById("primary_color_picker");
        const primaryInput = document.getElementById("primary_color");
        const primaryPreview = document.getElementById("primaryPreview");

        primaryPicker.addEventListener("input", function() {
            primaryInput.value = primaryPicker.value;
            primaryPreview.style.backgroundColor = primaryPicker.value;
        });
        primaryInput.addEventListener("input", function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(primaryInput.value)) {
                primaryPicker.value = primaryInput.value;
                primaryPreview.style.backgroundColor = primaryInput.value;
            }
        });

        const accentPicker = document.getElementById("accent_color_picker");
        const accentInput = document.getElementById("accent_color");
        const accentPreview = document.getElementById("accentPreview");

        accentPicker.addEventListener("input", function() {
            accentInput.value = accentPicker.value;
            accentPreview.style.backgroundColor = accentPicker.value;
        });
        accentInput.addEventListener("input", function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(accentInput.value)) {
                accentPicker.value = accentInput.value;
                accentPreview.style.backgroundColor = accentInput.value;
            }
        });

        // Scoring weights sum calculator
        const classWeight = document.getElementById("class_weight");
        const examWeight = document.getElementById("exam_weight");
        const weightTotal = document.getElementById("weightTotal");

        function updateWeightsSum() {
            const sum = (parseInt(classWeight.value) || 0) + (parseInt(examWeight.value) || 0);
            weightTotal.textContent = sum + "%";
            if (sum === 100) {
                weightTotal.className = "text-success fw-bold";
            } else {
                weightTotal.className = "text-danger fw-bold";
            }
        }
        classWeight.addEventListener("input", updateWeightsSum);
        examWeight.addEventListener("input", updateWeightsSum);

        // Wizard navigation logic
        const btnPrev = document.getElementById("btnPrev");
        const btnNext = document.getElementById("btnNext");
        const wizardError = document.getElementById("wizardError");

        btnPrev.addEventListener("click", () => {
            if (currentStep > 1) {
                navigateStep(currentStep - 1);
            }
        });

        btnNext.addEventListener("click", () => {
            submitStepData();
        });

        function navigateStep(step) {
            document.querySelectorAll(".wizard-step").forEach(el => el.classList.remove("active"));
            document.querySelector(`.wizard-step[data-step="${step}"]`).classList.add("active");

            document.querySelectorAll(".step-indicator").forEach((el, index) => {
                const indicatorStep = index + 1;
                el.classList.remove("active", "completed");
                if (indicatorStep === step) {
                    el.classList.add("active");
                } else if (indicatorStep < step) {
                    el.classList.add("completed");
                }
            });

            currentStep = step;
            
            // Progress Bar
            const percent = ((step - 1) / (totalSteps - 1)) * 100;
            document.getElementById("wizardProgress").style.width = percent + "%";

            // Update action buttons text
            btnPrev.style.display = (step === 1) ? "none" : "block";
            btnNext.innerHTML = (step === totalSteps) 
                ? 'Finish Onboarding <i class="bi bi-check2-circle ms-1"></i>' 
                : 'Next <i class="bi bi-arrow-right ms-1"></i>';
            
            wizardError.style.display = "none";
        }

        function submitStepData() {
            wizardError.style.display = "none";
            
            const form = document.getElementById("onboardingForm");
            const formData = new FormData(form);
            formData.append("step", currentStep);

            // Clientside validations before AJAX
            if (currentStep === 4) {
                const s = (parseInt(classWeight.value) || 0) + (parseInt(examWeight.value) || 0);
                if (s !== 100) {
                    showError("The sum of continuous assessment and exam weights must equal 100%.");
                    return;
                }
            }

            btnNext.disabled = true;
            btnNext.innerHTML = 'Saving <span class="spinner-border spinner-border-sm ms-1" role="status"></span>';

            fetch("{{ route('school.onboarding.store') }}", {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.json())
            .then(data => {
                btnNext.disabled = false;
                btnNext.innerHTML = (currentStep === totalSteps) 
                    ? 'Finish Onboarding <i class="bi bi-check2-circle ms-1"></i>' 
                    : 'Next <i class="bi bi-arrow-right ms-1"></i>';

                if (data.error) {
                    showError(data.error);
                } else {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        navigateStep(currentStep + 1);
                    }
                }
            })
            .catch(err => {
                btnNext.disabled = false;
                btnNext.innerHTML = (currentStep === totalSteps) 
                    ? 'Finish Onboarding <i class="bi bi-check2-circle ms-1"></i>' 
                    : 'Next <i class="bi bi-arrow-right ms-1"></i>';
                showError("An error occurred during submission. Please try again.");
            });
        }

        function showError(msg) {
            wizardError.textContent = msg;
            wizardError.style.display = "block";
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
</script>

</body>
</html>
