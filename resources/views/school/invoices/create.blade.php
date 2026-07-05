@extends('layouts.app')

@section('title', 'Generate Invoices | EduLink')
@section('header_title', 'Student Billing Engine')

@section('content')
<div class="container-fluid p-0" style="max-width: 900px; margin: 0 auto;">

    <div class="mb-4">
        <a href="{{ route('school.finance.invoices.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Bills
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <ul class="mb-0 list-unstyled">
                @foreach($errors->all() as $error)
                    <li><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Selection Tabs -->
    <div class="d-flex mb-4 gap-2">
        <button type="button" class="btn btn-dark px-4 py-2" id="tabBulkBtn" onclick="switchBillingType('bulk')" style="border-radius: 10px;">
            <i class="bi bi-people-fill me-2"></i>Bulk Class Billing
        </button>
        <button type="button" class="btn btn-outline-dark px-4 py-2" id="tabIndividualBtn" onclick="switchBillingType('individual')" style="border-radius: 10px;">
            <i class="bi bi-person-fill me-2"></i>Individual Student Billing
        </button>
    </div>

    <!-- BULK BILLING FORM CARD -->
    <div class="glass-card p-5" id="bulkBillingCard">
        <h5 class="font-weight-bold mb-4" style="font-weight: 700;"><i class="bi bi-people-fill text-primary me-2"></i>Generate Invoices for Entire Class</h5>
        <form action="{{ route('school.finance.invoices.bulk-store') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label font-weight-bold small">Target Academic Class</label>
                    <select class="form-select" name="class_id" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label font-weight-bold small">Due Date</label>
                    <input type="date" class="form-control" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label font-weight-bold small">Academic Year</label>
                    <select class="form-select" name="academic_year_id" required>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label font-weight-bold small">Term</label>
                    <select class="form-select" name="term_id" required>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}">{{ $term->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 mt-4">
                    <h6 class="font-weight-bold border-bottom pb-2 mb-3"><i class="bi bi-list-check me-1 text-primary"></i>Select Fee Items to include in bill:</h6>
                    <div class="row g-3">
                        @forelse($feeStructures as $fee)
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 d-flex align-items-center">
                                    <input class="form-check-input me-3" type="checkbox" name="fee_structure_ids[]" value="{{ $fee->id }}" id="bulkFee{{ $fee->id }}">
                                    <div>
                                        <label class="form-check-label font-weight-bold text-dark mb-0 d-block small" for="bulkFee{{ $fee->id }}">{{ $fee->name }}</label>
                                        <span class="text-muted small">GHS {{ number_format($fee->amount, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-muted small">No active fee items configured in catalog. Create fee structures first.</div>
                        @endforelse
                    </div>
                </div>

                <div class="col-12 mt-5 text-end border-top pt-3">
                    <button type="submit" class="btn btn-primary px-5 py-2.5" style="border-radius: 8px;"><i class="bi bi-lightning-charge me-1"></i>Generate Bulk Invoices</button>
                </div>
            </div>
        </form>
    </div>

    <!-- INDIVIDUAL BILLING FORM CARD (Hidden by default) -->
    <div class="glass-card p-5 d-none" id="individualBillingCard">
        <h5 class="font-weight-bold mb-4" style="font-weight: 700;"><i class="bi bi-person-fill text-primary me-2"></i>Generate Individual Student Invoice</h5>
        <form action="{{ route('school.finance.invoices.store') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label font-weight-bold small">Select Student</label>
                    <select class="form-select select2" name="student_id" required>
                        <option value="">Select Student</option>
                        @foreach($students as $std)
                            <option value="{{ $std->id }}">{{ $std->first_name }} {{ $std->last_name }} ({{ $std->student_id_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label font-weight-bold small">Due Date</label>
                    <input type="date" class="form-control" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label font-weight-bold small">Academic Year</label>
                    <select class="form-select" name="academic_year_id" required>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label font-weight-bold small">Term</label>
                    <select class="form-select" name="term_id" required>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}">{{ $term->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label font-weight-bold small">Invoice Remarks / Notes</label>
                    <textarea class="form-control" name="notes" rows="2" placeholder="e.g. Balance carried forward or customized details..."></textarea>
                </div>

                <div class="col-12 mt-4">
                    <h6 class="font-weight-bold border-bottom pb-2 mb-3"><i class="bi bi-list-check me-1 text-primary"></i>Define Bill Items & Discounts:</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center">Select</th>
                                    <th>Fee Description</th>
                                    <th style="width: 150px;">Base Amount</th>
                                    <th style="width: 150px;">Discount (GHS)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feeStructures as $index => $fee)
                                    <tr>
                                        <td class="text-center">
                                            <input class="form-check-input" type="checkbox" name="fees[{{ $index }}][id]" value="{{ $fee->id }}" id="indFee{{ $fee->id }}">
                                        </td>
                                        <td>
                                            <label class="form-check-label font-weight-bold" for="indFee{{ $fee->id }}">{{ $fee->name }}</label>
                                        </td>
                                        <td>GHS {{ number_format($fee->amount, 2) }}</td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" name="fees[{{ $index }}][discount]" value="0" placeholder="0.00">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">No fee structure items configured.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-12 mt-5 text-end border-top pt-3">
                    <button type="submit" class="btn btn-primary px-5 py-2.5" style="border-radius: 8px;"><i class="bi bi-save me-1"></i>Generate Bill</button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function switchBillingType(type) {
        const bulkCard = document.getElementById('bulkBillingCard');
        const individualCard = document.getElementById('individualBillingCard');
        const bulkBtn = document.getElementById('tabBulkBtn');
        const individualBtn = document.getElementById('tabIndividualBtn');

        if (type === 'bulk') {
            bulkCard.classList.remove('d-none');
            individualCard.classList.add('d-none');
            
            bulkBtn.className = 'btn btn-dark px-4 py-2';
            individualBtn.className = 'btn btn-outline-dark px-4 py-2';
        } else {
            bulkCard.classList.add('d-none');
            individualCard.classList.remove('d-none');
            
            bulkBtn.className = 'btn btn-outline-dark px-4 py-2';
            individualBtn.className = 'btn btn-dark px-4 py-2';
        }
    }
</script>
@endsection
