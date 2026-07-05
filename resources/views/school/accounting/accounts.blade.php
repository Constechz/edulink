@extends('layouts.app')

@section('title', 'Chart of Accounts')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chart of Accounts</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Chart of Accounts</h1>
                    <p class="text-muted mb-0 small">Manage school financial ledgers, account classification, and parent-child code structures.</p>
                </div>
                <div>
                    <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                        <i class="bi bi-plus-circle me-1"></i> Add Account
                    </button>
                </div>
            </div>

            <!-- Validation/Success Feedback Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                        <div>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Account Type Tabs -->
            <ul class="nav nav-pills mb-4" id="accountTabs" role="tablist">
                @foreach(['Asset' => 'Assets (1000-1999)', 'Liability' => 'Liabilities (2000-2999)', 'Equity' => 'Equity (3000-3999)', 'Revenue' => 'Revenue (4000-4999)', 'Expense' => 'Expenses (5000-5999)'] as $type => $label)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }} px-4 py-2 me-2" id="{{ $type }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $type }}-pane" type="button" role="tab" aria-controls="{{ $type }}-pane" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ $label }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="accountTabsContent">
                @foreach($grouped as $type => $typeAccounts)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $type }}-pane" role="tabpanel" aria-labelledby="{{ $type }}-tab" tabindex="0">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0 pt-4 pb-0 ps-4">
                                <h5 class="fw-bold text-dark mb-0">{{ $type }} Ledger Tree</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table align-middle table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4" style="width: 25%;">Account Code</th>
                                                <th style="width: 40%;">Account Name</th>
                                                <th style="width: 20%;">Parent Account</th>
                                                <th class="text-end pe-4" style="width: 15%;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($typeAccounts->sortBy('account_code') as $account)
                                                <tr>
                                                    <td class="ps-4 fw-mono fw-bold text-primary">{{ $account->account_code }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if($account->parent_id)
                                                                <span class="text-muted me-2">—</span>
                                                            @endif
                                                            <span class="fw-semibold text-dark">{{ $account->account_name }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-muted">
                                                        {{ $account->parent ? $account->parent->account_name . ' (' . $account->parent->account_code . ')' : 'Root Account' }}
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        @if($account->is_active)
                                                            <span class="badge bg-success-soft text-success px-3 py-2 rounded-pill">Active</span>
                                                        @else
                                                            <span class="badge bg-secondary text-white px-3 py-2 rounded-pill">Inactive</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-5 text-muted">
                                                        <i class="bi bi-folder-x fs-1 d-block mb-3 text-secondary"></i>
                                                        No accounts registered in this category.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Add Account Modal -->
<div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="addAccountModalLabel">Register New Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('school.finance.accounts.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="account_type" class="form-label fw-bold">Classification Type</label>
                        <select name="account_type" id="account_type" class="form-select" required>
                            <option value="">-- Select Type --</option>
                            <option value="Asset">Asset</option>
                            <option value="Liability">Liability</option>
                            <option value="Equity">Equity</option>
                            <option value="Revenue">Revenue</option>
                            <option value="Expense">Expense</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="account_code" class="form-label fw-bold">Account Code</label>
                        <input type="text" name="account_code" id="account_code" class="form-control fw-mono" placeholder="e.g. 1105 or 4110" required>
                        <div class="form-text small">Use standard numerical classifications (1xxx Assets, 2xxx Liabilities, etc.)</div>
                    </div>

                    <div class="mb-3">
                        <label for="account_name" class="form-label fw-bold">Account Name</label>
                        <input type="text" name="account_name" id="account_name" class="form-control" placeholder="e.g. Petty Cash, Canteen Revenue" required>
                    </div>

                    <div class="mb-3">
                        <label for="parent_id" class="form-label fw-bold">Parent Account (Optional)</label>
                        <select name="parent_id" id="parent_id" class="form-select">
                            <option value="">-- None (Root Account) --</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->account_name }} ({{ $acc->account_code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
