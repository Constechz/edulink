<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ScoreComponent;
use App\Models\ScoringConfiguration;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoringConfigurationController extends Controller
{
    /**
     * Display list of configurations.
     */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $configs = ScoringConfiguration::where('school_id', $schoolId)
            ->with(['components', 'subject'])
            ->get();

        return view('school.scoring_configs.index', compact('configs'));
    }

    /**
     * Show create wizard page.
     */
    public function create(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $academicYears = AcademicYear::where('school_id', $schoolId)->get();
        $subjects = Subject::where('school_id', $schoolId)->get();

        return view('school.scoring_configs.create', compact('academicYears', 'subjects'));
    }

    /**
     * Store new configuration with components validation.
     */
    public function store(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'name' => 'required|string|max:100',
            'level' => 'required|string|in:ALL,Nursery,KG,Primary,JHS,SHS,TVET,Tertiary',
            'subject_id' => 'nullable|exists:subjects,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'class_score_max' => 'required|numeric|min:1',
            'class_score_weight' => 'required|numeric|min:0|max:100',
            'exam_score_max' => 'required|numeric|min:1',
            'exam_score_weight' => 'required|numeric|min:0|max:100',
            'rounding_method' => 'required|string|in:ROUND,FLOOR,CEIL',
            'decimal_places' => 'required|integer|min:0|max:2',
            'is_default' => 'nullable|boolean',
            'components' => 'required|array|min:1',
            'components.*.name' => 'required|string|max:100',
            'components.*.max_marks' => 'required|numeric|min:0.1',
            'components.*.is_required' => 'nullable',
        ]);

        $grandTotal = floatval($request->class_score_weight) + floatval($request->exam_score_weight);

        // Validation 1: Weight Sum
        if ($grandTotal <= 0) {
            return redirect()->back()->withInput()->withErrors(['class_score_weight' => 'Combined scaled weights must exceed 0.']);
        }

        // Validation 2: Sum of components <= class_score_max
        $componentsSum = 0;
        foreach ($request->components as $comp) {
            $componentsSum += floatval($comp['max_marks']);
        }

        if ($componentsSum > floatval($request->class_score_max)) {
            return redirect()->back()->withInput()->withErrors(['class_score_max' => "Validation Error: The components marks sum ({$componentsSum}) exceeds the Class Score Maximum ({$request->class_score_max})."]);
        }

        try {
            DB::transaction(function () use ($request, $schoolId, $grandTotal) {
                $isDefault = $request->has('is_default') && $request->is_default;

                if ($isDefault) {
                    // Turn off other defaults for this level
                    ScoringConfiguration::where('school_id', $schoolId)
                        ->where('level', $request->level)
                        ->update(['is_default' => false]);
                }

                $config = ScoringConfiguration::create([
                    'school_id' => $schoolId,
                    'campus_id' => $request->user()->campus_id,
                    'level' => $request->level,
                    'subject_id' => $request->subject_id,
                    'academic_year_id' => $request->academic_year_id,
                    'name' => $request->name,
                    'class_score_max' => $request->class_score_max,
                    'class_score_weight' => $request->class_score_weight,
                    'exam_score_max' => $request->exam_score_max,
                    'exam_score_weight' => $request->exam_score_weight,
                    'grand_total' => $grandTotal,
                    'rounding_method' => $request->rounding_method,
                    'decimal_places' => $request->decimal_places,
                    'is_active' => true,
                    'is_default' => $isDefault,
                    'created_by' => $request->user()->id,
                ]);

                foreach ($request->components as $index => $comp) {
                    ScoreComponent::create([
                        'school_id' => $schoolId,
                        'scoring_configuration_id' => $config->id,
                        'name' => $comp['name'],
                        'max_marks' => $comp['max_marks'],
                        'display_order' => $index + 1,
                        'is_active' => true,
                        'is_required' => isset($comp['is_required']) && $comp['is_required'] == '1',
                        'created_by' => $request->user()->id,
                    ]);
                }
            });

            return redirect()->route('school.scoring-configs.index')->with('success', 'Scoring configuration created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to save configuration: ' . $e->getMessage()]);
        }
    }

    /**
     * Show configuration details.
     */
    public function show(Request $request, ScoringConfiguration $scoring_config)
    {
        $schoolId = $request->user()->school_id;

        if ($scoring_config->school_id !== $schoolId) {
            abort(403);
        }

        $scoring_config->load(['components', 'subject', 'academicYear']);

        return view('school.scoring_configs.show', [
            'scoringConfig' => $scoring_config
        ]);
    }

    /**
     * Show edit configuration page.
     */
    public function edit(Request $request, ScoringConfiguration $scoring_config)
    {
        $schoolId = $request->user()->school_id;

        if ($scoring_config->school_id !== $schoolId) {
            abort(403);
        }

        $scoring_config->load('components');
        $academicYears = AcademicYear::where('school_id', $schoolId)->get();
        $subjects = Subject::where('school_id', $schoolId)->get();

        return view('school.scoring_configs.edit', [
            'scoringConfig' => $scoring_config,
            'academicYears' => $academicYears,
            'subjects' => $subjects
        ]);
    }

    /**
     * Update configuration with component preservation.
     */
    public function update(Request $request, ScoringConfiguration $scoring_config)
    {
        $schoolId = $request->user()->school_id;

        if ($scoring_config->school_id !== $schoolId) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'level' => 'required|string|in:ALL,Nursery,KG,Primary,JHS,SHS,TVET,Tertiary',
            'subject_id' => 'nullable|exists:subjects,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'class_score_max' => 'required|numeric|min:1',
            'class_score_weight' => 'required|numeric|min:0|max:100',
            'exam_score_max' => 'required|numeric|min:1',
            'exam_score_weight' => 'required|numeric|min:0|max:100',
            'rounding_method' => 'required|string|in:ROUND,FLOOR,CEIL',
            'decimal_places' => 'required|integer|min:0|max:2',
            'is_default' => 'nullable|boolean',
            'components' => 'required|array|min:1',
            'components.*.id' => 'nullable|exists:score_components,id',
            'components.*.name' => 'required|string|max:100',
            'components.*.max_marks' => 'required|numeric|min:0.1',
            'components.*.is_required' => 'nullable',
        ]);

        $grandTotal = floatval($request->class_score_weight) + floatval($request->exam_score_weight);

        // Validation 1: Weight Sum
        if ($grandTotal <= 0) {
            return redirect()->back()->withInput()->withErrors(['class_score_weight' => 'Combined scaled weights must exceed 0.']);
        }

        // Validation 2: Sum of components <= class_score_max
        $componentsSum = 0;
        foreach ($request->components as $comp) {
            $componentsSum += floatval($comp['max_marks']);
        }

        if ($componentsSum > floatval($request->class_score_max)) {
            return redirect()->back()->withInput()->withErrors(['class_score_max' => "Validation Error: The components marks sum ({$componentsSum}) exceeds the Class SBA Score Max ({$request->class_score_max})."]);
        }

        try {
            DB::transaction(function () use ($request, $schoolId, $scoring_config, $grandTotal) {
                $isDefault = $request->has('is_default') && $request->is_default;

                if ($isDefault) {
                    // Turn off other defaults for this level
                    ScoringConfiguration::where('school_id', $schoolId)
                        ->where('level', $request->level)
                        ->where('id', '!=', $scoring_config->id)
                        ->update(['is_default' => false]);
                }

                $scoring_config->update([
                    'level' => $request->level,
                    'subject_id' => $request->subject_id,
                    'academic_year_id' => $request->academic_year_id,
                    'name' => $request->name,
                    'class_score_max' => $request->class_score_max,
                    'class_score_weight' => $request->class_score_weight,
                    'exam_score_max' => $request->exam_score_max,
                    'exam_score_weight' => $request->exam_score_weight,
                    'grand_total' => $grandTotal,
                    'rounding_method' => $request->rounding_method,
                    'decimal_places' => $request->decimal_places,
                    'is_default' => $isDefault,
                ]);

                // Maintain components
                $submittedIds = collect($request->components)->pluck('id')->filter()->toArray();
                
                // Delete components that are no longer in the request
                $scoring_config->components()->whereNotIn('id', $submittedIds)->delete();

                // Update or create components
                foreach ($request->components as $index => $comp) {
                    $isRequired = isset($comp['is_required']) && $comp['is_required'] == '1';
                    
                    if (!empty($comp['id'])) {
                        ScoreComponent::where('id', $comp['id'])
                            ->where('scoring_configuration_id', $scoring_config->id)
                            ->update([
                                'name' => $comp['name'],
                                'max_marks' => $comp['max_marks'],
                                'display_order' => $index + 1,
                                'is_required' => $isRequired,
                                'updated_by' => $request->user()->id,
                            ]);
                    } else {
                        ScoreComponent::create([
                            'school_id' => $schoolId,
                            'scoring_configuration_id' => $scoring_config->id,
                            'name' => $comp['name'],
                            'max_marks' => $comp['max_marks'],
                            'display_order' => $index + 1,
                            'is_active' => true,
                            'is_required' => $isRequired,
                            'created_by' => $request->user()->id,
                        ]);
                    }
                }
            });

            return redirect()->route('school.scoring-configs.index')->with('success', 'Scoring configuration updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update configuration: ' . $e->getMessage()]);
        }
    }

    /**
     * Destroy a configuration.
     */
    public function destroy(Request $request, ScoringConfiguration $scoring_config)
    {
        $schoolId = $request->user()->school_id;

        if ($scoring_config->school_id !== $schoolId) {
            abort(403);
        }

        $scoring_config->delete();

        return redirect()->route('school.scoring-configs.index')->with('success', 'Scoring configuration deleted successfully.');
    }
}
