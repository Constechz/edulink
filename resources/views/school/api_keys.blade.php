@extends('layouts.app')

@section('title', 'Developer Portal & API Reference | EduLink')
@section('header_title', 'Developer Portal & API Reference')

@section('content')
<div class="container-fluid p-0">
    <!-- Session Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show glass-card p-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Plain Key Display (One-time flash) -->
    @if(session('plain_token'))
        <div class="alert alert-warning border-start border-4 border-warning glass-card p-4 mb-4" role="alert">
            <h5 class="fw-bold text-dark"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Copy Your New API Key</h5>
            <p class="text-muted small">For security reasons, this key will only be shown to you once. If you lose it, you will need to revoke it and generate a new one.</p>
            <div class="input-group my-3 shadow-sm rounded-3 overflow-hidden">
                <input type="text" id="plainTokenInput" class="form-control border-0 bg-white py-3 fw-mono text-dark" readonly value="{{ session('plain_token') }}">
                <button class="btn btn-dark px-4" type="button" onclick="copyToken()">
                    <i class="bi bi-clipboard me-2"></i>Copy
                </button>
            </div>
            <span id="copySuccessMsg" class="text-success small d-none"><i class="bi bi-check2-circle me-1"></i>Copied to clipboard!</span>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="card border-0 shadow-sm glass-card mb-4">
        <div class="card-header bg-transparent border-0 p-3 pb-0">
            <ul class="nav nav-tabs border-0" id="apiTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold text-primary py-2.5 px-4 border-0 d-flex align-items-center" id="keys-tab" data-bs-toggle="tab" data-bs-target="#keys-content" type="button" role="tab" aria-controls="keys-content" aria-selected="true">
                        <i class="bi bi-key-fill me-2"></i>API Credentials
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-secondary py-2.5 px-4 border-0 d-flex align-items-center" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs-content" type="button" role="tab" aria-controls="docs-content" aria-selected="false">
                        <i class="bi bi-file-earmark-code-fill me-2"></i>Interactive API Reference
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="apiTabContent">
        <!-- Tab 1: API Keys Credentials Setup -->
        <div class="tab-pane fade show active" id="keys-content" role="tabpanel" aria-labelledby="keys-tab">
            <div class="row g-4">
                <!-- Generate Key Form -->
                <div class="col-md-4">
                    <div class="glass-card p-4">
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-plus-circle text-primary me-2"></i>Generate API Key</h5>
                        <form action="{{ route('school.api-keys.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold text-secondary">Key Description / Name</label>
                                <input type="text" class="form-control rounded-3 py-2 border-light shadow-xs" id="name" name="name" required placeholder="e.g., LMS Integration Key">
                            </div>
                            <div class="mb-4">
                                <label for="expires_at" class="form-label fw-semibold text-secondary">Expiration Date (Optional)</label>
                                <input type="date" class="form-control rounded-3 py-2 border-light shadow-xs" id="expires_at" name="expires_at">
                                <div class="form-text small text-muted">Leave empty for a key that never expires.</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                                <i class="bi bi-plus-lg me-2"></i>Generate Key
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Key Listing -->
                <div class="col-md-8">
                    <div class="glass-card p-4">
                        <h5 class="fw-bold text-dark mb-4"><i class="bi bi-shield-lock-fill text-success me-2"></i>Active Credentials</h5>
                        @if($keys->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-key fs-1 d-block text-secondary mb-2"></i>
                                <span>No API keys have been generated for this school yet.</span>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Token Hash (SHA256)</th>
                                            <th>Expires At</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($keys as $key)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-dark d-block">{{ $key->name }}</span>
                                                    <span class="text-muted small" style="font-size: 0.75rem;">Created: {{ $key->created_at->format('M d, Y') }}</span>
                                                </td>
                                                <td>
                                                    <code class="small text-secondary">{{ substr($key->token_hash, 0, 10) }}...{{ substr($key->token_hash, -10) }}</code>
                                                </td>
                                                <td class="small text-muted">
                                                    @if($key->expires_at)
                                                        {{ \Carbon\Carbon::parse($key->expires_at)->format('M d, Y') }}
                                                    @else
                                                        <span class="text-success fw-medium">Never Expires</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!$key->is_active)
                                                        <span class="badge bg-danger">Suspended</span>
                                                    @elseif($key->expires_at && \Carbon\Carbon::parse($key->expires_at)->isPast())
                                                        <span class="badge bg-secondary">Expired</span>
                                                    @else
                                                        <span class="badge bg-success">Active</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form action="{{ route('school.api-keys.destroy', $key->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to revoke this API key? This cannot be undone.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 px-3">
                                                            <i class="bi bi-trash3 me-1"></i>Revoke
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Interactive API Reference Documentation -->
        <div class="tab-pane fade" id="docs-content" role="tabpanel" aria-labelledby="docs-tab">
            <div class="row g-4">
                
                <!-- Endpoint list sidebar -->
                <div class="col-lg-3">
                    <div class="glass-card p-3 position-sticky" style="top: 100px;">
                        <h6 class="fw-bold mb-3 text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">API Overview</h6>
                        <div class="nav flex-column nav-pills" id="docs-endpoints-tab" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active text-start py-2.5 px-3 mb-1 small" id="endpoint-intro-tab" data-bs-toggle="pill" data-bs-target="#endpoint-intro" type="button" role="tab">
                                Introduction
                            </button>
                            
                            <h6 class="fw-bold mt-3 mb-2 text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Authentication</h6>
                            <button class="nav-link text-start py-2.5 px-3 mb-1 small d-flex align-items-center" id="endpoint-login-tab" data-bs-toggle="pill" data-bs-target="#endpoint-login" type="button" role="tab">
                                <span class="badge bg-success me-2" style="font-size: 0.65rem;">POST</span> login
                            </button>
                            
                            <h6 class="fw-bold mt-3 mb-2 text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Students Registry</h6>
                            <button class="nav-link text-start py-2.5 px-3 mb-1 small d-flex align-items-center" id="endpoint-students-get-tab" data-bs-toggle="pill" data-bs-target="#endpoint-students-get" type="button" role="tab">
                                <span class="badge bg-primary me-2" style="font-size: 0.65rem;">GET</span> list students
                            </button>
                            <button class="nav-link text-start py-2.5 px-3 mb-1 small d-flex align-items-center" id="endpoint-students-post-tab" data-bs-toggle="pill" data-bs-target="#endpoint-students-post" type="button" role="tab">
                                <span class="badge bg-success me-2" style="font-size: 0.65rem;">POST</span> create student
                            </button>
                            <button class="nav-link text-start py-2.5 px-3 mb-1 small d-flex align-items-center" id="endpoint-students-show-tab" data-bs-toggle="pill" data-bs-target="#endpoint-students-show" type="button" role="tab">
                                <span class="badge bg-primary me-2" style="font-size: 0.65rem;">GET</span> get student
                            </button>

                            <h6 class="fw-bold mt-3 mb-2 text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Scoring Engine</h6>
                            <button class="nav-link text-start py-2.5 px-3 mb-1 small d-flex align-items-center" id="endpoint-configs-get-tab" data-bs-toggle="pill" data-bs-target="#endpoint-configs-get" type="button" role="tab">
                                <span class="badge bg-primary me-2" style="font-size: 0.65rem;">GET</span> configurations
                            </button>
                            <button class="nav-link text-start py-2.5 px-3 mb-1 small d-flex align-items-center" id="endpoint-scores-bulk-tab" data-bs-toggle="pill" data-bs-target="#endpoint-scores-bulk" type="button" role="tab">
                                <span class="badge bg-success me-2" style="font-size: 0.65rem;">POST</span> bulk score updates
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Endpoint Documentation Panels -->
                <div class="col-lg-9">
                    <div class="glass-card p-4">
                        <div class="tab-content" id="docs-endpoints-tabContent">
                            
                            <!-- Introduction Panel -->
                            <div class="tab-pane fade show active" id="endpoint-intro" role="tabpanel">
                                <h4 class="fw-bold text-primary mb-3">EduLink REST API Integration Guide</h4>
                                <p class="text-muted">The EduLink API allows you to programmatically manage student rosters, integrate third-party LMS portals, synchronize class schedules, and upload continuous assessment scores in real time.</p>
                                
                                <div class="bg-light p-3 border rounded-3 mb-4">
                                    <h6 class="fw-bold mb-1">Base Endpoint Environment</h6>
                                    <code class="fs-6 text-dark font-monospace">https://api.edulink.edu.gh/v1</code>
                                </div>

                                <h5 class="fw-bold text-secondary mt-4">API Bearer Authorization</h5>
                                <p class="text-muted small">To authorize your HTTP requests, add your generated API Key to the request headers using the standard bearer scheme:</p>
                                <pre class="bg-dark text-light p-3 rounded-3" style="font-size: 0.85rem;"><code>Authorization: Bearer YOUR_GENERATED_API_KEY</code></pre>
                            </div>

                            <!-- POST Auth Login -->
                            <div class="tab-pane fade" id="endpoint-login" role="tabpanel">
                                <span class="badge bg-success mb-2 py-1.5 px-3 fs-7">POST</span>
                                <h4 class="fw-bold text-dark font-monospace mb-3">/auth/login</h4>
                                <p class="text-muted">Generate a Sanctum Bearer token dynamically using standard credentials. Useful for mobile integrations or third-party gateways.</p>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h6 class="fw-bold text-secondary">JSON Request Body</h6>
                                        <pre class="bg-dark text-light p-3 rounded-3" style="font-size: 0.8rem;"><code>{
  "email": "teacher@legacy.edu.gh",
  "password": "password123",
  "school_code": "LAAC"
}</code></pre>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6 class="fw-bold text-secondary">JSON Success Response (200)</h6>
                                        <pre class="bg-dark text-light p-3 rounded-3" style="font-size: 0.8rem;"><code>{
  "success": true,
  "token": "sanctum_session_token_xyz...",
  "user": {
    "id": 15,
    "name": "Teacher Asante",
    "email": "teacher@legacy.edu.gh"
  }
}</code></pre>
                                    </div>
                                </div>
                            </div>

                            <!-- GET Students -->
                            <div class="tab-pane fade" id="endpoint-students-get" role="tabpanel">
                                <span class="badge bg-primary mb-2 py-1.5 px-3 fs-7">GET</span>
                                <h4 class="fw-bold text-dark font-monospace mb-3">/students</h4>
                                <p class="text-muted">Retrieve a paginated roster of active students scoped to your school. Supports searching by classroom and status.</p>

                                <h6 class="fw-bold text-secondary">Query String Parameters</h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Parameter</th>
                                                <th>Type</th>
                                                <th>Required</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><code>class_id</code></td>
                                                <td>Integer</td>
                                                <td>No</td>
                                                <td>Filter list by a specific class level</td>
                                            </tr>
                                            <tr>
                                                <td><code>search</code></td>
                                                <td>String</td>
                                                <td>No</td>
                                                <td>Fuzzy search matches on student names or ID numbers</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="fw-bold text-secondary">Success Response (200)</h6>
                                        <pre class="bg-dark text-light p-3 rounded-3" style="font-size: 0.8rem;"><code>{
  "success": true,
  "data": [
    {
      "id": 105,
      "student_id_number": "ATA-2026-004",
      "first_name": "Kofi",
      "last_name": "Mensah",
      "gender": "Male",
      "current_class_id": 3
    }
  ]
}</code></pre>
                                    </div>
                                </div>
                            </div>

                            <!-- POST Students -->
                            <div class="tab-pane fade" id="endpoint-students-post" role="tabpanel">
                                <span class="badge bg-success mb-2 py-1.5 px-3 fs-7">POST</span>
                                <h4 class="fw-bold text-dark font-monospace mb-3">/students</h4>
                                <p class="text-muted">Programmatically register a new student profile in the school registry system.</p>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h6 class="fw-bold text-secondary">JSON Request Body</h6>
                                        <pre class="bg-dark text-light p-3 rounded-3" style="font-size: 0.8rem;"><code>{
  "first_name": "Ama",
  "last_name": "Serwaa",
  "gender": "Female",
  "date_of_birth": "2015-05-20",
  "class_id": 4
}</code></pre>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6 class="fw-bold text-secondary">JSON Success Response (201)</h6>
                                        <pre class="bg-dark text-light p-3 rounded-3" style="font-size: 0.8rem;"><code>{
  "success": true,
  "message": "Student created successfully.",
  "data": {
    "id": 108,
    "student_id_number": "ATA-2026-005",
    "first_name": "Ama",
    "last_name": "Serwaa",
    "class_id": 4
  }
}</code></pre>
                                    </div>
                                </div>
                            </div>

                            <!-- GET Student Details -->
                            <div class="tab-pane fade" id="endpoint-students-show" role="tabpanel">
                                <span class="badge bg-primary mb-2 py-1.5 px-3 fs-7">GET</span>
                                <h4 class="fw-bold text-dark font-monospace mb-3">/students/{id}</h4>
                                <p class="text-muted">Retrieve detailed profile data for a specific student, including parent allocations and active classes.</p>

                                <h6 class="fw-bold text-secondary">Success Response (200)</h6>
                                <pre class="bg-dark text-light p-3 rounded-3" style="font-size: 0.8rem;"><code>{
  "success": true,
  "data": {
    "id": 105,
    "student_id_number": "ATA-2026-004",
    "first_name": "Kofi",
    "last_name": "Mensah",
    "gender": "Male",
    "date_of_birth": "2014-03-12",
    "class_id": 3,
    "guardians": [
      {
        "name": "Kwame Mensah",
        "relationship": "Father",
        "phone": "+233201112223"
      }
    ]
  }
}</code></pre>
                            </div>

                            <!-- GET Scoring Configs -->
                            <div class="tab-pane fade" id="endpoint-configs-get" role="tabpanel">
                                <span class="badge bg-primary mb-2 py-1.5 px-3 fs-7">GET</span>
                                <h4 class="fw-bold text-dark font-monospace mb-3">/scoring/configurations</h4>
                                <p class="text-muted">Retrieve standard SBA scoring components and weight rules configured for different academic tiers.</p>

                                <h6 class="fw-bold text-secondary">Success Response (200)</h6>
                                <pre class="bg-dark text-light p-3 rounded-3" style="font-size: 0.8rem;"><code>{
  "success": true,
  "data": [
    {
      "id": 1,
      "level": "JHS",
      "name": "GES Standard JHS",
      "class_score_max": 50.00,
      "class_score_weight": 50.00,
      "exam_score_max": 100.00,
      "exam_score_weight": 50.00,
      "components": [
        { "id": 11, "name": "Classwork", "max_marks": 20 },
        { "id": 12, "name": "Homework", "max_marks": 10 },
        { "id": 13, "name": "Project", "max_marks": 20 }
      ]
    }
  ]
}</code></pre>
                            </div>

                            <!-- POST Bulk Scores -->
                            <div class="tab-pane fade" id="endpoint-scores-bulk" role="tabpanel">
                                <span class="badge bg-success mb-2 py-1.5 px-3 fs-7">POST</span>
                                <h4 class="fw-bold text-dark font-monospace mb-3">/scoring/scores/bulk</h4>
                                <p class="text-muted">Publish assessment marks for multiple students in a single transaction. Validates all inputs against configured max marks ranges before saving.</p>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h6 class="fw-bold text-secondary">JSON Request Body</h6>
                                        <pre class="bg-dark text-light p-3 rounded-3" style="font-size: 0.8rem;"><code>{
  "class_id": 3,
  "subject_id": 10,
  "term_id": 2,
  "academic_year_id": 1,
  "scores": [
    {
      "student_id": 105,
      "component_scores": {
        "11": 18,
        "12": 9,
        "13": 17
      },
      "raw_exam_score": 82
    },
    {
      "student_id": 106,
      "component_scores": {
        "11": 15,
        "12": 8,
        "13": 14
      },
      "raw_exam_score": 74
    }
  ]
}</code></pre>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6 class="fw-bold text-secondary">JSON Success Response (200)</h6>
                                        <pre class="bg-dark text-light p-3 rounded-3" style="font-size: 0.8rem;"><code>{
  "success": true,
  "message": "Successfully synchronized bulk continuous assessment marks.",
  "saved_records_count": 2
}</code></pre>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyToken() {
        const copyText = document.getElementById("plainTokenInput");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value).then(() => {
            const successMsg = document.getElementById("copySuccessMsg");
            successMsg.classList.remove("d-none");
            setTimeout(() => {
                successMsg.classList.add("d-none");
            }, 3000);
        });
    }
</script>
@endsection
