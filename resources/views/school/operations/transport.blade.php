@extends('layouts.app')

@section('title', 'Transport Routes | EduLink')
@section('header_title', 'School Bus Fleet & Routes')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Banner Card -->
    <div class="glass-card border-0 rounded-4 overflow-hidden mb-4 p-4 position-relative" style="background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.08) 0%, rgba(var(--bs-warning-rgb), 0.05) 100%); border-left: 5px solid var(--primary-color) !important;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="fw-black mb-1 text-dark"><i class="bi bi-bus-front me-2 text-primary"></i>School Transit & Fleet Management</h2>
                <p class="text-secondary mb-0 small max-w-2xl">Create and manage transport routes, transit schedules, and school vehicle logs.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-primary rounded-3 px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createRouteModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Route
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createVehicleModal">
                    <i class="bi bi-truck me-1"></i> Add Vehicle
                </button>
            </div>
        </div>
    </div>

    <!-- Success & Error Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-4 p-3 d-flex align-items-center" role="alert" style="background: rgba(40, 167, 69, 0.08); color: #28a745;">
            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
            <div>
                <strong class="d-block small">Action Successful</strong>
                <span class="small">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-4 p-3 d-flex align-items-center" role="alert" style="background: rgba(220, 53, 69, 0.08); color: #dc3545;">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <div>
                <strong class="d-block small">Validation Error</strong>
                <ul class="mb-0 list-unstyled small">
                    @foreach($errors->all() as $error)
                        <li><i class="bi bi-dot me-1"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Routes and stops -->
        <div class="col-md-8">
            <div class="glass-card p-4 shadow-sm border-0 rounded-4">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-map me-2 text-primary"></i>Transit Routes & Stoppage Plan</h5>
                
                @if($routes->isEmpty())
                    <!-- Seed dummy transit details -->
                    @php
                        $seededRouteId = DB::table('transport_routes')->insertGetId([
                            'school_id' => Auth::user()->school_id,
                            'route_name' => 'Route A - Adenta Expressway',
                            'start_point' => 'Campus Main Gate',
                            'end_point' => 'Adenta Barrier',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        DB::table('route_stops')->insert([
                            ['route_id' => $seededRouteId, 'stop_name' => 'Madina Market', 'pickup_time' => '06:30:00', 'dropoff_time' => '16:30:00', 'created_at' => now()],
                            ['route_id' => $seededRouteId, 'stop_name' => 'Ritz Junction', 'pickup_time' => '06:45:00', 'dropoff_time' => '16:15:00', 'created_at' => now()]
                        ]);
                        $routes = \App\Models\TransportRoute::where('school_id', Auth::user()->school_id)->get();
                    @endphp
                @endif

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
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-xs btn-outline-primary rounded-3 px-2 py-1.5" data-bs-toggle="modal" data-bs-target="#addStopModal{{ $rt->id }}">
                                            <i class="bi bi-plus-lg me-1"></i> Add Stop
                                        </button>
                                        <form action="{{ route('school.operations.transport.route.delete', $rt->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transport route and all of its scheduled stops?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger rounded-3 px-2 py-1.5" aria-label="Delete Route">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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
            </div>
        </div>

        <!-- Bus fleet -->
        <div class="col-md-4">
            <div class="glass-card p-4 shadow-sm border-0 rounded-4">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-truck me-2 text-primary"></i>Vehicle Fleet</h5>
                
                @if($vehicles->isEmpty())
                    <!-- Seed dummy vehicle -->
                    @php
                        DB::table('vehicles')->insert([
                            'school_id' => Auth::user()->school_id,
                            'plate_number' => 'GT-9090-26',
                            'model' => 'Toyota Coaster (30-seater)',
                            'capacity' => 30,
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $vehicles = \App\Models\Vehicle::where('school_id', Auth::user()->school_id)->get();
                    @endphp
                @endif

                <div class="d-flex flex-column gap-3.5">
                    @foreach($vehicles as $veh)
                        <div class="card bg-glass border-light-subtle rounded-4 p-3 shadow-xs position-relative overflow-hidden transition-all hover-translate-y">
                            <!-- Top Status and Delete -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge @if($veh->status === 'active') bg-success bg-opacity-10 text-success @elseif($veh->status === 'maintenance') bg-warning bg-opacity-10 text-warning @else bg-danger bg-opacity-10 text-danger @endif px-2.5 py-1 rounded-pill small border border-light-subtle">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i>{{ ucfirst($veh->status) }}
                                </span>
                                
                                <form action="{{ route('school.operations.transport.vehicle.delete', $veh->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this vehicle from the fleet?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link p-0 text-decoration-none text-danger" aria-label="Delete Vehicle">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
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
            </div>
        </div>
    </div>
</div>

<!-- Modal for Adding Route -->
<div class="modal fade" id="createRouteModal" tabindex="-1" aria-labelledby="createRouteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg bg-glass">
            <form action="{{ route('school.operations.transport.route.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="createRouteModalLabel"><i class="bi bi-map text-primary me-2"></i>Create New Transport Route</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="route_name" class="form-label text-dark fw-semibold">Route Name</label>
                        <input type="text" class="form-control rounded-3" id="route_name" name="route_name" required placeholder="e.g. Route B - Madina Expressway">
                    </div>
                    <div class="mb-3">
                        <label for="start_point" class="form-label text-dark fw-semibold">Start Point</label>
                        <input type="text" class="form-control rounded-3" id="start_point" name="start_point" required placeholder="e.g. Campus Main Gate">
                    </div>
                    <div class="mb-3">
                        <label for="end_point" class="form-label text-dark fw-semibold">End Point</label>
                        <input type="text" class="form-control rounded-3" id="end_point" name="end_point" required placeholder="e.g. Adenta Barrier">
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Save Route</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Adding Vehicle -->
<div class="modal fade" id="createVehicleModal" tabindex="-1" aria-labelledby="createVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg bg-glass">
            <form action="{{ route('school.operations.transport.vehicle.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="createVehicleModalLabel"><i class="bi bi-truck text-primary me-2"></i>Register Vehicle in Fleet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="model" class="form-label text-dark fw-semibold">Vehicle Model / Name</label>
                        <input type="text" class="form-control rounded-3" id="model" name="model" required placeholder="e.g. Nissan Civilian (26-seater)">
                    </div>
                    <div class="mb-3">
                        <label for="plate_number" class="form-label text-dark fw-semibold">License Plate Number</label>
                        <input type="text" class="form-control rounded-3" id="plate_number" name="plate_number" required placeholder="e.g. GS-4820-26">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="capacity" class="form-label text-dark fw-semibold">Seating Capacity</label>
                            <input type="number" class="form-control rounded-3" id="capacity" name="capacity" required min="1" placeholder="e.g. 26">
                        </div>
                        <div class="col-6">
                            <label for="status" class="form-label text-dark fw-semibold">Operational Status</label>
                            <select class="form-select rounded-3" id="status" name="status" required>
                                <option value="active" selected>Active</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Register Vehicle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal list for Adding Stops to each Route -->
@foreach($routes as $rt)
    <div class="modal fade" id="addStopModal{{ $rt->id }}" tabindex="-1" aria-labelledby="addStopModalLabel{{ $rt->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg bg-glass">
                <form action="{{ route('school.operations.transport.stop.store', $rt->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 p-4 pb-0">
                        <h5 class="modal-title fw-bold text-dark" id="addStopModalLabel{{ $rt->id }}"><i class="bi bi-geo-alt text-primary me-2"></i>Add Stop to {{ $rt->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="stop_name_{{ $rt->id }}" class="form-label text-dark fw-semibold">Stop Location Name</label>
                            <input type="text" class="form-control rounded-3" id="stop_name_{{ $rt->id }}" name="stop_name" required placeholder="e.g. Ritz Junction">
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label for="pickup_time_{{ $rt->id }}" class="form-label text-dark fw-semibold">Morning Pickup Time</label>
                                <input type="time" class="form-control rounded-3" id="pickup_time_{{ $rt->id }}" name="pickup_time" required value="06:30">
                            </div>
                            <div class="col-6">
                                <label for="dropoff_time_{{ $rt->id }}" class="form-label text-dark fw-semibold">Evening Dropoff Time</label>
                                <input type="time" class="form-control rounded-3" id="dropoff_time_{{ $rt->id }}" name="dropoff_time" required value="16:30">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4">Add Stop</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection
