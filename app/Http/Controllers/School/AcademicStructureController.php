<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Programme;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Term;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicStructureController extends Controller
{
    /**
     * Display unified academic structure management dashboard.
     */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $academicYears = AcademicYear::where('school_id', $schoolId)->with('terms')->get();
        $terms = Term::where('school_id', $schoolId)->with('academicYear')->get();
        $programmes = Programme::where('school_id', $schoolId)->with('department')->get();
        $classes = SchoolClass::where('school_id', $schoolId)->with(['campus', 'academicYear', 'programme', 'classTeacher', 'streams'])->get();
        $streams = Stream::where('school_id', $schoolId)->with(['class', 'classTeacher'])->get();

        $campuses = Campus::where('school_id', $schoolId)->get();
        $departments = Department::where('school_id', $schoolId)->with(['hod', 'programmes'])->get();
        
        // Fetch users who are staff/teachers to assign as class teachers
        $teachers = User::where('school_id', $schoolId)
            ->whereHas('role', function ($query) {
                $query->whereIn('slug', ['teacher', 'hod', 'headteacher', 'school-admin']);
            })->get();

        return view('school.academics', compact(
            'academicYears',
            'terms',
            'programmes',
            'classes',
            'streams',
            'campuses',
            'departments',
            'teachers'
        ));
    }

    /**
     * Store new Academic Year.
     */
    public function storeYear(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'nullable|boolean',
        ]);

        $isCurrent = $request->has('is_current');

        DB::transaction(function () use ($request, $schoolId, $isCurrent) {
            if ($isCurrent) {
                AcademicYear::where('school_id', $schoolId)->update(['is_current' => false]);
            }

            AcademicYear::create([
                'school_id' => $schoolId,
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_current' => $isCurrent,
                'created_by' => $request->user()->id,
            ]);
        });

        return redirect()->back()->with('success', 'Academic Year created successfully.');
    }

    /**
     * Store new Term.
     */
    public function storeTerm(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'reopening_date' => 'nullable|date',
            'is_current' => 'nullable|boolean',
        ]);

        $isCurrent = $request->has('is_current');

        DB::transaction(function () use ($request, $schoolId, $isCurrent) {
            if ($isCurrent) {
                Term::where('school_id', $schoolId)->update(['is_current' => false]);
            }

            Term::create([
                'school_id' => $schoolId,
                'academic_year_id' => $request->academic_year_id,
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'reopening_date' => $request->reopening_date,
                'is_current' => $isCurrent,
            ]);
        });

        return redirect()->back()->with('success', 'Term configured successfully.');
    }

    /**
     * Store new Programme.
     */
    public function storeProgramme(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'duration_years' => 'required|integer|min:1',
            'level' => 'required|string|max:100',
        ]);

        Programme::create([
            'school_id' => $schoolId,
            'department_id' => $request->department_id,
            'name' => $request->name,
            'code' => $request->code,
            'duration_years' => $request->duration_years,
            'level' => $request->level,
        ]);

        return redirect()->back()->with('success', 'Academic Programme added successfully.');
    }

    /**
     * Store new Class.
     */
    public function storeClass(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'campus_id' => 'nullable|exists:campuses,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'programme_id' => 'required|exists:programmes,id',
            'name' => 'required|string|max:255',
            'level' => 'required|string|max:50',
            'class_teacher_id' => 'nullable|exists:users,id',
            'capacity' => 'required|integer|min:1',
        ]);

        SchoolClass::create([
            'school_id' => $schoolId,
            'campus_id' => $request->campus_id,
            'academic_year_id' => $request->academic_year_id,
            'programme_id' => $request->programme_id,
            'name' => $request->name,
            'level' => $request->level,
            'class_teacher_id' => $request->class_teacher_id,
            'capacity' => $request->capacity,
        ]);

        return redirect()->back()->with('success', 'Class created successfully.');
    }

    /**
     * Update Class.
     */
    public function updateClass(Request $request, $id)
    {
        $schoolId = $request->user()->school_id;
        $class = SchoolClass::where('school_id', $schoolId)->findOrFail($id);

        $request->validate([
            'campus_id' => 'nullable|exists:campuses,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'programme_id' => 'required|exists:programmes,id',
            'name' => 'required|string|max:255',
            'level' => 'required|string|max:50',
            'class_teacher_id' => 'nullable|exists:users,id',
            'capacity' => 'required|integer|min:1',
        ]);

        $class->update([
            'campus_id' => $request->campus_id,
            'academic_year_id' => $request->academic_year_id,
            'programme_id' => $request->programme_id,
            'name' => $request->name,
            'level' => $request->level,
            'class_teacher_id' => $request->class_teacher_id,
            'capacity' => $request->capacity,
        ]);

        return redirect()->back()->with('success', 'Class updated successfully.');
    }

    /**
     * Store new Stream.
     */
    public function storeStream(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'name' => 'required|string|max:255',
            'class_teacher_id' => 'nullable|exists:users,id',
            'capacity' => 'required|integer|min:1',
        ]);

        Stream::create([
            'school_id' => $schoolId,
            'class_id' => $request->class_id,
            'name' => $request->name,
            'class_teacher_id' => $request->class_teacher_id,
            'capacity' => $request->capacity,
        ]);

        return redirect()->back()->with('success', 'Class Stream created successfully.');
    }

    /**
     * Update Academic Year.
     */
    public function updateYear(Request $request, $id)
    {
        $schoolId = $request->user()->school_id;
        $year = AcademicYear::where('school_id', $schoolId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'nullable|boolean',
        ]);

        $isCurrent = $request->has('is_current');

        DB::transaction(function () use ($request, $schoolId, $year, $isCurrent) {
            if ($isCurrent) {
                AcademicYear::where('school_id', $schoolId)->update(['is_current' => false]);
            }

            $year->update([
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_current' => $isCurrent,
            ]);
        });

        return redirect()->back()->with('success', 'Academic Year updated successfully.');
    }

    /**
     * Update Term.
     */
    public function updateTerm(Request $request, $id)
    {
        $schoolId = $request->user()->school_id;
        $term = Term::where('school_id', $schoolId)->findOrFail($id);

        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'reopening_date' => 'nullable|date',
            'is_current' => 'nullable|boolean',
        ]);

        $isCurrent = $request->has('is_current');

        DB::transaction(function () use ($request, $schoolId, $term, $isCurrent) {
            if ($isCurrent) {
                Term::where('school_id', $schoolId)->update(['is_current' => false]);
            }

            $term->update([
                'academic_year_id' => $request->academic_year_id,
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'reopening_date' => $request->reopening_date,
                'is_current' => $isCurrent,
            ]);
        });

        return redirect()->back()->with('success', 'Term updated successfully.');
    }

    /**
     * Update Programme.
     */
    public function updateProgramme(Request $request, $id)
    {
        $schoolId = $request->user()->school_id;
        $programme = Programme::where('school_id', $schoolId)->findOrFail($id);

        $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'duration_years' => 'required|integer|min:1',
            'level' => 'required|string|max:100',
        ]);

        $programme->update([
            'department_id' => $request->department_id,
            'name' => $request->name,
            'code' => $request->code,
            'duration_years' => $request->duration_years,
            'level' => $request->level,
        ]);

        return redirect()->back()->with('success', 'Academic Programme updated successfully.');
    }

    /**
     * Update Stream.
     */
    public function updateStream(Request $request, $id)
    {
        $schoolId = $request->user()->school_id;
        $stream = Stream::where('school_id', $schoolId)->findOrFail($id);

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'name' => 'required|string|max:255',
            'class_teacher_id' => 'nullable|exists:users,id',
            'capacity' => 'required|integer|min:1',
        ]);

        $stream->update([
            'class_id' => $request->class_id,
            'name' => $request->name,
            'class_teacher_id' => $request->class_teacher_id,
            'capacity' => $request->capacity,
        ]);

        return redirect()->back()->with('success', 'Class Stream updated successfully.');
    }

    /**
     * Store new Department.
     */
    public function storeDepartment(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'hod_user_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string|max:1000',
        ]);

        Department::create([
            'school_id' => $schoolId,
            'name' => $request->name,
            'code' => $request->code,
            'hod_user_id' => $request->hod_user_id,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Department created successfully.');
    }

    /**
     * Update Department.
     */
    public function updateDepartment(Request $request, $id)
    {
        $schoolId = $request->user()->school_id;
        $dept = Department::where('school_id', $schoolId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'hod_user_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string|max:1000',
        ]);

        $dept->update([
            'name' => $request->name,
            'code' => $request->code,
            'hod_user_id' => $request->hod_user_id,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Department updated successfully.');
    }
}
