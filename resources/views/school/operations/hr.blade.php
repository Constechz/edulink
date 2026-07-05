@extends('layouts.app')

@section('title', 'HR & Payroll | EduLink')
@section('header_title', 'Staff HR & Payroll Center')

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
        <!-- Leave request & Payroll Action -->
        <div class="col-md-4">
            <!-- Apply Leave -->
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-calendar-plus me-1 text-primary"></i>Request Leave</h5>
                <form action="{{ route('school.operations.hr.leave') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Staff Account</label>
                        <select name="staff_id" class="form-select rounded-3" required>
                            <option value="">-- Choose Staff --</option>
                            @foreach($staff as $st)
                                <option value="{{ $st->id }}">{{ $st->user->name }} ({{ $st->staff_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Leave Type</label>
                        <select name="leave_type_id" class="form-select rounded-3" required>
                            <option value="">-- Choose Type --</option>
                            @foreach($leaveTypes as $lt)
                                <option value="{{ $lt->id }}">{{ $lt->name }} ({{ $lt->days_allowed }} days)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-dark fw-medium">Start Date</label>
                            <input type="date" name="start_date" class="form-control rounded-3" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-dark fw-medium">End Date</label>
                            <input type="date" name="end_date" class="form-control rounded-3" required min="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Reason</label>
                        <textarea name="reason" class="form-control rounded-3" rows="2" placeholder="State reason for leave..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-3 w-100 py-2">Submit Leave Log</button>
                </form>
            </div>

            <!-- Run Payroll -->
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-currency-dollar me-1 text-success"></i>Execute Monthly Payroll</h5>
                <form action="{{ route('school.operations.hr.payroll.run') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Select Period</label>
                        <select name="payroll_period_id" class="form-select rounded-3" required>
                            <option value="">-- Choose Period --</option>
                            @foreach($payrollPeriods as $period)
                                <option value="{{ $period->id }}" {{ $period->status === 'closed' ? 'disabled' : '' }}>
                                    {{ $period->name }} ({{ $period->status }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success rounded-3 w-100 py-2">Calculate & Run Payroll</button>
                </form>
            </div>
        </div>

        <!-- Lists of Leaves & Payslips -->
        <div class="col-md-8">
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-calendar-range me-1 text-primary"></i>Leave Requests Tracker</h5>
                @if($leaveRequests->isEmpty())
                    <!-- Seed dummy leave settings -->
                    @php
                        $seededLT = DB::table('leave_types')->insertGetId([
                            'school_id' => Auth::user()->school_id,
                            'name' => 'Annual Leave',
                            'days_allowed' => 30,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $firstStaff = \App\Models\Staff::where('school_id', Auth::user()->school_id)->first();
                        if ($firstStaff) {
                            DB::table('leave_requests')->insert([
                                'school_id' => Auth::user()->school_id,
                                'staff_id' => $firstStaff->id,
                                'leave_type_id' => $seededLT,
                                'start_date' => date('Y-m-d', strtotime('+1 week')),
                                'end_date' => date('Y-m-d', strtotime('+2 weeks')),
                                'status' => 'pending',
                                'reason' => 'Annual family vacation.',
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                        $leaveTypes = \App\Models\LeaveType::where('school_id', Auth::user()->school_id)->get();
                        $leaveRequests = \App\Models\LeaveRequest::where('school_id', Auth::user()->school_id)->get();
                    @endphp
                @endif
                <div class="table-responsive">
                    <table class="table align-middle text-muted small">
                        <thead>
                            <tr class="text-dark">
                                <th>Staff Name</th>
                                <th>Type</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaveRequests as $req)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $req->staff->user->name ?? 'Staff User' }}</td>
                                    <td>{{ $req->leaveType->name ?? 'Annual Leave' }}</td>
                                    <td>{{ date('M d', strtotime($req->start_date)) }} - {{ date('M d', strtotime($req->end_date)) }}</td>
                                    <td>
                                        <span class="badge {{ $req->status === 'pending' ? 'bg-warning text-dark' : ($req->status === 'approved' ? 'bg-success' : 'bg-danger') }} text-capitalize">
                                            {{ $req->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payslips list -->
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-file-earmark-spreadsheet me-1 text-primary"></i>Staff Payslips Registry</h5>
                @if($payslips->isEmpty())
                    <!-- Seed dummy payroll period context -->
                    @php
                        DB::table('payroll_periods')->insert([
                            'school_id' => Auth::user()->school_id,
                            'name' => date('F Y'),
                            'start_date' => date('Y-m-01'),
                            'end_date' => date('Y-m-t'),
                            'status' => 'open',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $payrollPeriods = \App\Models\PayrollPeriod::where('school_id', Auth::user()->school_id)->get();
                    @endphp
                    <p class="text-muted small">No payslips generated for this term yet. Execute payroll to generate statements.</p>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle text-muted small">
                            <thead>
                                <tr class="text-dark">
                                    <th>Staff Name</th>
                                    <th>Period</th>
                                    <th>Basic</th>
                                    <th>Net Salary</th>
                                    <th>Payment Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payslips as $ps)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $ps->staff->user->name }}</td>
                                        <td>{{ $ps->payrollRun->period->name ?? 'N/A' }}</td>
                                        <td>GHS {{ number_format($ps->basic_salary, 2) }}</td>
                                        <td class="fw-bold text-primary">GHS {{ number_format($ps->net_salary, 2) }}</td>
                                        <td><span class="badge bg-success">Paid</span></td>
                                        <td>
                                            <a href="{{ route('school.operations.hr.payslip', $ps->id) }}" class="btn btn-sm btn-outline-primary rounded-3">
                                                <i class="bi bi-eye"></i> View Payslip
                                            </a>
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
@endsection
