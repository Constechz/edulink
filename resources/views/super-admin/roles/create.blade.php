@extends('layouts.app')

@section('title', 'Create Global Role | EduLink')
@section('header_title', 'Roles & Permissions Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Back to roles -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('super-admin.roles.index') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left me-1"></i>Back to Roles List
            </a>
        </div>
    </div>

    <!-- Title -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="fw-bold mb-1 text-dark">Create Custom Global Role</h5>
            <p class="text-muted small">Define a new platform role and map specific permissions to it.</p>
        </div>
    </div>

    <form action="{{ route('super-admin.roles.store') }}" method="POST" id="roleForm">
        @csrf
        
        <div class="row g-4">
            <!-- Left Info Panel -->
            <div class="col-lg-4">
                <div class="glass-card p-4 shadow-sm h-100">
                    <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-info-circle text-primary me-2"></i>Role Details</h6>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label small fw-semibold">Role Name</label>
                        <input type="text" class="form-control rounded-3" id="name" name="name" required placeholder="e.g. Platform Supervisor" value="{{ old('name') }}" oninput="autoGenerateSlug(this.value)">
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label small fw-semibold">Role Slug</label>
                        <input type="text" class="form-control rounded-3" id="slug" name="slug" required placeholder="e.g. platform-supervisor" value="{{ old('slug') }}">
                        <div class="form-text small text-muted">A unique URL-friendly string identifying the role.</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label small fw-semibold">Description</label>
                        <textarea class="form-control rounded-3" id="description" name="description" rows="4" placeholder="Brief explanation of this role's target responsibilities...">{{ old('description') }}</textarea>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded-4">
                        <h7 class="fw-bold text-dark d-block mb-2 small"><i class="bi bi-shield-lock-fill me-1 text-warning"></i>Security Note</h7>
                        <span class="text-muted small" style="font-size: 0.75rem;">
                            Custom roles created here apply globally. Permissions assigned here grant access to administrators working at the multi-tenant system operations level.
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Permissions Panel -->
            <div class="col-lg-8">
                <div class="glass-card p-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 pb-2 border-bottom">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-check text-success me-2"></i>Permissions Matrix</h6>
                        
                        <!-- Select all -->
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="select_all">
                            <label class="form-check-label small fw-semibold text-dark" for="select_all">Select All Permissions</label>
                        </div>
                    </div>

                    <!-- Permissions module list -->
                    <div class="d-flex flex-column gap-4">
                        @foreach($permissions as $module => $modulePerms)
                            <div class="p-3 bg-light rounded-4 border">
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <span class="fw-bold text-dark" style="font-size: 0.9rem;">
                                        <i class="bi bi-folder-fill text-warning me-2"></i>{{ $module }} Setup
                                    </span>
                                    <div class="form-check">
                                        <input class="form-check-input module-header-checkbox" type="checkbox" id="mod_{{ Str::slug($module) }}" onchange="toggleModule('{{ Str::slug($module) }}', this)">
                                        <label class="form-check-label small fw-semibold text-muted" for="mod_{{ Str::slug($module) }}">Toggle Module</label>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    @foreach($modulePerms as $perm)
                                        <div class="col-md-6">
                                            <div class="p-2 bg-white rounded-3 border d-flex gap-3 align-items-start h-100">
                                                <div class="form-check pt-1 ps-3">
                                                    <input class="form-check-input permission-checkbox perm-{{ Str::slug($module) }}" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}">
                                                </div>
                                                <label class="form-check-label w-100" for="perm_{{ $perm->id }}">
                                                    <span class="fw-bold text-dark d-block small">{{ $perm->name }}</span>
                                                    <span class="text-muted text-wrap d-block" style="font-size: 0.7rem;">{{ $perm->description }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Form actions -->
                    <div class="mt-4 pt-3 border-top text-end">
                        <a href="{{ route('super-admin.roles.index') }}" class="btn btn-outline-secondary rounded-3 px-4 py-2 me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold">
                            <i class="bi bi-save me-1"></i>Save & Register Role
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Auto-generate slug from name input
    function autoGenerateSlug(name) {
        const slugInput = document.getElementById('slug');
        if (slugInput) {
            slugInput.value = name
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-|-$)+/g, '');
        }
    }

    // Toggle child checkboxes when module toggle is clicked
    function toggleModule(moduleSlug, headerCheckbox) {
        const checkboxes = document.querySelectorAll(`.perm-${moduleSlug}`);
        checkboxes.forEach(cb => {
            cb.checked = headerCheckbox.checked;
        });
        updateSelectAllState();
    }

    // Select All checkbox toggling
    document.getElementById('select_all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
        });
        
        const moduleHeaders = document.querySelectorAll('.module-header-checkbox');
        moduleHeaders.forEach(cb => {
            cb.checked = this.checked;
        });
    });

    // Update Master Select All status based on actual check state
    function updateSelectAllState() {
        const total = document.querySelectorAll('.permission-checkbox').length;
        const checked = document.querySelectorAll('.permission-checkbox:checked').length;
        document.getElementById('select_all').checked = (total === checked && total > 0);
    }

    // Listen on child checkboxes to update master select state
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSelectAllState);
    });
</script>
@endsection
