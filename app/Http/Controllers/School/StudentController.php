<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Guardian;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Services\Subscription\SubscriptionLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    protected $limitService;

    public function __construct(SubscriptionLimitService $limitService)
    {
        $this->limitService = $limitService;
    }

    /**
     * List all students.
     */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $user = $request->user();

        if ($user->role && $user->role->slug === 'class-teacher') {
            $assignedStreams = Stream::where('class_teacher_id', $user->id)->get();
            $assignedStreamIds = $assignedStreams->pluck('id')->toArray();
            $assignedClassIds = $assignedStreams->pluck('class_id')->toArray();

            $students = Student::where('school_id', $schoolId)
                ->whereIn('current_stream_id', $assignedStreamIds)
                ->with(['currentClass', 'currentStream', 'campus'])
                ->get();
            $classes = SchoolClass::where('school_id', $schoolId)->whereIn('id', $assignedClassIds)->get();
            $streams = Stream::where('school_id', $schoolId)->whereIn('id', $assignedStreamIds)->get();
        } else {
            $students = Student::where('school_id', $schoolId)->with(['currentClass', 'currentStream', 'campus'])->get();
            $classes = SchoolClass::where('school_id', $schoolId)->get();
            $streams = Stream::where('school_id', $schoolId)->get();
        }

        $campuses = Campus::where('school_id', $schoolId)->get();
        $roles = Role::whereNull('school_id')->orWhere('school_id', $schoolId)->get();

        return view('school.students', compact('students', 'classes', 'streams', 'campuses', 'roles'));
    }

    /**
     * Store new student & parent profile.
     */
    public function store(Request $request)
    {
        $schoolId = $request->user()->school_id;

        // Verify capacity limit check
        $this->limitService->checkStudentLimit($schoolId);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|in:Male,Female,Other',
            'nationality' => 'required|string|max:100',
            'religion' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:5',
            'address' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'campus_id' => 'required|exists:campuses,id',
            'current_class_id' => 'required|exists:classes,id',
            'current_stream_id' => 'nullable|exists:streams,id',
            'enrollment_date' => 'required|date',
            'nhis_number' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // Manual ID overrides
            'manual_student_id' => 'nullable|in:0,1',
            'student_id_number' => 'nullable|required_if:manual_student_id,1|string|max:50|unique:students,student_id_number',
            
            // Guardian details
            'guardian_first_name' => 'required|string|max:255',
            'guardian_last_name' => 'required|string|max:255',
            'guardian_phone' => 'required|string|max:30',
            'guardian_email' => 'nullable|email|max:255',
            'guardian_relationship' => 'required|string|max:50',
        ]);

        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_current', true)->first();
        if (!$activeYear) {
            $activeYear = AcademicYear::where('school_id', $schoolId)->first();
        }

        try {
            DB::transaction(function () use ($request, $schoolId, $activeYear) {
                // 1. Determine or Auto-Generate Student ID Number
                $school = \App\Models\School::findOrFail($schoolId);
                
                if ($request->input('manual_student_id') == '1') {
                    $studentIdNumber = $request->input('student_id_number');
                } else {
                    $prefix = $school->settings['student_id_prefix'] ?? 'STD';
                    $format = $school->settings['student_id_format'] ?? '{PREFIX}-{YEAR}-{SEQUENCE}';
                    $sequence = $school->settings['student_id_next_sequence'] ?? 1;

                    $studentIdNumber = str_replace(
                        ['{PREFIX}', '{YEAR}', '{SEQUENCE}'],
                        [$prefix, date('Y'), sprintf('%04d', $sequence)],
                        $format
                    );

                    // Auto increment counter sequence
                    $settings = $school->settings;
                    $settings['student_id_next_sequence'] = $sequence + 1;
                    $school->settings = $settings;
                    $school->save();
                }
                
                $photoPath = null;
                if ($request->hasFile('photo')) {
                    $photoPath = $request->file('photo')->store('student-photos', 'public');
                }

                $student = Student::create([
                    'school_id' => $schoolId,
                    'campus_id' => $request->campus_id,
                    'student_id_number' => $studentIdNumber,
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'date_of_birth' => $request->date_of_birth,
                    'gender' => $request->gender,
                    'nationality' => $request->nationality,
                    'religion' => $request->religion,
                    'blood_group' => $request->blood_group,
                    'photo' => $photoPath,
                    'address' => $request->address,
                    'region' => $request->region,
                    'district' => $request->district,
                    'current_class_id' => $request->current_class_id,
                    'current_stream_id' => $request->current_stream_id,
                    'enrollment_date' => $request->enrollment_date,
                    'nhis_number' => $request->nhis_number,
                    'status' => 'active',
                ]);

                // 2. Create/Link Guardian
                $guardian = Guardian::create([
                    'school_id' => $schoolId,
                    'first_name' => $request->guardian_first_name,
                    'last_name' => $request->guardian_last_name,
                    'relationship' => $request->guardian_relationship,
                    'phone' => $request->guardian_phone,
                    'email' => $request->guardian_email,
                    'address' => $request->address,
                    'is_primary' => true,
                ]);

                // Link Student & Guardian
                $student->guardians()->attach($guardian->id, ['is_primary' => true]);

                // 3. Create Student Enrollment
                if ($activeYear) {
                    StudentEnrollment::create([
                        'school_id' => $schoolId,
                        'student_id' => $student->id,
                        'academic_year_id' => $activeYear->id,
                        'class_id' => $request->current_class_id,
                        'stream_id' => $request->current_stream_id,
                        'enrollment_date' => $request->enrollment_date,
                        'status' => 'active',
                    ]);

                    // 3.1. Auto create Student user account (Option B: ID-Based Login)
                    $studentRole = Role::where('slug', 'student')->first();
                    $dobPassword = \Carbon\Carbon::parse($request->date_of_birth)->format('dmY');
                    $placeholderEmail = strtolower($studentIdNumber) . '@' . strtolower(config('app.name', 'EduLink')) . '.local';

                    User::create([
                        'school_id' => $schoolId,
                        'campus_id' => $request->campus_id,
                        'name' => $request->first_name . ' ' . $request->last_name,
                        'email' => $placeholderEmail,
                        'password' => Hash::make($dobPassword),
                        'role_id' => $studentRole ? $studentRole->id : null,
                        'employee_id' => $studentIdNumber,
                        'is_active' => true,
                    ]);
                }

                // 4. Auto create Parent user account if email is provided
                if ($request->guardian_email) {
                    $userExists = User::where('school_id', $schoolId)->where('email', $request->guardian_email)->exists();
                    if (!$userExists) {
                        $parentRole = Role::where('slug', 'parent')->first();
                        $tempPassword = 'password123';
                        
                        $parentUser = User::create([
                            'school_id' => $schoolId,
                            'name' => $request->guardian_first_name . ' ' . $request->guardian_last_name,
                            'email' => $request->guardian_email,
                            'password' => Hash::make($tempPassword),
                            'role_id' => $parentRole ? $parentRole->id : null,
                            'is_active' => true,
                        ]);

                        Log::info("Auto-created parent user account for {$request->guardian_email} and sent invitation invite.");

                        // Dispatch Welcome Email
                        try {
                            $school = \App\Models\School::findOrFail($schoolId);
                            \Illuminate\Support\Facades\Mail::to($request->guardian_email)->send(
                                new \App\Mail\ParentWelcomeMail($parentUser, $school, $tempPassword)
                            );
                        } catch (\Exception $e) {
                            Log::error("Failed to send welcome email to parent: " . $e->getMessage());
                        }
                    }
                }
            });

            return redirect()->back()->with('success', 'Student profile and guardian portal account registered successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to register student details: ' . $e->getMessage()]);
        }
    }

    /**
     * Update student details.
     */
    public function update(Request $request, Student $student)
    {
        $schoolId = $request->user()->school_id;

        if ($student->school_id !== $schoolId) {
            abort(403, 'Unauthorized student profile edit.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|in:Male,Female,Other',
            'nationality' => 'required|string|max:100',
            'nhis_number' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:5',
            'religion' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'campus_id' => 'required|exists:campuses,id',
            'current_class_id' => 'required|exists:classes,id',
            'current_stream_id' => 'nullable|exists:streams,id',
            'status' => 'required|string|in:active,graduated,transferred,withdrawn,deceased',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'student_id_number' => 'required|string|max:50|unique:students,student_id_number,' . $student->id,
        ]);

        $photoPath = $student->photo;
        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $photoPath = $request->file('photo')->store('student-photos', 'public');
        }

        $oldStudentId = $student->student_id_number;
        $newStudentId = $request->input('student_id_number');

        $student->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'nationality' => $request->nationality,
            'nhis_number' => $request->nhis_number,
            'blood_group' => $request->blood_group,
            'religion' => $request->religion,
            'address' => $request->address,
            'region' => $request->region,
            'district' => $request->district,
            'campus_id' => $request->campus_id,
            'current_class_id' => $request->current_class_id,
            'current_stream_id' => $request->current_stream_id,
            'status' => $request->status,
            'photo' => $photoPath,
            'student_id_number' => $newStudentId,
        ]);

        if ($oldStudentId !== $newStudentId) {
            // Update linked portal user account if exists
            $studentUser = User::where('school_id', $schoolId)
                ->where('employee_id', $oldStudentId)
                ->first();

            if ($studentUser) {
                $newPlaceholderEmail = strtolower($newStudentId) . '@' . strtolower(config('app.name', 'EduLink')) . '.local';
                $studentUser->update([
                    'employee_id' => $newStudentId,
                    'email' => $newPlaceholderEmail,
                ]);
                Log::info("Synced portal user ID account #{$studentUser->id} credentials for new Student ID: {$newStudentId}");
            }
        }

        return redirect()->back()->with('success', 'Student profile details updated successfully.');
    }

    /**
     * Delete student.
     */
    public function destroy(Request $request, Student $student)
    {
        $schoolId = $request->user()->school_id;

        if ($student->school_id !== $schoolId) {
            abort(403, 'Unauthorized student profile delete.');
        }

        $student->delete();

        return redirect()->back()->with('success', 'Student record deleted successfully.');
    }

    /**
     * Reset student portal password to their default date of birth (DDMMYYYY).
     */
    public function resetPortalPassword(Request $request, Student $student)
    {
        $schoolId = $request->user()->school_id;

        if ($student->school_id !== $schoolId) {
            abort(403, 'Unauthorized student profile action.');
        }

        // Find the linked user account
        $user = User::where('school_id', $schoolId)
            ->where('employee_id', $student->student_id_number)
            ->first();

        if (!$user) {
            return redirect()->back()->withErrors(['error' => 'No linked user portal account found for this student.']);
        }

        // Reset password to default Date of Birth formatted as DDMMYYYY
        $dobPassword = \Carbon\Carbon::parse($student->date_of_birth)->format('dmY');
        $user->update([
            'password' => Hash::make($dobPassword),
        ]);

        return redirect()->back()->with('success', "Student portal password for {$student->first_name} has been reset to default Date of Birth ({$dobPassword}) successfully.");
    }

    /**
     * Generate bulk student details PDF report.
     */
    public function printPdf(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $school = \App\Models\School::find($schoolId);
        $user = $request->user();
        
        if ($user->role && $user->role->slug === 'class-teacher') {
            $assignedStreams = Stream::where('class_teacher_id', $user->id)->get();
            $assignedStreamIds = $assignedStreams->pluck('id')->toArray();
            
            $students = Student::where('school_id', $schoolId)
                ->whereIn('current_stream_id', $assignedStreamIds)
                ->with(['currentClass', 'currentStream', 'campus', 'guardians'])
                ->get();
        } else {
            $students = Student::where('school_id', $schoolId)
                ->with(['currentClass', 'currentStream', 'campus', 'guardians'])
                ->get();
        }
            
        $pdf = Pdf::loadView('school.reports.student_pdf', compact('students', 'school'))->setPaper('a4', 'landscape');
        return $pdf->download('student_directory_report.pdf');
    }
}
