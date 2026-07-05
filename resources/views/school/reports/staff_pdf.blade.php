<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Directory Report - {{ $school->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        @page {
            margin: 30px 40px 30px 40px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .school-title {
            font-size: 22px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            text-align: center;
        }
        .school-subtitle {
            font-size: 11px;
            font-weight: bold;
            color: #475569;
            margin: 3px 0 0 0;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .school-contact {
            font-size: 10px;
            color: #64748b;
            margin: 2px 0 0 0;
            text-align: center;
        }
        .title-block {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            padding: 10px;
            font-size: 13px;
            text-transform: uppercase;
            margin-bottom: 15px;
            color: #0f172a;
            letter-spacing: 2px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 5px 8px;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #cbd5e1;
        }
        .meta-label {
            background-color: #f1f5f9;
            color: #475569;
            width: 18%;
        }
        .meta-value {
            background-color: #ffffff;
            color: #0f172a;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .report-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 8px 10px;
            border: 1px solid #0f172a;
            text-align: left;
        }
        .report-table td {
            padding: 8px 10px;
            font-size: 10px;
            border: 1px solid #cbd5e1;
        }
        .report-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-active {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .badge-inactive {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            font-size: 8px;
            color: #94a3b8;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <!-- School Logo -->
            <td width="15%" align="left" valign="middle">
                @if($school->logo && !\Illuminate\Support\Str::contains($school->logo, 'http'))
                    <img src="{{ public_path('storage/' . $school->logo) }}" style="max-height: 55px; max-width: 80px; object-fit: contain;" alt="logo">
                @else
                    <div style="width: 50px; height: 50px; background-color: #0f172a; color: white; text-align: center; line-height: 50px; font-size: 20px; font-weight: bold; border-radius: 6px;">
                        {{ substr($school->name ?? 'ES', 0, 2) }}
                    </div>
                @endif
            </td>
            <!-- Center Text -->
            <td width="85%" align="center" valign="middle" style="padding-right: 15%;">
                <div class="school-title">{{ strtoupper($school->name ?? 'EDULINK ACADEMY') }}</div>
                <div class="school-subtitle">{{ strtoupper($school->address ?? 'ACCRA METRO') }}</div>
                <div class="school-contact">
                    {{ $school->phone ?? '0202620645' }} | {{ $school->email ?? 'constech304@gmail.com' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Document Title Band -->
    <div class="title-block">
        Staff Directory & Profile Report
    </div>

    <!-- Metadata Grid -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">DATE GENERATED:</td>
            <td class="meta-value">{{ now()->format('d M Y, h:i A') }}</td>
            <td class="meta-label">TOTAL STAFF:</td>
            <td class="meta-value">{{ count($staffMembers) }} Accounts</td>
        </tr>
        <tr>
            <td class="meta-label">ACTIVE USERS:</td>
            <td class="meta-value">{{ $staffMembers->filter(fn($s) => $s->user && $s->user->is_active)->count() }} Active</td>
            <td class="meta-label">DEACTIVATED:</td>
            <td class="meta-value">{{ $staffMembers->filter(fn($s) => !$s->user || !$s->user->is_active)->count() }} Suspended</td>
        </tr>
    </table>

    <!-- Staff Roster Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th width="25%">Staff Name & ID</th>
                <th width="25%">Email Address</th>
                <th width="20%">Role / Designation</th>
                <th width="15%">Campus Branch</th>
                <th width="15%">Date Joined</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffMembers as $staff)
                @php
                    $isActive = $staff->user ? $staff->user->is_active : false;
                @endphp
                <tr>
                    <td>
                        <strong>{{ $staff->user->name ?? 'Unknown Staff' }}</strong>
                        <div style="font-size: 8px; color: #64748b; margin-top: 2px;">ID: {{ $staff->staff_number }}</div>
                    </td>
                    <td>{{ $staff->user->email ?? '—' }}</td>
                    <td>
                        {{ $staff->user->role ? $staff->user->role->name : 'Staff' }}
                        <div style="font-size: 8px; color: #64748b; margin-top: 2px;">{{ $staff->designation }}</div>
                    </td>
                    <td>{{ $staff->campus ? $staff->campus->name : 'Main Campus' }}</td>
                    <td>{{ $staff->date_joined ? $staff->date_joined->format('d M Y') : '—' }}</td>
                    <td align="center">
                        @if($isActive)
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-inactive">Suspended</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" align="center" style="padding: 20px; color: #64748b;">No staff accounts registered in this school tenant.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer Watermark -->
    <div class="footer">
        Generated by {{ strtoupper(config('app.name', 'EduLink')) }} Ghana | Administrative Records
    </div>

</body>
</html>
