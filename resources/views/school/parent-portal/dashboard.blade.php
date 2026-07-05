@extends('layouts.app')

@section('title', 'Parent Portal | EduLink')
@section('header_title', 'Parent Portal Dashboard')

@section('content')
<style>
    .avatar-circle-lg {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.4rem;
        box-shadow: 0 4px 12px rgba(0, 51, 102, 0.15);
    }

    .metric-card-green {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid rgba(25, 135, 84, 0.12) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card-green:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(25, 135, 84, 0.08);
    }

    .metric-card-red {
        background: linear-gradient(135deg, #fff5f5 0%, #ffe3e3 100%);
        border: 1px solid rgba(220, 53, 69, 0.15) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card-red:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(220, 53, 69, 0.08);
    }

    .metric-card-amber {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border: 1px solid rgba(255, 193, 7, 0.25) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card-amber:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(255, 193, 7, 0.08);
    }

    .table-row-hover {
        transition: background-color 0.2s ease;
    }
    .table-row-hover:hover {
        background-color: rgba(0, 51, 102, 0.02) !important;
    }

    /* Text Visibility and High-Contrast Overrides */
    .text-muted {
        color: #64748b !important;
    }
    .text-secondary {
        color: #475569 !important;
    }
    .text-dark {
        color: #0f172a !important;
    }
    .card-metric {
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: -0.5px;
    }
</style>

<div class="container-fluid p-0">
    <!-- Success / Info Session Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm p-3 mb-4" role="alert" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($error) || !$activeChild)
        <div class="alert alert-warning glass-card p-4" style="border-radius: 16px;">
            <h5 class="fw-bold text-dark"><i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>No Children Profile Linked</h5>
            <p class="mb-0 text-secondary">{{ $error ?? 'No student accounts could be linked to your email contact.' }}</p>
        </div>
    @else
        <!-- Header Child Switcher Banner -->
        <div class="glass-card p-4 mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.05);">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-circle-lg bg-primary text-white">
                    {{ substr($activeChild->first_name, 0, 1) }}{{ substr($activeChild->last_name, 0, 1) }}
                </div>
                <div>
                    <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Parent Portal Dashboard</span>
                    <h3 class="fw-bold mb-1 text-primary" style="font-weight: 800;">{{ $activeChild->first_name }} {{ $activeChild->middle_name }} {{ $activeChild->last_name }}</h3>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-2.5 py-1.5 fw-bold" style="border-radius: 6px; font-family: monospace; font-size: 0.76rem;">
                            ID: {{ $activeChild->student_id_number }}
                        </span>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1.5 fw-bold" style="border-radius: 6px; font-size: 0.76rem;">
                            Class: {{ $activeChild->currentClass->name ?? 'N/A' }}
                        </span>
                        @if($activeChild->currentStream)
                            <span class="badge bg-info bg-opacity-10 text-info px-2.5 py-1.5 fw-bold" style="border-radius: 6px; font-size: 0.76rem;">
                                Stream: {{ $activeChild->currentStream->name }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle rounded-3 py-2 px-3 fw-bold d-inline-flex align-items-center gap-2" type="button" id="childSelector" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.85rem; border-radius: 10px !important;">
                        <i class="bi bi-person-fill-gear"></i> Switch Child Profile
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="childSelector" style="border-radius: 12px;">
                        @foreach($children as $child)
                            <li>
                                <a class="dropdown-item py-2.5 fw-bold text-dark {{ $child->id === $activeChild->id ? 'active bg-primary text-white' : '' }}" href="{{ route('school.parent-portal.select-child', $child->id) }}" style="font-size: 0.85rem;">
                                    <i class="bi bi-person-fill me-2"></i>{{ $child->first_name }} {{ $child->last_name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Portal Notifications & Absence Alerts -->
        @if(isset($portalMessages) && !$portalMessages->isEmpty())
            <div class="row mb-4">
                <div class="col-12">
                    @foreach($portalMessages as $msg)
                        @php
                            $isAbsence = str_contains(strtolower($msg->subject), 'absence') || str_contains(strtolower($msg->subject), 'absent');
                        @endphp
                        <div class="alert {{ $isAbsence ? 'alert-danger' : 'alert-info' }} border-0 shadow-sm d-flex align-items-start justify-content-between p-3 mb-2" style="border-radius: 12px; background-color: {{ $isAbsence ? 'rgba(220, 53, 69, 0.08)' : 'rgba(13, 202, 240, 0.08)' }}; border-left: 4px solid {{ $isAbsence ? '#dc3545' : '#0dcaf0' }} !important;">
                            <div class="d-flex gap-3">
                                <div class="fs-4 text-{{ $isAbsence ? 'danger' : 'info' }}">
                                    <i class="bi {{ $isAbsence ? 'bi-exclamation-octagon-fill' : 'bi-info-circle-fill' }}"></i>
                                </div>
                                <div style="flex: 1;">
                                    <h6 class="fw-bold mb-1 text-dark">{{ $msg->subject }}</h6>
                                    <p class="mb-0 text-secondary" style="font-size: 0.88rem;">{{ $msg->body }}</p>
                                    <span class="text-muted small mt-1 d-block" style="font-size: 0.72rem;">{{ $msg->created_at ? \Carbon\Carbon::parse($msg->created_at)->diffForHumans() : 'Just now' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Metric Summaries -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="glass-card p-4 text-center metric-card-green">
                    <div class="fs-1 text-success mb-2"><i class="bi bi-calendar2-check-fill"></i></div>
                    <div class="card-metric text-success">{{ $attendanceRate }}%</div>
                    <div class="text-muted small fw-bold text-uppercase mt-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Attendance Rate</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card p-4 text-center metric-card-red">
                    <div class="fs-1 text-danger mb-2"><i class="bi bi-wallet2"></i></div>
                    <div class="card-metric text-danger">GHS {{ number_format($outstandingAmount, 2) }}</div>
                    <div class="text-muted small fw-bold text-uppercase mt-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Outstanding Fees</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card p-4 text-center metric-card-amber">
                    <div class="fs-1 text-warning mb-2" style="color: #b08d00 !important;"><i class="bi bi-file-earmark-text-fill"></i></div>
                    <div class="card-metric text-warning" style="color: #b08d00 !important;">{{ $pendingInvoicesCount }}</div>
                    <div class="text-muted small fw-bold text-uppercase mt-1" style="font-size: 0.72rem; letter-spacing: 0.5px; color: #b08d00 !important;">Pending Invoices</div>
        </div>

        @if($hostelAllocation)
            <!-- Child's Hostel & Accommodation Assignment Details -->
            <div class="glass-card p-4 mb-4" style="background: linear-gradient(135deg, rgba(25, 135, 84, 0.05) 0%, rgba(0, 51, 102, 0.05) 100%); border-left: 5px solid #198754;">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="fw-bold text-dark mb-1"><i class="bi bi-house-door-fill text-success me-2"></i>Child's Hostel Accommodation</h5>
                        <p class="text-secondary small mb-0"><strong>{{ $activeChild->first_name }}</strong> has been allocated an active room bed in <strong>{{ $hostelAllocation->bed->room->dormitory->name ?? 'Nelson Mandela Block' }}</strong>.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-3 fs-7">
                            Room: {{ $hostelAllocation->bed->room->room_number ?? 'RM-101' }} / Bed: {{ $hostelAllocation->bed->bed_number }}
                        </span>
                        <div class="small text-muted mt-1" style="font-size: 0.75rem;">Check-in Date: {{ date('M d, Y', strtotime($hostelAllocation->allocated_date)) }}</div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-4 mb-5">
            <!-- Recent published grades -->
            <div class="col-md-7">
                <div class="glass-card p-4 h-100" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.05);">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-journal-check me-2 text-primary"></i>Recent Term Academic Scores</h5>
                    
                    @if($recentScores->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-file-bar-graph display-4 mb-3 d-block text-muted"></i>
                            <span class="fw-semibold">No recent subject scores published for this term.</span>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle" style="font-size: 0.9rem;">
                                <thead>
                                    <tr class="table-light">
                                        <th class="border-0 rounded-start">Subject</th>
                                        <th class="border-0">Term Reference</th>
                                        <th class="border-0">Percentage Score</th>
                                        <th class="border-0 rounded-end text-center">Grade Awarded</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentScores as $score)
                                        <tr class="table-row-hover">
                                            <td class="fw-bold text-dark">{{ $score->subject->name ?? 'N/A' }}</td>
                                            <td class="text-secondary">{{ $score->term->name ?? 'N/A' }}</td>
                                            <td class="text-primary fw-bold" style="font-family: monospace; font-size: 0.95rem;">{{ $score->grand_total }}%</td>
                                            <td class="text-center">
                                                <span class="badge bg-success px-2.5 py-1.5 fw-bold" style="border-radius: 6px;">
                                                    {{ $score->grade }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Bulletins & Notices -->
            <div class="col-md-5">
                <div class="glass-card p-4 h-100" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.05);">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-bell-fill me-2 text-warning"></i>School Announcement Board</h5>
                    
                    @if($announcements->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-bell-slash display-4 mb-3 d-block text-muted"></i>
                            <span class="fw-semibold">No announcements posted yet.</span>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-3">
                            @foreach($announcements as $ann)
                                <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid rgba(0,0,0,0.04); border-left: 4px solid var(--primary-color) !important;">
                                    <h6 class="fw-bold text-dark mb-1.5 d-flex align-items-center justify-content-between">
                                        {{ $ann->title }}
                                        @if(isset($ann->is_pinned) && $ann->is_pinned)
                                            <span class="badge bg-warning text-dark px-2 py-1" style="font-size: 0.62rem; border-radius: 4px;"><i class="bi bi-pin-angle-fill me-0.5"></i> Pinned</span>
                                        @endif
                                    </h6>
                                    <p class="mb-2 text-secondary small" style="line-height: 1.45;">{{ Str::limit($ann->content, 140) }}</p>
                                    <span class="text-muted small d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-clock"></i> {{ date('M d, Y', strtotime($ann->created_at)) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
