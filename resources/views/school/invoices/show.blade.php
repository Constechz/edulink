@extends('layouts.app')

@section('title', 'Invoice Details | ' . config('app.name', 'EduLink'))
@section('header_title', 'Student Bill & Ledger')

@section('content')
<div class="container-fluid p-0">

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('school.finance.invoices.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Invoices
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('school.finance.invoices.pdf', $invoice->id) }}" class="btn btn-sm btn-outline-primary px-3">
                <i class="bi bi-file-pdf me-1"></i>Export PDF Bill
            </a>
            @if($invoice->balance > 0)
                <button class="btn btn-sm btn-success px-4" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                    <i class="bi bi-wallet2 me-1"></i>Receive Payment
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <ul class="mb-0 list-unstyled">
                @foreach($errors->all() as $error)
                    <li><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <!-- Invoice Details Main Column -->
        <div class="col-lg-8">
            <div class="glass-card p-4 mb-4">
                <div class="d-flex justify-content-between border-bottom pb-3 mb-4">
                    <div>
                        <h4 class="font-weight-bold mb-1" style="font-weight: 700;">{{ config('app.name', 'EduLink') }} School Invoice</h4>
                        <span class="text-muted small">Academic Billing Service</span>
                    </div>
                    <div class="text-end">
                        <h5 class="text-primary font-weight-bold mb-1" style="font-weight: 700;">#{{ $invoice->invoice_number }}</h5>
                        <span class="badge @if($invoice->status === 'paid') bg-success @elseif($invoice->status === 'partial') bg-warning text-dark @else bg-danger @endif px-2.5 py-1.5 rounded-3">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                </div>

                <!-- Bio grid -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <span class="text-muted small d-block">STUDENT DETAILS</span>
                        <span class="font-weight-bold text-dark fs-5" style="font-weight: 600;">{{ $invoice->student->first_name }} {{ $invoice->student->last_name }}</span>
                        <span class="text-muted small d-block">Student ID: {{ $invoice->student->student_id_number }}</span>
                        <span class="text-muted small d-block">Class: {{ $invoice->student->currentClass ? $invoice->student->currentClass->name : 'Unallocated' }}</span>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="text-muted small d-block">BILLING INFORMATION</span>
                        <span class="text-dark d-block"><strong>Term:</strong> {{ $invoice->term->name }}</span>
                        <span class="text-dark d-block"><strong>Academic Year:</strong> {{ $invoice->academicYear->name }}</span>
                        <span class="text-dark d-block text-danger"><strong>Due Date:</strong> {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A' }}</span>
                    </div>
                </div>

                <!-- Items Table -->
                <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">Invoice Items Breakdown</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Item Description</th>
                                <th class="text-end">Base Amount</th>
                                <th class="text-end">Scholarship/Discount</th>
                                <th class="text-end">Net Billed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td>{{ $item->description }}</td>
                                    <td class="text-end">GHS {{ number_format($item->amount, 2) }}</td>
                                    <td class="text-end text-danger">-GHS {{ number_format($item->discount_amount, 2) }}</td>
                                    <td class="text-end font-weight-bold">GHS {{ number_format($item->net_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Total section -->
                <div class="row justify-content-end">
                    <div class="col-md-6 text-end">
                        <div class="d-flex justify-content-between py-1.5 border-bottom small">
                            <span class="text-muted">Total Gross Amount:</span>
                            <span class="text-dark font-weight-bold">GHS {{ number_format($invoice->items->sum('amount'), 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1.5 border-bottom small text-danger">
                            <span>Total Discounts:</span>
                            <span>-GHS {{ number_format($invoice->items->sum('discount_amount'), 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1.5 border-bottom fs-5 font-weight-bold text-dark" style="font-weight: 700;">
                            <span>Net Billed Amount:</span>
                            <span>GHS {{ number_format($invoice->total_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1.5 border-bottom text-success small">
                            <span>Total Payments Received:</span>
                            <span>GHS {{ number_format($invoice->amount_paid, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 fs-5 font-weight-bold text-danger" style="font-weight: 700;">
                            <span>Current Outstanding Balance:</span>
                            <span>GHS {{ number_format($invoice->balance, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payments History Ledger -->
            <div class="glass-card p-4">
                <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-wallet2 text-success me-1"></i>Allocated Transaction History</h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Receipt Number</th>
                                <th>Payment Date</th>
                                <th>Method</th>
                                <th>Ref/Reference</th>
                                <th class="text-end">Amount</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoice->payments as $payment)
                                <tr>
                                    <td class="font-weight-bold" style="font-weight: 600;">{{ $payment->receipt_number }}</td>
                                    <td>{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-secondary px-2.5 py-1" style="border-radius: 6px;">{{ strtoupper($payment->method) }}</span>
                                    </td>
                                    <td>{{ $payment->reference_number ?: 'N/A' }}</td>
                                    <td class="text-end font-weight-bold">GHS {{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        @if($payment->is_reversed)
                                            <span class="badge bg-danger">Reversed</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(!$payment->is_reversed)
                                            <form action="{{ route('school.finance.payments.reverse', $payment->id) }}" method="POST" class="m-0" onsubmit="return confirm('Reverse this payment? This subtracts the amount from invoice paid and updates accounts.');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-1" style="border-radius: 6px;">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reverse
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted">No payments logged against this invoice yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Notes Column -->
        <div class="col-lg-4">
            <div class="glass-card p-4 mb-4">
                <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">Staff Memo / Notes</h6>
                <p class="small text-muted">{{ $invoice->notes ?: 'No memo logged on this invoice.' }}</p>
                <hr>
                <div class="small">
                    <div><strong>Invoice ID:</strong> #{{ $invoice->id }}</div>
                    <div><strong>Created By:</strong> {{ $invoice->creator ? $invoice->creator->name : 'System Generated' }}</div>
                    <div><strong>Created At:</strong> {{ $invoice->created_at->format('d M Y, h:i A') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- RECEIVE PAYMENT MODAL -->
    @if($invoice->balance > 0)
        <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    <div class="modal-header border-bottom-0 p-4 pb-0">
                        <h5 class="modal-title font-weight-bold" style="font-weight: 700;">Receive Invoice Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('school.finance.payments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small font-weight-bold">Amount to pay (GHS)</label>
                                    <input type="number" step="0.01" class="form-control" name="amount" max="{{ $invoice->balance }}" value="{{ $invoice->balance }}" required>
                                    <div class="form-text">Cannot exceed outstanding balance of GHS {{ number_format($invoice->balance, 2) }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small font-weight-bold">Payment Method</label>
                                    <select class="form-select" name="method" required>
                                        <option value="cash" selected>Cash Payment</option>
                                        <option value="momo">Mobile Money (MoMo)</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="cheque">Cheque</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small font-weight-bold">Payment Date</label>
                                    <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small font-weight-bold">Transaction Reference Number</label>
                                    <input type="text" class="form-control" name="reference_number" placeholder="e.g. MoMo Transaction ID, Bank deposit reference">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small font-weight-bold">Notes / Comments</label>
                                    <textarea class="form-control" name="notes" rows="2" placeholder="e.g. Paid by parent Mr. Appiah"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 p-4">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                            <button type="submit" class="btn btn-success px-4" style="border-radius: 8px;">Record Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
