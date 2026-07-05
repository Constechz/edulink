@extends('layouts.app')

@section('title', 'Staff HR Details | EduLink')
@section('header_title', 'HR Profile & Document Manager')

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

    <!-- Back to directory -->
    <div class="mb-4">
        <a href="{{ route('school.staff') }}" class="btn btn-outline-secondary px-3 py-1.5" style="border-radius: 8px;">
            <i class="bi bi-arrow-left me-2"></i>Back to Staff Accounts
        </a>
    </div>

    <!-- Staff Header Card -->
    <div class="glass-card p-4 mb-4">
        <div class="d-flex align-items-center">
            <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-4" style="width: 70px; height: 70px; font-size: 1.8rem; font-weight: 700;">
                {{ substr($staff->user->name ?? 'ST', 0, 2) }}
            </div>
            <div>
                <h3 class="mb-1 font-weight-bold" style="font-weight: 700;">{{ $staff->user->name ?? 'Staff Member' }}</h3>
                <p class="text-muted mb-0">
                    Staff Number: <strong>{{ $staff->staff_number }}</strong> | Designation: <strong>{{ $staff->designation }}</strong>
                </p>
                <span class="badge bg-info mt-2 px-2.5 py-1.5 rounded-3">Employment Type: {{ ucfirst($staff->employment_type) }}</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- HR details form -->
        <div class="col-md-6">
            <div class="glass-card p-4 h-100">
                <h5 class="font-weight-bold mb-3"><i class="bi bi-credit-card-2-front text-primary me-2"></i>Financial & Identifications</h5>
                
                <form action="{{ route('school.staff-hr.update', $staff->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small font-weight-bold">Employment Type</label>
                            <select class="form-select" name="employment_type" required>
                                <option value="permanent" {{ $staff->employment_type == 'permanent' ? 'selected' : '' }}>Permanent</option>
                                <option value="contract" {{ $staff->employment_type == 'contract' ? 'selected' : '' }}>Contract</option>
                                <option value="temporary" {{ $staff->employment_type == 'temporary' ? 'selected' : '' }}>Temporary</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small font-weight-bold">Salary Grade</label>
                            <input type="text" class="form-control" name="salary_grade" value="{{ $staff->salary_grade }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small font-weight-bold">Basic Salary (GHS)</label>
                            <input type="number" class="form-control" name="basic_salary" value="{{ old('basic_salary', $staff->basic_salary) }}" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small font-weight-bold">Monthly Allowances (GHS)</label>
                            <input type="number" class="form-control" name="allowances" value="{{ old('allowances', $staff->allowances) }}" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small font-weight-bold">Monthly Deductions (GHS)</label>
                            <input type="number" class="form-control" name="deductions" value="{{ old('deductions', $staff->deductions) }}" step="0.01" min="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small font-weight-bold">Contract Start Date</label>
                            <input type="date" class="form-control" name="contract_start" value="{{ $staff->contract_start ? $staff->contract_start->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small font-weight-bold">Contract End Date</label>
                            <input type="date" class="form-control" name="contract_end" value="{{ $staff->contract_end ? $staff->contract_end->format('Y-m-d') : '' }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small font-weight-bold">Bank Name</label>
                            <input type="text" class="form-control" name="bank_name" value="{{ $staff->bank_name }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small font-weight-bold">Bank Account Number</label>
                            <input type="text" class="form-control" name="bank_account" value="{{ $staff->bank_account }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small font-weight-bold">Bank Branch</label>
                            <input type="text" class="form-control" name="bank_branch" value="{{ $staff->bank_branch }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small font-weight-bold">SSNIT Number</label>
                            <input type="text" class="form-control" name="ssnit_number" value="{{ $staff->ssnit_number }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small font-weight-bold">TIN/GRA Number</label>
                            <input type="text" class="form-control" name="tin_number" value="{{ $staff->tin_number }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small font-weight-bold">Emergency Contact Name</label>
                            <input type="text" class="form-control" name="emergency_contact_name" value="{{ $staff->emergency_contact_name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small font-weight-bold">Emergency Contact Phone</label>
                            <input type="text" class="form-control" name="emergency_contact_phone" value="{{ $staff->emergency_contact_phone }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small font-weight-bold">National ID Type</label>
                            <input type="text" class="form-control" name="national_id_type" value="{{ $staff->national_id_type }}" placeholder="e.g. Ghana Card">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small font-weight-bold">National ID Number</label>
                            <input type="text" class="form-control" name="national_id_number" value="{{ $staff->national_id_number }}">
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">
                                <i class="bi bi-save me-2"></i>Update HR Details
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Qualifications & Documents Upload -->
        <div class="col-md-6">
            <div class="row g-4">
                <!-- Academic Credentials / Qualifications -->
                <div class="col-12">
                    <div class="glass-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="font-weight-bold mb-0"><i class="bi bi-award text-success me-2"></i>Qualifications / Credentials</h5>
                            <button class="btn btn-sm btn-outline-success px-2.5 py-1.5" data-bs-toggle="modal" data-bs-target="#addQualificationModal" style="border-radius: 8px;">
                                <i class="bi bi-plus-circle me-1"></i>Add
                            </button>
                        </div>

                        <ul class="list-group list-group-flush mb-0 small">
                            @forelse($staff->qualifications as $qual)
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent py-2.5 px-0">
                                    <div>
                                        <div class="font-weight-bold">{{ $qual->qualification }}</div>
                                        <span class="text-muted">{{ $qual->institution }} ({{ $qual->year_obtained }})</span>
                                    </div>
                                    @if($qual->certificate_path)
                                        <a href="{{ asset('storage/' . $qual->certificate_path) }}" target="_blank" class="btn btn-link btn-sm text-primary p-0">
                                            <i class="bi bi-file-pdf me-1"></i>View Cert
                                        </a>
                                    @endif
                                </li>
                            @empty
                                <li class="list-group-item bg-transparent text-muted text-center py-3">No qualifications recorded.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Document Upload Section -->
                <div class="col-12">
                    <div class="glass-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="font-weight-bold mb-0"><i class="bi bi-folder2-open text-warning me-2"></i>Official HR Documents</h5>
                            <button class="btn btn-sm btn-outline-warning text-dark px-2.5 py-1.5" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal" style="border-radius: 8px;">
                                <i class="bi bi-cloud-upload me-1"></i>Upload Document
                            </button>
                        </div>

                        <ul class="list-group list-group-flush mb-0 small">
                            @forelse($staff->documents as $doc)
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent py-2.5 px-0">
                                    <div>
                                        <div class="font-weight-bold">{{ $doc->document_type }}</div>
                                        <span class="text-muted">Uploaded on: {{ $doc->uploaded_at ? $doc->uploaded_at->format('d M Y') : 'N/A' }}</span>
                                    </div>
                                    <div class="d-flex gap-2 align-items-center">
                                        @if($doc->expiry_date)
                                            <span class="badge bg-secondary">Expires: {{ $doc->expiry_date->format('d M Y') }}</span>
                                        @endif
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-light border" style="border-radius: 6px;">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item bg-transparent text-muted text-center py-3">No documents uploaded.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD QUALIFICATION MODAL -->
    <div class="modal fade" id="addQualificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Add Academic Qualification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('school.staff-hr.qualification', $staff->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div>
                                <label class="form-label small font-weight-bold">Institution</label>
                                <input type="text" class="form-control" name="institution" placeholder="e.g. University of Ghana" required>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Degree / Certificate / Diploma Name</label>
                                <input type="text" class="form-control" name="qualification" placeholder="e.g. B.Ed in Science Education" required>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Year Obtained</label>
                                <input type="number" class="form-control" name="year_obtained" min="1900" max="{{ date('Y') }}" required>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Upload Certificate File (PDF / Image)</label>
                                <input type="file" class="form-control" name="certificate">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-success px-4" style="border-radius: 8px;">Save Qualification</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- UPLOAD DOCUMENT MODAL -->
    <div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Upload Official HR Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('school.staff-hr.upload', $staff->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div>
                                <label class="form-label small font-weight-bold">Document Type</label>
                                <select class="form-select" name="document_type" required>
                                    <option value="Contract Agreement">Contract Agreement</option>
                                    <option value="Curriculum Vitae (CV)">Curriculum Vitae (CV)</option>
                                    <option value="National ID Scan">National ID Scan</option>
                                    <option value="Police Clearance">Police Clearance</option>
                                    <option value="Professional License Certificate">Professional License Certificate</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Document Expiry Date (if applicable)</label>
                                <input type="date" class="form-control" name="expiry_date">
                            </div>
                            <div>
                                <label class="form-label small font-weight-bold">Select File (PDF / Word / Image)</label>
                                <input type="file" class="form-control" name="document" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-warning px-4 text-dark" style="border-radius: 8px;">Upload Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
