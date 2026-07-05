@extends('layouts.app')

@section('title', 'Fee Structures | EduLink')
@section('header_title', 'Fee Structure Management')

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

    <!-- Actions Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0 font-weight-bold" style="font-weight: 700;">School Fee Catalog</h5>
            <p class="text-muted mb-0 small">Define fee schedules, tuition parameters, and mandatory class charges.</p>
        </div>
        <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#createFeeModal" style="border-radius: 10px;">
            <i class="bi bi-plus-lg me-2"></i>Create Fee Structure
        </button>
    </div>

    <!-- Catalog Card -->
    <div class="glass-card p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Fee Name</th>
                        <th>Applicable Level/Class</th>
                        <th>Academic Year & Term</th>
                        <th>Campus Scope</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Mandatory</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feeStructures as $fee)
                        <tr>
                            <td class="font-weight-bold" style="font-weight: 600;">{{ $fee->name }}</td>
                            <td>
                                @if($fee->class)
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1" style="border-radius: 6px;">
                                        {{ $fee->class->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1" style="border-radius: 6px;">
                                        All Classes
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $fee->academicYear->name }}</div>
                                <span class="text-muted small">{{ $fee->term ? $fee->term->name : 'All Terms' }}</span>
                            </td>
                            <td>{{ $fee->campus ? $fee->campus->name : 'All Campuses' }}</td>
                            <td class="font-weight-bold">GHS {{ number_format($fee->amount, 2) }}</td>
                            <td>{{ $fee->due_date ? $fee->due_date->format('d M Y') : 'N/A' }}</td>
                            <td>
                                @if($fee->is_mandatory)
                                    <span class="badge bg-success px-2 py-1" style="border-radius: 6px;">Yes</span>
                                @else
                                    <span class="badge bg-warning text-dark px-2 py-1" style="border-radius: 6px;">Optional</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end align-items-center gap-2">
                                    <button class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#editFeeModal{{ $fee->id }}" style="border-radius: 8px;">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <form action="{{ route('school.finance.fee-structures.destroy', $fee->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this fee item?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center" style="border-radius: 8px; width: 32px; height: 32px; padding: 0;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No fee structure items configured yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- CREATE FEE MODAL -->
    <div class="modal fade" id="createFeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Create Fee Structure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('school.finance.fee-structures.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small font-weight-bold">Fee Name / Description</label>
                                <input type="text" class="form-control" name="name" required placeholder="e.g. Tuition Fee (Term 1)">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small font-weight-bold">Amount (GHS)</label>
                                <input type="number" step="0.01" class="form-control" name="amount" required placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small font-weight-bold">Due Date</label>
                                <input type="date" class="form-control" name="due_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small font-weight-bold">Academic Year</label>
                                <select class="form-select" name="academic_year_id" required>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small font-weight-bold">Term (Optional)</label>
                                <select class="form-select" name="term_id">
                                    <option value="">Apply to all Terms</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}">{{ $term->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small font-weight-bold">Class Scope (Optional)</label>
                                <select class="form-select" name="class_id">
                                    <option value="">Apply to all Classes</option>
                                    @foreach($classes as $cls)
                                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small font-weight-bold">Campus Scope (Optional)</label>
                                <select class="form-select" name="campus_id">
                                    <option value="">Apply to all Campuses</option>
                                    @foreach($campuses as $campus)
                                        <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_mandatory" value="1" id="createIsMandatory" checked>
                                    <label class="form-check-label font-weight-bold small text-dark" for="createIsMandatory">Mandatory Fee (Applied automatically to invoices)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Create Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT FEE MODALS (placed outside table loop to avoid stacking context issues) -->
    @foreach($feeStructures as $fee)
        <div class="modal fade" id="editFeeModal{{ $fee->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    <div class="modal-header border-bottom-0 p-4 pb-0">
                        <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Edit Fee Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('school.finance.fee-structures.update', $fee->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small font-weight-bold">Fee Name / Description</label>
                                    <input type="text" class="form-control" name="name" value="{{ $fee->name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small font-weight-bold">Amount (GHS)</label>
                                    <input type="number" step="0.01" class="form-control" name="amount" value="{{ $fee->amount }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small font-weight-bold">Due Date</label>
                                    <input type="date" class="form-control" name="due_date" value="{{ $fee->due_date ? $fee->due_date->format('Y-m-d') : '' }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small font-weight-bold">Academic Year</label>
                                    <select class="form-select" name="academic_year_id" required>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ $fee->academic_year_id == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small font-weight-bold">Term (Optional)</label>
                                    <select class="form-select" name="term_id">
                                        <option value="" {{ is_null($fee->term_id) ? 'selected' : '' }}>Apply to all Terms</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term->id }}" {{ $fee->term_id == $term->id ? 'selected' : '' }}>{{ $term->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small font-weight-bold">Class Scope (Optional)</label>
                                    <select class="form-select" name="class_id">
                                        <option value="" {{ is_null($fee->class_id) ? 'selected' : '' }}>Apply to all Classes</option>
                                        @foreach($classes as $cls)
                                            <option value="{{ $cls->id }}" {{ $fee->class_id == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small font-weight-bold">Campus Scope (Optional)</label>
                                    <select class="form-select" name="campus_id">
                                        <option value="" {{ is_null($fee->campus_id) ? 'selected' : '' }}>Apply to all Campuses</option>
                                        @foreach($campuses as $campus)
                                            <option value="{{ $campus->id }}" {{ $fee->campus_id == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_mandatory" value="1" id="editIsMandatory{{ $fee->id }}" {{ $fee->is_mandatory ? 'checked' : '' }}>
                                        <label class="form-check-label font-weight-bold small text-dark" for="editIsMandatory{{ $fee->id }}">Mandatory Fee (Applied automatically to invoices)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 p-4">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

</div>
@endsection
