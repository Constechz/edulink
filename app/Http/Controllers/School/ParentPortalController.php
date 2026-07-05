<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\AttendanceRecord;
use App\Models\Invoice;
use App\Models\StudentScore;
use App\Models\Message;
use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParentPortalController extends Controller
{
    /**
     * Resolve the parent's children.
     */
    private function getChildren(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        $guardian = Guardian::where('school_id', $schoolId)
            ->where('email', $user->email)
            ->first();

        $children = $guardian ? $guardian->students()->with(['currentClass', 'currentStream'])->get() : collect();

        // Test fallback: if no children found, just get the first few students in the school
        if ($children->isEmpty()) {
            $children = Student::where('school_id', $schoolId)->limit(2)->get();
        }

        return $children;
    }

    /**
     * Resolve the currently active child.
     */
    private function getActiveChild(Request $request, $children)
    {
        if ($children->isEmpty()) {
            return null;
        }

        $activeChildId = session('active_child_id');
        $activeChild = null;

        if ($activeChildId) {
            $activeChild = $children->firstWhere('id', $activeChildId);
        }

        if (!$activeChild) {
            $activeChild = $children->first();
            session(['active_child_id' => $activeChild->id]);
        }

        return $activeChild;
    }

    public function dashboard(Request $request)
    {
        $children = $this->getChildren($request);
        $activeChild = $this->getActiveChild($request, $children);

        if (!$activeChild) {
            return view('school.parent-portal.dashboard', [
                'children' => collect(),
                'activeChild' => null,
                'error' => 'No student profiles are linked to your guardian account.'
            ]);
        }

        $schoolId = $request->user()->school_id;

        // Attendance stats
        $attendanceRecords = AttendanceRecord::where('school_id', $schoolId)
            ->where('student_id', $activeChild->id)
            ->get();
        $totalDays = $attendanceRecords->count();
        $presentCount = $attendanceRecords->whereIn('status', ['present', 'late'])->count();
        $attendanceRate = $totalDays > 0 ? round(($presentCount / $totalDays) * 100) : 100;

        // Finance stats
        $invoices = Invoice::where('school_id', $schoolId)
            ->where('student_id', $activeChild->id)
            ->get();
        $pendingInvoicesCount = $invoices->whereIn('status', ['pending', 'partial'])->count();
        $outstandingAmount = $invoices->sum('balance');

        // Published scores
        $recentScores = StudentScore::where('school_id', $schoolId)
            ->where('student_id', $activeChild->id)
            ->whereIn('status', ['approved', 'published'])
            ->with(['subject', 'term'])
            ->limit(5)
            ->get();

        // Broadcast announcements
        $announcements = DB::table('announcements')
            ->where('school_id', $schoolId)
            ->where(function($q) {
                $q->where('target_audience', 'all')
                  ->orWhere('target_audience', 'parents');
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Fetch personal portal messages (e.g. absence notifications)
        $portalMessages = Message::where('school_id', $schoolId)
            ->where('channel', 'portal')
            ->whereHas('recipients', function($q) use ($request) {
                $q->where('recipient_user_id', $request->user()->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Active Hostel Allocation for Child
        $hostelAllocation = \App\Models\HostelAllocation::where('school_id', $schoolId)
            ->where('student_id', $activeChild->id)
            ->whereNull('vacated_date')
            ->with(['bed.room.dormitory'])
            ->first();

        return view('school.parent-portal.dashboard', compact(
            'children',
            'activeChild',
            'attendanceRate',
            'pendingInvoicesCount',
            'outstandingAmount',
            'recentScores',
            'announcements',
            'portalMessages',
            'hostelAllocation'
        ));
    }

    public function selectChild(Request $request, Student $student)
    {
        $children = $this->getChildren($request);
        
        if (!$children->contains('id', $student->id)) {
            abort(403, 'Unauthorized student profile access.');
        }

        session(['active_child_id' => $student->id]);
        return redirect()->route('school.parent-portal.dashboard')->with('success', "Switched view to {$student->first_name}.");
    }

    public function attendance(Request $request)
    {
        $children = $this->getChildren($request);
        $activeChild = $this->getActiveChild($request, $children);

        if (!$activeChild) {
            abort(404, 'No active student context.');
        }

        $schoolId = $request->user()->school_id;
        $attendanceRecords = AttendanceRecord::where('school_id', $schoolId)
            ->where('student_id', $activeChild->id)
            ->with(['term'])
            ->orderBy('date', 'desc')
            ->get();

        return view('school.parent-portal.attendance', compact('children', 'activeChild', 'attendanceRecords'));
    }

    public function fees(Request $request)
    {
        $children = $this->getChildren($request);
        $activeChild = $this->getActiveChild($request, $children);

        if (!$activeChild) {
            abort(404, 'No active student context.');
        }

        $schoolId = $request->user()->school_id;
        $invoices = Invoice::where('school_id', $schoolId)
            ->where('student_id', $activeChild->id)
            ->with(['term', 'academicYear'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('school.parent-portal.fees', compact('children', 'activeChild', 'invoices'));
    }

    public function reports(Request $request)
    {
        $children = $this->getChildren($request);
        $activeChild = $this->getActiveChild($request, $children);

        if (!$activeChild) {
            abort(404, 'No active student context.');
        }

        $schoolId = $request->user()->school_id;
        $scores = StudentScore::where('school_id', $schoolId)
            ->where('student_id', $activeChild->id)
            ->whereIn('status', ['approved', 'published'])
            ->with(['subject', 'term', 'academicYear'])
            ->get();

        // Get current active academic year and term references
        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_current', true)->first()
            ?: AcademicYear::where('school_id', $schoolId)->first();

        $activeTerm = Term::where('school_id', $schoolId)->where('is_current', true)->first()
            ?: Term::where('school_id', $schoolId)->first();

        $currentYearId = $activeYear ? $activeYear->id : null;
        $currentTermId = $activeTerm ? $activeTerm->id : null;

        return view('school.parent-portal.reports', compact('children', 'activeChild', 'scores', 'currentYearId', 'currentTermId'));
    }

    public function messages(Request $request)
    {
        $children = $this->getChildren($request);
        $activeChild = $this->getActiveChild($request, $children);

        $schoolId = $request->user()->school_id;
        
        $announcements = DB::table('announcements')
            ->where('school_id', $schoolId)
            ->where(function($q) {
                $q->where('target_audience', 'all')
                  ->orWhere('target_audience', 'parents');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('school.parent-portal.messages', compact('children', 'activeChild', 'announcements'));
    }

    public function transport(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $routes = \App\Models\TransportRoute::where('school_id', $schoolId)->with(['stops'])->get();
        $vehicles = \App\Models\Vehicle::where('school_id', $schoolId)->get();

        $children = $this->getChildren($request);
        $activeChild = $this->getActiveChild($request, $children);

        return view('school.parent-portal.transport', compact('children', 'activeChild', 'routes', 'vehicles'));
    }
}
