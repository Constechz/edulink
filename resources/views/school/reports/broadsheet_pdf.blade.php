<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Broadsheet - {{ $class->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            color: #003366;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 12px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 4px;
            font-size: 11px;
        }
        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .matrix-table th {
            background-color: #003366;
            color: white;
            padding: 6px;
            font-weight: bold;
            border: 1px solid #ddd;
            text-align: center;
        }
        .matrix-table td {
            padding: 6px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .text-left {
            text-align: left !important;
        }
        .student-name {
            font-weight: bold;
            color: #003366;
        }
        .score-val {
            font-weight: bold;
        }
        .grade-val {
            font-size: 9px;
            color: #666;
            display: block;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>{{ $class->school ? $class->school->name : 'EDULINK ACADEMY' }}</h2>
        <p>CLASS GRADE BROADSHEET MATRIX</p>
    </div>

    <table class="meta-table">
        <tr>
            <td width="15%"><strong>Class:</strong></td>
            <td width="35%">{{ $class->name }}</td>
            <td width="15%"><strong>Academic Term:</strong></td>
            <td width="35%">{{ $term->name }}</td>
        </tr>
        <tr>
            <td><strong>Academic Year:</strong></td>
            <td>{{ $year->name }}</td>
            <td><strong>Date Generated:</strong></td>
            <td>{{ date('Y-m-d H:i') }}</td>
        </tr>
    </table>

    <table class="matrix-table">
        <thead>
            <tr>
                <th class="text-left" style="width: 25%;">Student Name</th>
                @foreach($subjects as $sub)
                    <th>{{ $sub->code }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td class="text-left student-name">
                        {{ $student->first_name }} {{ $student->last_name }}
                        <span style="font-weight: normal; font-size: 9px; color: #777; display: block;">Roll: #{{ $student->admission_no }}</span>
                    </td>
                    @foreach($subjects as $sub)
                        @php
                            $score = $scoresMap[$student->id][$sub->id] ?? null;
                        @endphp
                        <td>
                            @if($score)
                                <span class="score-val">{{ $score->grand_total }}%</span>
                                <span class="grade-val">({{ $score->grade }})</span>
                            @else
                                <span style="color: #bbb;">—</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
