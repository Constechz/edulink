<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentScore;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportCardController extends Controller
{
    /**
     * View reports dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;
        if ($user->role && $user->role->slug === 'class-teacher') {
            $assignedClassIdsFromStreams = \App\Models\Stream::where('class_teacher_id', $user->id)->pluck('class_id')->toArray();
            $classes = SchoolClass::where('school_id', $schoolId)
                ->where(function($q) use ($user, $assignedClassIdsFromStreams) {
                    $q->where('class_teacher_id', $user->id)
                      ->orWhereIn('id', $assignedClassIdsFromStreams);
                })
                ->get();
        } else {
            $classes = SchoolClass::where('school_id', $schoolId)->get();
        }
        $terms = Term::where('school_id', $schoolId)->get();
        $academicYears = AcademicYear::where('school_id', $schoolId)->get();

        $selectedClassId = $request->get('class_id');
        if ($selectedClassId && $user->role && $user->role->slug === 'class-teacher') {
            $assignedClassIdsFromStreams = \App\Models\Stream::where('class_teacher_id', $user->id)->pluck('class_id')->toArray();
            $isAuthorized = in_array($selectedClassId, $assignedClassIdsFromStreams);
            if (!$isAuthorized) {
                abort(403, 'Unauthorized class access.');
            }
        }
        $selectedTermId = $request->get('term_id') ?: ($terms->where('is_current', true)->first()->id ?? null);
        $selectedYearId = $request->get('academic_year_id') ?: ($academicYears->where('is_current', true)->first()->id ?? null);

        $students = [];
        $reportDetails = collect();
        if ($selectedClassId) {
            $enrollments = \App\Models\StudentEnrollment::where('school_id', $schoolId)
                ->where('class_id', $selectedClassId)
                ->where('academic_year_id', $selectedYearId)
                ->with('student')
                ->get();
            $students = $enrollments->pluck('student')->filter(function ($student) {
                return $student !== null && $student->status === 'active';
            });
            
            $reportDetails = \App\Models\StudentReportDetail::where('school_id', $schoolId)
                ->where('term_id', $selectedTermId)
                ->where('academic_year_id', $selectedYearId)
                ->get()
                ->keyBy('student_id');
        }

        return view('school.reports.index', compact(
            'classes', 'terms', 'academicYears', 'students',
            'selectedClassId', 'selectedTermId', 'selectedYearId', 'reportDetails'
        ));
    }

    /**
     * View or print school broadsheet.
     */
    public function broadsheet(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'format' => 'nullable|string|in:html,pdf',
        ]);

        $classId = $request->class_id;
        $user = $request->user();
        if ($user->role && $user->role->slug === 'class-teacher') {
            $assignedClassIdsFromStreams = \App\Models\Stream::where('class_teacher_id', $user->id)->pluck('class_id')->toArray();
            $isAuthorized = in_array($classId, $assignedClassIdsFromStreams);
            if (!$isAuthorized) {
                abort(403, 'Unauthorized class access.');
            }
        }
        $termId = $request->term_id;
        $yearId = $request->academic_year_id;

        $class = SchoolClass::find($classId);
        $term = Term::find($termId);
        $year = AcademicYear::find($yearId);

        $students = Student::where('school_id', $schoolId)
            ->where('current_class_id', $classId)
            ->get();

        $subjects = Subject::where('school_id', $schoolId)
            ->where('level', $class->level)
            ->get();

        $scoresList = StudentScore::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('term_id', $termId)
            ->where('academic_year_id', $yearId)
            ->get();

        // Map scores into: $scoresMap[student_id][subject_id] = grand_total (or score object)
        $scoresMap = [];
        foreach ($scoresList as $s) {
            $scoresMap[$s->student_id][$s->subject_id] = $s;
        }

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('school.reports.broadsheet_pdf', compact(
                'class', 'term', 'year', 'students', 'subjects', 'scoresMap'
            ))->setPaper('a4', 'landscape');

            return $pdf->download("broadsheet_{$class->name}_{$term->name}.pdf");
        }

        return view('school.reports.broadsheet', compact(
            'class', 'term', 'year', 'students', 'subjects', 'scoresMap'
        ));
    }

    /**
     * Compile and stream a student's report card PDF with QR validation.
     */
    public function generateCard(Request $request, Student $student)
    {
        $schoolId = $request->user()->school_id;

        if ($student->school_id !== $schoolId) {
            abort(403);
        }

        $user = $request->user();
        if ($user->role && $user->role->slug === 'class-teacher') {
            $assignedClassIdsFromStreams = \App\Models\Stream::where('class_teacher_id', $user->id)->pluck('class_id')->toArray();
            $isAuthorized = in_array($student->current_class_id, $assignedClassIdsFromStreams);
            if (!$isAuthorized) {
                abort(403, 'Unauthorized student profile access.');
            }
        }

        $request->validate([
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $termId = $request->term_id;
        $yearId = $request->academic_year_id;

        $term = Term::find($termId);
        $year = AcademicYear::find($yearId);
        $school = School::find($schoolId);
        $enrollment = \App\Models\StudentEnrollment::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->where('academic_year_id', $yearId)
            ->first();
        $class = $enrollment ? SchoolClass::find($enrollment->class_id) : SchoolClass::find($student->current_class_id);

        // Print Credit & Lock validation check
        $settings = $school->settings ?: [];
        $unlockedReports = $settings['unlocked_reports'] ?? [];
        $isReportUnlocked = isset($unlockedReports[$yearId][$termId][$student->id]) && $unlockedReports[$yearId][$termId][$student->id] == true;

        $paymentEnabled = \App\Models\SystemSetting::getVal('report_card_payment_enabled', '1') == '1';

        if ($paymentEnabled && !$isReportUnlocked) {
            $remainingCredits = isset($settings['report_credits']) ? intval($settings['report_credits']) : 0;
            if ($remainingCredits < 1) {
                return redirect()->route('school.billing.gateway', [
                    'type' => 'report_credits_purchase',
                    'credits' => 1,
                    'gateway' => 'paystack',
                    'class_id' => $student->current_class_id,
                    'term_id' => $termId,
                    'academic_year_id' => $yearId
                ])->with('info', 'Please complete the micro-payment to purchase 1 print credit for this student\'s report card.');
            }

            // Deduct 1 credit
            $settings['report_credits'] = $remainingCredits - 1;
            
            // Mark report as unlocked
            if (!isset($unlockedReports[$yearId])) {
                $unlockedReports[$yearId] = [];
            }
            if (!isset($unlockedReports[$yearId][$termId])) {
                $unlockedReports[$yearId][$termId] = [];
            }
            $unlockedReports[$yearId][$termId][$student->id] = true;
            $settings['unlocked_reports'] = $unlockedReports;

            $school->settings = $settings;
            $school->save();
        }

        // Fetch student scores (only approved or published for accuracy)
        $scores = StudentScore::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->where('term_id', $termId)
            ->where('academic_year_id', $yearId)
            ->whereIn('status', ['hod_verified', 'approved', 'published'])
            ->with('subject')
            ->get();

        // Calculate Position / Rank dynamically based on total student scores in this class
        $allClassStudents = Student::where('school_id', $schoolId)
            ->where('current_class_id', $student->current_class_id)
            ->get();

        $studentTotals = [];
        foreach ($allClassStudents as $cs) {
            $studentTotals[$cs->id] = StudentScore::where('school_id', $schoolId)
                ->where('student_id', $cs->id)
                ->where('term_id', $termId)
                ->where('academic_year_id', $yearId)
                ->sum('grand_total');
        }

        arsort($studentTotals);

        $rankMap = [];
        $rank = 1;
        $prevScore = null;
        $idx = 0;
        foreach ($studentTotals as $sId => $sum) {
            $idx++;
            if ($prevScore !== null && $sum < $prevScore) {
                $rank = $idx;
            }
            $rankMap[$sId] = $rank;
            $prevScore = $sum;
        }

        $positionRaw = $rankMap[$student->id] ?? 1;
        $position = $this->getOrdinalSuffix($positionRaw);
        $rollNo = count($allClassStudents);
        $totalMarks = $studentTotals[$student->id] ?? 0.00;

        $reportDetail = \App\Models\StudentReportDetail::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->where('term_id', $termId)
            ->where('academic_year_id', $yearId)
            ->first();

        // Fetch student promotion record for the current academic year
        $promotionRecord = \App\Models\StudentPromotionRecord::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->where('academic_year_id', $yearId)
            ->with('toClass')
            ->first();

        // Attendance stats
        $attendanceStats = AttendanceRecord::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->where('term_id', $termId)
            ->where('academic_year_id', $yearId)
            ->get();

        $stats = [
            'present' => $reportDetail && !is_null($reportDetail->attendance_present)
                ? $reportDetail->attendance_present
                : $attendanceStats->where('status', 'present')->count(),
            'total' => $reportDetail && !is_null($reportDetail->attendance_total)
                ? $reportDetail->attendance_total
                : ($attendanceStats->count() ?: 100),
        ];

        // Retrieve level-specific grading scale items
        $level = $class ? $class->level : 'Primary';
        $scale = \App\Models\GradingScale::where('school_id', $schoolId)
            ->where('is_active', true)
            ->where('level', $level)
            ->first();

        if (!$scale) {
            $scale = \App\Models\GradingScale::where('school_id', $schoolId)
                ->where('is_active', true)
                ->where('is_default', true)
                ->first() ?: \App\Models\GradingScale::where('school_id', $schoolId)->first();
        }

        $gradingScaleItems = $scale ? $scale->items : collect();

        // Fetch scoring weights dynamically
        $scoringConfig = \App\Models\ScoringConfiguration::where('school_id', $schoolId)
            ->where('is_active', true)
            ->where('level', $level)
            ->first();

        if (!$scoringConfig) {
            $scoringConfig = \App\Models\ScoringConfiguration::where('school_id', $schoolId)
                ->where('is_active', true)
                ->first();
        }

        $classWeight = $scoringConfig ? floatval($scoringConfig->class_score_weight) : 40.0;
        $examWeight = $scoringConfig ? floatval($scoringConfig->exam_score_weight) : 60.0;

        // Pre-calculate rank/position for each subject among all class students
        $subjectRanks = [];
        foreach ($scores as $score) {
            $subjectId = $score->subject_id;
            
            // Get all scores for this class in this subject
            $classScoresForSubject = StudentScore::where('school_id', $schoolId)
                ->where('term_id', $termId)
                ->where('academic_year_id', $yearId)
                ->where('subject_id', $subjectId)
                ->whereIn('student_id', $allClassStudents->pluck('id'))
                ->get()
                ->keyBy('student_id');
                
            $totals = [];
            foreach ($allClassStudents as $cs) {
                $totals[$cs->id] = isset($classScoresForSubject[$cs->id]) ? floatval($classScoresForSubject[$cs->id]->grand_total) : 0.0;
            }
            
            arsort($totals);
            
            $rankMap = [];
            $rank = 1;
            $prevScore = null;
            $idx = 0;
            foreach ($totals as $sId => $sum) {
                $idx++;
                if ($prevScore !== null && $sum < $prevScore) {
                    $rank = $idx;
                }
                $rankMap[$sId] = $rank;
                $prevScore = $sum;
            }
            
            $subjectRanks[$subjectId] = $this->getOrdinalSuffix($rankMap[$student->id] ?? 1);
        }

        $classTeacher = $class ? $class->classTeacher : null;

        // Verification security QR URL hash (base64 simple encryption)
        $hash = base64_encode($student->id . '-' . $termId . '-' . $yearId);
        $verificationUrl = route('public.verify-report', ['hash' => $hash]);

        $themeKey = $class->report_card_theme ?? 'classic';
        $themeStyles = $this->getThemeStyles($themeKey);

        $pdf = Pdf::loadView('school.reports.report_card_pdf', compact(
            'student', 'scores', 'stats', 'term', 'year', 'class', 'school',
            'verificationUrl', 'position', 'rollNo', 'totalMarks', 'reportDetail',
            'gradingScaleItems', 'classWeight', 'examWeight', 'subjectRanks', 'classTeacher', 'promotionRecord', 'themeStyles'
        ));

        return $pdf->stream("report_card_{$student->first_name}_{$student->last_name}.pdf");
    }

    /**
     * Save/update report card details (conduct, attitude, interest, custom remarks, reopening)
     */
    public function saveReportDetails(Request $request, Student $student)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        if ($student->school_id !== $schoolId) {
            abort(403);
        }

        if ($user->role && $user->role->slug === 'class-teacher') {
            $assignedClassIdsFromStreams = \App\Models\Stream::where('class_teacher_id', $user->id)->pluck('class_id')->toArray();
            $isAuthorized = in_array($student->current_class_id, $assignedClassIdsFromStreams);
            if (!$isAuthorized) {
                abort(403, 'Unauthorized. You are not the class teacher for this student.');
            }
        }

        $request->validate([
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'conduct' => 'nullable|string|max:255',
            'attitude' => 'nullable|string|max:255',
            'interest' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'reopening_date' => 'nullable|date',
            'attendance_present' => 'nullable|integer|min:0',
            'attendance_total' => 'nullable|integer|min:0',
        ]);

        \App\Models\StudentReportDetail::updateOrCreate([
            'school_id' => $schoolId,
            'student_id' => $student->id,
            'term_id' => $request->term_id,
            'academic_year_id' => $request->academic_year_id,
        ], [
            'conduct' => $request->conduct,
            'attitude' => $request->attitude,
            'interest' => $request->interest,
            'remarks' => $request->remarks,
            'reopening_date' => $request->reopening_date,
            'attendance_present' => $request->attendance_present,
            'attendance_total' => $request->attendance_total,
        ]);

        return redirect()->back()->with('success', 'Student report details saved successfully.');
    }

    /**
     * Add ordinal suffix to number
     */
    private function getOrdinalSuffix(int $number): string
    {
        if ($number % 100 >= 11 && $number % 100 <= 13) {
            return $number . 'th';
        }
        switch ($number % 10) {
            case 1:  return $number . 'st';
            case 2:  return $number . 'nd';
            case 3:  return $number . 'rd';
            default: return $number . 'th';
        }
    }

    /**
     * Unauthenticated public verification check page.
     */
    public function publicVerify(string $hash)
    {
        try {
            $decoded = base64_decode($hash);
            $parts = explode('-', $decoded);

            if (count($parts) !== 3) {
                abort(404, 'Invalid verification code.');
            }

            $studentId = $parts[0];
            $termId = $parts[1];
            $yearId = $parts[2];

            $student = Student::findOrFail($studentId);
            $term = Term::findOrFail($termId);
            $year = AcademicYear::findOrFail($yearId);
            $school = School::findOrFail($student->school_id);
            $class = SchoolClass::find($student->current_class_id);

            $scores = StudentScore::where('school_id', $school->id)
                ->where('student_id', $studentId)
                ->where('term_id', $termId)
                ->where('academic_year_id', $yearId)
                ->whereIn('status', ['hod_verified', 'approved', 'published'])
                ->with('subject')
                ->get();

            return view('public.verify_report', compact(
                'student', 'scores', 'term', 'year', 'school', 'class'
            ));
        } catch (\Exception $e) {
            abort(404, 'Verification target not found.');
        }
    }

    /**
     * Compile and download all report cards for a specific class as a single merged PDF.
     */
    public function bulkPrint(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $user = $request->user();

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $classId = $request->class_id;
        $termId = $request->term_id;
        $yearId = $request->academic_year_id;

        // Authorization check for Class Teachers
        if ($user->role && $user->role->slug === 'class-teacher') {
            $assignedClassIdsFromStreams = \App\Models\Stream::where('class_teacher_id', $user->id)->pluck('class_id')->toArray();
            $isAuthorized = in_array($classId, $assignedClassIdsFromStreams);
            if (!$isAuthorized) {
                abort(403, 'Unauthorized class access.');
            }
        }

        $class = SchoolClass::find($classId);
        $term = Term::find($termId);
        $year = AcademicYear::find($yearId);
        $school = School::find($schoolId);

        $enrollments = \App\Models\StudentEnrollment::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('academic_year_id', $yearId)
            ->with('student')
            ->get();
        $students = $enrollments->pluck('student')->filter(function ($student) {
            return $student !== null && $student->status === 'active';
        });

        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'No students found in this class to print.');
        }

        // Print Credit & Lock validation check
        $settings = $school->settings ?: [];
        $unlockedReports = $settings['unlocked_reports'] ?? [];

        $paymentEnabled = \App\Models\SystemSetting::getVal('report_card_payment_enabled', '1') == '1';

        // Count locked students
        $lockedCount = 0;
        if ($paymentEnabled) {
            foreach ($students as $student) {
                $isReportUnlocked = isset($unlockedReports[$yearId][$termId][$student->id]) && $unlockedReports[$yearId][$termId][$student->id] == true;
                if (!$isReportUnlocked) {
                    $lockedCount++;
                }
            }
        }

        if ($paymentEnabled && $lockedCount > 0) {
            $remainingCredits = isset($settings['report_credits']) ? intval($settings['report_credits']) : 0;
            if ($remainingCredits < $lockedCount) {
                $needed = $lockedCount - $remainingCredits;
                return redirect()->route('school.billing.gateway', [
                    'type' => 'report_credits_purchase',
                    'credits' => $needed,
                    'gateway' => 'paystack',
                    'class_id' => $classId,
                    'term_id' => $termId,
                    'academic_year_id' => $yearId
                ])->with('info', "Please complete the payment for {$needed} print credit(s) to unlock bulk printing for this class.");
            }

            // Deduct credits
            $settings['report_credits'] = $remainingCredits - $lockedCount;

            // Mark reports as unlocked
            if (!isset($unlockedReports[$yearId])) {
                $unlockedReports[$yearId] = [];
            }
            if (!isset($unlockedReports[$yearId][$termId])) {
                $unlockedReports[$yearId][$termId] = [];
            }

            foreach ($students as $student) {
                $unlockedReports[$yearId][$termId][$student->id] = true;
            }

            $settings['unlocked_reports'] = $unlockedReports;
            $school->settings = $settings;
            $school->save();
        }

        // Gather data for all students
        $bulkData = [];

        // Pre-calculate Rank/Position dynamically based on total student scores in this class
        $studentTotals = [];
        foreach ($students as $cs) {
            $studentTotals[$cs->id] = StudentScore::where('school_id', $schoolId)
                ->where('student_id', $cs->id)
                ->where('term_id', $termId)
                ->where('academic_year_id', $yearId)
                ->sum('grand_total');
        }

        arsort($studentTotals);

        $rankMap = [];
        $rank = 1;
        $prevScore = null;
        $idx = 0;
        foreach ($studentTotals as $sId => $sum) {
            $idx++;
            if ($prevScore !== null && $sum < $prevScore) {
                $rank = $idx;
            }
            $rankMap[$sId] = $rank;
            $prevScore = $sum;
        }

        // Level grading scale setup
        $level = $class ? $class->level : 'Primary';
        $scale = \App\Models\GradingScale::where('school_id', $schoolId)
            ->where('is_active', true)
            ->where('level', $level)
            ->first();

        if (!$scale) {
            $scale = \App\Models\GradingScale::where('school_id', $schoolId)
                ->where('is_active', true)
                ->where('is_default', true)
                ->first() ?: \App\Models\GradingScale::where('school_id', $schoolId)->first();
        }

        $gradingScaleItems = $scale ? $scale->items : collect();

        // Scoring weight configurations
        $scoringConfig = \App\Models\ScoringConfiguration::where('school_id', $schoolId)
            ->where('is_active', true)
            ->where('level', $level)
            ->first();

        if (!$scoringConfig) {
            $scoringConfig = \App\Models\ScoringConfiguration::where('school_id', $schoolId)
                ->where('is_active', true)
                ->first();
        }

        $classWeight = $scoringConfig ? floatval($scoringConfig->class_score_weight) : 40.0;
        $examWeight = $scoringConfig ? floatval($scoringConfig->exam_score_weight) : 60.0;

        foreach ($students as $student) {
            // Scores
            $scores = StudentScore::where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->where('term_id', $termId)
                ->where('academic_year_id', $yearId)
                ->whereIn('status', ['hod_verified', 'approved', 'published'])
                ->with('subject')
                ->get();

            // Total marks and position
            $totalMarks = $studentTotals[$student->id] ?? 0.0;
            $positionNum = $rankMap[$student->id] ?? 1;
            $position = $this->getOrdinalSuffix($positionNum);
            $rollNo = count($students);

            // Report Details
            $reportDetail = \App\Models\StudentReportDetail::where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->where('term_id', $termId)
                ->where('academic_year_id', $yearId)
                ->first();

            // Attendance
            $attendanceStats = \App\Models\AttendanceRecord::where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->where('term_id', $termId)
                ->where('academic_year_id', $yearId)
                ->get();

            $stats = [
                'present' => $reportDetail && !is_null($reportDetail->attendance_present)
                    ? $reportDetail->attendance_present
                    : $attendanceStats->where('status', 'present')->count(),
                'total' => $reportDetail && !is_null($reportDetail->attendance_total)
                    ? $reportDetail->attendance_total
                    : ($attendanceStats->count() ?: 100),
            ];

            // Subject ranks
            $subjectRanks = [];
            foreach ($scores as $score) {
                $subjectId = $score->subject_id;
                
                $classScoresForSubject = StudentScore::where('school_id', $schoolId)
                    ->where('term_id', $termId)
                    ->where('academic_year_id', $yearId)
                    ->where('subject_id', $subjectId)
                    ->whereIn('student_id', $students->pluck('id'))
                    ->get()
                    ->keyBy('student_id');
                    
                $totals = [];
                foreach ($students as $cs) {
                    $totals[$cs->id] = isset($classScoresForSubject[$cs->id]) ? floatval($classScoresForSubject[$cs->id]->grand_total) : 0.0;
                }
                
                arsort($totals);
                
                $sRankMap = [];
                $sRank = 1;
                $sPrevScore = null;
                $sIdx = 0;
                foreach ($totals as $subStudentId => $sumScore) {
                    $sIdx++;
                    if ($sPrevScore !== null && $sumScore < $sPrevScore) {
                        $sRank = $sIdx;
                    }
                    $sRankMap[$subStudentId] = $sRank;
                    $sPrevScore = $sumScore;
                }
                
                $subjectRanks[$subjectId] = $this->getOrdinalSuffix($sRankMap[$student->id] ?? 1);
            }

            // Promotion record
            $promotionRecord = \App\Models\StudentPromotionRecord::where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->where('academic_year_id', $yearId)
                ->with('toClass')
                ->first();

            $classTeacher = $class ? $class->classTeacher : null;

            // Verification URL
            $hash = base64_encode($student->id . '-' . $termId . '-' . $yearId);
            $verificationUrl = route('public.verify-report', ['hash' => $hash]);

            $bulkData[] = [
                'student' => $student,
                'scores' => $scores,
                'stats' => $stats,
                'position' => $position,
                'rollNo' => $rollNo,
                'totalMarks' => $totalMarks,
                'reportDetail' => $reportDetail,
                'subjectRanks' => $subjectRanks,
                'classTeacher' => $classTeacher,
                'promotionRecord' => $promotionRecord,
                'verificationUrl' => $verificationUrl,
            ];
        }

        $themeKey = $class->report_card_theme ?? 'classic';
        $themeStyles = $this->getThemeStyles($themeKey);

        $pdf = Pdf::loadView('school.reports.bulk_report_cards_pdf', compact(
            'bulkData', 'term', 'year', 'class', 'school',
            'gradingScaleItems', 'classWeight', 'examWeight', 'themeStyles'
        ));

        return $pdf->download("bulk_report_cards_{$class->name}_{$term->name}.pdf");
    }

    /**
     * Get style properties for a specific theme key.
     */
    private function getThemeStyles(string $themeKey): array
    {
        $themes = [
            'classic' => [
                'font_family' => "'Helvetica Neue', Helvetica, Arial, sans-serif",
                'primary_color' => '#002244',
                'secondary_color' => '#f1f5f9',
                'border_color' => '#000000',
            ],
            'royal' => [
                'font_family' => "'Georgia', Times, serif",
                'primary_color' => '#660000',
                'secondary_color' => '#fdfbf7',
                'border_color' => '#D4AF37',
            ],
            'vibrant' => [
                'font_family' => "'Outfit', 'Inter', sans-serif",
                'primary_color' => '#0f766e',
                'secondary_color' => '#fefce8',
                'border_color' => '#0d9488',
            ],
            'minimalist' => [
                'font_family' => "'Inter', sans-serif",
                'primary_color' => '#1e293b',
                'secondary_color' => '#fafafa',
                'border_color' => '#cbd5e1',
            ]
        ];

        return $themes[$themeKey] ?? $themes['classic'];
    }
}
