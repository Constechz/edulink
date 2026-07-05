<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\Term;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    /**
     * View weekly timetable dashboard.
     */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $classes = SchoolClass::where('school_id', $schoolId)->get();
        $streams = Stream::where('school_id', $schoolId)->get();
        $subjects = Subject::where('school_id', $schoolId)->get();
        
        $teachers = User::where('school_id', $schoolId)
            ->whereHas('role', function ($query) {
                $query->whereIn('slug', ['teacher', 'hod', 'headteacher', 'school-admin']);
            })->get();

        $selectedClassId = $request->get('class_id') ?: ($classes->first()->id ?? null);
        $selectedStreamId = $request->get('stream_id') ?: null;

        // Fetch slots
        $slotsQuery = Timetable::where('school_id', $schoolId)
            ->with(['subject', 'teacher', 'class', 'stream']);

        if ($selectedClassId) {
            $slotsQuery->where('class_id', $selectedClassId);
            if ($selectedStreamId) {
                $slotsQuery->where('stream_id', $selectedStreamId);
            }
        }

        $slots = $slotsQuery->get();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        return view('school.timetable', compact(
            'classes',
            'streams',
            'subjects',
            'teachers',
            'slots',
            'selectedClassId',
            'selectedStreamId',
            'days'
        ));
    }

    /**
     * Store new timetable slot with clash detection.
     */
    public function store(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'stream_id' => 'nullable|exists:streams,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:100',
        ]);

        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_current', true)->first()
            ?: AcademicYear::where('school_id', $schoolId)->first();

        if (!$activeYear) {
            return redirect()->back()->withErrors(['error' => 'Please set up and activate an Academic Year context first.']);
        }

        $activeTerm = Term::where('school_id', $schoolId)->where('is_current', true)->first();

        $dayOfWeek = $request->day_of_week;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $teacherId = $request->teacher_id;
        $classId = $request->class_id;
        $streamId = $request->stream_id;

        // 1. Teacher Clash Detection
        $teacherClash = Timetable::where('school_id', $schoolId)
            ->where('day_of_week', $dayOfWeek)
            ->where('teacher_id', $teacherId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })->exists();

        if ($teacherClash) {
            return redirect()->back()->withErrors(['teacher_id' => 'Scheduling Conflict: This teacher is already allocated to another class at this time block on ' . $dayOfWeek . '.']);
        }

        // 2. Class/Stream Overlap Detection
        $classClash = Timetable::where('school_id', $schoolId)
            ->where('day_of_week', $dayOfWeek)
            ->where('class_id', $classId)
            ->where('stream_id', $streamId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })->exists();

        if ($classClash) {
            return redirect()->back()->withErrors(['class_id' => 'Scheduling Conflict: This class/stream already has another subject allocated at this time block on ' . $dayOfWeek . '.']);
        }

        // Create Slot
        Timetable::create([
            'school_id' => $schoolId,
            'campus_id' => $request->user()->campus_id,
            'class_id' => $classId,
            'stream_id' => $streamId,
            'subject_id' => $request->subject_id,
            'teacher_id' => $teacherId,
            'day_of_week' => $dayOfWeek,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'room' => $request->room,
            'academic_year_id' => $activeYear->id,
            'term_id' => $activeTerm ? $activeTerm->id : null,
        ]);

        return redirect()->back()->with('success', 'Timetable slot scheduled successfully.');
    }

    /**
     * Delete a scheduled slot.
     */
    public function destroy(Request $request, Timetable $timetable)
    {
        $schoolId = $request->user()->school_id;

        if ($timetable->school_id !== $schoolId) {
            abort(403);
        }

        $timetable->delete();

        return redirect()->back()->with('success', 'Scheduled slot removed successfully.');
    }
}
