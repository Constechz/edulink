@extends('layouts.app')

@section('title', 'Report Card Themes | EduLink')
@section('header_title', 'Configure Report Card Styling')

@section('content')
<div class="container-fluid p-0">
    <!-- Back to Hub -->
    <div class="mb-4">
        <a href="{{ route('school.reports.index') }}" class="btn btn-outline-secondary px-3 py-1.5" style="border-radius: 8px;">
            <i class="bi bi-arrow-left me-2"></i>Back to Reports Hub
        </a>
    </div>

    <!-- Instructions -->
    <div class="glass-card p-4 mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="fw-bold mb-1 text-dark" style="font-weight: 700;"><i class="bi bi-palette text-primary me-2"></i>Report Card Style Themes</h5>
                <p class="text-muted small mb-0">Select from the available design presets to dynamically style your school's generated PDF report cards. If a class is not assigned a custom theme, it will default to the standard Classic style.</p>
            </div>
        </div>
    </div>

    <!-- Theme Gallery Cards -->
    <div class="row g-4 mb-5">
        @foreach($themes as $key => $info)
            <div class="col-md-3 col-sm-6">
                <div class="glass-card p-3 h-100 d-flex flex-column justify-content-between shadow-xs border-top" style="border-top: 4px solid {{ $info['primary'] }} !important;">
                    <div>
                        <h6 class="fw-bold mb-2 text-dark">{{ $info['name'] }}</h6>
                        <p class="text-muted small mb-3" style="font-size: 0.8rem; min-height: 48px;">
                            {{ $info['description'] }}
                        </p>
                        
                        <!-- Theme Colors Swatch -->
                        <div class="d-flex gap-2 mb-3">
                            <span class="d-inline-block rounded-circle border" style="width: 20px; height: 20px; background-color: {{ $info['primary'] }};" title="Header Color: {{ $info['primary'] }}"></span>
                            <span class="d-inline-block rounded-circle border" style="width: 20px; height: 20px; background-color: {{ $info['secondary'] }};" title="Background: {{ $info['secondary'] }}"></span>
                            <span class="d-inline-block rounded-circle border" style="width: 20px; height: 20px; background-color: {{ $info['border'] }};" title="Border: {{ $info['border'] }}"></span>
                        </div>
                    </div>
                    
                    <div class="text-muted small border-top pt-2" style="font-size: 0.75rem;">
                        <i class="bi bi-fonts me-1"></i>Font: <strong>{{ $info['font'] }}</strong>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Form for Class Theme Assignments -->
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="glass-card p-4">
                <h5 class="fw-bold text-dark border-bottom pb-3 mb-4"><i class="bi bi-gear-fill text-secondary me-2"></i>Class Theme Assignments</h5>
                
                @if($classes->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-mortarboard display-5 d-block mb-3"></i>
                        <p class="mb-0">No classes assigned to you.</p>
                    </div>
                @else
                    <form action="{{ route('school.reports.themes.update') }}" method="POST">
                        @csrf
                        <div class="table-responsive mb-4">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Class Name</th>
                                        <th>Level</th>
                                        <th style="width: 280px;">Selected Theme</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($classes as $class)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $class->name }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-secondary border px-2.5 py-1">{{ $class->level }}</span>
                                            </td>
                                            <td>
                                                <select name="class_themes[{{ $class->id }}]" class="form-select rounded-3" required>
                                                    @foreach($themes as $key => $info)
                                                        <option value="{{ $key }}" {{ old('class_themes.'.$class->id, $class->report_card_theme) === $key ? 'selected' : '' }}>
                                                            {{ $info['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 8px;">
                                <i class="bi bi-save me-2"></i>Save Theme Settings
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
