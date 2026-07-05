<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Term;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * Display attendance registration grid.
     */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $user = $request->user();

        if ($user->role && $user->role->slug === 'class-teacher') {
            $assignedStreams = Stream::where('class_teacher_id', $user->id)->get();
            $assignedStreamIds = $assignedStreams->pluck('id')->toArray();
            $assignedClassIds = $assignedStreams->pluck('class_id')->toArray();

            $classes = SchoolClass::where('school_id', $schoolId)->whereIn('id', $assignedClassIds)->get();
            $streams = Stream::where('school_id', $schoolId)->whereIn('id', $assignedStreamIds)->get();

            $selectedClassId = $request->get('class_id');
            $selectedStreamId = $request->get('stream_id');

            // Default to first assigned class and stream context if not set or invalid
            if (!$selectedClassId || !in_array($selectedClassId, $assignedClassIds)) {
                $selectedClassId = $classes->first()->id ?? null;
            }
            if (!$selectedStreamId || !in_array($selectedStreamId, $assignedStreamIds)) {
                $selectedStreamId = $streams->where('class_id', $selectedClassId)->first()->id ?? null;
            }
        } else {
            $classes = SchoolClass::where('school_id', $schoolId)->get();
            $streams = Stream::where('school_id', $schoolId)->get();

            $selectedClassId = $request->get('class_id') ?: ($classes->first()->id ?? null);
            $selectedStreamId = $request->get('stream_id') ?: null;
        }

        $selectedDate = $request->get('date') ?: date('Y-m-d');

        $students = [];
        $records = [];

        if ($selectedClassId) {
            $studentsQuery = Student::where('school_id', $schoolId)
                ->where('current_class_id', $selectedClassId);

            if ($selectedStreamId) {
                $studentsQuery->where('current_stream_id', $selectedStreamId);
            }

            $students = $studentsQuery->get();

            // Fetch existing attendance records for the date
            $records = AttendanceRecord::where('school_id', $schoolId)
                ->where('class_id', $selectedClassId)
                ->whereDate('date', $selectedDate)
                ->get()
                ->pluck('status', 'student_id')
                ->toArray();
        }

        return view('school.attendance', compact(
            'classes',
            'streams',
            'students',
            'records',
            'selectedClassId',
            'selectedStreamId',
            'selectedDate'
        ));
    }

    /**
     * Submit manual daily attendance.
     */
    public function store(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'stream_id' => 'nullable|exists:streams,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'required|string|in:present,absent,late,excused',
        ]);

        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_current', true)->first()
            ?: AcademicYear::where('school_id', $schoolId)->first();

        $activeTerm = Term::where('school_id', $schoolId)->where('is_current', true)->first()
            ?: Term::where('school_id', $schoolId)->first();

        if (!$activeYear || !$activeTerm) {
            return redirect()->back()->withErrors(['error' => 'Please set up active Academic Year and Term first.']);
        }

        try {
            DB::transaction(function () use ($request, $schoolId, $activeYear, $activeTerm) {
                foreach ($request->attendance as $studentId => $status) {
                    $student = Student::find($studentId);
                    if (!$student || $student->school_id !== $schoolId) continue;

                    // Update or create attendance
                    AttendanceRecord::updateOrCreate(
                        [
                            'school_id' => $schoolId,
                            'student_id' => $studentId,
                            'date' => $request->date,
                            'term_id' => $activeTerm->id,
                        ],
                        [
                            'campus_id' => $student->campus_id,
                            'class_id' => $request->class_id,
                            'stream_id' => $request->stream_id ?: $student->current_stream_id,
                            'academic_year_id' => $activeYear->id,
                            'status' => $status,
                            'recorded_by' => $request->user()->id,
                            'method' => 'manual',
                        ]
                    );

                    // If student is absent, dispatch Portal Notification & Email alert to parent
                    if ($status === 'absent') {
                        $parent = $student->guardians()->where('student_guardians.is_primary', true)->first();
                        if ($parent) {
                            $subject = 'Absence Alert: ' . $student->first_name . ' ' . $student->last_name;
                            $body = 'Dear Parent, your ward ' . $student->first_name . ' ' . $student->last_name . ' was marked ABSENT today, ' . date('d M Y', strtotime($request->date)) . '.';
                            
                            // 1. Create Portal Notification message log
                            $message = Message::create([
                                'school_id' => $schoolId,
                                'sender_user_id' => $request->user()->id,
                                'channel' => 'portal',
                                'subject' => $subject,
                                'body' => $body,
                                'status' => 'sent'
                            ]);

                            // 2. Map recipient user profile if parent login account exists
                            if (!empty($parent->email)) {
                                $parentUser = User::where('school_id', $schoolId)->where('email', $parent->email)->first();
                                if ($parentUser) {
                                    MessageRecipient::create([
                                        'message_id' => $message->id,
                                        'recipient_user_id' => $parentUser->id,
                                        'recipient_phone' => $parent->phone ?? '+233240000000',
                                        'recipient_email' => $parent->email,
                                        'status' => 'sent'
                                    ]);
                                }

                                // 3. Dispatch Email Alert
                                try {
                                    $school = School::find($schoolId);
                                    \Illuminate\Support\Facades\Mail::to($parent->email)->send(
                                        new \App\Mail\SchoolNoticeMail($subject, $body, $school)
                                    );
                                    Log::info("Absence Email alert dispatched successfully to {$parent->email}.");
                                } catch (\Exception $e) {
                                    Log::error("Failed to send absence email to {$parent->email}: " . $e->getMessage());
                                    \App\Models\EmailLog::create([
                                        'recipient_email' => $parent->email,
                                        'subject' => $subject,
                                        'body' => $body,
                                        'status' => 'failed',
                                        'error_message' => $e->getMessage(),
                                    ]);
                                }
                            }
                        }
                    }
                }
            });

            return redirect()->back()->with('success', 'Attendance sheets logged successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to save attendance sheets: ' . $e->getMessage()]);
        }
    }

    /**
     * QR Code Check-in Kiosk view.
     */
    public function qrKiosk(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $classes = SchoolClass::where('school_id', $schoolId)->get();
        return view('school.qr_kiosk', compact('classes'));
    }

    /**
     * Process student QR code scanning check-in.
     */
    public function qrCheckIn(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'student_id_number' => 'required|string|max:100',
        ]);

        $student = Student::where('school_id', $schoolId)
            ->where('student_id_number', $request->student_id_number)
            ->first();

        if (!$student) {
            return response()->json(['error' => 'Student ID not registered in this school.'], 404);
        }

        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_current', true)->first()
            ?: AcademicYear::where('school_id', $schoolId)->first();

        $activeTerm = Term::where('school_id', $schoolId)->where('is_current', true)->first()
            ?: Term::where('school_id', $schoolId)->first();

        if (!$activeYear || !$activeTerm) {
            return response()->json(['error' => 'Active calendar context missing.'], 422);
        }

        $date = date('Y-m-d');
        $time = date('H:i:s');

        // Fetch school dynamic attendance cutoff time setting
        $school = \App\Models\School::find($schoolId);
        $cutoff = ($school && isset($school->settings['attendance_cutoff_time'])) 
            ? $school->settings['attendance_cutoff_time'] 
            : '08:30:00';

        // Check if student arrived late
        $status = 'present';
        $lateMinutes = 0;
        if ($time > $cutoff) {
            $status = 'late';
            $lateMinutes = round((strtotime($time) - strtotime($cutoff)) / 60);
        }

        $record = AttendanceRecord::updateOrCreate(
            [
                'school_id' => $schoolId,
                'student_id' => $student->id,
                'date' => $date,
                'term_id' => $activeTerm->id,
            ],
            [
                'campus_id' => $student->campus_id,
                'class_id' => $student->current_class_id,
                'stream_id' => $student->current_stream_id,
                'academic_year_id' => $activeYear->id,
                'status' => $status,
                'arrival_time' => $time,
                'method' => 'qr',
                'recorded_by' => $request->user()->id,
                'late_minutes' => $lateMinutes,
            ]
        );

        return response()->json([
            'success' => true,
            'student_name' => $student->first_name . ' ' . $student->last_name,
            'time' => date('h:i A', strtotime($time)),
            'status' => ucfirst($status),
            'late_minutes' => $lateMinutes,
        ]);
    }

    /**
     * Viewing attendance statistical report summaries.
     */
    public function reports(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $classes = SchoolClass::where('school_id', $schoolId)->get();
        
        $selectedClassId = $request->get('class_id');
        $selectedDate = $request->get('date') ?: date('Y-m-d');

        $query = AttendanceRecord::where('school_id', $schoolId)->whereDate('date', $selectedDate);

        if ($selectedClassId) {
            $query->where('class_id', $selectedClassId);
        }

        $records = $query->with(['student', 'class'])->get();

        $stats = [
            'present' => $records->where('status', 'present')->count(),
            'absent' => $records->where('status', 'absent')->count(),
            'late' => $records->where('status', 'late')->count(),
            'excused' => $records->where('status', 'excused')->count(),
        ];

        return view('school.attendance_reports', compact('classes', 'records', 'stats', 'selectedClassId', 'selectedDate'));
    }
}
