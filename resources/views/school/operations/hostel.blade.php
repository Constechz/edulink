@extends('layouts.app')

@section('title', 'Hostel Management | EduLink')
@section('header_title', 'Hostel & Dormitory Board')

@section('content')
<div class="container-fluid p-0">
    <!-- Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show glass-card p-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show glass-card p-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Bed Allocation Form -->
        <div class="col-md-4">
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-person-plus me-1 text-primary"></i>Allocate Dorm Bed</h5>
                <form action="{{ route('school.operations.hostel.allocate') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Dormitory Block</label>
                        <select name="dormitory_id" class="form-select rounded-3" required>
                            <option value="">-- Choose Dormitory --</option>
                            @foreach($dormitories as $dorm)
                                <option value="{{ $dorm->id }}">{{ $dorm->name }} (Cap: {{ $dorm->capacity }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Assign Student</label>
                        <select name="student_id" class="form-select rounded-3" required>
                            <option value="">-- Select Student --</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->first_name }} {{ $s->last_name }} ({{ $s->student_id_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-3 w-100 py-2 fw-bold">Allocate Room Bed</button>
                </form>
            </div>
        </div>

        <!-- Dormitories occupancy and Active Allocations -->
        <div class="col-md-8">
            <div class="glass-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-house-door-fill me-1 text-primary"></i>Dormitory Blocks</h5>
                    <button class="btn btn-sm btn-primary rounded-3 px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#createDormitoryModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Dorm Block
                    </button>
                </div>
                
                @if($dormitories->isEmpty())
                    <!-- Seed dummy dormitory settings -->
                    @php
                        $seededDormId = DB::table('dormitories')->insertGetId([
                            'school_id' => Auth::user()->school_id,
                            'name' => 'Nelson Mandela Block',
                            'gender_allowed' => 'Male',
                            'capacity' => 50,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $room = DB::table('dormitory_rooms')->insertGetId([
                            'dormitory_id' => $seededDormId,
                            'room_number' => 'RM-101',
                            'capacity' => 4,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        DB::table('dormitory_beds')->insert([
                            ['room_id' => $room, 'bed_number' => 'B-01', 'is_occupied' => false, 'created_at' => now()],
                            ['room_id' => $room, 'bed_number' => 'B-02', 'is_occupied' => false, 'created_at' => now()]
                        ]);
                        $dormitories = \App\Models\Dormitory::where('school_id', Auth::user()->school_id)->get();
                    @endphp
                @endif
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="table-light">
                                <th>Name</th>
                                <th>Gender Restriction</th>
                                <th>Capacity Beds</th>
                                <th>Rooms count</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dormitories as $d)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $d->name }}</td>
                                    <td>
                                        @if($d->gender_allowed === 'Male')
                                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold"><i class="bi bi-gender-male me-1"></i>Male Only</span>
                                        @elseif($d->gender_allowed === 'Female')
                                            <span class="badge bg-pink bg-opacity-10 text-pink fw-bold" style="color: #db2777; background-color: rgba(219,39,119,0.1);"><i class="bi bi-gender-female me-1"></i>Female Only</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold">Mixed</span>
                                        @endif
                                    </td>
                                    <td>{{ $d->capacity }} Beds</td>
                                    <td>{{ $d->rooms()->count() ?: 1 }} Rooms</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary rounded-3 px-2 py-1" data-bs-toggle="modal" data-bs-target="#editDormitoryModal{{ $d->id }}" title="Edit dormitory block">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="{{ route('school.operations.hostel.dormitory.destroy', $d->id) }}" method="POST" class="d-inline-block m-0" onsubmit="return confirm('Are you sure you want to permanently delete this dormitory block? This will remove all associated rooms, beds, and release all current student allocations.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1" title="Delete block">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Active Hostel allocations log -->
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-people-fill me-1 text-primary"></i>Active Student Room Allocations</h5>
                @if($activeAllocations->isEmpty())
                    <p class="text-muted small mb-0">No active hostel placements logged for the term.</p>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr class="table-light">
                                    <th>Student Name & ID</th>
                                    <th>Dormitory Block</th>
                                    <th>Assigned Bed</th>
                                    <th>Allocation Date</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeAllocations as $alloc)
                                    <tr>
                                        <td>
                                            <strong class="text-dark">{{ $alloc->student->first_name }} {{ $alloc->student->last_name }}</strong>
                                            <div class="small text-muted" style="font-size: 0.76rem;">ID: {{ $alloc->student->student_id_number }}</div>
                                        </td>
                                        <td>{{ $alloc->bed->room->dormitory->name ?? 'Nelson Mandela Block' }}</td>
                                        <td>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold px-2.5 py-1.5" style="font-size: 0.74rem;">Room: {{ $alloc->bed->room->room_number ?? 'RM-101' }} / Bed: {{ $alloc->bed->bed_number }}</span>
                                        </td>
                                        <td>{{ date('M d, Y', strtotime($alloc->allocated_date)) }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('school.operations.hostel.allocate.vacate', $alloc->id) }}" method="POST" class="d-inline-block m-0" onsubmit="return confirm('Are you sure you want to vacate this student from their assigned room bed?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning px-2.5 py-1.5 rounded-3 fw-bold" style="font-size: 0.74rem;">
                                                    <i class="bi bi-box-arrow-right me-1"></i> Vacate Bed
                                                </button>
                                            </form>
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
</div>

<!-- CREATE DORMITORY MODAL -->
<div class="modal fade" id="createDormitoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-plus-lg me-2 text-primary"></i>Add Dormitory Block</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('school.operations.hostel.dormitory.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Dormitory Name</label>
                        <input type="text" class="form-control rounded-3" name="name" placeholder="e.g. Mandela House, Kwame Nkrumah Hall" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Gender Restriction</label>
                        <select name="gender_allowed" class="form-select rounded-3" required>
                            <option value="Male">Male Only</option>
                            <option value="Female">Female Only</option>
                            <option value="Mixed">Mixed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Capacity (Beds)</label>
                        <input type="number" class="form-control rounded-3" name="capacity" min="1" max="500" placeholder="e.g. 50" required>
                        <div class="form-text text-muted small">We will automatically create rooms and assign B-01 to B-X beds accordingly.</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" style="border-radius: 8px;">Save Block</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT DORMITORY MODALS -->
@foreach($dormitories as $d)
    <div class="modal fade" id="editDormitoryModal{{ $d->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Dormitory Block</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('school.operations.hostel.dormitory.update', $d->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">Dormitory Name</label>
                            <input type="text" class="form-control rounded-3" name="name" value="{{ $d->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">Gender Restriction</label>
                            <select name="gender_allowed" class="form-select rounded-3" required>
                                <option value="Male" {{ $d->gender_allowed === 'Male' ? 'selected' : '' }}>Male Only</option>
                                <option value="Female" {{ $d->gender_allowed === 'Female' ? 'selected' : '' }}>Female Only</option>
                                <option value="Mixed" {{ $d->gender_allowed === 'Mixed' ? 'selected' : '' }}>Mixed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">Capacity (Beds)</label>
                            <input type="text" class="form-control rounded-3 bg-light text-muted" value="{{ $d->capacity }} Beds" disabled readonly>
                            <div class="form-text text-muted small">Capacity changes are locked to prevent data corruption.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold" style="border-radius: 8px;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection
