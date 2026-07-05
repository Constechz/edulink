@extends('layouts.app')

@section('title', 'Student ID Card | EduLink')
@section('header_title', 'Student Identity Card')

@section('content')
<div class="container d-flex justify-content-center align-items-center py-5">
    <div class="glass-card p-5" style="max-width: 450px; width: 100%; background: linear-gradient(135deg, #002244 0%, #003366 100%); color: #ffffff; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
        <!-- Top header -->
        <div class="text-center mb-4">
            <h5 class="fw-bold mb-1 tracking-wider text-uppercase" style="letter-spacing: 1px;">{{ $school->name ?? 'Green Valley Int School' }}</h5>
            <span class="text-warning small text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Student Credential</span>
        </div>

        <div class="row g-4 align-items-center">
            <!-- Student Photo -->
            <div class="col-md-5 text-center">
                @if($student->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo))
                    <img src="{{ asset('storage/' . $student->photo) }}" class="img-thumbnail rounded-4" style="width: 130px; height: 130px; object-fit: cover; border: 3px solid var(--accent-color);">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->first_name . ' ' . $student->last_name) }}&background=FFD700&color=003366&size=128" class="img-thumbnail rounded-4" style="width: 130px; height: 130px; border: 3px solid #FFD700;">
                @endif
            </div>

            <!-- Student Info -->
            <div class="col-md-7">
                <div class="mb-2">
                    <span class="text-white-50 small text-uppercase" style="font-size: 0.7rem;">Name</span>
                    <h6 class="fw-bold mb-0 text-white">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</h6>
                </div>
                <div class="mb-2">
                    <span class="text-white-50 small text-uppercase" style="font-size: 0.7rem;">Student ID</span>
                    <h6 class="fw-semibold mb-0 text-warning">{{ $student->student_id_number }}</h6>
                </div>
                <div class="mb-2">
                    <span class="text-white-50 small text-uppercase" style="font-size: 0.7rem;">Grade / Class</span>
                    <h6 class="fw-semibold mb-0 text-white">{{ $student->currentClass->name ?? 'N/A' }}</h6>
                </div>
                <div>
                    <span class="text-white-50 small text-uppercase" style="font-size: 0.7rem;">NHIS No.</span>
                    <h6 class="fw-semibold mb-0 text-white">{{ $student->nhis_number ?? 'N/A' }}</h6>
                </div>
            </div>
        </div>

        <hr class="my-4 border-light opacity-20">

        <!-- Barcode / QR placeholder -->
        <div class="text-center">
            <div class="bg-white p-2 rounded-3 d-inline-block mb-2">
                <!-- Simple barcode generator stub using CSS -->
                <div class="d-flex align-items-center justify-content-center bg-dark text-white fw-mono px-3 py-1 rounded" style="font-family: monospace; letter-spacing: 4px; font-size: 0.8rem;">
                    *{{ $student->student_id_number }}*
                </div>
            </div>
            <p class="mb-0 text-white-50 small text-uppercase" style="font-size: 0.65rem;">System Verified QR / Barcode Scan</p>
        </div>
    </div>
</div>
@endsection
