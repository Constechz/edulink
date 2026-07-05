<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\ScoringConfiguration;
use App\Models\StudentScore;
use App\Services\Scoring\ScoringEngineService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ScoringApiController extends Controller
{
    protected $scoringService;
    protected $webhookService;

    public function __construct(ScoringEngineService $scoringService, WebhookService $webhookService)
    {
        $this->scoringService = $scoringService;
        $this->webhookService = $webhookService;
    }

    /**
     * Get list of scoring configurations.
     */
    public function getConfigurations(Request $request)
    {
        $configs = ScoringConfiguration::with('subject')->get();
        return response()->json([
            'success' => true,
            'data' => $configs
        ]);
    }

    /**
     * Create a scoring configuration.
     */
    public function storeConfiguration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'class_score_max' => 'required|numeric|min:1',
            'class_score_weight' => 'required|numeric|min:0|max:100',
            'exam_score_max' => 'required|numeric|min:1',
            'exam_score_weight' => 'required|numeric|min:0|max:100',
            'rounding_method' => 'required|string|in:ROUND,FLOOR,CEIL',
            'decimal_places' => 'required|integer|min:0|max:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $config = ScoringConfiguration::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Scoring configuration created successfully.',
            'data' => $config
        ], 201);
    }

    /**
     * Get list of student scores.
     */
    public function getScores(Request $request)
    {
        $scores = StudentScore::with(['student', 'class', 'stream', 'subject', 'term', 'academicYear'])->get();
        return response()->json([
            'success' => true,
            'data' => $scores
        ]);
    }

    /**
     * Store/Update a student score.
     */
    public function storeScore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'stream_id' => 'nullable|exists:streams,id',
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'raw_exam_score' => 'nullable|numeric|min:0',
            'component_scores' => 'nullable|array',
            'is_absent_exam' => 'nullable|boolean',
            'remarks' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $schoolId = app('tenant')->id;

        // Resolve Scoring Configuration
        $config = ScoringConfiguration::where('school_id', $schoolId)
            ->where('level', function ($query) use ($request) {
                $query->select('level')->from('classes')->where('id', $request->class_id);
            })
            ->where(function ($q) use ($request) {
                $q->where('subject_id', $request->subject_id)->orWhereNull('subject_id');
            })
            ->where('is_active', true)
            ->orderBy('subject_id', 'desc')
            ->first();

        if (!$config) {
            $config = ScoringConfiguration::where('school_id', $schoolId)
                ->where('is_active', true)
                ->where('is_default', true)
                ->first() ?: ScoringConfiguration::where('school_id', $schoolId)->first();
        }

        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'No active scoring configuration found for this level/subject.'
            ], 422);
        }

        try {
            $score = DB::transaction(function () use ($request, $schoolId, $config) {
                $score = StudentScore::updateOrCreate(
                    [
                        'student_id' => $request->student_id,
                        'class_id' => $request->class_id,
                        'subject_id' => $request->subject_id,
                        'term_id' => $request->term_id,
                        'academic_year_id' => $request->academic_year_id,
                    ],
                    [
                        'stream_id' => $request->stream_id,
                        'raw_exam_score' => $request->raw_exam_score,
                        'component_scores' => $request->component_scores ?: [],
                        'is_absent_exam' => $request->is_absent_exam ?: false,
                        'remarks' => $request->remarks,
                        'scoring_configuration_id' => $config->id,
                        'teacher_id' => $request->user()->id,
                        'status' => 'published',
                        'published_at' => now(),
                    ]
                );

                // Run computation
                $this->scoringService->calculateScore($score, $config);

                // Recalculate positions for this class-subject combo
                $this->scoringService->recalculateSubjectPositions(
                    $schoolId,
                    $request->class_id,
                    $request->term_id,
                    $request->academic_year_id,
                    $request->subject_id
                );

                return $score->fresh();
            });

            // Dispatch webhook
            $this->webhookService->dispatch('score.published', $score->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Student score saved and computed successfully.',
                'data' => $score
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save score: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store/Update student scores in bulk.
     */
    public function bulkStoreScores(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'scores' => 'required|array',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.class_id' => 'required|exists:classes,id',
            'scores.*.stream_id' => 'nullable|exists:streams,id',
            'scores.*.subject_id' => 'required|exists:subjects,id',
            'scores.*.term_id' => 'required|exists:terms,id',
            'scores.*.academic_year_id' => 'required|exists:academic_years,id',
            'scores.*.raw_exam_score' => 'nullable|numeric|min:0',
            'scores.*.component_scores' => 'nullable|array',
            'scores.*.is_absent_exam' => 'nullable|boolean',
            'scores.*.remarks' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $schoolId = app('tenant')->id;
        $results = [];

        try {
            DB::transaction(function () use ($request, $schoolId, &$results) {
                $combosToRecalculate = [];

                foreach ($request->scores as $index => $scoreData) {
                    // Resolve config per record (could vary by class level/subject)
                    $config = ScoringConfiguration::where('school_id', $schoolId)
                        ->where('level', function ($query) use ($scoreData) {
                            $query->select('level')->from('classes')->where('id', $scoreData['class_id']);
                        })
                        ->where(function ($q) use ($scoreData) {
                            $q->where('subject_id', $scoreData['subject_id'])->orWhereNull('subject_id');
                        })
                        ->where('is_active', true)
                        ->orderBy('subject_id', 'desc')
                        ->first();

                    if (!$config) {
                        $config = ScoringConfiguration::where('school_id', $schoolId)
                            ->where('is_active', true)
                            ->where('is_default', true)
                            ->first() ?: ScoringConfiguration::where('school_id', $schoolId)->first();
                    }

                    if (!$config) {
                        $results[] = [
                            'index' => $index,
                            'success' => false,
                            'message' => 'No active scoring configuration found for this level/subject.'
                        ];
                        continue;
                    }

                    $score = StudentScore::updateOrCreate(
                        [
                            'student_id' => $scoreData['student_id'],
                            'class_id' => $scoreData['class_id'],
                            'subject_id' => $scoreData['subject_id'],
                            'term_id' => $scoreData['term_id'],
                            'academic_year_id' => $scoreData['academic_year_id'],
                        ],
                        [
                            'stream_id' => $scoreData['stream_id'] ?? null,
                            'raw_exam_score' => $scoreData['raw_exam_score'] ?? null,
                            'component_scores' => $scoreData['component_scores'] ?? [],
                            'is_absent_exam' => $scoreData['is_absent_exam'] ?? false,
                            'remarks' => $scoreData['remarks'] ?? null,
                            'scoring_configuration_id' => $config->id,
                            'teacher_id' => $request->user()->id,
                            'status' => 'published',
                            'published_at' => now(),
                        ]
                    );

                    $this->scoringService->calculateScore($score, $config);

                    $comboKey = "{$scoreData['class_id']}-{$scoreData['term_id']}-{$scoreData['academic_year_id']}-{$scoreData['subject_id']}";
                    $combosToRecalculate[$comboKey] = [
                        'class_id' => $scoreData['class_id'],
                        'term_id' => $scoreData['term_id'],
                        'academic_year_id' => $scoreData['academic_year_id'],
                        'subject_id' => $scoreData['subject_id'],
                    ];

                    $results[] = [
                        'index' => $index,
                        'success' => true,
                        'data' => $score->fresh()
                    ];

                    // Dispatch webhook
                    $this->webhookService->dispatch('score.published', $score->toArray());
                }

                // Recalculate positions for all unique combinations touched
                foreach ($combosToRecalculate as $combo) {
                    $this->scoringService->recalculateSubjectPositions(
                        $schoolId,
                        $combo['class_id'],
                        $combo['term_id'],
                        $combo['academic_year_id'],
                        $combo['subject_id']
                    );
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Bulk student scores saved and computed successfully.',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to execute bulk score entry: ' . $e->getMessage()
            ], 500);
        }
    }
}
