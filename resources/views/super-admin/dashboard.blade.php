@extends('layouts.app')

@section('title', 'Super Admin Dashboard | ' . config('app.name', 'EduLink'))
@section('header_title', config('app.name', 'EduLink') . ' SaaS Platform Dashboard')

@section('content')
<div class="container-fluid p-0">
    <!-- Session Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Welcome banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4 text-white" style="background: linear-gradient(135deg, #003366 0%, #0c4a6e 100%);">
                <h2 style="font-weight: 700; margin-bottom: 0.5rem;">SaaS Platform Analytics</h2>
                <p class="mb-0 text-white-50" style="font-size: 0.95rem;">Real-time overview of active tenant schools, subscription health, and platform system performance in Ghana.</p>
            </div>
        </div>
    </div>

    <!-- Admin Feature Gating Quick Guide -->
    <div class="alert alert-info border-0 shadow-sm p-4 mb-4 rounded-4" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.05) 0%, rgba(13, 202, 240, 0.05) 100%); border-left: 5px solid #0d6efd !important;">
        <div class="d-flex align-items-start gap-3">
            <div class="fs-3 text-primary"><i class="bi bi-shield-check"></i></div>
            <div>
                <h6 class="fw-bold text-dark mb-1">Super Admin Subscriptions & Feature Gating Guide</h6>
                <p class="small text-secondary mb-2">You have full capacity to restrict operational modules and decide which pages are available to schools based on their subscription plans:</p>
                <ul class="small text-secondary mb-0 ps-3">
                    <li>Go to <a href="{{ route('super-admin.plans.index') }}" class="fw-bold text-primary">Subscription Plans</a> to Create/Edit tiers and select allowed features.</li>
                    <li>Unchecked modules are dynamically paywalled via route-level middleware gatekeepers.</li>
                    <li>Refer to the <a href="{{ route('school.docs.help', ['school_subdomain' => \App\Models\School::value('subdomain') ?? 'admin']) }}" class="fw-bold text-primary">Help & Reference Hub</a> under the <strong>Super Administrator Portal Guide</strong> tab for detailed instructions.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Metrics row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-4 d-flex align-items-center justify-content-between shadow-xs">
                <div>
                    <h6 class="text-muted mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Active Schools</h6>
                    <div class="card-metric">{{ $totalSchools }}</div>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-4 text-primary">
                    <i class="bi bi-building fs-3"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 d-flex align-items-center justify-content-between shadow-xs">
                <div>
                    <h6 class="text-muted mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">SaaS Plans</h6>
                    <div class="card-metric">{{ $totalPlans }}</div>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-4 text-success">
                    <i class="bi bi-card-list fs-3"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 d-flex align-items-center justify-content-between shadow-xs">
                <div>
                    <h6 class="text-muted mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Platform Users</h6>
                    <div class="card-metric">{{ $totalUsers }}</div>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded-4 text-warning">
                    <i class="bi bi-people fs-3"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 d-flex align-items-center justify-content-between shadow-xs">
                <div>
                    <h6 class="text-muted mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Monthly Revenue</h6>
                    <div class="card-metric" style="font-size: 1.8rem; word-break: break-all;">GHS 0.00</div>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded-4 text-info">
                    <i class="bi bi-wallet2 fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Multi-Tab Dashboard Directory -->
    <div class="row">
        <div class="col-12">
            <div class="glass-card p-4 shadow-sm">
                <!-- Nav Pills -->
                <ul class="nav nav-pills mb-4" id="dashboardTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
                            <i class="bi bi-pie-chart-fill me-2"></i>Platform Overview
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="schools-tab" data-bs-toggle="tab" data-bs-target="#schools" type="button" role="tab" aria-controls="schools" aria-selected="false">
                            <i class="bi bi-building me-2"></i>Registered Schools
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="false">
                            <i class="bi bi-people-fill me-2"></i>SaaS Users & Roles
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="dashboardTabContent">
                    
                    <!-- Tab 1: Charts Overview -->
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4 h-100 border-0">
                                    <h5 class="mb-4 fw-semibold text-dark"><i class="bi bi-pie-chart me-2 text-primary"></i>Subscription Status Distribution</h5>
                                    <div style="position: relative; height: 300px;">
                                        <canvas id="subscriptionChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4 h-100 border-0">
                                    <h5 class="mb-4 fw-semibold text-dark"><i class="bi bi-geo-alt me-2 text-danger"></i>Tenant Distribution by Region</h5>
                                    <div style="position: relative; height: 300px;">
                                        <canvas id="regionChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Registered Schools Directory -->
                    <div class="tab-pane fade" id="schools" role="tabpanel" aria-labelledby="schools-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-building me-2 text-secondary"></i>Schools Registry</h5>
                            <!-- Search -->
                            <form action="{{ route('super-admin.dashboard') }}" method="GET" class="d-flex gap-2">
                                <input type="hidden" name="tab" value="schools">
                                <input type="text" class="form-control form-control-sm rounded-3 py-1 px-3" name="school_search" placeholder="Search by name, code, email..." value="{{ request('school_search') }}">
                                <button type="submit" class="btn btn-sm btn-outline-dark rounded-3 px-3">Search</button>
                            </form>
                        </div>

                        @if($schoolsList->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-building-exclamation fs-1 d-block mb-2 text-secondary"></i>
                                <span>No registered schools found.</span>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table align-middle table-sm small table-hover">
                                    <thead>
                                        <tr>
                                            <th>School Info</th>
                                            <th>Subdomain</th>
                                            <th>Owner</th>
                                            <th>Plan</th>
                                            <th>Region</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($schoolsList as $sch)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-dark d-block">{{ $sch->name }}</span>
                                                    <span class="text-muted" style="font-size: 0.75rem;">Code: {{ $sch->school_code }}</span>
                                                </td>
                                                <td><code>{{ $sch->subdomain }}.{{ request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1' ? strtolower(config('app.name', 'EduLink')) . '.local' : preg_replace('/^(admin|www)\./', '', request()->getHost()) }}</code></td>
                                                <td>
                                                    <span class="d-block">{{ $sch->owner_name }}</span>
                                                    <span class="text-muted" style="font-size: 0.75rem;">{{ $sch->owner_email }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-secondary border px-2">{{ $sch->plan ? $sch->plan->name : 'N/A' }}</span>
                                                </td>
                                                <td>{{ $sch->region ?: 'N/A' }}</td>
                                                <td>
                                                    <span class="badge {{ $sch->is_active ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $sch->is_active ? 'Active' : 'Suspended' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <form action="{{ route('super-admin.schools.toggle-status', $sch->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn {{ $sch->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} btn-xs rounded-2 px-2 py-1">
                                                                <i class="bi {{ $sch->is_active ? 'bi-shield-slash' : 'bi-shield-check' }} me-1"></i>
                                                                {{ $sch->is_active ? 'Suspend' : 'Activate' }}
                                                            </button>
                                                        </form>

                                                        @if($sch->is_active)
                                                        <form action="{{ route('super-admin.schools.impersonate', $sch->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-primary btn-xs rounded-2 px-2 py-1">
                                                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                                                Login As
                                                            </button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $schoolsList->links() }}
                            </div>
                        @endif
                    </div>

                    <!-- Tab 3: SaaS Users Directory -->
                    <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-people-fill me-2 text-warning"></i>Users Directory</h5>
                            
                            <!-- Search & Filter Form -->
                            <form action="{{ route('super-admin.dashboard') }}" method="GET" class="d-flex gap-2">
                                <input type="hidden" name="tab" value="users">
                                
                                <select class="form-select form-select-sm rounded-3 py-1" name="role_filter" style="width: 150px;">
                                    <option value="">-- All Roles --</option>
                                    @foreach($rolesList as $role)
                                        <option value="{{ $role->slug }}" {{ request('role_filter') === $role->slug ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                <input type="text" class="form-control form-control-sm rounded-3 py-1 px-3" name="user_search" placeholder="Search name, email..." value="{{ request('user_search') }}">
                                <button type="submit" class="btn btn-sm btn-outline-dark rounded-3 px-3">Apply</button>
                            </form>
                        </div>

                        @if($usersList->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                                <span>No SaaS users found.</span>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table align-middle table-sm small table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>School Association</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($usersList as $usr)
                                            <tr>
                                                <td><span class="fw-bold text-dark">{{ $usr->name }}</span></td>
                                                <td><code>{{ $usr->email }}</code></td>
                                                <td>
                                                    <span class="text-dark">{{ $usr->school ? $usr->school->name : 'Platform Level / System' }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary text-dark text-capitalize px-2">{{ $usr->role ? $usr->role->name : 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $usr->is_active ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $usr->is_active ? 'Active' : 'Blocked' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <form action="{{ route('super-admin.users.toggle-status', $usr->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn {{ $usr->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} btn-xs rounded-2 px-2 py-1">
                                                            <i class="bi {{ $usr->is_active ? 'bi-lock' : 'bi-unlock' }} me-1"></i>
                                                            {{ $usr->is_active ? 'Block' : 'Unblock' }}
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $usersList->links() }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Persist Tab on reload/pagination
        const urlParams = new URLSearchParams(window.location.search);
        let activeTab = urlParams.get('tab');
        if (activeTab) {
            const tabButton = document.querySelector(`#dashboardTab button[data-bs-target="#${activeTab}"]`);
            if (tabButton) {
                const tab = new bootstrap.Tab(tabButton);
                tab.show();
            }
        }

        // Store selected tab in query string on click using History API
        document.querySelectorAll('#dashboardTab button').forEach(button => {
            button.addEventListener('shown.bs.tab', function (e) {
                const target = e.target.getAttribute('data-bs-target').substring(1);
                const url = new URL(window.location);
                url.searchParams.set('tab', target);
                window.history.pushState({}, '', url);
            });
        });

        // Subscription distribution Chart
        const subCtx = document.getElementById('subscriptionChart').getContext('2d');
        const statusCounts = @json($statusCounts);
        
        const labels = Object.keys(statusCounts).length ? Object.keys(statusCounts) : ['trial', 'active', 'suspended'];
        const data = Object.keys(statusCounts).length ? Object.values(statusCounts) : [1, 0, 0];

        new Chart(subCtx, {
            type: 'doughnut',
            data: {
                labels: labels.map(l => l.toUpperCase()),
                datasets: [{
                    data: data,
                    backgroundColor: ['#3b82f6', '#10b981', '#f43f5e', '#f59e0b', '#64748b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { family: 'Outfit' }
                        }
                    }
                }
            }
        });

        // Regional distribution chart
        const regCtx = document.getElementById('regionChart').getContext('2d');
        const regionCounts = @json($regionCounts);
        
        const regLabels = regionCounts.length ? regionCounts.map(r => r.region) : ['Greater Accra', 'Ashanti', 'Western', 'Northern'];
        const regData = regionCounts.length ? regionCounts.map(r => r.count) : [0, 0, 0, 0];

        new Chart(regCtx, {
            type: 'bar',
            data: {
                labels: regLabels,
                datasets: [{
                    label: 'Schools',
                    data: regData,
                    backgroundColor: '#003366',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    });
</script>
@endsection
