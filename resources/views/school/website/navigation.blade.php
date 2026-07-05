@extends('layouts.app')

@section('title', 'Navigation Menu Builder')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Menu Navigation</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Website Navigation Builder</h1>
                    <p class="text-muted mb-0 small">Customize links, pages, and order for the header menu and footer links of your public portal.</p>
                </div>
            </div>

            <!-- Validation/Success Feedback Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                        <div>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-4">
                <!-- Header Menu Card -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-light border-0 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-compass me-1"></i> Header Menu Links</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMenuRow('header')">
                                <i class="bi bi-plus-lg"></i> Add Link
                            </button>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('school.website.navigation.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="menu_id" value="{{ $headerMenu->id }}">
                                
                                <div id="header-items-container">
                                    @forelse($headerItems as $index => $item)
                                        <div class="menu-row border rounded p-3 mb-3 bg-light position-relative">
                                            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Link Label</label>
                                                <input type="text" name="items[{{ $index }}][label]" class="form-control form-control-sm" value="{{ $item->label }}" placeholder="e.g. Admissions" required>
                                            </div>
                                            <div class="row g-2 mb-2">
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold">Internal Page</label>
                                                    <select name="items[{{ $index }}][page_id]" class="form-select form-select-sm page-select" onchange="toggleUrlField(this)">
                                                        <option value="">-- Custom URL --</option>
                                                        @foreach($pages as $p)
                                                            <option value="{{ $p->id }}" {{ $item->page_id == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold">Custom URL</label>
                                                    <input type="text" name="items[{{ $index }}][url]" class="form-control form-control-sm url-field" value="{{ $item->url }}" placeholder="e.g. /admissions/apply" {{ $item->page_id ? 'disabled' : '' }}>
                                                </div>
                                            </div>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="items[{{ $index }}][open_new_tab]" value="1" id="h-tab-{{ $index }}" {{ $item->open_new_tab ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="h-tab-{{ $index }}">Open link in new tab</label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted small header-placeholder">
                                            No links configured. Click "Add Link" to populate main header menu.
                                        </div>
                                    @endforelse
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2 mt-2">Save Header Menu</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Footer Menu Card -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-light border-0 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-list-columns-reverse me-1"></i> Footer Menu Links</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMenuRow('footer')">
                                <i class="bi bi-plus-lg"></i> Add Link
                            </button>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('school.website.navigation.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="menu_id" value="{{ $footerMenu->id }}">
                                
                                <div id="footer-items-container">
                                    @forelse($footerItems as $index => $item)
                                        <div class="menu-row border rounded p-3 mb-3 bg-light position-relative">
                                            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Link Label</label>
                                                <input type="text" name="items[{{ $index }}][label]" class="form-control form-control-sm" value="{{ $item->label }}" placeholder="e.g. Terms & Policy" required>
                                            </div>
                                            <div class="row g-2 mb-2">
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold">Internal Page</label>
                                                    <select name="items[{{ $index }}][page_id]" class="form-select form-select-sm page-select" onchange="toggleUrlField(this)">
                                                        <option value="">-- Custom URL --</option>
                                                        @foreach($pages as $p)
                                                            <option value="{{ $p->id }}" {{ $item->page_id == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold">Custom URL</label>
                                                    <input type="text" name="items[{{ $index }}][url]" class="form-control form-control-sm url-field" value="{{ $item->url }}" placeholder="e.g. /privacy" {{ $item->page_id ? 'disabled' : '' }}>
                                                </div>
                                            </div>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="items[{{ $index }}][open_new_tab]" value="1" id="f-tab-{{ $index }}" {{ $item->open_new_tab ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="f-tab-{{ $index }}">Open link in new tab</label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted small footer-placeholder">
                                            No links configured. Click "Add Link" to populate footer panel menu.
                                        </div>
                                    @endforelse
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2 mt-2">Save Footer Links</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let indices = {
        header: {{ $headerItems->count() }},
        footer: {{ $footerItems->count() }}
    };

    function addMenuRow(location) {
        const container = document.getElementById(`${location}-items-container`);
        
        // Remove placeholder if present
        const placeholder = container.querySelector(`.${location}-placeholder`);
        if (placeholder) {
            placeholder.remove();
        }

        const index = indices[location];
        const div = document.createElement('div');
        div.className = 'menu-row border rounded p-3 mb-3 bg-light position-relative';
        div.innerHTML = `
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>
            <div class="mb-2">
                <label class="form-label small fw-bold">Link Label</label>
                <input type="text" name="items[${index}][label]" class="form-control form-control-sm" placeholder="Label text" required>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <label class="form-label small fw-bold">Internal Page</label>
                    <select name="items[${index}][page_id]" class="form-select form-select-sm page-select" onchange="toggleUrlField(this)">
                        <option value="">-- Custom URL --</option>
                        @foreach($pages as $p)
                            <option value="{{ $p->id }}">{{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-bold">Custom URL</label>
                    <input type="text" name="items[${index}][url]" class="form-control form-control-sm url-field" placeholder="e.g. /admissions/apply">
                </div>
            </div>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" name="items[${index}][open_new_tab]" value="1" id="${location}-tab-${index}">
                <label class="form-check-label small" for="${location}-tab-${index}">Open link in new tab</label>
            </div>
        `;
        container.appendChild(div);
        indices[location]++;
    }

    function toggleUrlField(select) {
        const row = select.closest('.menu-row');
        const urlField = row.querySelector('.url-field');
        if (select.value) {
            urlField.value = '';
            urlField.disabled = true;
        } else {
            urlField.disabled = false;
        }
    }
</script>
@endsection
