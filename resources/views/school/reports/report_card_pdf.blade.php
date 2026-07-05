<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Report Card - {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #000000;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        @page {
            margin: 20px 30px 25px 30px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #000000;
            padding-bottom: 8px;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .school-title {
            font-size: 20px;
            font-weight: bold;
            color: #000000;
            margin: 0;
            text-align: center;
        }
        .school-subtitle {
            font-size: 11px;
            font-weight: bold;
            color: #000000;
            margin: 2px 0 0 0;
            text-align: center;
            text-transform: uppercase;
        }
        .school-contact {
            font-size: 10px;
            color: #000000;
            margin: 2px 0 0 0;
            text-align: center;
        }
        .title-block {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            padding: 8px 5px;
            font-size: 13px;
            text-transform: uppercase;
            margin-bottom: 15px;
            color: #000000;
            letter-spacing: 4px;
        }
        .profile-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .profile-table td {
            padding: 6px 8px;
            font-size: 10px;
            font-weight: bold;
            vertical-align: middle;
            border: 1px solid #000000;
        }
        .profile-table td.profile-label {
            background-color: #f1f5f9;
            color: #000000;
            text-align: left;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .profile-table td.profile-value {
            background-color: #ffffff;
            color: #000000;
            text-align: center;
        }
        .profile-value-underlined {
            border-bottom: 1px solid #000000;
            text-align: center;
            font-weight: bold;
            padding-bottom: 1px;
        }
        .scores-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .scores-table th {
            background-color: #ffffff;
            color: #000000;
            font-weight: bold;
            padding: 8px 4px;
            font-size: 10px;
            border: 1px solid #000000;
            text-transform: uppercase;
            text-align: center;
        }
        .scores-table td {
            padding: 6px 5px;
            border: 1px solid #000000;
            font-size: 10px;
            text-align: center;
            font-weight: bold;
        }
        .scores-table td.subject-name {
            text-align: left;
            font-weight: bold;
            padding-left: 8px;
        }
        .remarks-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        .remarks-table td {
            padding: 5px 0;
            font-size: 11px;
            font-weight: bold;
            vertical-align: middle;
        }
        .remarks-val {
            border-bottom: 1px solid #000000;
            font-weight: bold;
            padding-left: 10px;
        }
        .signature-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-top: 1px solid #000000;
            width: 80%;
            padding-top: 5px;
            text-align: center;
            margin: 0 auto;
        }
        
        /* Dynamic Theme Overrides */
        body {
            font-family: {!! $themeStyles['font_family'] !!} !important;
        }
        .school-title, .scores-table th, .signature-title {
            color: {{ $themeStyles['primary_color'] }} !important;
        }
        .title-block {
            background-color: {{ $themeStyles['secondary_color'] }} !important;
            color: {{ $themeStyles['primary_color'] }} !important;
        }
        .header-table {
            border-bottom: 2px solid {{ $themeStyles['border_color'] }} !important;
        }
        .profile-table td {
            border: 1px solid {{ $themeStyles['border_color'] }} !important;
        }
        .profile-table td.profile-label {
            background-color: {{ $themeStyles['secondary_color'] }} !important;
            color: {{ $themeStyles['primary_color'] }} !important;
        }
        .profile-value-underlined, .remarks-val {
            border-bottom: 1px solid {{ $themeStyles['border_color'] }} !important;
        }
        .scores-table th, .scores-table td {
            border: 1px solid {{ $themeStyles['border_color'] }} !important;
        }
        .signature-title {
            border-top: 1px solid {{ $themeStyles['border_color'] }} !important;
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
                    <div style="width: 50px; height: 50px; background-color: #000000; color: white; text-align: center; line-height: 50px; font-size: 20px; font-weight: bold; border-radius: 4px;">
                        {{ substr($school->name ?? 'ES', 0, 2) }}
                    </div>
                @endif
            </td>
            <!-- Center Text -->
            <td width="70%" align="center" valign="middle">
                <div class="school-title">{{ strtoupper($school->name ?? 'EDULINK ACADEMY') }}</div>
                <div class="school-subtitle">{{ strtoupper($school->address ?? 'ACCRA METRO') }}</div>
                <div class="school-contact">
                    {{ $school->phone ?? '0202620645' }} | {{ $school->email ?? 'constech304@gmail.com' }}
                </div>
            </td>
            <!-- Student Avatar -->
            <td width="15%" align="right" valign="bottom" style="padding-bottom: 8px;">
                @if($student->photo)
                    <img src="{{ public_path('storage/' . $student->photo) }}" style="width: 72px; height: auto; max-height: 82px; border-radius: 12px; border: 1.5px solid #000000; display: inline-block;" alt="photo">
                @else
                    <div style="width: 72px; height: 82px; border: 1.5px dashed #cbd5e1; border-radius: 12px; line-height: 82px; font-size: 8px; color: #94a3b8; text-align: center; background-color: #ffffff; display: inline-block;">No Photo</div>
                @endif
            </td>
        </tr>
    </table>

    <!-- Document Title Band -->
    <div class="title-block">
        TERMINAL REPORT SHEET
    </div>

    @php
        $levelUpper = strtoupper($class->level ?? '');
        $isSchoolLevel = in_array($levelUpper, ['KG', 'CRECHE', 'PRIMARY', 'JHS', 'SHS']);
        $termLabel = $isSchoolLevel ? 'Subjects' : 'Courses';
    @endphp

    <!-- Student Profile Metadata Grid -->
    <table class="profile-table">
        <tr>
            <td class="profile-label" style="width: 14%;">NAME:</td>
            <td colspan="5" class="profile-value" style="font-size: 12px; text-align: center;">
                {{ $student->first_name }} {{ $student->last_name }}
            </td>
        </tr>
        <tr>
            <td class="profile-label" style="width: 14%;">BASIC:</td>
            <td class="profile-value" style="width: 19%;">{{ $class->name }}</td>
            <td class="profile-label" style="width: 14%;">YEAR:</td>
            <td class="profile-value" style="width: 19%;">{{ $year->name }}</td>
            <td class="profile-label" style="width: 15%;">TERM:</td>
            <td class="profile-value" style="width: 19%;">{{ substr($term->name, -1) }}</td>
        </tr>
        <tr>
            <td class="profile-label" style="width: 14%;">POSITION:</td>
            <td class="profile-value" style="width: 19%;">{{ $position }}</td>
            <td class="profile-label" style="width: 14%;">TOTAL MARKS:</td>
            <td colspan="3" class="profile-value" style="text-align: center;">{{ round($totalMarks) }}</td>
        </tr>
        @php
            $isTerm3 = (substr($term->name, -1) === '3' || str_contains(strtolower($term->name), '3') || str_contains(strtolower($term->name), 'third'));
        @endphp
        <tr>
            <td class="profile-label" style="width: 14%;">ROLL NO:</td>
            <td class="profile-value" style="width: 19%;">{{ $rollNo }}</td>
            @if($isTerm3)
                <td class="profile-label" style="width: 14%;">REOPENING:</td>
                <td class="profile-value" style="width: 19%;">
                    {{ $reportDetail && $reportDetail->reopening_date ? $reportDetail->reopening_date->format('d M Y') : '' }}
                </td>
                <td class="profile-label" style="width: 15%;">PROMOTED TO:</td>
                <td class="profile-value" style="width: 19%;">
                    @if(isset($promotionRecord) && $promotionRecord)
                        @php
                            $decisionUpper = strtoupper($promotionRecord->decision);
                        @endphp
                        @if($decisionUpper === 'PROMOTED' || $decisionUpper === 'PROMOTE')
                            {{ strtoupper($promotionRecord->toClass ? $promotionRecord->toClass->name : 'Next Class') }}
                        @elseif($decisionUpper === 'CONDITIONAL' || $decisionUpper === 'PROMOTED_ON_TRIAL')
                            {{ strtoupper($promotionRecord->toClass ? $promotionRecord->toClass->name : 'Next Class') }} (TRIAL)
                        @elseif($decisionUpper === 'REPEAT' || $decisionUpper === 'RETAINED')
                            REPEAT {{ strtoupper($promotionRecord->fromClass ? $promotionRecord->fromClass->name : '') }}
                        @elseif($decisionUpper === 'BECE_CANDIDATE')
                            BECE CANDIDATE
                        @elseif($decisionUpper === 'WASSCE_CANDIDATE')
                            WASSCE CANDIDATE
                        @else
                            {{ $decisionUpper }}
                        @endif
                    @else
                        &nbsp;
                    @endif
                </td>
            @else
                <td class="profile-label" style="width: 14%;">REOPENING:</td>
                <td colspan="3" class="profile-value" style="text-align: center;">
                    {{ $reportDetail && $reportDetail->reopening_date ? $reportDetail->reopening_date->format('d M Y') : '' }}
                </td>
            @endif
        </tr>
    </table>

    <!-- Scores & Subject Breakdown Table -->
    <table class="scores-table">
        <thead>
            <tr>
                <th width="35%">{{ $termLabel }}</th>
                <th width="15%">CLASS SCORE<br><span style="font-size: 8px; font-weight: normal;">{{ round($classWeight) }}%</span></th>
                <th width="15%">EXAMS<br><span style="font-size: 8px; font-weight: normal;">{{ round($examWeight) }}%</span></th>
                <th width="15%">TOTAL SCORE<br><span style="font-size: 8px; font-weight: normal;">100%</span></th>
                <th width="10%">POSITION</th>
                <th width="10%">REMARKS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($scores as $score)
                @php
                    $gradeItem = $gradingScaleItems->first(function($item) use ($score) {
                        return $score->grand_total >= $item->min_score && $score->grand_total <= $item->max_score;
                    });
                    $remarkStr = $gradeItem ? $gradeItem->description : ($score->grade ?? '—');
                @endphp
                <tr>
                    <td class="subject-name">
                        {{ strtoupper($score->subject ? $score->subject->name : ($isSchoolLevel ? 'Unknown Subject' : 'Unknown Course')) }}
                    </td>
                    <td>{{ $score->scaled_class_score ? round($score->scaled_class_score) : '' }}</td>
                    <td>
                        @if($score->is_absent_exam)
                            <span style="color: #ef4444; font-size: 8px;">ABSENT</span>
                        @else
                            {{ $score->scaled_exam_score ? round($score->scaled_exam_score) : '' }}
                        @endif
                    </td>
                    <td style="color: #000000; font-weight: bold;">{{ round($score->grand_total) }}</td>
                    <td>{{ $subjectRanks[$score->subject_id] ?? '—' }}</td>
                    <td>{{ $remarkStr }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 15px; color: #64748b;">No {{ strtolower($termLabel) }} grades recorded for this term.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Attendance & Remarks Grid -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 5px;">
        <tr>
            <td width="15%" style="padding: 5px 0; font-weight: bold;">ATTENDANCE:</td>
            <td width="35%" style="font-weight: bold; padding-left: 10px;">{{ $stats['present'] }}</td>
            <td width="15%" style="padding: 5px 0; padding-left: 15px; font-weight: bold;">OUT OF:</td>
            <td width="35%" style="font-weight: bold; padding-left: 10px;">{{ $stats['total'] }}</td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 5px;">
        <tr>
            <td width="12%" style="padding: 6px 0; font-weight: bold; vertical-align: bottom;">CONDUCT:</td>
            <td width="88%" style="border-bottom: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: bottom;">{{ $reportDetail ? $reportDetail->conduct : '' }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-weight: bold; vertical-align: bottom;">ATTITUDE:</td>
            <td style="border-bottom: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: bottom;">{{ $reportDetail ? $reportDetail->attitude : '' }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-weight: bold; vertical-align: bottom;">INTEREST:</td>
            <td style="border-bottom: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: bottom;">{{ $reportDetail ? $reportDetail->interest : '' }}</td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
        <tr>
            <td width="27%" style="padding: 6px 0; font-weight: bold; vertical-align: bottom;">CLASS TEACHER'S REMARKS:</td>
            <td width="73%" style="border-bottom: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: bottom;">{{ $reportDetail ? $reportDetail->remarks : '' }}</td>
        </tr>
    </table>

    <!-- Signatures -->
    <table style="width: 100%; margin-top: 30px; border-collapse: collapse;">
        <tr>
            <td width="45%" align="center">
                <div style="height: 30px; text-align: center; line-height: 30px;">
                    @if($classTeacher && $classTeacher->signature)
                        <img src="{{ public_path('storage/' . $classTeacher->signature) }}" style="max-height: 30px; max-width: 120px;" alt="Signature">
                    @endif
                </div>
                <div class="signature-title">CLASS TEACHER'S SIGNATURE</div>
            </td>
            <td width="10%"></td>
            <td width="45%" align="center">
                <div style="height: 30px; text-align: center; line-height: 30px;">
                    @if($school->logo && !\Illuminate\Support\Str::contains($school->logo, 'http'))
                        @if(isset($school->settings['headteacher_signature']))
                            <img src="{{ public_path('storage/' . $school->settings['headteacher_signature']) }}" style="max-height: 30px; max-width: 120px;" alt="Signature">
                        @endif
                    @endif
                </div>
                <div class="signature-title">HEAD TEACHER'S SIGNATURE</div>
            </td>
        </tr>
    </table>

    <!-- Dynamic Grading Scale Legend (Symmetric Double Column Layout) -->
    <div style="margin-top: 25px; font-family: Arial, sans-serif;">
        <div style="font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; border-bottom: 1px solid #000000; padding-bottom: 2px;">GRADING SCALE</div>
        <table style="width: 100%; font-size: 9px; color: #000000; border-collapse: collapse;">
            @php
                $sortedItems = $gradingScaleItems->sortByDesc('min_score')->values();
                $leftColumnItems = [];
                $rightColumnItems = [];
                foreach ($sortedItems as $index => $item) {
                    if ($index % 2 === 0) {
                        $leftColumnItems[] = $item;
                    } else {
                        $rightColumnItems[] = $item;
                    }
                }
                $maxRows = max(count($leftColumnItems), count($rightColumnItems));
            @endphp
            @for($i = 0; $i < $maxRows; $i++)
                <tr>
                    <!-- Left Column Item -->
                    @if(isset($leftColumnItems[$i]))
                        @php $gi = $leftColumnItems[$i]; @endphp
                        <td width="15%" style="padding: 3px 0;">
                            <strong>
                                @if($gi->min_score == 0)
                                    Below {{ ceil($gi->max_score) }}
                                @else
                                    {{ round($gi->min_score) }} - {{ round($gi->max_score) }}
                                @endif
                            </strong>
                        </td>
                        <td width="35%" style="padding: 3px 0;">{{ $gi->description }}</td>
                    @else
                        <td width="15%"></td>
                        <td width="35%"></td>
                    @endif

                    <!-- Right Column Item -->
                    @if(isset($rightColumnItems[$i]))
                        @php $gi = $rightColumnItems[$i]; @endphp
                        <td width="15%" style="padding: 3px 0; padding-left: 15px;">
                            <strong>
                                @if($gi->min_score == 0)
                                    Below {{ ceil($gi->max_score) }}
                                @else
                                    {{ round($gi->min_score) }} - {{ round($gi->max_score) }}
                                @endif
                            </strong>
                        </td>
                        <td width="35%" style="padding: 3px 0;">{{ $gi->description }}</td>
                    @else
                        <td width="15%"></td>
                        <td width="35%"></td>
                    @endif
                </tr>
            @endfor
        </table>
    </div>

    <!-- Footer Sep & Brand Watermark -->
    <div style="margin-top: 30px; text-align: center; border-top: 1px solid #000000; padding-top: 8px; font-size: 8px; color: #555555; letter-spacing: 0.5px; text-transform: uppercase;">
        GENERATED BY {{ strtoupper(config('app.name', 'EduLink')) }}
    </div>

</body>
</html>
