@extends('layouts.app')

@section('title', 'My Transit & Bus Routes | EduLink')
@section('header_title', 'My School Bus Transit')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Banner Card -->
    <div class="glass-card border-0 rounded-4 overflow-hidden mb-4 p-4 position-relative" style="background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.08) 0%, rgba(var(--bs-warning-rgb), 0.05) 100%); border-left: 5px solid var(--primary-color) !important;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="fw-black mb-1 text-dark"><i class="bi bi-bus-front me-2 text-primary"></i>School Transit & Bus Routes</h2>
                <p class="text-secondary mb-0 small max-w-2xl">View designated school transport routes, active vehicle fleet plates, and stoppage schedules.</p>
            </div>
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2.5 rounded-3 border border-primary border-opacity-10"><i class="bi bi-geo-alt me-1"></i> Transit Directory</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Routes and stops -->
        <div class="col-md-8">
            <div class="glass-card p-4 shadow-sm border-0 rounded-4">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-map me-2 text-primary"></i>Transit Routes & Stoppage Plan</h5>
                
                @if($routes->isEmpty())
                    <div class="text-center py-5 text-muted bg-light bg-opacity-5 rounded-4 border border-dashed border-light-subtle">
                        <i class="bi bi-map-fill fs-1 mb-2 text-secondary d-block"></i>
                        <span class="fw-semibold">No transport routes active</span>
                        <p class="small text-muted mb-0 mt-1">Please check back later or contact the administration office for details.</p>
                    </div>
                @else
                    <div class="d-flex flex-column gap-4">
                        @foreach($routes as $rt)
                            <div class="card bg-glass border-light-subtle rounded-4 shadow-xs overflow-hidden">
                                <div class="card-body p-3.5">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark fs-5">{{ $rt->name }}</h6>
                                            <span class="text-secondary small d-flex align-items-center gap-1">
                                                <i class="bi bi-geo-alt-fill text-muted"></i>
                                                {{ $rt->start_point }} <i class="bi bi-arrow-right text-primary"></i> {{ $rt->end_point }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Stops list -->
                                    <div class="mt-3 pt-3 border-top border-light-subtle">
                                        <strong class="small d-block text-dark mb-2"><i class="bi bi-stopwatch text-warning me-1"></i>Transit Stops Schedule</strong>
                                        
                                        @if($rt->stops->isEmpty())
                                            <div class="text-center py-3 text-muted small bg-light bg-opacity-5 rounded-3">
                                                No scheduled stops defined for this route.
                                            </div>
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover align-middle mb-0" style="border-collapse: collapse;">
                                                    <thead>
                                                        <tr class="text-muted small text-uppercase" style="font-size: 0.7rem; background: rgba(var(--bs-primary-rgb), 0.02);">
                                                            <th class="ps-3" style="width: 50%;">Stop Location</th>
                                                            <th class="text-end" style="width: 25%;">Pickup Time</th>
                                                            <th class="text-end pe-3" style="width: 25%;">Dropoff Time</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($rt->stops as $stop)
                                                            <tr class="border-bottom border-light-subtle">
                                                                <td class="ps-3 py-2.5 small fw-bold text-dark">
                                                                    <i class="bi bi-geo-alt text-danger me-1"></i>{{ $stop->stop_name }}
                                                                </td>
                                                                <td class="text-end py-2.5 small text-primary fw-semibold">
                                                                    {{ date('h:i A', strtotime($stop->pickup_time)) }}
                                                                </td>
                                                                <td class="text-end pe-3 py-2.5 small text-secondary">
                                                                    {{ date('h:i A', strtotime($stop->dropoff_time)) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Bus fleet -->
        <div class="col-md-4">
            <div class="glass-card p-4 shadow-sm border-0 rounded-4">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-truck me-2 text-primary"></i>Vehicle Fleet</h5>
                
                @if($vehicles->isEmpty())
                    <div class="text-center py-5 text-muted bg-light bg-opacity-5 rounded-4 border border-dashed border-light-subtle">
                        <i class="bi bi-bus-front-fill fs-1 mb-2 text-secondary d-block"></i>
                        <span class="fw-semibold">No vehicles registered</span>
                    </div>
                @else
                    <div class="d-flex flex-column gap-3.5">
                        @foreach($vehicles as $veh)
                            <div class="card bg-glass border-light-subtle rounded-4 p-3 shadow-xs position-relative overflow-hidden transition-all hover-translate-y">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge @if($veh->status === 'active') bg-success bg-opacity-10 text-success @elseif($veh->status === 'maintenance') bg-warning bg-opacity-10 text-warning @else bg-danger bg-opacity-10 text-danger @endif px-2.5 py-1 rounded-pill small border border-light-subtle">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i>{{ ucfirst($veh->status) }}
                                    </span>
                                </div>
                                
                                <div class="text-center py-2">
                                    <div class="fs-1 text-primary mb-2 opacity-80"><i class="bi bi-bus-front-fill"></i></div>
                                    <h6 class="fw-bold mb-1 text-dark fs-5">{{ $veh->model }}</h6>
                                    <span class="badge bg-secondary bg-opacity-15 text-dark fw-bold border border-secondary border-opacity-10 mb-2 px-2.5 py-1.5">{{ $veh->plate_number }}</span>
                                    <div class="text-muted small mt-1"><i class="bi bi-people me-1"></i>Capacity: <strong>{{ $veh->capacity }} seats</strong></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection