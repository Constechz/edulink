@extends('layouts.app')

@section('title', 'Create Subscription Plan | EduLink')
@section('header_title', 'Configure Subscription Tier')

@section('content')
<div class="container-fluid p-0">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('super-admin.plans.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 text-dark small fw-medium">
            <i class="bi bi-arrow-left me-2"></i>Back to Plans Registry
        </a>
    </div>

    <!-- Error Alerts -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i><strong>Validation Failed:</strong>
            <ul class="mb-0 mt-2 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-8 col-lg-10 mx-auto">
            <div class="glass-card p-4 shadow-sm">
                <div class="border-bottom pb-3 mb-4">
                    <h5 class="fw-bold text-dark mb-1"><i class="bi bi-plus-square text-primary me-2"></i>Create New Subscription Tier</h5>
                    <p class="text-muted small mb-0">Define pricing, storage space, SMS credits, school limits, and module permissions.</p>
                </div>

                <form action="{{ route('super-admin.plans.store') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <!-- Plan Name & Status -->
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-dark">Plan Name</label>
                            <input type="text" name="name" class="form-control rounded-3" value="{{ old('name') }}" placeholder="e.g. Standard Plus" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">SaaS Status</label>
                            <select name="is_active" class="form-select rounded-3" required>
                                <option value="1" {{ old('is_active') === '1' ? 'selected' : '' }}>Active / Public</option>
                                <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Inactive / Private</option>
                            </select>
                        </div>

                        <!-- Pricing Tiers -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Termly Pricing (GHS)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light rounded-start-3">GHS</span>
                                <input type="number" step="0.01" min="0" name="price_monthly" class="form-control rounded-end-3" value="{{ old('price_monthly', '0.00') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Yearly Pricing (GHS)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light rounded-start-3">GHS</span>
                                <input type="number" step="0.01" min="0" name="price_yearly" class="form-control rounded-end-3" value="{{ old('price_yearly', '0.00') }}" required>
                            </div>
                        </div>

                        <!-- Limits -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Max Students</label>
                            <input type="number" name="max_students" class="form-control rounded-3" value="{{ old('max_students', '500') }}" placeholder="e.g. 500 (-1 for unlimited)" required>
                            <div class="form-text small" style="font-size: 0.75rem;">Set to <strong>-1</strong> for unlimited students.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Max Staff Members</label>
                            <input type="number" name="max_staff" class="form-control rounded-3" value="{{ old('max_staff', '50') }}" placeholder="e.g. 50 (-1 for unlimited)" required>
                            <div class="form-text small" style="font-size: 0.75rem;">Set to <strong>-1</strong> for unlimited staff.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Max Campuses</label>
                            <input type="number" name="max_campuses" class="form-control rounded-3" value="{{ old('max_campuses', '2') }}" placeholder="e.g. 2 (-1 for unlimited)" required>
                            <div class="form-text small" style="font-size: 0.75rem;">Set to <strong>-1</strong> for unlimited campuses.</div>
                        </div>

                        <!-- Storage & SMS Package -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Included Disk Storage (GB)</label>
                            <div class="input-group">
                                <input type="number" min="1" name="storage_gb" class="form-control rounded-start-3" value="{{ old('storage_gb', '20') }}" required>
                                <span class="input-group-text bg-light rounded-end-3">GB Space</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Termly SMS Credits Bundle</label>
                            <div class="input-group">
                                <input type="number" min="0" name="sms_credits_monthly" class="form-control rounded-start-3" value="{{ old('sms_credits_monthly', '1000') }}" required>
                                <span class="input-group-text bg-light rounded-end-3">SMS/term</span>
                            </div>
                        </div>

                        <!-- Unlocked Features checklist -->
                        <div class="col-12 mt-4">
                            <div class="border-top pt-4">
                                <label class="form-label small fw-bold text-dark d-block mb-3">SaaS Tier Feature Checklist</label>
                                <div class="row g-3">
                                    @foreach($allowedFeatures as $slug => $label)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="form-check p-3 border rounded-3 bg-light bg-opacity-50">
                                                <input class="form-check-input ms-0 me-2" type="checkbox" name="features[]" value="{{ $slug }}" id="feature_{{ $slug }}" {{ is_array(old('features')) && in_array($slug, old('features')) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-semibold text-dark cursor-pointer" for="feature_{{ $slug }}">
                                                    {{ $label }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="col-12 text-end mt-5 border-top pt-4">
                            <a href="{{ route('super-admin.plans.index') }}" class="btn btn-outline-secondary px-4 py-2 me-2 rounded-3">Cancel</a>
                            <button type="submit" class="btn btn-success px-4 py-2 rounded-3">
                                <i class="bi bi-save me-2"></i>Create SaaS Plan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
