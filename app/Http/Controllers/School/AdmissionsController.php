<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AdmissionApplication;
use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Guardian;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Services\Subscription\SubscriptionLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdmissionsController extends Controller
{
    protected $limitService;

    public function __construct(SubscriptionLimitService $limitService)
    {
        $this->limitService = $limitService;
    }

    /**
     * Public application form.
     */
    public function publicForm(Request $request)
    {
        $school = null;
        if (app()->bound('tenant')) {
            $school = app('tenant');
        } else {
            $schoolId = $request->get('school_id') ?? $request->session()->get('school_id');
            if ($schoolId) {
                $school = School::find($schoolId);
            }
        }

        if (!$school) {
            $school = School::first();
        }

        if (!$school) {
            return "No school found. Please configure the platform first.";
        }

        $campuses = Campus::where('school_id', $school->id)->where('is_active', true)->get();
        $classes = SchoolClass::where('school_id', $school->id)->get();

        return view('school.apply', compact('school', 'campuses', 'classes'));
    }

    /**
     * Submit public application.
     */
    public function submitForm(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'campus_id' => 'required|exists:campuses,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|in:Male,Female,Other',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'guardian_name' => 'required|string|max:255',
            'guardian_phone' => 'required|string|max:50',
            'guardian_email' => 'required|email|max:255',
            'class_id' => 'required|exists:classes,id',
            'birth_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'transcript' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $documents = [];
        if ($request->hasFile('birth_certificate')) {
            $path = $request->file('birth_certificate')->store('admissions/birth_certificates', 'public');
            $documents['birth_certificate'] = $path;
        }
        if ($request->hasFile('transcript')) {
            $path = $request->file('transcript')->store('admissions/transcripts', 'public');
            $documents['transcript'] = $path;
        }

        AdmissionApplication::create([
            'school_id' => $request->school_id,
            'campus_id' => $request->campus_id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'email' => $request->email,
            'phone' => $request->phone,
            'guardian_name' => $request->guardian_name,
            'guardian_phone' => $request->guardian_phone,
            'guardian_email' => $request->guardian_email,
            'class_id' => $request->class_id,
            'status' => 'reviewing',
            'documents' => $documents,
        ]);

        return redirect()->back()->with('success', 'Your application has been submitted successfully. Our admissions team will review it shortly.');
    }

    /**
     * Dashboard directory of applicants.
     */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $applications = AdmissionApplication::where('school_id', $schoolId)
            ->with(['campus', 'class'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('school.admissions', compact('applications'));
    }

    /**
     * Update application status (e.g. Schedule Interview, Reject).
     */
    public function updateStatus(Request $request, AdmissionApplication $application)
    {
        $schoolId = $request->user()->school_id;

        if ($application->school_id !== $schoolId) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|string|in:reviewing,interview,rejected',
            'interview_notes' => 'nullable|string',
            'review_notes' => 'nullable|string',
        ]);

        $application->update([
            'status' => $request->status,
            'interview_notes' => $request->interview_notes ?? $application->interview_notes,
            'review_notes' => $request->review_notes ?? $application->review_notes,
        ]);

        return redirect()->back()->with('success', 'Application status updated to ' . $request->status);
    }

    /**
     * Approve and register student.
     */
    public function approve(Request $request, AdmissionApplication $application)
    {
        $schoolId = $request->user()->school_id;

        if ($application->school_id !== $schoolId) {
            abort(403);
        }

        // Limit Check
        $this->limitService->checkStudentLimit($schoolId);

        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_current', true)->first()
            ?: AcademicYear::where('school_id', $schoolId)->first();

        if (!$activeYear) {
            return redirect()->back()->withErrors(['error' => 'No active academic year configured. Please set up the academic structure first.']);
        }

        $school = School::findOrFail($schoolId);

        $parentUser = null;
        $parentPassword = 'password123';
        $studentUser = null;
        $studentDobPassword = null;
        $generatedStudentId = null;

        try {
            DB::transaction(function () use ($application, $school, $activeYear, &$parentUser, &$studentUser, &$studentDobPassword, &$generatedStudentId) {
                $schoolId = $school->id;
                
                // 1. Parse guardian name into first & last
                $nameParts = explode(' ', trim($application->guardian_name), 2);
                $guardianFirstName = $nameParts[0];
                $guardianLastName = isset($nameParts[1]) ? $nameParts[1] : 'Guardian';

                // 2. Create Guardian Profile
                $guardian = Guardian::create([
                    'school_id' => $schoolId,
                    'first_name' => $guardianFirstName,
                    'last_name' => $guardianLastName,
                    'relationship' => 'Parent',
                    'phone' => $application->guardian_phone,
                    'email' => $application->guardian_email,
                    'is_primary' => true,
                ]);

                // 3. Generate Student ID using Customizer settings rules
                $prefix = $school->settings['student_id_prefix'] ?? 'STD';
                $format = $school->settings['student_id_format'] ?? '{PREFIX}-{YEAR}-{SEQUENCE}';
                $sequence = $school->settings['student_id_next_sequence'] ?? 1;

                $generatedStudentId = str_replace(
                    ['{PREFIX}', '{YEAR}', '{SEQUENCE}'],
                    [$prefix, date('Y'), sprintf('%04d', $sequence)],
                    $format
                );

                // Increment sequence
                $settingsObj = $school->settings;
                $settingsObj['student_id_next_sequence'] = $sequence + 1;
                $school->settings = $settingsObj;
                $school->save();

                // 4. Create Student Profile
                $student = Student::create([
                    'school_id' => $schoolId,
                    'campus_id' => $application->campus_id,
                    'student_id_number' => $generatedStudentId,
                    'first_name' => $application->first_name,
                    'middle_name' => $application->middle_name,
                    'last_name' => $application->last_name,
                    'date_of_birth' => $application->date_of_birth,
                    'gender' => $application->gender,
                    'current_class_id' => $application->class_id,
                    'enrollment_date' => now(),
                    'status' => 'active',
                ]);

                // 5. Link Guardian
                $student->guardians()->attach($guardian->id, ['is_primary' => true]);

                // 6. Create Enrollment record
                StudentEnrollment::create([
                    'school_id' => $schoolId,
                    'student_id' => $student->id,
                    'academic_year_id' => $activeYear->id,
                    'class_id' => $application->class_id,
                    'enrollment_date' => now(),
                    'status' => 'active',
                ]);

                // 7. Student User Account
                $studentRole = Role::where('slug', 'student')->first();
                $studentDobPassword = \Carbon\Carbon::parse($application->date_of_birth)->format('dmY');
                $studentPlaceholderEmail = strtolower($generatedStudentId) . '@' . strtolower(config('app.name', 'EduLink')) . '.local';

                $studentUser = User::create([
                    'school_id' => $schoolId,
                    'campus_id' => $application->campus_id,
                    'name' => $application->first_name . ' ' . $application->last_name,
                    'email' => $studentPlaceholderEmail,
                    'password' => Hash::make($studentDobPassword),
                    'role_id' => $studentRole ? $studentRole->id : null,
                    'employee_id' => $generatedStudentId,
                    'is_active' => true,
                ]);

                // 8. Parent User Account
                if ($application->guardian_email) {
                    $parentUser = User::where('school_id', $schoolId)->where('email', $application->guardian_email)->first();
                    if (!$parentUser) {
                        $parentRole = Role::where('slug', 'parent')->first();
                        
                        $parentUser = User::create([
                            'school_id' => $schoolId,
                            'name' => $application->guardian_name,
                            'email' => $application->guardian_email,
                            'password' => Hash::make('password123'),
                            'role_id' => $parentRole ? $parentRole->id : null,
                            'is_active' => true,
                        ]);
                    }
                }

                // 9. Update Application status
                $application->update([
                    'status' => 'approved',
                ]);
            });

            // 10. Dispatch Welcome Notifications (Email/SMS) based on settings
            $channel = $school->settings['welcome_notification_channel'] ?? 'both';

            if ($parentUser && $parentUser->email && ($channel === 'email' || $channel === 'both')) {
                try {
                    \Illuminate\Support\Facades\Mail::to($parentUser->email)->send(
                        new \App\Mail\AdmissionsApprovedWelcomeMail($parentUser, $parentPassword, $studentUser, $studentDobPassword, $school)
                    );
                } catch (\Exception $e) {
                    Log::error("Admissions welcome email failed: " . $e->getMessage());
                }
            }

            if ($application->guardian_phone && ($channel === 'sms' || $channel === 'both')) {
                try {
                    $smsService = new \App\Services\SmsService();
                    $smsBody = "Welcome to {$school->name}! Admissions approved. Parent login: {$parentUser->email} (pass: {$parentPassword}). Student ID: {$generatedStudentId} (pass: {$studentDobPassword}). Portal: " . url('/login');
                    $senderId = $school->sms_gateway_config['sender_id'] ?? null;

                    $smsResult = $smsService->send($application->guardian_phone, $smsBody, $senderId);

                    if ($smsResult['success']) {
                        \App\Models\SmsDeliveryLog::create([
                            'school_id' => $school->id,
                            'recipient' => $application->guardian_phone,
                            'message' => $smsBody,
                            'channel' => 'sms',
                            'status' => 'delivered',
                            'reference' => $smsResult['reference'] ?? 'TXN-' . mt_rand(100000, 999999)
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error("Admissions welcome SMS failed: " . $e->getMessage());
                }
            }

            return redirect()->back()->with('success', 'Application approved and student profile generated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to approve application: ' . $e->getMessage()]);
        }
    }
}
