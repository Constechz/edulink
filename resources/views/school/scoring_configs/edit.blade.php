@extends('layouts.app')

@section('title', 'Edit Scoring Config Wizard | EduLink')
@section('header_title', 'Edit SBA Configuration')

@section('styles')
<style>
    .wizard-step-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2.5rem;
        position: relative;
    }
    .wizard-step-header::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background-color: rgba(0, 0, 0, 0.05);
        z-index: 1;
        transform: translateY(-50%);
    }
    .step-node {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background-color: #ffffff;
        border: 2px solid #cbd5e1;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        position: relative;
        z-index: 2;
        transition: all 0.3s ease;
    }
    .step-node.active {
        border-color: var(--primary-color);
        background-color: var(--primary-color);
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(0, 51, 102, 0.15);
    }
    .step-node.completed {
        border-color: #10b981;
        background-color: #10b981;
        color: #ffffff;
    }
    .step-label {
        position: absolute;
        top: 42px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
        color: #64748b;
        left: 50%;
        transform: translateX(-50%);
    }
    .step-node.active .step-label {
        color: var(--primary-color);
    }
    @media (max-width: 768px) {
        .wizard-step-header {
            margin-bottom: 3.5rem;
        }
        .step-label {
            display: none;
        }
        .step-node.active .step-label {
            display: block;
            background-color: #ffffff;
            padding: 3px 10px;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            z-index: 10;
            top: 45px;
        }
    }
    .wizard-card {
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0" style="max-width: 900px; margin: 0 auto;">

    <div class="mb-4">
        <a href="{{ route('school.scoring-configs.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Rulesets
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <ul class="mb-0 list-unstyled">
                @foreach($errors->all() as $error)
                    <li><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="wizard-card p-5">
        
        <!-- Step indicators -->
        <div class="wizard-step-header">
            <div class="step-node active" id="node-1"><span class="step-label">1. Scope</span>1</div>
            <div class="step-node" id="node-2"><span class="step-label">2. Totals</span>2</div>
            <div class="step-node" id="node-3"><span class="step-label">3. Weights</span>3</div>
            <div class="step-node" id="node-4"><span class="step-label">4. Components</span>4</div>
            <div class="step-node" id="node-5"><span class="step-label">5. Rounding</span>5</div>
            <div class="step-node" id="node-6"><span class="step-label">6. Preview</span>6</div>
        </div>

        <form action="{{ route('school.scoring-configs.update', $scoringConfig->id) }}" method="POST" id="wizardForm">
            @csrf
            @method('PUT')
            
            <!-- STEP 1: Scope & Profile -->
            <div class="wizard-step-content" id="step-1">
                <h5 class="font-weight-bold mb-4" style="font-weight: 700;">Step 1: Scope & Ruleset Profile</h5>
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label font-weight-bold">Configuration / Ruleset Name</label>
                        <input type="text" class="form-control" name="name" id="config_name" value="{{ old('name', $scoringConfig->name) }}" placeholder="e.g. Standard SHS English Config" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Target Level Scope</label>
                        <select class="form-select" name="level" id="level" required>
                            <option value="ALL" {{ old('level', $scoringConfig->level) == 'ALL' ? 'selected' : '' }}>All Academic Levels</option>
                            <option value="Nursery" {{ old('level', $scoringConfig->level) == 'Nursery' ? 'selected' : '' }}>Nursery</option>
                            <option value="KG" {{ old('level', $scoringConfig->level) == 'KG' ? 'selected' : '' }}>KG</option>
                            <option value="Primary" {{ old('level', $scoringConfig->level) == 'Primary' ? 'selected' : '' }}>Primary</option>
                            <option value="JHS" {{ old('level', $scoringConfig->level) == 'JHS' ? 'selected' : '' }}>JHS</option>
                            <option value="SHS" {{ old('level', $scoringConfig->level) == 'SHS' ? 'selected' : '' }}>SHS</option>
                            <option value="TVET" {{ old('level', $scoringConfig->level) == 'TVET' ? 'selected' : '' }}>TVET</option>
                            <option value="Tertiary" {{ old('level', $scoringConfig->level) == 'Tertiary' ? 'selected' : '' }}>Tertiary</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Subject Override Scope (Optional)</label>
                        <select class="form-select" name="subject_id">
                            <option value="" {{ is_null(old('subject_id', $scoringConfig->subject_id)) ? 'selected' : '' }}>Apply to all subjects at this level</option>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}" {{ old('subject_id', $scoringConfig->subject_id) == $sub->id ? 'selected' : '' }}>{{ $sub->name }} ({{ $sub->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label font-weight-bold">Academic Year Scope (Optional)</label>
                        <select class="form-select" name="academic_year_id">
                            <option value="" {{ is_null(old('academic_year_id', $scoringConfig->academic_year_id)) ? 'selected' : '' }}>Apply to all Academic Years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id', $scoringConfig->academic_year_id) == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Raw Max marks -->
            <div class="wizard-step-content d-none" id="step-2">
                <h5 class="font-weight-bold mb-4" style="font-weight: 700;">Step 2: Score Entry Maximum Limits</h5>
                <p class="text-muted small mb-4">Set the raw maximum marks teachers can input for class SBA total and final exams.</p>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Class SBA Score Max (Raw total)</label>
                        <input type="number" step="0.5" class="form-control" name="class_score_max" id="class_score_max" value="{{ old('class_score_max', $scoringConfig->class_score_max) }}" required>
                        <div class="form-text">e.g. 50 or 100. Component sum cannot exceed this raw limit.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Exam Score Max (Raw exam mark)</label>
                        <input type="number" step="0.5" class="form-control" name="exam_score_max" id="exam_score_max" value="{{ old('exam_score_max', $scoringConfig->exam_score_max) }}" required>
                        <div class="form-text">e.g. 100. Cutoff threshold for exam entry.</div>
                    </div>
                </div>
            </div>

            <!-- STEP 3: Scaling & Report Card Weights -->
            <div class="wizard-step-content d-none" id="step-3">
                <h5 class="font-weight-bold mb-4" style="font-weight: 700;">Step 3: Report Card Weight Scaling</h5>
                <p class="text-muted small mb-4">Set how scores will scale on final report cards (e.g. 50/50, 30/70, 40/60).</p>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Class SBA Weight (%)</label>
                        <input type="number" step="0.5" class="form-control" name="class_score_weight" id="class_score_weight" value="{{ old('class_score_weight', $scoringConfig->class_score_weight) }}" oninput="updateGrandTotal()" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Exam Score Weight (%)</label>
                        <input type="number" step="0.5" class="form-control" name="exam_score_weight" id="exam_score_weight" value="{{ old('exam_score_weight', $scoringConfig->exam_score_weight) }}" oninput="updateGrandTotal()" required>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold" style="font-weight: 600;">Report Card Grand Total Weight</span>
                            <span class="fs-4 font-weight-bold text-primary" style="font-weight: 800;" id="grand_total_display">{{ $scoringConfig->grand_total }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 4: Component Builder -->
            <div class="wizard-step-content d-none" id="step-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="font-weight-bold mb-1" style="font-weight: 700;">Step 4: Define Class SBA Components</h5>
                        <p class="text-muted small mb-0">Build components that make up the SBA class score. Total must match raw maximum.</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-dark" onclick="addComponentRow()">
                        <i class="bi bi-plus-lg me-1"></i>Add Component
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle" id="componentsTable">
                        <thead>
                            <tr>
                                <th>Component Name</th>
                                <th style="width: 150px;">Max Marks</th>
                                <th class="text-center" style="width: 100px;">Required</th>
                                <th class="text-end" style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="componentsContainer">
                            <!-- Rows injected dynamically by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center mt-3">
                    <span class="font-weight-bold" style="font-weight: 600;">Total Components Marks Defined:</span>
                    <span class="fs-5 font-weight-bold text-success" style="font-weight: 700;" id="components_sum_display">0 / {{ $scoringConfig->class_score_max }}</span>
                </div>
                <div class="alert alert-danger d-none border-0 mt-3" id="componentWarning" style="border-radius: 10px;">
                    <i class="bi bi-exclamation-octagon me-2"></i>Warning: Components marks total exceeds configured Class SBA Score Max!
                </div>
            </div>

            <!-- STEP 5: Rounding and Defaults -->
            <div class="wizard-step-content d-none" id="step-5">
                <h5 class="font-weight-bold mb-4" style="font-weight: 700;">Step 5: Rounding & Settings</h5>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Rounding Method</label>
                        <select class="form-select" name="rounding_method" id="rounding_method" required>
                            <option value="ROUND" {{ old('rounding_method', $scoringConfig->rounding_method) == 'ROUND' ? 'selected' : '' }}>Standard Rounding (ROUND)</option>
                            <option value="FLOOR" {{ old('rounding_method', $scoringConfig->rounding_method) == 'FLOOR' ? 'selected' : '' }}>Truncate Down (FLOOR)</option>
                            <option value="CEIL" {{ old('rounding_method', $scoringConfig->rounding_method) == 'CEIL' ? 'selected' : '' }}>Round Up (CEIL)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Decimal Places</label>
                        <select class="form-select" name="decimal_places" id="decimal_places" required>
                            <option value="0" {{ old('decimal_places', $scoringConfig->decimal_places) == '0' ? 'selected' : '' }}>0 Decimal Places (No Decimals)</option>
                            <option value="1" {{ old('decimal_places', $scoringConfig->decimal_places) == '1' ? 'selected' : '' }}>1 Decimal Place (e.g. 75.5)</option>
                            <option value="2" {{ old('decimal_places', $scoringConfig->decimal_places) == '2' ? 'selected' : '' }}>2 Decimal Places (e.g. 75.54)</option>
                        </select>
                    </div>
                    <div class="col-12 mt-4">
                        <div class="form-check form-switch p-3 bg-light rounded-3 d-flex align-items-center justify-content-between" style="padding-left: 3.5rem !important;">
                            <div>
                                <label class="form-check-label font-weight-bold text-dark" for="is_default" style="font-weight: 600;">Set as Default ruleset</label>
                                <div class="form-text mt-0">If true, this ruleset applies as default fallback scoring rules for this level.</div>
                            </div>
                            <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $scoringConfig->is_default) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 6: Interactive Preview -->
            <div class="wizard-step-content d-none" id="step-6">
                <h5 class="font-weight-bold mb-1" style="font-weight: 700;">Step 6: Mathematical Preview Sandbox</h5>
                <p class="text-muted small mb-4">Input sample marks below to preview how the engine aggregates, scales, and rounds final scores.</p>
                
                <div class="row g-4">
                    <!-- Inputs -->
                    <div class="col-md-6">
                        <div class="glass-card p-4 border-0 bg-light">
                            <h6 class="font-weight-bold mb-3">Simulation Input Marks</h6>
                            <div id="previewInputsContainer" class="row g-2">
                                <!-- JS inputs injected here -->
                            </div>
                            <hr>
                            <div class="col-12">
                                <label class="form-label small font-weight-bold">Raw Exam Score</label>
                                <input type="number" class="form-control" id="preview_exam_input" value="70" oninput="runSimulation()">
                            </div>
                        </div>
                    </div>

                    <!-- Output Math Box -->
                    <div class="col-md-6">
                        <div class="glass-card p-4 bg-dark text-white border-0">
                            <h6 class="font-weight-bold mb-3" style="color: var(--accent-color);">Calculated Output Formulas</h6>
                            <div class="mb-2">
                                <span class="text-secondary small">Raw SBA Total:</span>
                                <div class="fs-5" id="sim_raw_sba">0.00</div>
                            </div>
                            <div class="mb-2">
                                <span class="text-secondary small">Scaled SBA Score:</span>
                                <div class="fs-5" id="sim_scaled_sba">0.00</div>
                                <div class="text-muted small" style="font-size: 0.75rem;" id="sim_scaled_sba_formula"></div>
                            </div>
                            <div class="mb-2">
                                <span class="text-secondary small">Scaled Exam Score:</span>
                                <div class="fs-5" id="sim_scaled_exam">0.00</div>
                                <div class="text-muted small" style="font-size: 0.75rem;" id="sim_scaled_exam_formula"></div>
                            </div>
                            <hr class="border-secondary">
                            <div class="mb-0">
                                <span class="text-secondary small font-weight-bold">Report Grand Total:</span>
                                <div class="fs-3 font-weight-bold text-warning" id="sim_grand_total">0.00</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary px-4 py-2 d-none" id="prevBtn" onclick="navigateStep(-1)">
                    <i class="bi bi-chevron-left me-1"></i>Previous Step
                </button>
                <button type="button" class="btn btn-primary px-5 py-2 ms-auto" id="nextBtn" onclick="navigateStep(1)">
                    Next Step<i class="bi bi-chevron-right ms-1"></i>
                </button>
            </div>
        </form>

    </div>

</div>
@endsection

@section('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 6;
    let componentIndex = 0;

    document.addEventListener('DOMContentLoaded', function() {
        // Render existing configuration components from DB
        @foreach($scoringConfig->components as $comp)
            addComponentRow('{{ e($comp->name) }}', {{ $comp->max_marks }}, {{ $comp->is_required ? 'true' : 'false' }}, {{ $comp->id }});
        @endforeach
        
        updateGrandTotal();
    });

    function navigateStep(direction) {
        // Validation check before leaving step 4
        if (currentStep === 4 && direction === 1) {
            const maxVal = parseFloat(document.getElementById('class_score_max').value);
            const sumVal = getComponentsSum();
            if (sumVal > maxVal) {
                alert('Warning: SBA Components sum exceeds the configured Class Score Max. Adjust components before continuing.');
                return;
            }
        }

        // Hide current step
        document.getElementById(`step-${currentStep}`).classList.add('d-none');
        document.getElementById(`node-${currentStep}`).classList.remove('active');
        if (direction === 1) {
            document.getElementById(`node-${currentStep}`).classList.add('completed');
        }

        currentStep += direction;

        // Show next step
        document.getElementById(`step-${currentStep}`).classList.remove('d-none');
        document.getElementById(`node-${currentStep}`).classList.add('active');

        // Manage button visibility
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        if (currentStep === 1) {
            prevBtn.classList.add('d-none');
        } else {
            prevBtn.classList.remove('d-none');
        }

        if (currentStep === totalSteps) {
            nextBtn.innerHTML = '<i class="bi bi-save2 me-1"></i>Update ruleset config';
            setupPreviewInputs();
            runSimulation();
        } else {
            nextBtn.innerHTML = 'Next Step<i class="bi bi-chevron-right ms-1"></i>';
        }

        if (currentStep > totalSteps) {
            document.getElementById('wizardForm').submit();
        }
    }

    function updateGrandTotal() {
        const classWeight = parseFloat(document.getElementById('class_score_weight').value) || 0;
        const examWeight = parseFloat(document.getElementById('exam_score_weight').value) || 0;
        const grand = classWeight + examWeight;
        document.getElementById('grand_total_display').textContent = `${grand}%`;
    }

    function addComponentRow(name = '', maxMarks = '', isRequired = false, id = '') {
        const container = document.getElementById('componentsContainer');
        const rowId = `comp-row-${componentIndex}`;
        
        const html = `
            <tr id="${rowId}">
                <td>
                    <input type="hidden" name="components[${componentIndex}][id]" value="${id}">
                    <input type="text" class="form-control form-control-sm" name="components[${componentIndex}][name]" value="${name}" required>
                </td>
                <td>
                    <input type="number" step="0.5" class="form-control form-control-sm comp-marks-input" name="components[${componentIndex}][max_marks]" value="${maxMarks}" oninput="checkComponentsSum()" required>
                </td>
                <td class="text-center">
                    <input type="checkbox" class="form-check-input" name="components[${componentIndex}][is_required]" value="1" ${isRequired ? 'checked' : ''}>
                </td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeComponentRow('${rowId}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        container.insertAdjacentHTML('beforeend', html);
        componentIndex++;
        checkComponentsSum();
    }

    function removeComponentRow(rowId) {
        document.getElementById(rowId).remove();
        checkComponentsSum();
    }

    function getComponentsSum() {
        const inputs = document.getElementsByClassName('comp-marks-input');
        let sum = 0;
        for (let input of inputs) {
            sum += parseFloat(input.value) || 0;
        }
        return sum;
    }

    function checkComponentsSum() {
        const maxVal = parseFloat(document.getElementById('class_score_max').value) || 0;
        const sum = getComponentsSum();
        
        const display = document.getElementById('components_sum_display');
        display.textContent = `${sum} / ${maxVal}`;

        const warning = document.getElementById('componentWarning');
        if (sum > maxVal) {
            display.className = "fs-5 font-weight-bold text-danger";
            warning.classList.remove('d-none');
        } else if (sum === maxVal) {
            display.className = "fs-5 font-weight-bold text-success";
            warning.classList.add('d-none');
        } else {
            display.className = "fs-5 font-weight-bold text-warning";
            warning.classList.add('d-none');
        }
    }

    function setupPreviewInputs() {
        const inputsContainer = document.getElementById('previewInputsContainer');
        inputsContainer.innerHTML = '';
        
        const rows = document.getElementById('componentsContainer').getElementsByTagName('tr');
        for (let row of rows) {
            const nameInput = row.querySelector('input[type="text"]');
            const maxMarksInput = row.querySelector('.comp-marks-input');
            const name = nameInput.value;
            const maxMarks = maxMarksInput.value;
            const idx = name.replace(/\s+/g, '-').toLowerCase();

            const html = `
                <div class="col-6 mb-2">
                    <label class="form-label small mb-1">${name} (max ${maxMarks})</label>
                    <input type="number" class="form-control form-control-sm sim-comp-input" data-max="${maxMarks}" value="${parseFloat(maxMarks) * 0.8}" oninput="runSimulation()">
                </div>
            `;
            inputsContainer.insertAdjacentHTML('beforeend', html);
        }
    }

    function runSimulation() {
        // Collect class scores
        const inputs = document.getElementsByClassName('sim-comp-input');
        let rawSbaTotal = 0;
        for (let input of inputs) {
            const max = parseFloat(input.getAttribute('data-max'));
            let val = parseFloat(input.value) || 0;
            if (val > max) val = max; // cap at max
            rawSbaTotal += val;
        }

        const classMax = parseFloat(document.getElementById('class_score_max').value) || 1.0;
        const classWeight = parseFloat(document.getElementById('class_score_weight').value) || 0;
        const examMax = parseFloat(document.getElementById('exam_score_max').value) || 1.0;
        const examWeight = parseFloat(document.getElementById('exam_score_weight').value) || 0;
        const roundingMethod = document.getElementById('rounding_method').value;
        const decimals = parseInt(document.getElementById('decimal_places').value);

        // Class calculation
        let scaledSba = (rawSbaTotal / classMax) * classWeight;
        scaledSba = applyRounding(scaledSba, roundingMethod, decimals);

        // Exam calculation
        const rawExam = parseFloat(document.getElementById('preview_exam_input').value) || 0;
        let scaledExam = (rawExam / examMax) * examWeight;
        scaledExam = applyRounding(scaledExam, roundingMethod, decimals);

        // Grand total
        let grand = scaledSba + scaledExam;
        grand = applyRounding(grand, roundingMethod, decimals);

        // Render UI
        document.getElementById('sim_raw_sba').textContent = `${rawSbaTotal.toFixed(2)} / ${classMax}`;
        document.getElementById('sim_scaled_sba').textContent = `${scaledSba.toFixed(decimals)}%`;
        document.getElementById('sim_scaled_sba_formula').textContent = `Formula: (${rawSbaTotal.toFixed(2)} / ${classMax}) * ${classWeight}%`;

        document.getElementById('sim_scaled_exam').textContent = `${scaledExam.toFixed(decimals)}%`;
        document.getElementById('sim_scaled_exam_formula').textContent = `Formula: (${rawExam} / ${examMax}) * ${examWeight}%`;

        document.getElementById('sim_grand_total').textContent = `${grand.toFixed(decimals)}%`;
    }

    function applyRounding(val, method, decimals) {
        const mult = Math.pow(10, decimals);
        if (method === 'FLOOR') {
            return Math.floor(val * mult) / mult;
        } else if (method === 'CEIL') {
            return Math.ceil(val * mult) / mult;
        } else {
            return Math.round(val * mult) / mult;
        }
    }
</script>
@endsection
