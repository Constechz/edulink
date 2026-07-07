@extends('layouts.app')

@section('title', 'Campus Directory | EduLink')
@section('header_title', 'Campus Management Hub')

@section('content')
<style>
    @media (max-width: 575.98px) {
        .btn-responsive {
            padding: 0.5rem 0.85rem !important;
            font-size: 0.85rem !important;
        }
    }
</style>
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
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="mb-0 font-weight-bold" style="font-weight: 700;">School Campuses</h5>
            <p class="text-muted mb-0 small">Create, edit, and assign staff members to regional branch campuses.</p>
        </div>
        <button class="btn btn-primary px-4 py-2 btn-responsive" data-bs-toggle="modal" data-bs-target="#addCampusModal" style="border-radius: 10px;">
            <i class="bi bi-plus-circle me-2"></i>Register New Campus
        </button>
    </div>

    <!-- Campus Cards Directory -->
    <div class="row g-4">
        @forelse($campuses as $campus)
            <div class="col-md-4">
                <div class="glass-card p-4 h-100 position-relative d-flex flex-column justify-content-between">
                    <div>
                        <!-- Header badge -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge {{ $campus->is_active ? 'bg-success' : 'bg-secondary' }} px-2.5 py-1.5 rounded-3">
                                {{ $campus->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if($campus->is_main)
                                <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-3" style="font-weight: 600;">
                                    <i class="bi bi-star-fill me-1"></i>Main Campus
                                </span>
                            @endif
                        </div>

                        <h4 class="font-weight-bold mb-1" style="font-weight: 700;">{{ $campus->name }}</h4>
                        <span class="text-muted small d-block mb-3">Code: <strong>{{ $campus->code ?: 'N/A' }}</strong></span>

                        <ul class="list-unstyled mb-4 small text-muted">
                            <li class="mb-2"><i class="bi bi-person-fill me-2 text-primary"></i>Principal: {{ $campus->principal_name ?: 'Not Assigned' }}</li>
                            <li class="mb-2"><i class="bi bi-geo-alt-fill me-2 text-danger"></i>Address: {{ $campus->address ?: 'N/A' }}</li>
                            <li class="mb-2"><i class="bi bi-telephone-fill me-2 text-success"></i>Phone: {{ $campus->phone ?: 'N/A' }}</li>
                            <li class="mb-2"><i class="bi bi-envelope-fill me-2 text-info"></i>Email: {{ $campus->email ?: 'N/A' }}</li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm w-100 py-2" data-bs-toggle="modal" data-bs-target="#editCampusModal{{ $campus->id }}" style="border-radius: 8px;">
                            <i class="bi bi-pencil-square me-1"></i>Edit Campus
                        </button>
                        <form action="{{ route('school.campuses.destroy', $campus->id) }}" method="POST" class="w-100" onsubmit="return confirm('Are you sure you want to delete this campus?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100 py-2" style="border-radius: 8px;">
                                <i class="bi bi-trash3 me-1"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- EDIT CAMPUS MODAL -->
            <div class="modal fade" id="editCampusModal{{ $campus->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                        <div class="modal-header border-bottom-0 p-4 pb-0">
                            <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Edit Campus Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('school.campuses.update', $campus->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Campus Name</label>
                                        <input type="text" class="form-control" name="name" value="{{ $campus->name }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Campus Code</label>
                                        <input type="text" class="form-control" name="code" value="{{ $campus->code }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Principal Name</label>
                                        <input type="text" class="form-control" name="principal_name" value="{{ $campus->principal_name }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Physical Address</label>
                                        <input type="text" class="form-control" name="address" value="{{ $campus->address }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contact Phone</label>
                                        <input type="text" class="form-control" name="phone" value="{{ $campus->phone }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contact Email</label>
                                        <input type="email" class="form-control" name="email" value="{{ $campus->email }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Is Main Campus?</label>
                                        <select class="form-select" name="is_main">
                                            <option value="1" {{ $campus->is_main ? 'selected' : '' }}>Yes, Main Campus</option>
                                            <option value="0" {{ !$campus->is_main ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="is_active">
                                            <option value="1" {{ $campus->is_active ? 'selected' : '' }}>Active Branch</option>
                                            <option value="0" {{ !$campus->is_active ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 p-4 pt-0">
                                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                                <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted">
                    <i class="bi bi-building-exclamation display-4 d-block mb-3"></i>
                    <h5>No Campuses Registered</h5>
                    <p class="small mb-0">Register your first branch campus to start enrolling students.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- ADD CAMPUS MODAL -->
    <div class="modal fade" id="addCampusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Register New Campus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('school.campuses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Campus Name</label>
                                <input type="text" class="form-control" name="name" placeholder="e.g. Accra Main Campus" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Campus Code</label>
                                <input type="text" class="form-control" name="code" placeholder="e.g. AMC">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Principal Name</label>
                                <input type="text" class="form-control" name="principal_name" placeholder="e.g. Dr. Kwame Osei">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Physical Address</label>
                                <input type="text" class="form-control" name="address" placeholder="e.g. 15 Giffard Road, Cantonments">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Phone</label>
                                <input type="text" class="form-control" name="phone" placeholder="e.g. +233 24 000 0000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Email</label>
                                <input type="email" class="form-control" name="email" placeholder="e.g. principal.main@school.edu.gh">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Is Main Campus?</label>
                                <select class="form-select" name="is_main">
                                    <option value="1">Yes, Main Campus</option>
                                    <option value="0" selected>No</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="is_active">
                                    <option value="1" selected>Active Branch</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Register Branch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
