<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\PromotionConfiguration;
use Illuminate\Http\Request;

class PromotionRuleController extends Controller
{
    /**
     * Display listing of promotion rules.
     */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $configs = PromotionConfiguration::where('school_id', $schoolId)
            ->with('class')
            ->get();

        $classes = SchoolClass::where('school_id', $schoolId)->get();

        return view('school.promotion_rules.index', compact('configs', 'classes'));
    }

    /**
     * Store new promotion rule configuration.
     */
    public function store(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'level' => 'required|string|in:nursery,kg,primary,jhs,shs,tertiary',
            'class_id' => 'nullable|exists:classes,id',
            'method' => 'required|string|in:annual_average,two_of_three,subject_pass_count',
            'term_weights_term1' => 'required|numeric|min:0',
            'term_weights_term2' => 'required|numeric|min:0',
            'term_weights_term3' => 'required|numeric|min:0',
            'promotion_threshold' => 'required|numeric|min:0|max:100',
            'conditional_threshold' => 'nullable|numeric|min:0|max:100',
            'min_subjects_to_pass' => 'nullable|integer|min:1',
            'per_subject_pass_mark' => 'nullable|numeric|min:0|max:100',
            'repeat_limit' => 'required|integer|min:1',
        ]);

        $termWeights = [
            'term1' => floatval($request->term_weights_term1),
            'term2' => floatval($request->term_weights_term2),
            'term3' => floatval($request->term_weights_term3),
        ];

        // Ensure unique rule for school, level and class
        $existing = PromotionConfiguration::where('school_id', $schoolId)
            ->where('level', $request->level)
            ->where('class_id', $request->class_id)
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->withErrors([
                'level' => 'A configuration for this level/class already exists. Please edit it instead.'
            ]);
        }

        PromotionConfiguration::create([
            'school_id' => $schoolId,
            'level' => $request->level,
            'class_id' => $request->class_id,
            'method' => $request->method,
            'term_weights_json' => $termWeights,
            'promotion_threshold' => $request->promotion_threshold,
            'conditional_threshold' => $request->conditional_threshold,
            'min_subjects_to_pass' => $request->min_subjects_to_pass,
            'per_subject_pass_mark' => $request->per_subject_pass_mark,
            'repeat_limit' => $request->repeat_limit,
            'exclude_terminal_year' => $request->has('exclude_terminal_year') ? true : false,
            'is_active' => true,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('school.settings.promotions.index')->with('success', 'Promotion rule configuration created successfully.');
    }

    /**
     * Update existing promotion configuration.
     */
    public function update(Request $request, $id)
    {
        $schoolId = $request->user()->school_id;
        $config = PromotionConfiguration::where('school_id', $schoolId)->findOrFail($id);

        $request->validate([
            'method' => 'required|string|in:annual_average,two_of_three,subject_pass_count',
            'term_weights_term1' => 'required|numeric|min:0',
            'term_weights_term2' => 'required|numeric|min:0',
            'term_weights_term3' => 'required|numeric|min:0',
            'promotion_threshold' => 'required|numeric|min:0|max:100',
            'conditional_threshold' => 'nullable|numeric|min:0|max:100',
            'min_subjects_to_pass' => 'nullable|integer|min:1',
            'per_subject_pass_mark' => 'nullable|numeric|min:0|max:100',
            'repeat_limit' => 'required|integer|min:1',
        ]);

        $termWeights = [
            'term1' => floatval($request->term_weights_term1),
            'term2' => floatval($request->term_weights_term2),
            'term3' => floatval($request->term_weights_term3),
        ];

        $config->update([
            'method' => $request->method,
            'term_weights_json' => $termWeights,
            'promotion_threshold' => $request->promotion_threshold,
            'conditional_threshold' => $request->conditional_threshold,
            'min_subjects_to_pass' => $request->min_subjects_to_pass,
            'per_subject_pass_mark' => $request->per_subject_pass_mark,
            'repeat_limit' => $request->repeat_limit,
            'exclude_terminal_year' => $request->has('exclude_terminal_year') ? true : false,
        ]);

        return redirect()->route('school.settings.promotions.index')->with('success', 'Promotion rule configuration updated successfully.');
    }

    /**
     * Destroy a configuration.
     */
    public function destroy(Request $request, $id)
    {
        $schoolId = $request->user()->school_id;
        $config = PromotionConfiguration::where('school_id', $schoolId)->findOrFail($id);
        $config->delete();

        return redirect()->route('school.settings.promotions.index')->with('success', 'Promotion rule configuration deleted successfully.');
    }
}
