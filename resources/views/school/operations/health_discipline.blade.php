@extends('layouts.app')

@section('title', 'Health & Discipline | EduLink')
@section('header_title', 'Health Records & Discipline Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Success/Error Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Please check the errors below:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4" id="healthDisciplineTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-4 py-2 fw-semibold rounded-pill me-2" id="health-tab" data-bs-toggle="tab" data-bs-target="#health-pane" type="button" role="tab" aria-controls="health-pane" aria-selected="true">
                <i class="bi bi-heart-pulse me-2"></i>Clinic & Health Visits
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 fw-semibold rounded-pill" id="discipline-tab" data-bs-toggle="tab" data-bs-target="#discipline-pane" type="button" role="tab" aria-controls="discipline-pane" aria-selected="false">
                <i class="bi bi-shield-exclamation me-2"></i>Discipline Registry
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="healthDisciplineTabsContent">
        
        <!-- Health visits tab pane -->
        <div class="tab-pane fade show active" id="health-pane" role="tabpanel" aria-labelledby="health-tab" tabindex="0">
            <div class="row g-4">
                
                <!-- Log visit form -->
                <div class="col-lg-4">
                    <div class="glass-card p-4">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-plus-circle me-2 text-primary"></i>Log Clinic Visit</h5>
                        <form action="{{ route('school.operations.health.visit.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="student_id_health" class="form-label small fw-semibold">Select Student</label>
                                <select class="form-select rounded-3" id="student_id_health" name="student_id" required>
                                    <option value="" disabled selected>Select Student...</option>
                                    @foreach($students as $st)
                                        <option value="{{ $st->id }}">{{ $st->first_name }} {{ $st->last_name }} ({{ $st->student_id_number }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="visit_date" class="form-label small fw-semibold">Visit Date & Time</label>
                                <input type="datetime-local" class="form-control rounded-3" id="visit_date" name="visit_date" value="{{ date('Y-m-d\TH:i') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="symptoms" class="form-label small fw-semibold">Symptoms / Complaints</label>
                                <textarea class="form-control rounded-3" id="symptoms" name="symptoms" rows="3" placeholder="e.g. Headache, body weakness, feverishness..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="diagnosis" class="form-label small fw-semibold">Diagnosis (Optional)</label>
                                <input type="text" class="form-control rounded-3" id="diagnosis" name="diagnosis" placeholder="e.g. Suspected malaria, general fatigue...">
                            </div>

                            <div class="mb-3">
                                <label for="treatment" class="form-label small fw-semibold">Treatment / Action Taken (Optional)</label>
                                <textarea class="form-control rounded-3" id="treatment" name="treatment" rows="2" placeholder="e.g. Given paracetamol 500mg, rest for 1 hour..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                                <i class="bi bi-check-circle me-1"></i>Save Health Log
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Visits List -->
                <div class="col-lg-8">
                    <div class="glass-card p-4">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-file-medical me-2 text-primary"></i>Recent Clinic Attendance</h5>
                        @if($healthVisits->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-journal-medical fs-1 d-block mb-3 opacity-50"></i>
                                <p class="mb-0">No health visits recorded for this school term yet.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>Date & Time</th>
                                            <th>Symptoms</th>
                                            <th>Diagnosis</th>
                                            <th>Treatment</th>
                                            <th>Logged By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($healthVisits as $visit)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $visit->student->first_name }} {{ $visit->student->last_name }}</div>
                                                    <span class="text-muted small">{{ $visit->student->student_id_number }}</span>
                                                </td>
                                                <td>
                                                    <span class="small">{{ date('M d, Y h:i A', strtotime($visit->visit_date)) }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-dark small d-inline-block text-truncate" style="max-width: 150px;" title="{{ $visit->symptoms }}">{{ $visit->symptoms }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info text-dark fw-medium">{{ $visit->diagnosis ?: 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="small text-muted">{{ $visit->treatment ?: 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="small text-muted">{{ $visit->recorder ? $visit->recorder->name : 'System' }}</span>
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

        <!-- Discipline Registry tab pane -->
        <div class="tab-pane fade" id="discipline-pane" role="tabpanel" aria-labelledby="discipline-tab" tabindex="0">
            <div class="row g-4">
                
                <!-- Log case form -->
                <div class="col-lg-4">
                    <div class="glass-card p-4">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-shield-fill-plus me-2 text-danger"></i>Report Disciplinary Case</h5>
                        <form action="{{ route('school.operations.discipline.case.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="student_id_discipline" class="form-label small fw-semibold">Select Student</label>
                                <select class="form-select rounded-3" id="student_id_discipline" name="student_id" required>
                                    <option value="" disabled selected>Select Student...</option>
                                    @foreach($students as $st)
                                        <option value="{{ $st->id }}">{{ $st->first_name }} {{ $st->last_name }} ({{ $st->student_id_number }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="incident_date" class="form-label small fw-semibold">Incident Date</label>
                                <input type="date" class="form-control rounded-3" id="incident_date" name="incident_date" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label small fw-semibold">Severity Category</label>
                                <select class="form-select rounded-3" id="category" name="category" required>
                                    <option value="minor" selected>Minor Misconduct</option>
                                    <option value="major">Major Infraction</option>
                                    <option value="critical">Critical/Severe Breach</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label small fw-semibold">Details / Description</label>
                                <textarea class="form-control rounded-3" id="description" name="description" rows="4" placeholder="Detailed breakdown of the incident, rules breached, and immediate statements..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-danger w-100 rounded-3 py-2 fw-semibold">
                                <i class="bi bi-exclamation-octagon me-1"></i>File Incident Report
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Cases List -->
                <div class="col-lg-8">
                    <div class="glass-card p-4">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-shield-fill-exclamation me-2 text-danger"></i>Discipline Case Registry</h5>
                        @if($disciplineCases->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-shield-check fs-1 d-block mb-3 text-success opacity-75"></i>
                                <p class="mb-0">Excellent! There are no disciplinary cases logged for this term.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>Incident Date</th>
                                            <th>Severity</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Reported By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($disciplineCases as $case)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $case->student->first_name }} {{ $case->student->last_name }}</div>
                                                    <span class="text-muted small">{{ $case->student->student_id_number }}</span>
                                                </td>
                                                <td>
                                                    <span class="small">{{ date('M d, Y', strtotime($case->incident_date)) }}</span>
                                                </td>
                                                <td>
                                                    @if($case->category === 'critical')
                                                        <span class="badge bg-danger text-white fw-bold">Critical</span>
                                                    @elseif($case->category === 'major')
                                                        <span class="badge bg-warning text-dark fw-semibold">Major</span>
                                                    @else
                                                        <span class="badge bg-secondary text-dark fw-medium">Minor</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="small d-inline-block text-truncate" style="max-width: 200px;" title="{{ $case->description }}">{{ $case->description }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-muted border text-capitalize small">{{ $case->status }}</span>
                                                </td>
                                                <td>
                                                    <span class="small text-muted">{{ $case->reporter ? $case->reporter->name : 'System' }}</span>
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

    </div>
</div>
@endsection
