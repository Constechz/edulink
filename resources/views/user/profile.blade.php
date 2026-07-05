@extends('layouts.app')

@section('title', 'My Profile & Security | ' . config('app.name', 'EduLink'))
@section('header_title', 'User Settings Dashboard')

@section('content')
<div class="container-fluid p-0">
    <!-- Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i><strong>Action Failed:</strong>
            <ul class="mb-0 mt-2 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Profile Header Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4 d-flex flex-column flex-sm-row align-items-center gap-4 shadow-sm" style="background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);">
                <div class="position-relative">
                    <img src="{{ $user->profile_photo ? (str_starts_with($user->profile_photo, 'http') ? $user->profile_photo : asset('storage/' . $user->profile_photo)) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=003366&color=fff&size=120' }}" alt="avatar" class="rounded-circle border border-4 border-white shadow" style="width: 110px; height: 110px; object-fit: cover;">
                </div>
                <div class="text-center text-sm-start">
                    <h4 class="fw-bold mb-1 text-dark">{{ $user->name }}</h4>
                    <span class="badge bg-primary bg-opacity-10 text-primary border px-3 py-1 mb-2">
                        <i class="bi bi-shield-check me-1"></i>{{ $user->role ? $user->role->name : 'Platform Account' }}
                    </span>
                    <p class="text-muted small mb-0"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Workspace Tabs Layout -->
    <div class="row g-4">
        <div class="col-12">
            <div class="glass-card p-4 shadow-sm">
                <!-- Nav Tabs -->
                <ul class="nav nav-pills mb-4" id="profileTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">
                            <i class="bi bi-person-fill me-2"></i>My Profile Details
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">
                            <i class="bi bi-shield-lock-fill me-2"></i>Security & Credentials
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="profileTabContent">
                    
                    <!-- Tab 1: Profile Info -->
                    <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-badge text-primary me-2"></i>Edit Profile Details</h6>
                        <p class="text-muted small">Update your general information and platform avatar.</p>

                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="profile_name" class="form-label small fw-semibold">Full Name</label>
                                        <input type="text" class="form-control rounded-3" id="profile_name" name="name" required value="{{ old('name', $user->name) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="profile_email" class="form-label small fw-semibold">Email Address</label>
                                        <input type="email" class="form-control rounded-3" id="profile_email" name="email" required value="{{ old('email', $user->email) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="profile_photo" class="form-label small fw-semibold">Profile Photo (Avatar)</label>
                                        <input class="form-control rounded-3" type="file" id="profile_photo" name="profile_photo" accept="image/*">
                                        <div class="form-text small text-muted">Supports JPG, PNG, GIF formats. Max size 2MB.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="signature" class="form-label small fw-semibold">Personal Signature (For Report Cards)</label>
                                        <input class="form-control rounded-3" type="file" id="signature" name="signature" accept="image/*">
                                        <div class="form-text small text-muted">Supports PNG, JPG formats. Used for teacher signatures.</div>
                                    </div>
                                    @if($user->signature)
                                        <div class="mt-2 p-2 border rounded bg-white d-inline-block">
                                            <div class="text-muted small mb-1 fw-bold">Active Signature Preview:</div>
                                            <img src="{{ asset('storage/' . $user->signature) }}" alt="signature" style="max-height: 40px; max-width: 150px; object-fit: contain;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="text-start mt-3">
                                <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold shadow-xs">
                                    <i class="bi bi-save me-1"></i>Save Settings
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tab 2: Security & Password -->
                    <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-shield-lock text-danger me-2"></i>Update Password</h6>
                        <p class="text-muted small">Ensure your account uses a secure password (minimum 8 characters).</p>

                        <form action="{{ route('profile.password') }}" method="POST" class="mt-3" style="max-width: 550px;">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label small fw-semibold">Current Password</label>
                                <input type="password" class="form-control rounded-3" id="current_password" name="current_password" required placeholder="Type your current password">
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label small fw-semibold">New Password</label>
                                <input type="password" class="form-control rounded-3" id="new_password" name="new_password" required placeholder="Create a new password (min. 8 characters)">
                            </div>

                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label small fw-semibold">Confirm New Password</label>
                                <input type="password" class="form-control rounded-3" id="new_password_confirmation" name="new_password_confirmation" required placeholder="Retype your new password">
                            </div>

                            <div class="text-start mt-4">
                                <button type="submit" class="btn btn-danger rounded-3 px-4 py-2 fw-semibold shadow-xs">
                                    <i class="bi bi-shield-check me-1"></i>Change Password
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Toggle tab based on window location hash (e.g. #security)
        const hash = window.location.hash;
        if (hash === '#security') {
            const securityTab = document.querySelector('#profileTab button[data-bs-target="#security"]');
            if (securityTab) {
                const tabInstance = new bootstrap.Tab(securityTab);
                tabInstance.show();
            }
        }

        // Keep hash updated on tab clicks
        document.querySelectorAll('#profileTab button').forEach(tabButton => {
            tabButton.addEventListener('shown.bs.tab', function(e) {
                const target = e.target.getAttribute('data-bs-target');
                window.location.hash = target;
            });
        });
    });
</script>
@endsection
