@extends('layouts.app')

@section('title', 'Inventory & Stock | EduLink')
@section('header_title', 'Warehouse Inventory Ledger')

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
        <!-- Stock Transaction Log form -->
        <div class="col-md-4">
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-arrow-left-right me-1 text-primary"></i>Record Stock Movement</h5>
                <form action="{{ route('school.operations.inventory.transaction') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Inventory Item</label>
                        <select name="inventory_item_id" class="form-select rounded-3" required>
                            <option value="">-- Choose Item --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} (Stock: {{ $item->quantity_in_stock }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Movement Type</label>
                        <select name="type" class="form-select rounded-3" required>
                            <option value="in">Restock / Addition (IN)</option>
                            <option value="out">Issue / Disbursement (OUT)</option>
                            <option value="adjustment">Stock Audit (Adjustment)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Quantity</label>
                        <input type="number" name="quantity" class="form-control rounded-3" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Notes / Remarks</label>
                        <textarea name="notes" class="form-control rounded-3" rows="3" placeholder="Reference, supplier or recipient details..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-3 w-100 py-2">Log Stock Movement</button>
                </form>
            </div>
        </div>

        <!-- Inventory List and Stock Log Ledger -->
        <div class="col-md-8">
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-box-seam me-1 text-primary"></i>Stock Catalog Registry</h5>
                @if($items->isEmpty())
                    <!-- Seed dummy inventory items -->
                    @php
                        $seededCat = DB::table('inventory_categories')->insertGetId([
                            'school_id' => Auth::user()->school_id,
                            'name' => 'Stationery',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        DB::table('inventory_items')->insert([
                            'school_id' => Auth::user()->school_id,
                            'category_id' => $seededCat,
                            'name' => 'A4 Printing Sheets',
                            'code' => 'ST-A4',
                            'quantity_in_stock' => 15,
                            'reorder_level' => 5,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $items = \App\Models\InventoryItem::where('school_id', Auth::user()->school_id)->get();
                    @endphp
                @endif
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Stock Level</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $item->code }}</td>
                                    <td class="fw-bold">{{ $item->name }}</td>
                                    <td><span class="badge bg-secondary">{{ $item->category->name ?? 'Stationery' }}</span></td>
                                    <td>{{ $item->quantity_in_stock }} {{ $item->unit_of_measure }}</td>
                                    <td>
                                        @if($item->quantity_in_stock <= $item->reorder_level)
                                            <span class="badge bg-danger"><i class="bi bi-exclamation-triangle"></i> Low Stock</span>
                                        @else
                                            <span class="badge bg-success">In Stock</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Transaction list -->
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-clock-history me-1 text-primary"></i>Movement Transaction Logs</h5>
                @if($transactions->isEmpty())
                    <p class="text-muted small">No stock transactions registered yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Item</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Recorded By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $txn)
                                    <tr>
                                        <td>{{ date('M d, Y', strtotime($txn->transaction_date)) }}</td>
                                        <td class="fw-bold text-dark">{{ $txn->item->name }}</td>
                                        <td>
                                            <span class="badge {{ $txn->type === 'in' ? 'bg-success' : ($txn->type === 'out' ? 'bg-danger' : 'bg-warning') }} text-uppercase">
                                                {{ $txn->type }}
                                            </span>
                                        </td>
                                        <td class="fw-bold">{{ $txn->quantity }}</td>
                                        <td class="small text-muted">{{ $txn->recorder->name }}</td>
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
