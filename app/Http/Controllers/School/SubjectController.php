<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Department;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    /**
     * Display a listing of subjects and allocations.
     */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $subjects = Subject::where('school_id', $schoolId)->with('department')->get();
        $departments = Department::where('school_id', $schoolId)->get();
        $classes = SchoolClass::where('school_id', $schoolId)->with('streams')->get();
        $streams = Stream::where('school_id', $schoolId)->get();
        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_current', true)->first() 
            ?: AcademicYear::where('school_id', $schoolId)->first();
        $activeTerm = Term::where('school_id', $schoolId)->where('is_current', true)->first();

        // Get all ClassSubject allocations
        $allocations = ClassSubject::where('school_id', $schoolId)
            ->with(['class', 'stream', 'subject', 'teacher'])
            ->get();

        $teachers = User::where('school_id', $schoolId)
            ->whereHas('role', function ($query) {
                $query->whereIn('slug', ['teacher', 'hod', 'headteacher', 'school-admin']);
            })->get();

        return view('school.subjects', compact(
            'subjects',
            'departments',
            'classes',
            'streams',
            'allocations',
            'teachers',
            'activeYear',
            'activeTerm'
        ));
    }

    /**
     * Store a newly created subject in storage.
     */
    public function store(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'level' => 'required|string|max:100',
            'type' => 'required|string|in:core,elective',
        ]);

        Subject::create([
            'school_id' => $schoolId,
            'department_id' => $request->department_id,
            'name' => $request->name,
            'code' => $request->code,
            'level' => $request->level,
            'is_core' => $request->type === 'core',
            'is_elective' => $request->type === 'elective',
        ]);

        return redirect()->back()->with('success', 'Subject created successfully.');
    }

    /**
     * Allocate a teacher to a class/stream and subject.
     */
    public function allocateTeacher(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'stream_id' => 'nullable|exists:streams,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'periods_per_week' => 'nullable|integer|min:1|max:40',
        ]);

        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_current', true)->first() 
            ?: AcademicYear::where('school_id', $schoolId)->first();
        
        $activeTerm = Term::where('school_id', $schoolId)->where('is_current', true)->first();

        if (!$activeYear) {
            return redirect()->back()->withErrors(['error' => 'You must configure and activate an Academic Year before allocating subjects.']);
        }

        // Create or update class subject allocation
        ClassSubject::updateOrCreate(
            [
                'school_id' => $schoolId,
                'class_id' => $request->class_id,
                'stream_id' => $request->stream_id,
                'subject_id' => $request->subject_id,
                'academic_year_id' => $activeYear->id,
            ],
            [
                'teacher_id' => $request->teacher_id,
                'term_id' => $activeTerm ? $activeTerm->id : null,
                'periods_per_week' => $request->periods_per_week ?: 4,
            ]
        );

        return redirect()->back()->with('success', 'Teacher allocated to subject successfully.');
    }

    /**
     * Update the specified subject in storage.
     */
    public function update(Request $request, $id)
    {
        $schoolId = $request->user()->school_id;
        $subject = Subject::where('school_id', $schoolId)->findOrFail($id);

        $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'level' => 'required|string|max:100',
            'type' => 'required|string|in:core,elective',
        ]);

        $subject->update([
            'department_id' => $request->department_id,
            'name' => $request->name,
            'code' => $request->code,
            'level' => $request->level,
            'is_core' => $request->type === 'core',
            'is_elective' => $request->type === 'elective',
        ]);

        return redirect()->back()->with('success', 'Subject updated successfully.');
    }

    /**
     * Remove the specified subject from storage.
     */
    public function destroy(Request $request, $id)
    {
        $schoolId = $request->user()->school_id;
        $subject = Subject::where('school_id', $schoolId)->findOrFail($id);
        $subject->delete();

        return redirect()->back()->with('success', 'Subject deleted successfully.');
    }

    /**
     * Update the specified class subject allocation.
     */
    public function updateAllocation(Request $request, $id)
    {
        $schoolId = $request->user()->school_id;
        $allocation = ClassSubject::where('school_id', $schoolId)->findOrFail($id);

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'stream_id' => 'nullable|exists:streams,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'periods_per_week' => 'nullable|integer|min:1|max:40',
        ]);

        $allocation->update([
            'class_id' => $request->class_id,
            'stream_id' => $request->stream_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'periods_per_week' => $request->periods_per_week ?: 4,
        ]);

        return redirect()->back()->with('success', 'Teacher allocation updated successfully.');
    }

    /**
     * Remove the specified class subject allocation.
     */
    public function destroyAllocation(Request $request, $id)
    {
        $schoolId = $request->user()->school_id;
        $allocation = ClassSubject::where('school_id', $schoolId)->findOrFail($id);
        $allocation->delete();

        return redirect()->back()->with('success', 'Teacher allocation deleted successfully.');
    }
}
