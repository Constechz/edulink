@extends('layouts.app')

@section('title', 'Employee Payslip | ' . config('app.name', 'EduLink'))
@section('header_title', 'Staff Payslip')

@section('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .main-wrapper {
            margin-left: 0 !important;
        }
        .navbar-top, .sidebar, .no-print {
            display: none !important;
        }
        .print-area, .print-area * {
            visibility: visible;
        }
        .print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none !important;
            box-shadow: none !important;
            background: white !important;
            padding: 0 !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    
    <!-- Action buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="{{ route('school.operations.hr.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i>Back to HR Center
        </a>
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-printer me-2"></i>Print Payslip
        </button>
    </div>

    <!-- Payslip sheet -->
    <div class="card glass-card border-0 p-5 mx-auto print-area" style="max-width: 800px; border-radius: 16px;">
        
        <!-- Header -->
        <div class="row align-items-center mb-4 pb-4 border-bottom">
            <div class="col-sm-7">
                <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-globe-europe-africa me-2"></i>{{ strtoupper(config('app.name', 'EduLink')) }} GHANA ERP</h4>
                <p class="text-muted small mb-0">GES Compliant SaaS Educational Management Platform</p>
                <p class="text-muted small mb-0">Ghana Basic & Second Cycle Administration</p>
            </div>
            <div class="col-sm-5 text-sm-end mt-3 mt-sm-0">
                <h5 class="fw-bold mb-1 text-dark">PAYSLIP</h5>
                <span class="badge bg-secondary text-dark px-3 py-2 fw-semibold">
                    Period: {{ $payslip->payrollRun && $payslip->payrollRun->period ? $payslip->payrollRun->period->name : 'N/A' }}
                </span>
            </div>
        </div>

        <!-- Employee Info -->
        <div class="row g-3 mb-4 p-3 bg-light rounded-4">
            <div class="col-md-6">
                <table class="table table-borderless table-sm mb-0 small">
                    <tr>
                        <td class="text-muted w-40">Employee Name:</td>
                        <td class="fw-bold text-dark">{{ $payslip->staff && $payslip->staff->user ? $payslip->staff->user->name : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Staff ID:</td>
                        <td class="text-dark">{{ $payslip->staff ? $payslip->staff->staff_id_number : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Department:</td>
                        <td class="text-dark">{{ $payslip->staff && $payslip->staff->department ? $payslip->staff->department->name : 'Teaching Staff' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm mb-0 small">
                    <tr>
                        <td class="text-muted w-40">Payment Date:</td>
                        <td class="text-dark">{{ date('F d, Y', strtotime($payslip->created_at)) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Bank Name:</td>
                        <td class="text-dark">Commercial Bank of Ghana</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Account Number:</td>
                        <td class="text-dark">******1029</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Earnings and Deductions tables -->
        <div class="row g-4 mb-4">
            
            <!-- Earnings column -->
            <div class="col-md-6">
                <div class="border rounded-4 p-3 h-100 bg-white">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Earnings & Allowances</h6>
                    <table class="table table-borderless table-sm small">
                        <tbody>
                            <tr>
                                <td>Basic Salary</td>
                                <td class="text-end fw-bold text-dark">GHS {{ number_format($payslip->basic_salary, 2) }}</td>
                            </tr>
                            @foreach($payslip->items->where('type', 'earning') as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td class="text-end text-dark">GHS {{ number_format($item->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Deductions column -->
            <div class="col-md-6">
                <div class="border rounded-4 p-3 h-100 bg-white">
                    <h6 class="fw-bold text-danger border-bottom pb-2 mb-3">Taxes & Deductions</h6>
                    <table class="table table-borderless table-sm small">
                        <tbody>
                            @if($payslip->items->where('type', 'deduction')->isEmpty())
                                <tr>
                                    <td class="text-muted py-3 text-center" colspan="2">No deductions recorded.</td>
                                </tr>
                            @else
                                @foreach($payslip->items->where('type', 'deduction') as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td class="text-end text-dark">GHS {{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Financial Summary totals -->
        <div class="p-3 bg-light rounded-4 mb-4">
            <div class="row g-3 text-center">
                <div class="col-4">
                    <span class="text-muted small d-block">Gross Earnings</span>
                    <strong class="text-dark fs-5">GHS {{ number_format($payslip->gross_salary, 2) }}</strong>
                </div>
                <div class="col-4 border-start border-end">
                    <span class="text-muted small d-block">Total Deductions</span>
                    <strong class="text-danger fs-5">GHS {{ number_format($payslip->total_deductions, 2) }}</strong>
                </div>
                <div class="col-4">
                    <span class="text-muted small d-block">Net Take-Home Pay</span>
                    <strong class="text-success fs-5">GHS {{ number_format($payslip->net_salary, 2) }}</strong>
                </div>
            </div>
        </div>

        <!-- Footer signatures -->
        <div class="row pt-5 mt-4 border-top text-center small text-muted">
            <div class="col-6">
                <div style="height: 40px;"></div>
                <div class="border-top pt-2 mx-auto" style="max-width: 200px;">Prepared by (Finance)</div>
            </div>
            <div class="col-6">
                <div style="height: 40px;"></div>
                <div class="border-top pt-2 mx-auto" style="max-width: 200px;">Authorized Signatory</div>
            </div>
        </div>

    </div>
</div>
@endsection
