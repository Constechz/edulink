<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\ScoringConfiguration;
use App\Models\Student;
use App\Models\StudentScore;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use App\Models\ScoreHistory;
use App\Services\Scoring\ScoringEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoreEntryController extends Controller
{
    protected $scoringService;

    public function __construct(ScoringEngineService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    /**
     * View spreadsheet score entry grid.
     */
    public function enter(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $user = $request->user();

        if ($user->role && $user->role->slug === 'class-teacher') {
            $assignedClassIds = \App\Models\Stream::where('class_teacher_id', $user->id)
                ->pluck('class_id')
                ->unique();
            $classes = SchoolClass::where('school_id', $schoolId)->whereIn('id', $assignedClassIds)->get();
        } else {
            $classes = SchoolClass::where('school_id', $schoolId)->get();
        }

        $subjects = Subject::where('school_id', $schoolId)->get();
        $terms = Term::where('school_id', $schoolId)->get();
        $academicYears = AcademicYear::where('school_id', $schoolId)->get();

        $selectedClassId = $request->get('class_id');
        if ($selectedClassId && $user->role && $user->role->slug === 'class-teacher') {
            $isAuthorized = \App\Models\Stream::where('class_teacher_id', $user->id)
                ->where('class_id', $selectedClassId)
                ->exists();
            if (!$isAuthorized) {
                abort(403, 'Unauthorized class access.');
            }
        }
        $selectedSubjectId = $request->get('subject_id');
        $selectedTermId = $request->get('term_id') ?: ($terms->where('is_current', true)->first()->id ?? null);
        $selectedYearId = $request->get('academic_year_id') ?: ($academicYears->where('is_current', true)->first()->id ?? null);

        $students = [];
        $scores = [];
        $config = null;
        $sheetStatus = 'draft';

        if ($selectedClassId && $selectedSubjectId && $selectedTermId && $selectedYearId) {
            $class = SchoolClass::find($selectedClassId);
            
            // Resolve Scoring Configuration
            $config = ScoringConfiguration::where('school_id', $schoolId)
                ->where('level', $class->level)
                ->where(function ($q) use ($selectedSubjectId) {
                    $q->where('subject_id', $selectedSubjectId)->orWhereNull('subject_id');
                })
                ->where('is_active', true)
                ->orderBy('subject_id', 'desc') // specific subject configuration takes precedence
                ->first();

            if (!$config) {
                // Check if default config exists
                $config = ScoringConfiguration::where('school_id', $schoolId)
                    ->where('is_active', true)
                    ->where('is_default', true)
                    ->first() ?: ScoringConfiguration::where('school_id', $schoolId)->first();
            }

            if ($config) {
                $config->load('components');
                
                $students = Student::where('school_id', $schoolId)
                    ->where('current_class_id', $selectedClassId)
                    ->get();

                $scoresList = StudentScore::where('school_id', $schoolId)
                    ->where('class_id', $selectedClassId)
                    ->where('subject_id', $selectedSubjectId)
                    ->where('term_id', $selectedTermId)
                    ->where('academic_year_id', $selectedYearId)
                    ->get();

                $scores = $scoresList->keyBy('student_id');
                
                // Sheet status is determined by the first student score record or draft
                if ($scoresList->count() > 0) {
                    $sheetStatus = $scoresList->first()->status;
                }
            }
        }

        return view('school.scores.enter', compact(
            'classes',
            'subjects',
            'terms',
            'academicYears',
            'students',
            'scores',
            'config',
            'selectedClassId',
            'selectedSubjectId',
            'selectedTermId',
            'selectedYearId',
            'sheetStatus'
        ));
    }

    /**
     * Autosave score draft via JSON.
     */
    public function saveDraft(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'scoring_configuration_id' => 'required|exists:scoring_configurations,id',
            'component_scores' => 'nullable|array',
            'raw_exam_score' => 'nullable|numeric|min:0',
            'is_absent_exam' => 'nullable|boolean',
            'remarks' => 'nullable|string',
        ]);

        $config = ScoringConfiguration::findOrFail($request->scoring_configuration_id);
        if ($config->school_id !== $schoolId) {
            return response()->json(['error' => 'Unauthorized config scope.'], 403);
        }

        // Check if student exists under tenant
        $student = Student::findOrFail($request->student_id);
        if ($student->school_id !== $schoolId) {
            return response()->json(['error' => 'Unauthorized student scope.'], 403);
        }

        $existingScore = StudentScore::where('school_id', $schoolId)
            ->where('student_id', $request->student_id)
            ->where('subject_id', $request->subject_id)
            ->where('term_id', $request->term_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->first();

        // Lock verification
        if ($existingScore && $existingScore->status !== 'draft') {
            return response()->json(['error' => 'Locked: Scores have already been submitted/verified.'], 403);
        }

        // Validate individual component scores range
        $components = $config->components->keyBy('id');
        $rawScoresInput = $request->component_scores ?: [];
        $validatedScores = [];
        
        foreach ($rawScoresInput as $compId => $val) {
            if ($val === '' || is_null($val)) continue;
            
            $comp = $components->get($compId);
            if (!$comp) continue;

            if (floatval($val) > floatval($comp->max_marks)) {
                return response()->json(['error' => "Score for {$comp->name} cannot exceed max marks ({$comp->max_marks})."], 422);
            }
            $validatedScores[$compId] = floatval($val);
        }

        // Check raw exam bounds
        if ($request->raw_exam_score && floatval($request->raw_exam_score) > floatval($config->exam_score_max)) {
            return response()->json(['error' => "Exam score cannot exceed max marks ({$config->exam_score_max})."], 422);
        }

        try {
            $score = DB::transaction(function () use ($request, $schoolId, $config, $validatedScores, $existingScore) {
                $oldValues = $existingScore ? $existingScore->toArray() : null;

                $studentScore = StudentScore::updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'student_id' => $request->student_id,
                        'subject_id' => $request->subject_id,
                        'term_id' => $request->term_id,
                        'academic_year_id' => $request->academic_year_id,
                    ],
                    [
                        'class_id' => $request->class_id,
                        'stream_id' => $request->stream_id ?: null,
                        'scoring_configuration_id' => $config->id,
                        'teacher_id' => $request->user()->id,
                        'component_scores' => $validatedScores,
                        'raw_exam_score' => $request->raw_exam_score,
                        'is_absent_exam' => $request->has('is_absent_exam') && $request->is_absent_exam == 1,
                        'remarks' => $request->remarks,
                        'status' => 'draft',
                        'created_by' => $request->user()->id,
                    ]
                );

                // Run math aggregation
                $this->scoringService->calculateScore($studentScore, $config);

                // Log audit trail
                ScoreHistory::create([
                    'student_score_id' => $studentScore->id,
                    'changed_by' => $request->user()->id,
                    'change_type' => $existingScore ? 'update' : 'create',
                    'old_values' => $oldValues,
                    'new_values' => $studentScore->toArray(),
                    'reason' => 'Teacher autosave draft',
                    'ip_address' => $request->ip(),
                ]);

                return $studentScore;
            });

            return response()->json([
                'success' => true,
                'raw_class_total' => $score->raw_class_total,
                'scaled_class_score' => $score->scaled_class_score,
                'scaled_exam_score' => $score->scaled_exam_score,
                'grand_total' => $score->grand_total,
                'grade' => $score->grade,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Database failure: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Submit whole score sheet to HOD.
     */
    public function submit(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $scoresCount = StudentScore::where('school_id', $schoolId)
            ->where('class_id', $request->class_id)
            ->where('subject_id', $request->subject_id)
            ->where('term_id', $request->term_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('status', 'draft')
            ->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

        return redirect()->back()->with('success', "Submitted {$scoresCount} scores to HOD successfully.");
    }

    /**
     * HOD verify scores.
     */
    public function verify(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'action' => 'required|string|in:approve,reject',
            'moderation_note' => 'nullable|string',
        ]);

        $query = StudentScore::where('school_id', $schoolId)
            ->where('class_id', $request->class_id)
            ->where('subject_id', $request->subject_id)
            ->where('term_id', $request->term_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('status', 'submitted');

        if ($request->action === 'approve') {
            $query->update([
                'status' => 'hod_verified',
                'hod_verified_at' => now(),
                'hod_verified_by' => $request->user()->id,
                'moderation_note' => $request->moderation_note,
            ]);
            $msg = 'Scores verified and forwarded to Headteacher.';
        } else {
            $query->update([
                'status' => 'draft',
                'moderation_note' => $request->moderation_note,
            ]);
            $msg = 'Scores rejected and sent back to teacher.';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Headteacher Approve Scores.
     */
    public function approve(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'action' => 'required|string|in:approve,reject',
            'moderation_note' => 'nullable|string',
        ]);

        $scores = StudentScore::where('school_id', $schoolId)
            ->where('class_id', $request->class_id)
            ->where('subject_id', $request->subject_id)
            ->where('term_id', $request->term_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('status', 'hod_verified')
            ->get();

        if ($request->action === 'approve') {
            foreach ($scores as $score) {
                $score->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => $request->user()->id,
                    'moderation_note' => $request->moderation_note,
                ]);
            }

            // Recalculate class subject position rankings
            $this->scoringService->recalculateSubjectPositions(
                $schoolId,
                $request->class_id,
                $request->term_id,
                $request->academic_year_id,
                $request->subject_id
            );

            $msg = 'Scores approved successfully. Ranks recalculated.';
        } else {
            foreach ($scores as $score) {
                $score->update([
                    'status' => 'draft',
                    'moderation_note' => $request->moderation_note,
                ]);
            }
            $msg = 'Scores rejected and returned to teacher draft mode.';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Unlock approved or published score sheets back to draft.
     */
    public function unlock(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        if (!$request->user()->hasPermission('approve-scores')) {
            return redirect()->back()->with('error', 'Unauthorized: You do not have permission to unlock scores.');
        }

        $scoresCount = StudentScore::where('school_id', $schoolId)
            ->where('class_id', $request->class_id)
            ->where('subject_id', $request->subject_id)
            ->where('term_id', $request->term_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->whereIn('status', ['approved', 'published'])
            ->update([
                'status' => 'draft',
                'approved_at' => null,
                'approved_by' => null,
                'published_at' => null,
            ]);

        return redirect()->back()->with('success', "Unlocked {$scoresCount} scores and returned to draft successfully.");
    }

    /**
     * Export loaded student scores to a CSV template.
     */
    public function exportCsv(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $schoolId = $request->user()->school_id;
        $class = SchoolClass::findOrFail($request->class_id);
        $subject = Subject::findOrFail($request->subject_id);

        $config = ScoringConfiguration::where('school_id', $schoolId)
            ->where('level', $class->level)
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
            return redirect()->back()->with('error', 'No active scoring configuration found.');
        }

        $config->load('components');

        $students = Student::where('school_id', $schoolId)
            ->where('current_class_id', $request->class_id)
            ->get();

        $scores = StudentScore::where('school_id', $schoolId)
            ->where('class_id', $request->class_id)
            ->where('subject_id', $request->subject_id)
            ->where('term_id', $request->term_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->get()
            ->keyBy('student_id');

        $fileName = "Scores_Template_" . str_replace(' ', '_', $class->name) . "_" . str_replace(' ', '_', $subject->name) . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Student ID', 'Admission No', 'Student Name'];
        foreach ($config->components as $comp) {
            $columns[] = "Component:{$comp->id} ({$comp->name}, Max: {$comp->max_marks})";
        }
        $columns[] = "Exam (Max: {$config->exam_score_max})";
        $columns[] = 'Remarks';

        $callback = function() use ($students, $columns, $config, $scores) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($students as $student) {
                $score = $scores->get($student->id);
                $componentScores = $score ? $score->component_scores : [];

                $row = [
                    $student->id,
                    $student->admission_no,
                    $student->first_name . ' ' . $student->last_name,
                ];

                foreach ($config->components as $comp) {
                    $row[] = isset($componentScores[$comp->id]) ? $componentScores[$comp->id] : '';
                }

                $row[] = ($score && !is_null($score->raw_exam_score)) ? $score->raw_exam_score : '';
                $row[] = $score ? $score->remarks : '';

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import scores from CSV and validate/calculate.
     */
    public function importCsv(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'csv_file' => 'required|file|mimes:csv,txt|max:4096',
        ]);

        if (!$request->user()->hasPermission('enter-scores')) {
            return redirect()->back()->with('error', 'Unauthorized: You do not have permission to enter scores.');
        }

        // Check lock status
        $firstScore = StudentScore::where('school_id', $schoolId)
            ->where('class_id', $request->class_id)
            ->where('subject_id', $request->subject_id)
            ->where('term_id', $request->term_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->first();

        if ($firstScore && $firstScore->status !== 'draft') {
            return redirect()->back()->with('error', 'Import Locked: Scores have already been submitted/verified.');
        }

        $class = SchoolClass::findOrFail($request->class_id);
        $config = ScoringConfiguration::where('school_id', $schoolId)
            ->where('level', $class->level)
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
            return redirect()->back()->with('error', 'No active scoring configuration found.');
        }

        $config->load('components');
        $components = $config->components->keyBy('id');

        $path = $request->file('csv_file')->getRealPath();
        $file = fopen($path, 'r');
        $headers = fgetcsv($file);

        if (!$headers) {
            fclose($file);
            return redirect()->back()->with('error', 'Invalid CSV file structure.');
        }

        // Map header columns
        $studentIdIdx = -1;
        $studentNameIdx = -1;
        $componentMapping = []; // CSV column index => component_id
        $examIdx = -1;
        $remarksIdx = -1;

        foreach ($headers as $idx => $header) {
            $header = trim($header);
            if (strcasecmp($header, 'Student ID') === 0) {
                $studentIdIdx = $idx;
            } elseif (strcasecmp($header, 'Student Name') === 0) {
                $studentNameIdx = $idx;
            } elseif (stripos($header, 'Component:') === 0) {
                preg_match('/Component:(\d+)/i', $header, $matches);
                if (isset($matches[1])) {
                    $compId = intval($matches[1]);
                    if ($components->has($compId)) {
                        $componentMapping[$idx] = $compId;
                    }
                }
            } elseif (stripos($header, 'Exam') === 0) {
                $examIdx = $idx;
            } elseif (strcasecmp($header, 'Remarks') === 0) {
                $remarksIdx = $idx;
            }
        }

        if ($studentIdIdx === -1) {
            fclose($file);
            return redirect()->back()->with('error', 'CSV is missing "Student ID" column.');
        }

        $rowsData = [];
        $lineNum = 1;
        $validationErrors = [];

        // Validate all rows first (dry run)
        while (($row = fgetcsv($file)) !== false) {
            $lineNum++;
            if (empty($row) || count($row) < $studentIdIdx + 1) continue;

            $studentId = trim($row[$studentIdIdx]);
            if ($studentId === '') continue;

            $student = Student::where('school_id', $schoolId)
                ->where('id', $studentId)
                ->where('current_class_id', $request->class_id)
                ->first();

            $studentName = $studentNameIdx !== -1 && isset($row[$studentNameIdx]) ? trim($row[$studentNameIdx]) : "ID {$studentId}";

            if (!$student) {
                $validationErrors[] = "Row {$lineNum}: Student [{$studentName}] not found in this class.";
                continue;
            }

            $componentScores = [];
            foreach ($componentMapping as $colIdx => $compId) {
                $valStr = isset($row[$colIdx]) ? trim($row[$colIdx]) : '';
                if ($valStr !== '') {
                    $val = floatval($valStr);
                    $comp = $components->get($compId);
                    if ($val < 0 || $val > floatval($comp->max_marks)) {
                        $validationErrors[] = "Row {$lineNum}: {$studentName} - score {$val} for {$comp->name} exceeds limit (Max: {$comp->max_marks}).";
                    }
                    $componentScores[$compId] = $val;
                }
            }

            $rawExam = null;
            if ($examIdx !== -1 && isset($row[$examIdx]) && trim($row[$examIdx]) !== '') {
                $examVal = floatval(trim($row[$examIdx]));
                if ($examVal < 0 || $examVal > floatval($config->exam_score_max)) {
                    $validationErrors[] = "Row {$lineNum}: {$studentName} - Exam score {$examVal} exceeds limit (Max: {$config->exam_score_max}).";
                }
                $rawExam = $examVal;
            }

            $remarks = $remarksIdx !== -1 && isset($row[$remarksIdx]) ? trim($row[$remarksIdx]) : null;

            $rowsData[] = [
                'student_id' => $studentId,
                'component_scores' => $componentScores,
                'raw_exam_score' => $rawExam,
                'remarks' => $remarks,
            ];
        }
        fclose($file);

        if (count($validationErrors) > 0) {
            return redirect()->back()->with('error', 'CSV Import Failed: ' . implode(' | ', array_slice($validationErrors, 0, 5)) . (count($validationErrors) > 5 ? '... and more.' : ''));
        }

        // DB Transaction saving
        try {
            DB::transaction(function () use ($rowsData, $schoolId, $request, $config) {
                foreach ($rowsData as $data) {
                    $existingScore = StudentScore::where('school_id', $schoolId)
                        ->where('student_id', $data['student_id'])
                        ->where('subject_id', $request->subject_id)
                        ->where('term_id', $request->term_id)
                        ->where('academic_year_id', $request->academic_year_id)
                        ->first();

                    $oldValues = $existingScore ? $existingScore->toArray() : null;

                    $studentScore = StudentScore::updateOrCreate(
                        [
                            'school_id' => $schoolId,
                            'student_id' => $data['student_id'],
                            'subject_id' => $request->subject_id,
                            'term_id' => $request->term_id,
                            'academic_year_id' => $request->academic_year_id,
                        ],
                        [
                            'class_id' => $request->class_id,
                            'scoring_configuration_id' => $config->id,
                            'teacher_id' => $request->user()->id,
                            'component_scores' => $data['component_scores'],
                            'raw_exam_score' => $data['raw_exam_score'],
                            'is_absent_exam' => false,
                            'remarks' => $data['remarks'],
                            'status' => 'draft',
                            'created_by' => $request->user()->id,
                        ]
                    );

                    $this->scoringService->calculateScore($studentScore, $config);

                    ScoreHistory::create([
                        'student_score_id' => $studentScore->id,
                        'changed_by' => $request->user()->id,
                        'change_type' => $existingScore ? 'update' : 'create',
                        'old_values' => $oldValues,
                        'new_values' => $studentScore->toArray(),
                        'reason' => 'Teacher CSV bulk import',
                        'ip_address' => $request->ip(),
                    ]);
                }
            });

            return redirect()->back()->with('success', 'Imported ' . count($rowsData) . ' student scores successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Database failure during import: ' . $e->getMessage());
        }
    }
}


