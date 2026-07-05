<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Directory Report - {{ $school->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
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
            font-size: 9px;
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
            font-size: 8px;
            padding: 8px 10px;
            border: 1px solid #0f172a;
            text-align: left;
        }
        .report-table td {
            padding: 7px 10px;
            font-size: 9px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        .report-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 7px;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-active {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .badge-inactive {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
        .badge-neutral {
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
            <td width="12%" align="left" valign="middle">
                @if($school->logo && !\Illuminate\Support\Str::contains($school->logo, 'http'))
                    <img src="{{ public_path('storage/' . $school->logo) }}" style="max-height: 55px; max-width: 80px; object-fit: contain;" alt="logo">
                @else
                    <div style="width: 45px; height: 45px; background-color: #0f172a; color: white; text-align: center; line-height: 45px; font-size: 18px; font-weight: bold; border-radius: 5px;">
                        {{ substr($school->name ?? 'ES', 0, 2) }}
                    </div>
                @endif
            </td>
            <!-- Center Text -->
            <td width="88%" align="center" valign="middle" style="padding-right: 12%;">
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
        Student Registry Directory Report
    </div>

    <!-- Metadata Grid -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">DATE GENERATED:</td>
            <td class="meta-value">{{ now()->format('d M Y, h:i A') }}</td>
            <td class="meta-label">TOTAL STUDENTS:</td>
            <td class="meta-value">{{ count($students) }} Registered</td>
        </tr>
        <tr>
            <td class="meta-label">ACTIVE ENROLLMENT:</td>
            <td class="meta-value">{{ $students->where('status', 'active')->count() }} Active</td>
            <td class="meta-label">OTHER STATUSES:</td>
            <td class="meta-value">{{ $students->where('status', '!=', 'active')->count() }} Inactive/Graduated</td>
        </tr>
    </table>

    <!-- Student Roster Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th width="20%">Student Name & ID</th>
                <th width="15%">Class & Stream</th>
                <th width="12%">Gender & Age</th>
                <th width="12%">NHIS / Nationality</th>
                <th width="18%">Primary Guardian</th>
                <th width="15%">Guardian Contact</th>
                <th width="8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
                @php
                    $primaryGuardian = $student->guardians->first();
                    $age = $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->age : '—';
                @endphp
                <tr>
                    <td>
                        <strong>{{ strtoupper($student->first_name) }} {{ strtoupper($student->last_name) }}</strong>
                        <div style="font-size: 8px; color: #64748b; margin-top: 2px;">ID: {{ $student->student_id_number }}</div>
                    </td>
                    <td>
                        {{ $student->currentClass ? $student->currentClass->name : 'Unassigned Class' }}
                        <div style="font-size: 8px; color: #64748b; margin-top: 2px;">{{ $student->currentStream ? 'Stream ' . $student->currentStream->name : 'No Stream' }}</div>
                    </td>
                    <td>
                        {{ $student->gender }}
                        <div style="font-size: 8px; color: #64748b; margin-top: 2px;">Age: {{ $age }} Yrs ({{ $student->date_of_birth ? $student->date_of_birth->format('d M Y') : '—' }})</div>
                    </td>
                    <td>
                        {{ $student->nationality }}
                        <div style="font-size: 8px; color: #64748b; margin-top: 2px;">NHIS: {{ $student->nhis_number ?: 'None' }}</div>
                    </td>
                    <td>
                        @if($primaryGuardian)
                            {{ $primaryGuardian->first_name }} {{ $primaryGuardian->last_name }}
                            <div style="font-size: 7px; color: #64748b; margin-top: 2px;">Rel: {{ $primaryGuardian->relationship }}</div>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if($primaryGuardian)
                            {{ $primaryGuardian->phone }}
                            <div style="font-size: 7px; color: #64748b; margin-top: 2px;">{{ $primaryGuardian->email ?: 'No Email' }}</div>
                        @else
                            —
                        @endif
                    </td>
                    <td align="center">
                        @if($student->status === 'active')
                            <span class="badge badge-active">Active</span>
                        @elseif(in_array($student->status, ['graduated', 'withdrawn', 'transferred']))
                            <span class="badge badge-neutral">{{ $student->status }}</span>
                        @else
                            <span class="badge badge-inactive">{{ $student->status }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" align="center" style="padding: 20px; color: #64748b;">No student records found in this school tenant.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer Watermark -->
    <div class="footer">
        Generated by {{ strtoupper(config('app.name', 'EduLink')) }} Ghana | Student Registry Records
    </div>

</body>
</html>
