<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 20px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .header-table td {
            vertical-align: top;
        }
        .school-name {
            font-size: 22px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 5px 0;
        }
        .school-info {
            color: #475569;
            font-size: 11px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            text-align: right;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .invoice-meta {
            text-align: right;
            color: #475569;
            font-size: 11px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table td {
            width: 50%;
            vertical-align: top;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 10px;
            width: 90%;
        }
        .info-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .info-list li {
            margin-bottom: 4px;
            color: #334155;
        }
        .info-list strong {
            color: #0f172a;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #cbd5e1;
            font-size: 11px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }
        .items-table .text-right {
            text-align: right;
        }
        .summary-table {
            width: 40%;
            float: right;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .summary-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .summary-table .label {
            color: #475569;
            font-weight: 500;
        }
        .summary-table .value {
            text-align: right;
            font-weight: bold;
            color: #0f172a;
        }
        .summary-table .total-row td {
            background-color: #f1f5f9;
            border-bottom: 2px solid #cbd5e1;
            font-size: 14px;
        }
        .summary-table .balance-row td {
            background-color: #eff6ff;
            border-bottom: 2px solid #bfdbfe;
            color: #1d4ed8;
            font-size: 15px;
        }
        .summary-table .balance-row .value {
            color: #1d4ed8;
        }
        .payments-section {
            clear: both;
            margin-top: 40px;
        }
        .payments-table {
            width: 100%;
            border-collapse: collapse;
        }
        .payments-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #cbd5e1;
            font-size: 10px;
            text-transform: uppercase;
        }
        .payments-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
            font-size: 12px;
        }
        .payments-table .text-right {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-paid {
            background-color: #dcfce7;
            color: #15803d;
        }
        .badge-partial {
            background-color: #fef9c3;
            color: #a16207;
        }
        .badge-pending {
            background-color: #f1f5f9;
            color: #475569;
        }
        .badge-overdue {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        .badge-waived {
            background-color: #f3e8ff;
            color: #6b21a8;
        }
        .notes-box {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8fafc;
            border-left: 4px solid #cbd5e1;
            color: #475569;
            font-size: 11px;
            width: 50%;
            float: left;
        }
        .footer {
            margin-top: 100px;
            text-align: center;
            color: #94a3b8;
            font-size: 10px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td>
                    <h1 class="school-name">{{ auth()->user()->school->name ?? config('app.name', 'EduLink') . ' School' }}</h1>
                    <div class="school-info">
                        @if($invoice->campus)
                            Campus: {{ $invoice->campus->name }}<br>
                        @endif
                        Email: {{ auth()->user()->school->email ?? 'finance@' . strtolower(config('app.name', 'edulink')) . '.edu' }}<br>
                        Phone: {{ auth()->user()->school->phone ?? 'N/A' }}
                    </div>
                </td>
                <td>
                    <div class="invoice-title">Invoice</div>
                    <div class="invoice-meta">
                        <strong>Invoice No:</strong> {{ $invoice->invoice_number }}<br>
                        <strong>Date Issued:</strong> {{ $invoice->created_at->format('M d, Y') }}<br>
                        <strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}<br>
                        <strong>Status:</strong> 
                        <span class="badge badge-{{ $invoice->status }}">
                            {{ $invoice->status }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Details -->
        <table class="details-table">
            <tr>
                <td>
                    <div class="section-title">Bill To</div>
                    <ul class="info-list">
                        <li><strong>Student Name:</strong> {{ $invoice->student->first_name }} {{ $invoice->student->last_name }}</li>
                        <li><strong>ID Number:</strong> {{ $invoice->student->student_id_number }}</li>
                        <li><strong>Class:</strong> {{ $invoice->student->currentClass->name ?? 'N/A' }}</li>
                    </ul>
                </td>
                <td>
                    <div class="section-title">Academic Info</div>
                    <ul class="info-list">
                        <li><strong>Academic Year:</strong> {{ $invoice->academicYear->name }}</li>
                        <li><strong>Term:</strong> {{ $invoice->term->name }}</li>
                    </ul>
                </td>
            </tr>
        </table>

        <!-- Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Fee Description</th>
                    <th class="text-right" style="width: 15%;">Original Amount</th>
                    <th class="text-right" style="width: 15%;">Discount</th>
                    <th class="text-right" style="width: 20%;">Net Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="text-right">GHS {{ number_format($item->amount, 2) }}</td>
                        <td class="text-right">GHS {{ number_format($item->discount_amount, 2) }}</td>
                        <td class="text-right">GHS {{ number_format($item->net_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Notes and Summary -->
        <div>
            @if($invoice->notes)
                <div class="notes-box">
                    <strong>Invoice Notes:</strong><br>
                    {{ $invoice->notes }}
                </div>
            @endif

            <table class="summary-table">
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="value">GHS {{ number_format($invoice->items->sum('amount'), 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Total Discount</td>
                    <td class="value">GHS {{ number_format($invoice->items->sum('discount_amount'), 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label">Total Invoiced</td>
                    <td class="value">GHS {{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Amount Paid</td>
                    <td class="value">GHS {{ number_format($invoice->amount_paid, 2) }}</td>
                </tr>
                <tr class="balance-row">
                    <td class="label">Balance Due</td>
                    <td class="value">GHS {{ number_format($invoice->balance, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Payments Log -->
        @if($invoice->payments->isNotEmpty())
            <div class="payments-section">
                <div class="section-title" style="width: 100%;">Payment Transactions</div>
                <table class="payments-table">
                    <thead>
                        <tr>
                            <th>Receipt Number</th>
                            <th>Date</th>
                            <th>Payment Method</th>
                            <th>Reference Number</th>
                            <th class="text-right">Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                            <tr>
                                <td>{{ $payment->receipt_number }}</td>
                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td>{{ strtoupper($payment->method) }}</td>
                                <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                                <td class="text-right">GHS {{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    @if($payment->is_reversed)
                                        <span style="color: #b91c1c; font-weight: bold;">REVERSED</span>
                                    @else
                                        <span style="color: #15803d; font-weight: bold;">SUCCESS</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            Thank you for your prompt payment.<br>
            {{ config('app.name', 'EduLink') }} Ghana ERP - Secure & Dynamic Financial Management System.
        </div>
    </div>
</body>
</html>
