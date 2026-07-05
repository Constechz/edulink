<?php

namespace App\Services;

use App\Models\Student;
use App\Models\AttendanceRecord;
use App\Models\StudentScore;
use App\Models\Invoice;
use App\Models\AiFlag;
use App\Models\AiFlagType;
use App\Models\AiRecommendation;
use Illuminate\Support\Facades\DB;

class AiAnalyticsService
{
    /**
     * Flag at-risk students based on attendance and grades trend.
     */
    public function flagAtRiskStudents($schoolId)
    {
        $students = Student::where('school_id', $schoolId)->where('status', 'active')->get();
        $results = [];

        // Ensure flag types exist
        $academicType = AiFlagType::firstOrCreate(
            ['slug' => 'academic-risk'],
            ['name' => 'Academic Risk', 'description' => 'Flagged due to declining grade trends.']
        );
        $attendanceType = AiFlagType::firstOrCreate(
            ['slug' => 'attendance-risk'],
            ['name' => 'Attendance Risk', 'description' => 'Flagged due to low attendance rate (< 80%).']
        );

        foreach ($students as $student) {
            // 1. Attendance Risk Check
            $totalDays = AttendanceRecord::where('student_id', $student->id)->count();
            if ($totalDays >= 5) { // minimum days threshold to run analytics
                $presentDays = AttendanceRecord::where('student_id', $student->id)->where('status', 'present')->count();
                $rate = ($presentDays / $totalDays) * 100;
                if ($rate < 80) {
                    $triggerReason = "Attendance rate is " . number_format($rate, 1) . "% (below 80% threshold).";
                    $flag = AiFlag::updateOrCreate(
                        [
                            'school_id' => $schoolId,
                            'student_id' => $student->id,
                            'flag_type_id' => $attendanceType->id,
                        ],
                        [
                            'severity' => $rate < 50 ? 'high' : 'medium',
                            'trigger_reason' => $triggerReason,
                            'is_resolved' => false,
                        ]
                    );

                    AiRecommendation::updateOrCreate(
                        [
                            'school_id' => $schoolId,
                            'student_id' => $student->id,
                            'recommendation_text' => "Conduct guardian consultation regarding attendance gaps."
                        ],
                        ['status' => 'pending']
                    );
                    $results[] = $flag;
                }
            }

            // 2. Academic Risk Check (Declining grade trends)
            $terms = StudentScore::where('student_id', $student->id)
                ->where('status', 'published')
                ->select('term_id', DB::raw('avg(grand_total) as avg_score'))
                ->groupBy('term_id')
                ->orderBy('term_id', 'desc')
                ->limit(2)
                ->get();

            if ($terms->count() === 2) {
                $latestTermAvg = $terms[0]->avg_score;
                $previousTermAvg = $terms[1]->avg_score;
                if (($previousTermAvg - $latestTermAvg) >= 10) {
                    $triggerReason = "Average marks declined by " . number_format($previousTermAvg - $latestTermAvg, 1) . " points between terms.";
                    $flag = AiFlag::updateOrCreate(
                        [
                            'school_id' => $schoolId,
                            'student_id' => $student->id,
                            'flag_type_id' => $academicType->id,
                        ],
                        [
                            'severity' => ($previousTermAvg - $latestTermAvg) >= 20 ? 'high' : 'medium',
                            'trigger_reason' => $triggerReason,
                            'is_resolved' => false,
                        ]
                    );

                    AiRecommendation::updateOrCreate(
                        [
                            'school_id' => $schoolId,
                            'student_id' => $student->id,
                            'recommendation_text' => "Schedule academic counseling and remedial study support."
                        ],
                        ['status' => 'pending']
                    );
                    $results[] = $flag;
                }
            }
        }

        return $results;
    }

    /**
     * Predict default risk based on invoices & outstanding arrears.
     */
    public function predictFeeDefaultRisk($schoolId)
    {
        $defaultType = AiFlagType::firstOrCreate(
            ['slug' => 'financial-default-risk'],
            ['name' => 'Financial Default Risk', 'description' => 'Flagged due to outstanding fee balances past due date.']
        );

        $invoices = Invoice::where('school_id', $schoolId)
            ->where('balance', '>', 0)
            ->where('due_date', '<', now())
            ->with('student')
            ->get();

        $results = [];

        foreach ($invoices as $inv) {
            if ($inv->student) {
                $triggerReason = "Outstanding balance of GHS " . number_format($inv->balance, 2) . " remains unpaid past due date " . $inv->due_date->format('Y-m-d') . ".";
                $flag = AiFlag::updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'student_id' => $inv->student_id,
                        'flag_type_id' => $defaultType->id,
                    ],
                    [
                        'severity' => $inv->balance >= 500 ? 'high' : 'medium',
                        'trigger_reason' => $triggerReason,
                        'is_resolved' => false,
                    ]
                );

                AiRecommendation::updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'student_id' => $inv->student_id,
                        'recommendation_text' => "Send payment reminder alert and set up installment payment schedule."
                    ],
                    ['status' => 'pending']
                );
                $results[] = $flag;
            }
        }

        return $results;
    }

    /**
     * Suggest comment for report card based on student score.
     */
    public function suggestReportCardComment($scoreVal)
    {
        if ($scoreVal >= 80) {
            return "Outstanding academic performance. Keep up the excellent work.";
        } elseif ($scoreVal >= 60) {
            return "Satisfactory work, showing consistent effort throughout the term.";
        } elseif ($scoreVal >= 40) {
            return "Average performance. Encouraged to pay closer attention to class assignments.";
        } else {
            return "Below expectations. Recommend additional tutoring and extra revision exercises.";
        }
    }
}
