<?php

namespace App\Services\Scoring;

use App\Models\GradingScale;
use App\Models\GradingScaleItem;
use App\Models\ScoringConfiguration;
use App\Models\StudentScore;
use Illuminate\Support\Facades\DB;

class ScoringEngineService
{
    /**
     * Calculate and store scores for a single student subject score record.
     */
    public function calculateScore(StudentScore $score, ScoringConfiguration $config): StudentScore
    {
        // 1. Calculate Raw Class Total from components
        $componentScores = $score->component_scores ?: [];
        $rawClassTotal = 0;
        foreach ($componentScores as $compVal) {
            $rawClassTotal += floatval($compVal);
        }

        // Avoid division by zero
        $classScoreMax = floatval($config->class_score_max) ?: 1.0;
        $classScoreWeight = floatval($config->class_score_weight);

        // 2. Scaled Class Score
        $scaledClassScore = ($rawClassTotal / $classScoreMax) * $classScoreWeight;
        $scaledClassScore = $this->roundValue($scaledClassScore, $config->rounding_method, $config->decimal_places);

        // 3. Scaled Exam Score
        $scaledExamScore = null;
        if ($score->is_absent_exam) {
            $score->raw_exam_score = 0;
            $scaledExamScore = 0.0;
        } elseif (!is_null($score->raw_exam_score)) {
            $examScoreMax = floatval($config->exam_score_max) ?: 1.0;
            $examScoreWeight = floatval($config->exam_score_weight);
            $scaledExamScore = (floatval($score->raw_exam_score) / $examScoreMax) * $examScoreWeight;
            $scaledExamScore = $this->roundValue($scaledExamScore, $config->rounding_method, $config->decimal_places);
        }

        // 4. Grand Total
        $grandTotal = null;
        if (!is_null($scaledExamScore)) {
            $grandTotal = $scaledClassScore + $scaledExamScore;
            $grandTotal = $this->roundValue($grandTotal, $config->rounding_method, $config->decimal_places);
        }

        // 5. Look up Grade, Grade Point, and Remarks
        $grade = null;
        $gradePoint = null;
        $remarks = null;

        if (!is_null($grandTotal)) {
            $class = $score->class;
            $level = $class ? $class->level : 'Primary';

            // Find Grading Scale
            $scale = GradingScale::where('school_id', $score->school_id)
                ->where('is_active', true)
                ->where('level', $level)
                ->first();

            if (!$scale) {
                // Fallback to default scale
                $scale = GradingScale::where('school_id', $score->school_id)
                    ->where('is_active', true)
                    ->where('is_default', true)
                    ->first() ?: GradingScale::where('school_id', $score->school_id)->first();
            }

            if ($scale) {
                $item = GradingScaleItem::where('grading_scale_id', $scale->id)
                    ->where('min_score', '<=', $grandTotal)
                    ->where('max_score', '>=', $grandTotal)
                    ->first();

                if ($item) {
                    $grade = $item->grade;
                    $gradePoint = $item->grade_point;
                    $remarks = $item->description;
                }
            }
        }

        // Save computed properties
        $score->raw_class_total = $rawClassTotal;
        $score->scaled_class_score = $scaledClassScore;
        $score->raw_exam_score = $score->is_absent_exam ? 0 : $score->raw_exam_score;
        $score->scaled_exam_score = $scaledExamScore;
        $score->grand_total = $grandTotal;
        $score->grade = $grade;
        $score->grade_point = $gradePoint;
        $score->remarks = $score->remarks ?: $remarks;
        $score->scoring_configuration_id = $config->id;

        $score->save();

        return $score;
    }

    /**
     * Round value according to the configured rounding method and decimals.
     */
    private function roundValue(float $value, string $method, int $places): float
    {
        $multiplier = pow(10, $places);
        switch (strtoupper($method)) {
            case 'FLOOR':
                return floor($value * $multiplier) / $multiplier;
            case 'CEIL':
                return ceil($value * $multiplier) / $multiplier;
            case 'ROUND':
            default:
                return round($value, $places);
        }
    }

    /**
     * Recalculate student subject positions in a specific class context.
     */
    public function recalculateSubjectPositions(int $schoolId, int $classId, int $termId, int $yearId, int $subjectId): void
    {
        $scores = StudentScore::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('term_id', $termId)
            ->where('academic_year_id', $yearId)
            ->where('subject_id', $subjectId)
            ->whereNotNull('grand_total')
            ->orderBy('grand_total', 'desc')
            ->get();

        $totalStudents = $scores->count();

        $position = 1;
        $prevScore = null;
        $tiedCount = 0;

        foreach ($scores as $index => $score) {
            $currentScore = floatval($score->grand_total);
            
            if ($index > 0) {
                if ($currentScore === $prevScore) {
                    $tiedCount++;
                } else {
                    $position += $tiedCount + 1;
                    $tiedCount = 0;
                }
            }

            $score->update([
                'subject_position' => $position,
                'total_students' => $totalStudents,
            ]);

            $prevScore = $currentScore;
        }
    }
}
