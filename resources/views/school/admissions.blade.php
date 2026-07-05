@extends('layouts.app')

@section('title', 'Admissions CRM Pipeline | EduLink')
@section('header_title', 'Admissions CRM Pipeline')

@section('content')
<div class="container-fluid p-0">

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <ul class="mb-0 list-unstyled">
                @foreach($errors->all() as $error)
                    <li><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Metrics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-4 text-center">
                <span class="text-muted small uppercase font-weight-bold" style="font-weight: 600; letter-spacing: 0.5px;">TOTAL APPLICANTS</span>
                <h2 class="card-metric mt-2 mb-0">{{ $applications->count() }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center">
                <span class="text-muted small uppercase font-weight-bold" style="font-weight: 600; letter-spacing: 0.5px;">UNDER REVIEW</span>
                <h2 class="card-metric mt-2 mb-0 text-warning">{{ $applications->where('status', 'reviewing')->count() }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center">
                <span class="text-muted small uppercase font-weight-bold" style="font-weight: 600; letter-spacing: 0.5px;">INTERVIEW SCHEDULED</span>
                <h2 class="card-metric mt-2 mb-0 text-info">{{ $applications->where('status', 'interview')->count() }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center">
                <span class="text-muted small uppercase font-weight-bold" style="font-weight: 600; letter-spacing: 0.5px;">APPROVED</span>
                <h2 class="card-metric mt-2 mb-0 text-success">{{ $applications->where('status', 'approved')->count() }}</h2>
            </div>
        </div>
    </div>

    <!-- Title and details -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0 font-weight-bold" style="font-weight: 700;">Admissions Application Pipeline</h5>
            <p class="text-muted mb-0 small">Review incoming public applications, inspect birth certs/transcripts, schedule interview tasks, and promote to students.</p>
        </div>
    </div>

    <!-- Applicants table list -->
    <div class="glass-card p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Candidate Name</th>
                        <th>Target Grade/Class</th>
                        <th>Guardian</th>
                        <th>Uploaded Documents</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr>
                            <td>
                                <div class="font-weight-bold" style="font-weight: 600;">{{ $app->first_name }} {{ $app->middle_name }} {{ $app->last_name }}</div>
                                <span class="text-muted small">{{ $app->gender }}, DOB: {{ $app->date_of_birth ? $app->date_of_birth->format('d M Y') : 'N/A' }}</span>
                            </td>
                            <td>
                                <div>{{ $app->class->name ?? 'N/A' }}</div>
                                <span class="text-muted small">{{ $app->campus->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div>{{ $app->guardian_name }}</div>
                                <span class="text-muted small">{{ $app->guardian_phone }} | {{ $app->guardian_email }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @if(is_array($app->documents))
                                        @foreach($app->documents as $key => $filePath)
                                            <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="text-decoration-none small text-primary">
                                                <i class="bi bi-file-earmark-arrow-up me-1"></i>{{ ucwords(str_replace('_', ' ', $key)) }}
                                            </a>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">No documents</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge @if($app->status === 'approved') bg-success @elseif($app->status === 'rejected') bg-danger @elseif($app->status === 'interview') bg-info @else bg-warning text-dark @endif px-2.5 py-1.5 rounded-3">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    @if($app->status !== 'approved' && $app->status !== 'rejected')
                                        <!-- Review / Status Update Modal Button -->
                                        <button class="btn btn-outline-warning text-dark btn-sm px-2.5 py-1.5" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $app->id }}" style="border-radius: 8px;">
                                            <i class="bi bi-pencil-square me-1"></i>Review
                                        </button>
 
                                        <!-- Direct Approve Route Button -->
                                        <form action="{{ route('school.admissions.approve', $app->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm px-2.5 py-1.5" style="border-radius: 8px;" onclick="return confirm('Promote candidate to registered student? This generates student & guardian profiles, parent portal accounts, and active enrollments.');">
                                                <i class="bi bi-person-check-fill me-1"></i>Approve & Register
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-outline-secondary btn-sm px-2.5 py-1.5" disabled style="border-radius: 8px;">
                                            <i class="bi bi-lock-fill me-1"></i>Finalized
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>


                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No admission applications logged.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- REVIEW MODALS (outside table to avoid stacking issues) -->
    @foreach($applications as $app)
        <div class="modal fade" id="reviewModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form action="{{ route('school.admissions.updateStatus', $app->id) }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    @csrf
                    <div class="modal-header border-bottom-0 p-4 pb-0">
                        <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Review Admission Candidate</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div>
                                <label class="form-label small font-weight-bold">Candidate</label>
                                <input type="text" class="form-control" value="{{ $app->first_name }} {{ $app->last_name }}" disabled>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Pipeline Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="reviewing" {{ $app->status == 'reviewing' ? 'selected' : '' }}>Under Review</option>
                                    <option value="interview" {{ $app->status == 'interview' ? 'selected' : '' }}>Schedule / Conduct Interview</option>
                                    <option value="rejected" {{ $app->status == 'rejected' ? 'selected' : '' }}>Reject / Close Application</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Interview / Assessment Remarks</label>
                                <textarea class="form-control" name="interview_notes" rows="3" placeholder="Log candidate scoring, interview dates, or general assessment feedback...">{{ $app->interview_notes }}</textarea>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Review Notes</label>
                                <textarea class="form-control" name="review_notes" rows="3" placeholder="Internal team notes/document checklist tracking...">{{ $app->review_notes }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4">
                        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-warning px-4 text-dark" style="border-radius: 8px;">Save Review Details</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

</div>
@endsection
