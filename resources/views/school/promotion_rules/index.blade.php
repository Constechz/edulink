@extends('layouts.app')

@section('title', 'Promotion Configurations | EduLink')
@section('header_title', 'Promotion Configurations')

@section('styles')
<style>
    .rules-card {
        border-radius: 16px;
        border: 1px solid var(--border-color);
        background: var(--card-bg);
        transition: all 0.3s ease;
    }
    .badge-method {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.3rem 0.6rem;
        border-radius: 8px;
    }
    .badge-method.annual_average {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }
    .badge-method.two_of_three {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }
    .badge-method.subject_pass_count {
        background-color: rgba(111, 66, 193, 0.1);
        color: #6f42c1;
    }
    .badge-scope {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        border-radius: 6px;
        padding: 0.25rem 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.settings') }}" class="text-decoration-none">Settings</a></li>
            <li class="breadcrumb-item active" aria-current="page">Promotion Rules</li>
        </ol>
    </nav>

    <!-- Success/Error Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Configuration Error</h6>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4 text-white rounded-4" style="background: linear-gradient(135deg, #0b1a30 0%, #1e3a68 100%); border: 1px solid rgba(255,255,255,0.05);">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h4 class="fw-bold mb-2">Dynamic Promotion Engine Setup</h4>
                        <p class="mb-0 text-white-70 small">
                            Define level-wise or class-specific academic promotion thresholds and calculation methodologies.
                            These settings will drive recommendations automatically in the rollover promotion wizard.
                        </p>
                    </div>
                    <button class="btn btn-warning py-2.5 px-4 rounded-3 fw-bold shadow-xs text-dark" data-bs-toggle="modal" data-bs-target="#createRuleModal">
                        <i class="bi bi-plus-circle me-1"></i> Add Promotion Rule
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Configurations List -->
    <div class="row">
        <div class="col-12">
            <div class="rules-card p-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-sliders text-primary me-2"></i>Active Rule Policies</h5>
                
                @if($configs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-0">
                            <thead>
                                <tr class="bg-light">
                                    <th class="border-0 rounded-start text-secondary small fw-bold">Level / Scope</th>
                                    <th class="border-0 text-secondary small fw-bold">Method</th>
                                    <th class="border-0 text-secondary small fw-bold text-center">Promo Threshold</th>
                                    <th class="border-0 text-secondary small fw-bold text-center">Cond. Threshold</th>
                                    <th class="border-0 text-secondary small fw-bold text-center">Term Weights</th>
                                    <th class="border-0 text-secondary small fw-bold text-center">Subject Pass Criteria</th>
                                    <th class="border-0 text-secondary small fw-bold text-center">Exclude Terminal Yr</th>
                                    <th class="border-0 rounded-end text-secondary small fw-bold text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($configs as $config)
                                    <tr style="border-bottom: 1px solid var(--border-color);">
                                        <td class="py-3">
                                            <span class="d-block fw-bold text-dark text-capitalize mb-0">{{ $config->level }}</span>
                                            @if($config->class)
                                                <span class="badge bg-info-subtle text-info badge-scope">Class: {{ $config->class->name }}</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary badge-scope">Level-Wide</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge-method {{ $config->method }}">
                                                {{ str_replace('_', ' ', ucwords($config->method, '_')) }}
                                            </span>
                                        </td>
                                        <td class="text-center fw-semibold text-dark">{{ $config->promotion_threshold }}%</td>
                                        <td class="text-center text-muted">
                                            {{ $config->conditional_threshold ? $config->conditional_threshold . '%' : 'N/A' }}
                                        </td>
                                        <td class="text-center small">
                                            T1: <strong>{{ $config->term_weights_json['term1'] ?? 1 }}</strong> |
                                            T2: <strong>{{ $config->term_weights_json['term2'] ?? 1 }}</strong> |
                                            T3: <strong>{{ $config->term_weights_json['term3'] ?? 1 }}</strong>
                                        </td>
                                        <td class="text-center small">
                                            @if($config->method === 'subject_pass_count')
                                                Min: <strong>{{ $config->min_subjects_to_pass }}</strong> subjs<br>
                                                Pass Mark: <strong>{{ $config->per_subject_pass_mark }}%</strong>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($config->exclude_terminal_year)
                                                <span class="badge bg-success-subtle text-success">Yes</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">No</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-2">
                                                <button class="btn btn-sm btn-outline-primary rounded-3" data-bs-toggle="modal" data-bs-target="#editRuleModal{{ $config->id }}">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                <form action="{{ route('school.settings.promotions.destroy', $config->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this promotion rule? Fallback default rules will apply.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Edit Configuration Modal -->
                                    <div class="modal fade" id="editRuleModal{{ $config->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 rounded-4 shadow-sm bg-body">
                                                <div class="modal-header border-bottom-0 p-4 pb-0">
                                                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Promotion Configuration ({{ ucfirst($config->level) }})</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('school.settings.promotions.update', $config->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body p-4">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold text-secondary">Method / Logic</label>
                                                                <select name="method" class="form-select rounded-3 py-2" onchange="toggleEditFields(this, '{{ $config->id }}')" required>
                                                                    <option value="annual_average" {{ $config->method == 'annual_average' ? 'selected' : '' }}>Annual Average</option>
                                                                    <option value="two_of_three" {{ $config->method == 'two_of_three' ? 'selected' : '' }}>Best 2 of 3 Terms</option>
                                                                    <option value="subject_pass_count" {{ $config->method == 'subject_pass_count' ? 'selected' : '' }}>Subject Pass Count</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold text-secondary">Maximum Repeats allowed</label>
                                                                <input type="number" name="repeat_limit" class="form-select rounded-3 py-2" value="{{ $config->repeat_limit }}" min="1" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold text-secondary">Promotion Passing Threshold (%)</label>
                                                                <input type="number" step="0.01" name="promotion_threshold" class="form-control rounded-3 py-2" value="{{ $config->promotion_threshold }}" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold text-secondary">Conditional Threshold (%) - Optional</label>
                                                                <input type="number" step="0.01" name="conditional_threshold" class="form-control rounded-3 py-2" value="{{ $config->conditional_threshold }}">
                                                            </div>

                                                            <!-- Weights Section -->
                                                            <div class="col-12 mt-4">
                                                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-bar-chart-steps text-secondary me-2"></i>Term Weightings</h6>
                                                                <div class="row g-2">
                                                                    <div class="col-4">
                                                                        <label class="form-label small text-muted">Term 1 Weight</label>
                                                                        <input type="number" step="0.1" name="term_weights_term1" class="form-control rounded-3" value="{{ $config->term_weights_json['term1'] ?? 1 }}" required>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label class="form-label small text-muted">Term 2 Weight</label>
                                                                        <input type="number" step="0.1" name="term_weights_term2" class="form-control rounded-3" value="{{ $config->term_weights_json['term2'] ?? 1 }}" required>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label class="form-label small text-muted">Term 3 Weight</label>
                                                                        <input type="number" step="0.1" name="term_weights_term3" class="form-control rounded-3" value="{{ $config->term_weights_json['term3'] ?? 1 }}" required>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Subject Specifics (Method 3) -->
                                                            <div class="col-12 edit-subject-fields-{{ $config->id }}" style="{{ $config->method !== 'subject_pass_count' ? 'display: none;' : '' }}">
                                                                <h6 class="fw-bold text-dark mt-3 mb-2"><i class="bi bi-journal-check text-secondary me-2"></i>Subject-Wise Pass Requirements</h6>
                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label small text-muted">Per-Subject Pass Score (%)</label>
                                                                        <input type="number" step="0.01" name="per_subject_pass_mark" class="form-control rounded-3" value="{{ $config->per_subject_pass_mark }}">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label small text-muted">Minimum Subjects to Pass</label>
                                                                        <input type="number" name="min_subjects_to_pass" class="form-control rounded-3" value="{{ $config->min_subjects_to_pass }}">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-12 mt-3">
                                                                <div class="form-check form-switch py-2">
                                                                    <input class="form-check-input" type="checkbox" name="exclude_terminal_year" value="1" id="excludeTerminalEdit{{ $config->id }}" {{ $config->exclude_terminal_year ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-bold small text-secondary" for="excludeTerminalEdit{{ $config->id }}">
                                                                        Exclude BECE/WASSCE candidate classes from internal rollover recommendations
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0 p-4 pt-0">
                                                        <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary rounded-3 px-4">Update Configuration</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-gear-wide-connected fs-1 d-block mb-3 opacity-30 text-secondary"></i>
                        <h6 class="fw-bold">No Custom Configurations Found</h6>
                        <p class="small mb-0">System fallback defaults will apply automatically (Annual Average method, 40% pass threshold).</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Create Configuration Modal -->
<div class="modal fade" id="createRuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-sm bg-body">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-plus-circle-fill text-warning me-2"></i>Create New Promotion Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('school.settings.promotions.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Level Class Scope</label>
                            <select name="level" class="form-select rounded-3 py-2" required>
                                <option value="primary">Primary</option>
                                <option value="jhs">JHS</option>
                                <option value="shs">SHS</option>
                                <option value="nursery">Nursery</option>
                                <option value="kg">KG</option>
                                <option value="tertiary">Tertiary</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Class Override (Optional)</label>
                            <select name="class_id" class="form-select rounded-3 py-2">
                                <option value="">-- Apply Level-Wide --</option>
                                @foreach($classes as $cls)
                                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Method / Logic</label>
                            <select name="method" class="form-select rounded-3 py-2" id="createMethodSelect" onchange="toggleCreateFields(this)" required>
                                <option value="annual_average" selected>Annual Average</option>
                                <option value="two_of_three">Best 2 of 3 Terms</option>
                                <option value="subject_pass_count">Subject Pass Count</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Maximum Repeats allowed</label>
                            <input type="number" name="repeat_limit" class="form-control rounded-3 py-2" value="1" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Promotion Passing Threshold (%)</label>
                            <input type="number" step="0.01" name="promotion_threshold" class="form-control rounded-3 py-2" value="40.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Conditional Threshold (%) - Optional</label>
                            <input type="number" step="0.01" name="conditional_threshold" class="form-control rounded-3 py-2" value="35.00">
                        </div>

                        <!-- Weights Section -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-bar-chart-steps text-secondary me-2"></i>Term Weightings</h6>
                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="form-label small text-muted">Term 1 Weight</label>
                                    <input type="number" step="0.1" name="term_weights_term1" class="form-control rounded-3" value="1.0" required>
                                </div>
                                <div class="col-4">
                                    <label class="form-label small text-muted">Term 2 Weight</label>
                                    <input type="number" step="0.1" name="term_weights_term2" class="form-control rounded-3" value="1.0" required>
                                </div>
                                <div class="col-4">
                                    <label class="form-label small text-muted">Term 3 Weight</label>
                                    <input type="number" step="0.1" name="term_weights_term3" class="form-control rounded-3" value="1.0" required>
                                </div>
                            </div>
                        </div>

                        <!-- Subject Specifics (Method 3) -->
                        <div class="col-12" id="createSubjectFields" style="display: none;">
                            <h6 class="fw-bold text-dark mt-3 mb-2"><i class="bi bi-journal-check text-secondary me-2"></i>Subject-Wise Pass Requirements</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Per-Subject Pass Score (%)</label>
                                    <input type="number" step="0.01" name="per_subject_pass_mark" class="form-control rounded-3" value="40.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Minimum Subjects to Pass</label>
                                    <input type="number" name="min_subjects_to_pass" class="form-control rounded-3" value="6">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="form-check form-switch py-2">
                                <input class="form-check-input" type="checkbox" name="exclude_terminal_year" value="1" id="excludeTerminalCreate" checked>
                                <label class="form-check-label fw-bold small text-secondary" for="excludeTerminalCreate">
                                    Exclude BECE/WASSCE candidate classes from internal rollover recommendations
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4 fw-bold">Create Configuration</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleCreateFields(selectElement) {
        var fieldsDiv = document.getElementById('createSubjectFields');
        if (selectElement.value === 'subject_pass_count') {
            fieldsDiv.style.display = 'block';
        } else {
            fieldsDiv.style.display = 'none';
        }
    }

    function toggleEditFields(selectElement, configId) {
        var fieldsDiv = document.querySelector('.edit-subject-fields-' + configId);
        if (selectElement.value === 'subject_pass_count') {
            fieldsDiv.style.display = 'block';
        } else {
            fieldsDiv.style.display = 'none';
        }
    }
</script>
@endsection
