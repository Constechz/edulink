<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\Term;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    /**
     * Display a listing of the fee structures.
     */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $feeStructures = FeeStructure::where('school_id', $schoolId)
            ->with(['campus', 'academicYear', 'term', 'class'])
            ->get();

        $campuses = Campus::where('school_id', $schoolId)->get();
        $academicYears = AcademicYear::where('school_id', $schoolId)->get();
        $terms = Term::where('school_id', $schoolId)->get();
        $classes = SchoolClass::where('school_id', $schoolId)->get();

        return view('school.fee_structures.index', compact('feeStructures', 'campuses', 'academicYears', 'terms', 'classes'));
    }

    /**
     * Store a newly created fee structure.
     */
    public function store(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'nullable|exists:terms,id',
            'class_id' => 'nullable|exists:classes,id',
            'campus_id' => 'nullable|exists:campuses,id',
            'is_mandatory' => 'nullable|boolean',
        ]);

        FeeStructure::create([
            'school_id' => $schoolId,
            'campus_id' => $request->campus_id,
            'academic_year_id' => $request->academic_year_id,
            'term_id' => $request->term_id,
            'class_id' => $request->class_id,
            'name' => $request->name,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'is_mandatory' => $request->has('is_mandatory') ? (bool) $request->is_mandatory : true,
        ]);

        return redirect()->back()->with('success', 'Fee structure item created successfully.');
    }

    /**
     * Update the specified fee structure.
     */
    public function update(Request $request, FeeStructure $feeStructure)
    {
        $schoolId = $request->user()->school_id;

        if ($feeStructure->school_id !== $schoolId) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'nullable|exists:terms,id',
            'class_id' => 'nullable|exists:classes,id',
            'campus_id' => 'nullable|exists:campuses,id',
            'is_mandatory' => 'nullable|boolean',
        ]);

        $feeStructure->update([
            'campus_id' => $request->campus_id,
            'academic_year_id' => $request->academic_year_id,
            'term_id' => $request->term_id,
            'class_id' => $request->class_id,
            'name' => $request->name,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'is_mandatory' => $request->has('is_mandatory') ? (bool) $request->is_mandatory : true,
        ]);

        return redirect()->back()->with('success', 'Fee structure item updated successfully.');
    }

    /**
     * Remove the specified fee structure from storage.
     */
    public function destroy(Request $request, FeeStructure $feeStructure)
    {
        $schoolId = $request->user()->school_id;

        if ($feeStructure->school_id !== $schoolId) {
            abort(403);
        }

        $feeStructure->delete();

        return redirect()->back()->with('success', 'Fee structure item deleted successfully.');
    }
}
