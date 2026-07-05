<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\PromotionRun;
use App\Models\StudentPromotionRecord;
use App\Services\Scoring\PromotionEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentPromotionController extends Controller
{
    protected $promotionEngine;

    public function __construct(PromotionEngineService $promotionEngine)
    {
        $this->promotionEngine = $promotionEngine;
    }

    /**
     * Display the student promotion wizard dashboard.
     */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $classes = SchoolClass::where('school_id', $schoolId)->get();
        $streams = Stream::where('school_id', $schoolId)->get();
        
        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->orderBy('start_date', 'asc')
            ->get();
            
        $currentYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_current', true)
            ->first();

        // Load filtered students with calculated recommendations if parameters are provided
        $students = collect();
        $sourceClassId = $request->get('source_class_id');
        $sourceYearId = $request->get('source_academic_year_id');
        $sourceStreamId = $request->get('source_stream_id');

        if ($sourceClassId && $sourceYearId) {
            // Get calculated recommendations from engine
            $recommendations = $this->promotionEngine->getPromotionRecommendations(
                $schoolId,
                $sourceClassId,
                $sourceYearId,
                $sourceStreamId ? intval($sourceStreamId) : null
            )->keyBy('student_id');

            $enrollmentQuery = StudentEnrollment::where('school_id', $schoolId)
                ->where('class_id', $sourceClassId)
                ->where('academic_year_id', $sourceYearId)
                ->where('status', 'active');

            if ($sourceStreamId) {
                $enrollmentQuery->where('stream_id', $sourceStreamId);
            }

            $enrollments = $enrollmentQuery->with('student')->get();
            
            // Extract active students and map computed scores & recommendations
            $students = $enrollments->pluck('student')->filter(function ($student) {
                return $student !== null && $student->status === 'active';
            })->map(function ($student) use ($recommendations) {
                $rec = $recommendations->get($student->id);
                $student->term1_score = $rec ? $rec->term1_score : null;
                $student->term2_score = $rec ? $rec->term2_score : null;
                $student->term3_score = $rec ? $rec->term3_score : null;
                $student->computed_average = $rec ? $rec->computed_average : 0.00;
                $student->recommended_decision = $rec ? $rec->decision : 'promote';
                return $student;
            });
        }

        return view('school.students.promotion', compact(
            'classes', 
            'streams', 
            'academicYears', 
            'currentYear', 
            'students', 
            'sourceClassId', 
            'sourceYearId', 
            'sourceStreamId'
        ));
    }

    /**
     * Process student promotions in bulk.
     */
    public function process(Request $request)
    {
        $schoolId = $request->user()->school_id;

        // Determine if there are promotions in the request and merge it
        $hasPromotions = false;
        if ($request->has('students') && is_array($request->students)) {
            foreach ($request->students as $studentData) {
                if (($studentData['status'] ?? '') === 'promote') {
                    $hasPromotions = true;
                    break;
                }
            }
        }
        $request->merge(['has_promotions' => $hasPromotions ? 1 : 0]);

        $request->validate([
            'source_class_id' => 'required|exists:classes,id',
            'source_academic_year_id' => 'required|exists:academic_years,id',
            'destination_class_id' => 'required_if:has_promotions,1|nullable|exists:classes,id',
            'destination_academic_year_id' => 'required|exists:academic_years,id',
            'students' => 'required|array',
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.status' => 'required|in:promote,repeat,graduate,none',
        ]);

        if ($request->source_academic_year_id == $request->destination_academic_year_id) {
            return redirect()->back()->withErrors([
                'destination_academic_year_id' => 'Source and Target Academic Years cannot be the same. Rollover requires transitioning to a different year.'
            ])->withInput();
        }

        try {
            DB::transaction(function () use ($request, $schoolId) {
                $sourceClassId = $request->source_class_id;
                $sourceYearId = $request->source_academic_year_id;
                $destinationClassId = $request->destination_class_id;
                $destinationYearId = $request->destination_academic_year_id;

                $sourceClass = SchoolClass::where('school_id', $schoolId)->findOrFail($sourceClassId);
                $level = strtolower($sourceClass->level);

                // Fetch recommendations snapshot to check for overrides and compute details
                $recommendations = $this->promotionEngine->getPromotionRecommendations($schoolId, $sourceClassId, $sourceYearId)->keyBy('student_id');

                // Create or find Promotion Run log
                $promotionRun = PromotionRun::firstOrCreate([
                    'school_id' => $schoolId,
                    'academic_year_id' => $sourceYearId,
                    'level' => $level,
                ], [
                    'run_by' => $request->user()->id,
                    'status' => 'published',
                    'generated_at' => now(),
                    'approved_by' => $request->user()->id,
                    'approved_at' => now(),
                    'published_at' => now(),
                ]);

                foreach ($request->students as $studentData) {
                    $studentId = $studentData['student_id'];
                    $status = $studentData['status'];

                    if ($status === 'none') {
                        continue;
                    }

                    $student = Student::where('school_id', $schoolId)->findOrFail($studentId);
                    $rec = $recommendations->get($studentId);

                    // 1. Mark the student's previous enrollment in the source year as completed
                    StudentEnrollment::where('school_id', $schoolId)
                        ->where('student_id', $studentId)
                        ->where('academic_year_id', $sourceYearId)
                        ->update(['status' => 'completed']);

                    $destinationClass = null;

                    if ($status === 'promote') {
                        $destinationClass = $destinationClassId;
                        // Update current placement on student record
                        $student->update([
                            'current_class_id' => $destinationClassId,
                            'current_stream_id' => null, // Reset stream to allow reassignment in the new class Stream
                        ]);

                        // Create promotion enrollment log for destination year
                        StudentEnrollment::create([
                            'school_id' => $schoolId,
                            'student_id' => $studentId,
                            'academic_year_id' => $destinationYearId,
                            'class_id' => $destinationClassId,
                            'stream_id' => null,
                            'status' => 'active',
                            'enrollment_date' => now(),
                            'promoted_from_class_id' => $sourceClassId,
                        ]);
                    } 
                    elseif ($status === 'repeat') {
                        $destinationClass = $sourceClassId;
                        // Student stays in source class
                        $student->update([
                            'current_class_id' => $sourceClassId,
                        ]);

                        // Create repetition enrollment log in the destination year
                        StudentEnrollment::create([
                            'school_id' => $schoolId,
                            'student_id' => $studentId,
                            'academic_year_id' => $destinationYearId,
                            'class_id' => $sourceClassId,
                            'stream_id' => $student->current_stream_id,
                            'status' => 'active',
                            'enrollment_date' => now(),
                            'promoted_from_class_id' => null,
                        ]);

                        // Log to student_repeat_history
                        \App\Models\StudentRepeatHistory::create([
                            'school_id' => $schoolId,
                            'student_id' => $studentId,
                            'class_id' => $sourceClassId,
                            'academic_year_id' => $sourceYearId,
                            'reason' => 'Calculated average or user decision to repeat',
                            'recorded_by' => $request->user()->id,
                        ]);
                    } 
                    elseif ($status === 'graduate') {
                        // Mark student as graduated
                        $student->update([
                            'current_class_id' => null,
                            'current_stream_id' => null,
                            'status' => 'graduated',
                        ]);
                    }

                    // Save computed snapshot to student_promotion_records
                    $term1 = $rec ? $rec->term1_score : null;
                    $term2 = $rec ? $rec->term2_score : null;
                    $term3 = $rec ? $rec->term3_score : null;
                    $avg = $rec ? $rec->computed_average : null;
                    $ruleSnapshot = $rec ? $rec->rule_snapshot_json : [];
                    $methodUsed = $rec ? $rec->method_used : 'manual';
                    $engineDecision = $rec ? $rec->decision : 'promoted';

                    // Map action status to database decision
                    $dbDecision = 'promoted';
                    if ($status === 'repeat') {
                        $dbDecision = 'repeat';
                    } elseif ($status === 'graduate') {
                        $dbDecision = 'promoted';
                    }

                    if ($engineDecision === 'bece_candidate' || $engineDecision === 'wassce_candidate') {
                        $dbDecision = $engineDecision;
                    }

                    $isOverride = ($dbDecision !== $engineDecision);
                    $overrideReason = $isOverride ? 'Manual override by user in rollover promotion wizard' : null;

                    StudentPromotionRecord::updateOrCreate([
                        'school_id' => $schoolId,
                        'student_id' => $studentId,
                        'academic_year_id' => $sourceYearId,
                    ], [
                        'promotion_run_id' => $promotionRun->id,
                        'from_class_id' => $sourceClassId,
                        'to_class_id' => $destinationClass,
                        'term1_score' => $term1,
                        'term2_score' => $term2,
                        'term3_score' => $term3,
                        'computed_average' => $avg,
                        'method_used' => $methodUsed,
                        'rule_snapshot_json' => $ruleSnapshot,
                        'decision' => $dbDecision,
                        'is_override' => $isOverride,
                        'override_reason' => $overrideReason,
                        'decided_by' => $request->user()->id,
                        'decided_at' => now(),
                    ]);
                }
            });

            return redirect()->route('school.students')->with('success', 'Student promotions and rollover processed successfully.');
            
        } catch (\Exception $e) {
            Log::error('Student Promotion Failed: ' . $e->getMessage());
            return redirect()->back()->withErrors([
                'error' => 'An error occurred while processing student promotions: ' . $e->getMessage()
            ])->withInput();
        }
    }
}

