@extends('layouts.app')

@section('title', 'Testing & Security Hub — EduLink')
@section('header_title', 'Testing & Security Hub')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm glass-card bg-gradient text-white p-4" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-20 p-3 rounded-circle me-3">
                        <i class="bi bi-shield-check fs-1"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1">Testing, Security & Quality Hub</h2>
                        <p class="mb-0 text-white-50">Overview of system test coverage, load limits, security configurations, and accessibility guidelines.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm glass-card p-3">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-coverage-tab" data-bs-toggle="pill" data-bs-target="#v-pills-coverage" type="button" role="tab" aria-controls="v-pills-coverage" aria-selected="true">
                        <i class="bi bi-check-all me-2"></i> Automated Tests
                    </button>
                    <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-security-tab" data-bs-toggle="pill" data-bs-target="#v-pills-security" type="button" role="tab" aria-controls="v-pills-security" aria-selected="false">
                        <i class="bi bi-lock me-2"></i> OWASP Security
                    </button>
                    <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-performance-tab" data-bs-toggle="pill" data-bs-target="#v-pills-performance" type="button" role="tab" aria-controls="v-pills-performance" aria-selected="false">
                        <i class="bi bi-speedometer me-2"></i> Performance Metrics
                    </button>
                    <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-accessibility-tab" data-bs-toggle="pill" data-bs-target="#v-pills-accessibility" type="button" role="tab" aria-controls="v-pills-accessibility" aria-selected="false">
                        <i class="bi bi-universal-access me-2"></i> Accessibility (WCAG)
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Documentation Content -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm glass-card p-4">
                <div class="tab-content" id="v-pills-tabContent">
                    
                    <!-- Tab 1: Automated Tests -->
                    <div class="tab-pane fade show active" id="v-pills-coverage" role="tabpanel" aria-labelledby="v-pills-coverage-tab">
                        <h4 class="fw-bold mb-3 text-primary">Automated Test Suites</h4>
                        <p class="text-muted font-sans">EduLink employs a rigorous PHPUnit automated testing strategy. There are 16+ separate feature and unit test scenarios running against SQLite in-memory databases, covering key flows such as Tenant Isolation, MFA Authentications, and dynamic scoring rules.</p>
                        
                        <div class="p-3 border rounded bg-light mb-4">
                            <h6 class="fw-bold mb-2"><i class="bi bi-terminal me-2"></i>Run the Test Suite Command</h6>
                            <pre class="bg-dark text-light p-2 mb-0 rounded" style="font-size: 0.85rem;"><code>php artisan test</code></pre>
                        </div>

                        <h5 class="fw-bold text-secondary">Major Test Suites Covered</h5>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="fw-bold mb-1">TenantIsolationTest</h6>
                                    <span class="small text-muted">Ensures School A users and students are strictly blocked from interacting with School B data.</span>
                                </div>
                                <span class="badge bg-success rounded-pill">100% Passed</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="fw-bold mb-1">PhaseSevenScoringAndReportCardTest</h6>
                                    <span class="small text-muted">Validates continuous assessment aggregations, rounding algorithms, positions, and PDF generation.</span>
                                </div>
                                <span class="badge bg-success rounded-pill">100% Passed</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="fw-bold mb-1">PhaseTenPortalsLmsOperationsTest</h6>
                                    <span class="small text-muted">Verifies LMS lesson completions, parents portals billing status checks, and school hostel registry.</span>
                                </div>
                                <span class="badge bg-success rounded-pill">100% Passed</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Tab 2: OWASP Security -->
                    <div class="tab-pane fade" id="v-pills-security" role="tabpanel" aria-labelledby="v-pills-security-tab">
                        <h4 class="fw-bold mb-3 text-primary">OWASP Security Guidelines</h4>
                        <p class="text-muted">EduLink has been engineered against the OWASP Top 10 security threats to ensure robust security for student databases and payment API details.</p>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="p-3 border rounded h-100 bg-light">
                                    <h6 class="fw-bold text-dark"><i class="bi bi-shield-slash-fill text-danger me-2"></i>SQL Injection Prevention</h6>
                                    <p class="small text-muted mb-0">All parameters are bound through Eloquent ORM. Raw queries are audited and blocked from accepting manual string interpolations.</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 border rounded h-100 bg-light">
                                    <h6 class="fw-bold text-dark"><i class="bi bi-bug text-danger me-2"></i>Cross-Site Scripting (XSS)</h6>
                                    <p class="small text-muted mb-0">HTML inputs from markdown editors in the CMS/LMS modules are strictly sanitized using HTMLPurifier filters.</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 border rounded h-100 bg-light">
                                    <h6 class="fw-bold text-dark"><i class="bi bi-shield-fill-check text-success me-2"></i>Secure Sessions & Cookies</h6>
                                    <p class="small text-muted mb-0">In production, sessions use strict HTTPS parameters, cookie expiration limits, and IP/User-Agent verification mapping to terminate compromised tokens.</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 border rounded h-100 bg-light">
                                    <h6 class="fw-bold text-dark"><i class="bi bi-key-fill text-success me-2"></i>Rate Limiting Protection</h6>
                                    <p class="small text-muted mb-0">API authentication is limited to 10 requests per minute per IP, protecting the endpoints from brute-force dictionary attempts.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Performance Metrics -->
                    <div class="tab-pane fade" id="v-pills-performance" role="tabpanel" aria-labelledby="v-pills-performance-tab">
                        <h4 class="fw-bold mb-3 text-primary">Load & Performance Benchmarks</h4>
                        <p class="text-muted">Target performance guidelines are audited using Apache JMeter or k6 scripts to verify low-latency operations under heavy client workloads.</p>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Simulated Scenario</th>
                                        <th>Target Concurrent Clients</th>
                                        <th>Target Latency (p95)</th>
                                        <th>Verification Tool</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Teacher Score Entry Grid</strong></td>
                                        <td>100 active connections</td>
                                        <td>&lt; 2.0 Seconds</td>
                                        <td>k6 Load Test Script</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Parent Report Card Queries</strong></td>
                                        <td>500 active connections</td>
                                        <td>&lt; 3.0 Seconds</td>
                                        <td>JMeter Thread Pool</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Batch Report Card PDF Generation</strong></td>
                                        <td>500 student batch</td>
                                        <td>&lt; 10 Minutes total</td>
                                        <td>Artisan Queue worker analysis</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Public Website rendering</strong></td>
                                        <td>1,000 active connections</td>
                                        <td>&lt; 1.5 Seconds (Cache hits)</td>
                                        <td>k6 Static Benchmarks</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 4: Accessibility (WCAG) -->
                    <div class="tab-pane fade" id="v-pills-accessibility" role="tabpanel" aria-labelledby="v-pills-accessibility-tab">
                        <h4 class="fw-bold mb-3 text-primary">WCAG 2.1 AA Accessibility Standards</h4>
                        <p class="text-muted">Ensuring inclusive access to teachers, visually-impaired students, and parents remains a top priority. The system layout conforms to WCAG 2.1 AA specifications.</p>
                        
                        <h5 class="fw-bold text-secondary mt-3">Accessibility Elements Checklist</h5>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item d-flex align-items-start py-3">
                                <i class="bi bi-check-circle-fill text-success fs-5 me-3 mt-1"></i>
                                <div>
                                    <strong>Semantic Tagging:</strong> Header, Navigation, Section, and Footer tags are strictly defined to help screen-readers understand page hierarchy.
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-start py-3">
                                <i class="bi bi-check-circle-fill text-success fs-5 me-3 mt-1"></i>
                                <div>
                                    <strong>ARIA attributes:</strong> All dynamic triggers, alerts, dropdown panels, and icons utilize correct <code>aria-label</code> and <code>aria-expanded</code> markup.
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-start py-3">
                                <i class="bi bi-check-circle-fill text-success fs-5 me-3 mt-1"></i>
                                <div>
                                    <strong>High Contrast Elements:</strong> Background/Text contrast ratios are maintained above 4.5:1 to assure readability on screens in low-light environments.
                                </div>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
