@extends('layouts.app')

@section('title', 'Library Management | EduLink')
@section('header_title', 'Library Management System')

@section('content')
<div class="container-fluid p-0">
    <!-- Success/Error Messages -->
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
        <!-- Borrow Form -->
        <div class="col-md-4">
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-journal-plus me-1 text-primary"></i>Borrow / Issue Book</h5>
                <form action="{{ route('school.operations.library.borrow') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Select Book</label>
                        <select name="book_id" class="form-select rounded-3" required>
                            <option value="">-- Choose Book --</option>
                            @foreach($books as $b)
                                <option value="{{ $b->id }}" {{ $b->copies_available <= 0 ? 'disabled' : '' }}>
                                    {{ $b->title }} (Avail: {{ $b->copies_available }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Borrower Student</label>
                        <select name="student_id" class="form-select rounded-3" required>
                            <option value="">-- Choose Student --</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->first_name }} {{ $s->last_name }} ({{ $s->student_id_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Due Date</label>
                        <input type="date" name="due_date" class="form-control rounded-3" required min="{{ date('Y-m-d') }}">
                    </div>
                    <button type="submit" class="btn btn-primary rounded-3 w-100 py-2">Issue Book</button>
                </form>
            </div>
        </div>

        <!-- Books List and Active Loans -->
        <div class="col-md-8">
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-bookshelf me-1 text-primary"></i>Books Catalog</h5>
                @if($books->isEmpty())
                    <!-- Seed dummy book category and catalog if empty -->
                    @php
                        $seededCat = DB::table('library_categories')->insertGetId([
                            'school_id' => Auth::user()->school_id,
                            'name' => 'General Science',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        DB::table('library_books')->insert([
                            'school_id' => Auth::user()->school_id,
                            'category_id' => $seededCat,
                            'title' => 'Integrated Science for Basic Schools',
                            'author' => 'A. B. Mensah',
                            'copies_total' => 20,
                            'copies_available' => 20,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $books = \App\Models\LibraryBook::where('school_id', Auth::user()->school_id)->get();
                    @endphp
                @endif
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Total</th>
                                <th>Available</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($books as $b)
                                <tr>
                                    <td class="fw-bold">{{ $b->title }}</td>
                                    <td>{{ $b->author }}</td>
                                    <td><span class="badge bg-secondary">{{ $b->category->name ?? 'Science' }}</span></td>
                                    <td>{{ $b->copies_total }}</td>
                                    <td>{{ $b->copies_available }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Active Loans list -->
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-clock-history me-1 text-primary"></i>Active Checkout Loans</h5>
                @if($activeLoans->isEmpty())
                    <p class="text-muted small">No active book loans currently checked out.</p>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Book</th>
                                    <th>Borrower</th>
                                    <th>Loan Date</th>
                                    <th>Due Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeLoans as $loan)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $loan->book->title }}</td>
                                        <td>{{ $loan->user->name }}</td>
                                        <td>{{ date('M d, Y', strtotime($loan->loan_date)) }}</td>
                                        <td><span class="text-danger fw-semibold">{{ date('M d, Y', strtotime($loan->due_date)) }}</span></td>
                                        <td>
                                            <form action="{{ route('school.operations.library.return', $loan->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success rounded-3"><i class="bi bi-check2-all"></i> Return</button>
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
@endsection
