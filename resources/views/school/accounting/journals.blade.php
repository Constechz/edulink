@extends('layouts.app')

@section('title', 'General Ledger Journal Entries')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Banner Card -->
    <div class="glass-card border-0 rounded-4 overflow-hidden mb-4 p-4 position-relative" style="background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.08) 0%, rgba(var(--bs-warning-rgb), 0.05) 100%); border-left: 5px solid var(--primary-color) !important;">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-primary small"><i class="bi bi-house me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active text-dark small" aria-current="page">Journal Entries</li>
            </ol>
        </nav>
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="fw-black mb-1 text-dark"><i class="bi bi-journals me-2 text-primary"></i>General Ledger Journals</h2>
                <p class="text-secondary mb-0 small max-w-2xl">Audit, record, and post double-entry transactions to the system's ledger.</p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2.5 rounded-3 border border-primary border-opacity-10"><i class="bi bi-journal-check me-1"></i> {{ $journals->total() }} Logged Entries</span>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-4 p-3 d-flex align-items-center" role="alert" style="background: rgba(40, 167, 69, 0.08); color: #28a745;">
            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
            <div>
                <strong class="d-block small">Posting Successful</strong>
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
        <!-- Main Ledger Logs Column -->
        <div class="col-lg-8">
            <div class="glass-card border-0 rounded-4 p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-list-stars me-2 text-primary"></i>Posted Journal Log</h5>
                        <p class="text-muted small mb-0">List of double-entry vouchers recorded in database memory</p>
                    </div>
                </div>

                @forelse($journals as $entry)
                    <div class="card bg-glass border-light-subtle rounded-4 mb-4 shadow-xs overflow-hidden transition-all hover-translate-y">
                        <!-- Voucher Header -->
                        <div class="card-header bg-light bg-opacity-10 border-bottom border-light-subtle p-3">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <h6 class="fw-bold text-dark mb-0">{{ $entry->description }}</h6>
                                        <code class="badge bg-secondary bg-opacity-15 text-dark fw-bold border border-secondary border-opacity-10">{{ $entry->reference ?? 'JV-N/A' }}</code>
                                    </div>
                                    <div class="text-muted small d-flex flex-wrap align-items-center gap-x-3 gap-y-1">
                                        <span><i class="bi bi-calendar-event me-1"></i>{{ $entry->entry_date->format('M d, Y') }}</span>
                                        <span><i class="bi bi-person me-1"></i>{{ $entry->creator->name ?? 'System Ledger' }}</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-2 rounded-3 small fw-semibold">
                                        <i class="bi bi-shield-check me-1"></i> Posted
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Voucher Lines Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="border-collapse: collapse;">
                                <thead>
                                    <tr class="text-muted small text-uppercase" style="background: rgba(var(--bs-primary-rgb), 0.02); font-size: 0.725rem; border-bottom: 2px solid var(--border-color);">
                                        <th class="ps-4" style="width: 50%;">Account details</th>
                                        <th class="text-end" style="width: 25%;">Debit</th>
                                        <th class="text-end pe-4" style="width: 25%;">Credit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($entry->lines as $line)
                                        <tr class="border-bottom border-light-subtle">
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary small px-2 py-1" style="font-size: 0.75rem;">{{ $line->account->account_code }}</span>
                                                    <span class="fw-bold text-dark" style="font-size: 0.875rem;">{{ $line->account->account_name }}</span>
                                                </div>
                                                @if($line->description)
                                                    <div class="text-secondary small mt-1 italic ps-4" style="font-size: 0.75rem; border-left: 2px solid var(--border-color);"><i class="bi bi-chat-left-dots me-1"></i>{{ $line->description }}</div>
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold text-dark py-3" style="font-size: 0.875rem;">
                                                {!! $line->debit > 0 ? '<span class="text-success">GHS ' . number_format($line->debit, 2) . '</span>' : '<span class="text-muted opacity-50">—</span>' !!}
                                            </td>
                                            <td class="text-end fw-bold text-dark pe-4 py-3" style="font-size: 0.875rem;">
                                                {!! $line->credit > 0 ? '<span class="text-primary">GHS ' . number_format($line->credit, 2) . '</span>' : '<span class="text-muted opacity-50">—</span>' !!}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted bg-light bg-opacity-5 rounded-4 border border-dashed border-light-subtle">
                        <i class="bi bi-journal-x fs-1 mb-2 text-secondary d-block"></i>
                        <span class="fw-semibold">No journal transactions recorded</span>
                        <p class="small text-muted mb-0 mt-1">Manual and system postings will generate double-entry voucher rows here.</p>
                    </div>
                @endforelse

                @if($journals->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $journals->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Create Voucher Column -->
        <div class="col-lg-4">
            <div class="glass-card border-0 rounded-4 p-4 shadow-sm sticky-top" style="top: 2rem; z-index: 5;">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-file-earmark-plus-fill me-2 text-warning"></i>New Journal Voucher</h5>
                
                <form action="{{ route('school.finance.journals.store') }}" method="POST" id="journalEntryForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="entry_date" class="form-label fw-bold text-dark">Posting Date</label>
                        <input type="date" name="entry_date" id="entry_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="reference" class="form-label fw-bold text-dark">Reference / Voucher #</label>
                        <input type="text" name="reference" id="reference" class="form-control rounded-3" placeholder="e.g. JV-2026-0001">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold text-dark">Memo / Description</label>
                        <textarea name="description" id="description" rows="2" class="form-control rounded-3" placeholder="Describe the transaction purpose..." required></textarea>
                    </div>

                    <div class="border-top border-light-subtle pt-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold text-dark"><i class="bi bi-diagram-2 me-1 text-primary"></i>Transaction Lines</span>
                            <button type="button" class="btn btn-xs btn-primary rounded-3 px-3 py-1.5 shadow-xs" id="addLineBtn">
                                <i class="bi bi-plus-lg me-1"></i> Add Row
                            </button>
                        </div>

                        <!-- Lines Container -->
                        <div id="linesContainer" style="max-height: 400px; overflow-y: auto; scrollbar-width: thin; padding-right: 2px;">
                            <!-- Line 1 -->
                            <div class="line-row border border-light-subtle rounded-4 p-3 mb-3 bg-light bg-opacity-5 position-relative">
                                <div class="mb-2">
                                    <select name="lines[0][account_id]" class="form-select account-select rounded-3" required>
                                        <option value="">Select Account</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->account_name }} ({{ $acc->account_code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light bg-opacity-10 text-muted px-2 small">Dr</span>
                                            <input type="number" name="lines[0][debit]" class="form-control debit-input rounded-end-3" step="0.01" min="0" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light bg-opacity-10 text-muted px-2 small">Cr</span>
                                            <input type="number" name="lines[0][credit]" class="form-control credit-input rounded-end-3" step="0.01" min="0" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <input type="text" name="lines[0][description]" class="form-control form-control-sm rounded-3" placeholder="Line description (optional)">
                                </div>
                            </div>

                            <!-- Line 2 -->
                            <div class="line-row border border-light-subtle rounded-4 p-3 mb-3 bg-light bg-opacity-5 position-relative">
                                <div class="mb-2">
                                    <select name="lines[1][account_id]" class="form-select account-select rounded-3" required>
                                        <option value="">Select Account</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->account_name }} ({{ $acc->account_code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light bg-opacity-10 text-muted px-2 small">Dr</span>
                                            <input type="number" name="lines[1][debit]" class="form-control debit-input rounded-end-3" step="0.01" min="0" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light bg-opacity-10 text-muted px-2 small">Cr</span>
                                            <input type="number" name="lines[1][credit]" class="form-control credit-input rounded-end-3" step="0.01" min="0" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <input type="text" name="lines[1][description]" class="form-control form-control-sm rounded-3" placeholder="Line description (optional)">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Real-time Balance Gauge Dashboard -->
                    <div class="card bg-light bg-opacity-10 border border-light-subtle rounded-4 mb-4 shadow-xs">
                        <div class="card-body p-3">
                            <div class="row text-center g-0">
                                <div class="col-6 border-end border-light-subtle">
                                    <div class="small text-muted mb-1"><i class="bi bi-arrow-down-left-circle me-1 text-success"></i>Total Debits</div>
                                    <div class="fw-black text-success fs-6" id="totalDebitsLabel">GHS 0.00</div>
                                </div>
                                <div class="col-6">
                                    <div class="small text-muted mb-1"><i class="bi bi-arrow-up-right-circle me-1 text-primary"></i>Total Credits</div>
                                    <div class="fw-black text-primary fs-6" id="totalCreditsLabel">GHS 0.00</div>
                                </div>
                            </div>
                            
                            <!-- Balanced / Unbalanced Alerts -->
                            <div class="alert alert-warning border-0 mb-0 mt-3 py-2 text-center rounded-3 small d-none" id="unbalancedWarning" style="background: rgba(255, 193, 7, 0.08); color: #ffc107;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Postings are unbalanced
                            </div>
                            <div class="alert alert-success border-0 mb-0 mt-3 py-2 text-center rounded-3 small d-none" id="balancedSuccess" style="background: rgba(40, 167, 69, 0.08); color: #28a745;">
                                <i class="bi bi-check-circle-fill me-1"></i> Debits match Credits
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning text-dark w-100 py-2.5 rounded-3 fs-6 fw-bold shadow-sm" id="submitBtn" disabled>
                        <i class="bi bi-shield-lock me-1"></i> Post Journal Voucher
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('linesContainer');
    const addBtn = document.getElementById('addLineBtn');
    const totalDebitsLabel = document.getElementById('totalDebitsLabel');
    const totalCreditsLabel = document.getElementById('totalCreditsLabel');
    const unbalancedWarning = document.getElementById('unbalancedWarning');
    const balancedSuccess = document.getElementById('balancedSuccess');
    const submitBtn = document.getElementById('submitBtn');
    
    let lineIndex = 2;

    addBtn.addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'line-row border border-light-subtle rounded-4 p-3 mb-3 bg-light bg-opacity-5 position-relative';
        div.innerHTML = `
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2.5 btn-remove-line" style="font-size: 0.65rem;" aria-label="Remove Line"></button>
            <div class="mb-2">
                <select name="lines[${lineIndex}][account_id]" class="form-select account-select rounded-3" required>
                    <option value="">Select Account</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->account_name }} ({{ $acc->account_code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light bg-opacity-10 text-muted px-2 small">Dr</span>
                        <input type="number" name="lines[${lineIndex}][debit]" class="form-control debit-input rounded-end-3" step="0.01" min="0" placeholder="0.00">
                    </div>
                </div>
                <div class="col-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light bg-opacity-10 text-muted px-2 small">Cr</span>
                        <input type="number" name="lines[${lineIndex}][credit]" class="form-control credit-input rounded-end-3" step="0.01" min="0" placeholder="0.00">
                    </div>
                </div>
            </div>
            <div>
                <input type="text" name="lines[${lineIndex}][description]" class="form-control form-control-sm rounded-3" placeholder="Line description (optional)">
            </div>
        `;
        container.appendChild(div);
        
        // Remove line listener
        div.querySelector('.btn-remove-line').addEventListener('click', function() {
            div.remove();
            calculateBalances();
        });

        // Add change listeners
        div.querySelector('.debit-input').addEventListener('input', calculateBalances);
        div.querySelector('.credit-input').addEventListener('input', calculateBalances);

        lineIndex++;
        calculateBalances();
    });

    // Attach listeners to initial rows
    document.querySelectorAll('.debit-input').forEach(input => input.addEventListener('input', calculateBalances));
    document.querySelectorAll('.credit-input').forEach(input => input.addEventListener('input', calculateBalances));

    function calculateBalances() {
        let debits = 0;
        let credits = 0;

        document.querySelectorAll('.debit-input').forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val)) debits += val;
        });

        document.querySelectorAll('.credit-input').forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val)) credits += val;
        });

        totalDebitsLabel.textContent = `GHS ${debits.toFixed(2)}`;
        totalCreditsLabel.textContent = `GHS ${credits.toFixed(2)}`;

        if (debits === 0 && credits === 0) {
            unbalancedWarning.classList.add('d-none');
            balancedSuccess.classList.add('d-none');
            submitBtn.disabled = true;
        } else if (Math.abs(debits - credits) < 0.009) {
            unbalancedWarning.classList.add('d-none');
            balancedSuccess.classList.remove('d-none');
            submitBtn.disabled = false;
        } else {
            unbalancedWarning.classList.remove('d-none');
            balancedSuccess.classList.add('d-none');
            submitBtn.disabled = true;
        }
    }
});
</script>
@endsection
@endsection
