@extends('layouts.app')

@section('title', 'Class Timetable | EduLink')
@section('header_title', 'Weekly Class Timetable')

@section('content')
<div class="container-fluid p-0">
    <div class="glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar3 me-2 text-primary"></i>Weekly Schedule Overview</h5>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fs-7 fw-semibold">Class: {{ $student->currentClass->name ?? 'N/A' }}</span>
        </div>

        <div class="row g-4">
            @foreach($days as $day)
                @php
                    $daySlots = $slots->where('day_of_week', $day)->sortBy('start_time');
                @endphp
                <div class="col-lg">
                    <div class="card border-0 bg-light rounded-4 h-100 shadow-sm">
                        <div class="card-header bg-primary text-white text-center py-2 fw-bold rounded-top-4 border-0">
                            {{ $day }}
                        </div>
                        <div class="card-body p-3 d-flex flex-column gap-2">
                            @if($daySlots->isEmpty())
                                <div class="text-center py-4 text-muted small">
                                    <i class="bi bi-calendar-x fs-4 mb-1 d-block"></i>
                                    No slots
                                </div>
                            @else
                                @foreach($daySlots as $slot)
                                    <div class="p-3 bg-white rounded-3 border-start border-4 border-primary shadow-xs">
                                        <div class="fw-bold text-dark small">{{ $slot->subject->name ?? 'N/A' }}</div>
                                        <span class="text-primary small fw-semibold" style="font-size: 0.75rem;">
                                            {{ date('h:i A', strtotime($slot->start_time)) }} - {{ date('h:i A', strtotime($slot->end_time)) }}
                                        </span>
                                        <div class="text-muted mt-1" style="font-size: 0.7rem;">
                                            <i class="bi bi-person me-1"></i>{{ $slot->teacher->name ?? 'N/A' }}
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
