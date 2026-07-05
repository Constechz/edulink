<?php

namespace App\Services\Scoring;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentScore;
use App\Models\Term;
use App\Models\PromotionConfiguration;
use Illuminate\Support\Collection;

class PromotionEngineService
{
    /**
     * Get computed promotion recommendations for all active students in a class.
     *
     * @param int $schoolId
     * @param int $classId
     * @param int $academicYearId
     * @param int|null $streamId
     * @return Collection
     */
    public function getPromotionRecommendations(int $schoolId, int $classId, int $academicYearId, ?int $streamId = null): Collection
    {
        $class = SchoolClass::where('school_id', $schoolId)->findOrFail($classId);

        // 1. Fetch promotion configuration for this level/class or fall back to default
        $config = $this->getPromotionConfiguration($schoolId, $classId, strtolower($class->level));

        // 2. Fetch terms of the academic year sorted by start date
        $terms = Term::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->orderBy('start_date', 'asc')
            ->get();

        $termIds = $terms->pluck('id')->toArray();
        $term1Id = $termIds[0] ?? null;
        $term2Id = $termIds[1] ?? null;
        $term3Id = $termIds[2] ?? null;

        // 3. Fetch all active students in this class/stream
        $studentQuery = Student::where('school_id', $schoolId)
            ->where('current_class_id', $classId)
            ->where('status', 'active');

        if ($streamId) {
            $studentQuery->where('current_stream_id', $streamId);
        }

        $students = $studentQuery->get();

        // 4. Fetch all student scores in this class & academic year
        $scoresQuery = StudentScore::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('academic_year_id', $academicYearId)
            ->whereIn('status', ['hod_verified', 'approved', 'published']);

        if ($streamId) {
            $scoresQuery->where('stream_id', $streamId);
        }

        $allScores = $scoresQuery->get()->groupBy('student_id');

        // 5. Check if class is a terminal year (BECE/WASSCE candidate)
        $isTerminal = false;
        $terminalDecision = null;
        if ($config->exclude_terminal_year) {
            $classNameLower = strtolower($class->name);
            $levelLower = strtolower($class->level);
            if ($levelLower === 'jhs' && (str_contains($classNameLower, '3') || str_contains($classNameLower, '9') || str_contains($classNameLower, 'basic 9'))) {
                $isTerminal = true;
                $terminalDecision = 'bece_candidate';
            } elseif ($levelLower === 'shs' && (str_contains($classNameLower, '3') || str_contains($classNameLower, 'form 3'))) {
                $isTerminal = true;
                $terminalDecision = 'wassce_candidate';
            }
        }

        // Get Term Weights
        $w1 = floatval($config->term_weights_json['term1'] ?? 1);
        $w2 = floatval($config->term_weights_json['term2'] ?? 1);
        $w3 = floatval($config->term_weights_json['term3'] ?? 1);
        $totalWeight = ($w1 + $w2 + $w3) ?: 1.0;

        $results = collect();

        foreach ($students as $student) {
            $studentScores = $allScores->get($student->id) ?? collect();

            // Calculate term-wise overall scores (averages of all subjects)
            $t1Scores = $studentScores->where('term_id', $term1Id)->whereNotNull('grand_total');
            $t2Scores = $studentScores->where('term_id', $term2Id)->whereNotNull('grand_total');
            $t3Scores = $studentScores->where('term_id', $term3Id)->whereNotNull('grand_total');

            $term1_score = $t1Scores->count() > 0 ? floatval($t1Scores->avg('grand_total')) : null;
            $term2_score = $t2Scores->count() > 0 ? floatval($t2Scores->avg('grand_total')) : null;
            $term3_score = $t3Scores->count() > 0 ? floatval($t3Scores->avg('grand_total')) : null;

            // Treat missing term averages as 0 for cumulative computation
            $t1Calc = $term1_score ?? 0.00;
            $t2Calc = $term2_score ?? 0.00;
            $t3Calc = $term3_score ?? 0.00;

            // Compute composite/annual average based on selected method
            $computed_average = 0.00;
            $decision = 'repeat';

            if ($isTerminal) {
                $decision = $terminalDecision;
                $computed_average = $t3Scores->count() > 0 ? floatval($t3Scores->avg('grand_total')) : (($t1Calc + $t2Calc + $t3Calc) / 3);
            } else {
                switch ($config->method) {
                    case 'two_of_three':
                        // Best 2 of 3 terms, where Term 3 is typically required
                        // If Term 3 is null, default back to best 2 overall
                        $avg1_3 = ($t1Calc + $t3Calc) / 2;
                        $avg2_3 = ($t2Calc + $t3Calc) / 2;
                        $avg1_2 = ($t1Calc + $t2Calc) / 2;

                        if ($term3_score !== null) {
                            $computed_average = max($avg1_3, $avg2_3);
                        } else {
                            $computed_average = max($avg1_3, $avg2_3, $avg1_2);
                        }
                        break;

                    case 'subject_pass_count':
                        // Weighted overall average
                        $computed_average = (($t1Calc * $w1) + ($t2Calc * $w2) + ($t3Calc * $w3)) / $totalWeight;
                        break;

                    case 'annual_average':
                    default:
                        // Weighted overall average
                        $computed_average = (($t1Calc * $w1) + ($t2Calc * $w2) + ($t3Calc * $w3)) / $totalWeight;
                        break;
                }

                // Check thresholds
                $promoThreshold = floatval($config->promotion_threshold);
                $condThreshold = floatval($config->conditional_threshold) ?: $promoThreshold;

                if ($config->method === 'subject_pass_count') {
                    // Group scores by subject to find subject annual averages
                    $subjectsPassed = 0;
                    $subjectScoresGrouped = $studentScores->groupBy('subject_id');
                    $passMark = floatval($config->per_subject_pass_mark) ?: 40.00;
                    $minPassCount = intval($config->min_subjects_to_pass) ?: 1;

                    foreach ($subjectScoresGrouped as $subjectId => $scoresList) {
                        $s1 = $scoresList->where('term_id', $term1Id)->first();
                        $s2 = $scoresList->where('term_id', $term2Id)->first();
                        $s3 = $scoresList->where('term_id', $term3Id)->first();

                        $s1Val = $s1 ? floatval($s1->grand_total) : 0.00;
                        $s2Val = $s2 ? floatval($s2->grand_total) : 0.00;
                        $s3Val = $s3 ? floatval($s3->grand_total) : 0.00;

                        $subjAvg = (($s1Val * $w1) + ($s2Val * $w2) + ($s3Val * $w3)) / $totalWeight;
                        if ($subjAvg >= $passMark) {
                            $subjectsPassed++;
                        }
                    }

                    if ($subjectsPassed >= $minPassCount && $computed_average >= $promoThreshold) {
                        $decision = 'promoted';
                    } elseif ($computed_average >= $condThreshold) {
                        $decision = 'conditional';
                    } else {
                        $decision = 'repeat';
                    }
                } else {
                    if ($computed_average >= $promoThreshold) {
                        $decision = 'promoted';
                    } elseif ($computed_average >= $condThreshold) {
                        $decision = 'conditional';
                    } else {
                        $decision = 'repeat';
                    }
                }
            }

            // Round values to 2 decimal places
            $results->push((object)[
                'student_id' => $student->id,
                'student' => $student,
                'term1_score' => $term1_score !== null ? round($term1_score, 2) : null,
                'term2_score' => $term2_score !== null ? round($term2_score, 2) : null,
                'term3_score' => $term3_score !== null ? round($term3_score, 2) : null,
                'computed_average' => round($computed_average, 2),
                'decision' => $decision,
                'method_used' => $config->method,
                'rule_snapshot_json' => [
                    'method' => $config->method,
                    'promotion_threshold' => $config->promotion_threshold,
                    'conditional_threshold' => $config->conditional_threshold,
                    'min_subjects_to_pass' => $config->min_subjects_to_pass,
                    'per_subject_pass_mark' => $config->per_subject_pass_mark,
                    'term_weights' => $config->term_weights_json,
                ]
            ]);
        }

        return $results;
    }

    /**
     * Fetch active promotion rules configuration or return fallback instance.
     *
     * @param int $schoolId
     * @param int|null $classId
     * @param string $level
     * @return PromotionConfiguration
     */
    public function getPromotionConfiguration(int $schoolId, ?int $classId, string $level): PromotionConfiguration
    {
        $config = null;
        
        // Match specific class first
        if ($classId) {
            $config = PromotionConfiguration::where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->where('is_active', true)
                ->first();
        }

        // Match level-wide config
        if (!$config) {
            $config = PromotionConfiguration::where('school_id', $schoolId)
                ->where('level', $level)
                ->whereNull('class_id')
                ->where('is_active', true)
                ->first();
        }

        // Create standard fallback if missing
        if (!$config) {
            $config = new PromotionConfiguration([
                'school_id' => $schoolId,
                'level' => $level,
                'method' => 'annual_average',
                'term_weights_json' => ['term1' => 1, 'term2' => 1, 'term3' => 1],
                'promotion_threshold' => 40.00,
                'conditional_threshold' => 35.00,
                'repeat_limit' => 1,
                'exclude_terminal_year' => true,
                'is_active' => true,
            ]);
        }

        return $config;
    }
}
