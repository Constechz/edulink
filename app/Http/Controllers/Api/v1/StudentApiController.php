<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentApiController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $schoolId = app('tenant')->id;
        $students = Student::with(['currentClass', 'currentStream', 'campus'])->get();

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    /**
     * Display a single student's details.
     */
    public function show(Request $request, $id)
    {
        $student = Student::with(['currentClass', 'currentStream', 'campus', 'guardians'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
            
            // Guardian details (optional in API, but created if provided)
            'guardian_first_name' => 'nullable|string|max:255',
            'guardian_last_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:30',
            'guardian_email' => 'nullable|email|max:255',
            'guardian_relationship' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $schoolId = app('tenant')->id;
        $activeYear = AcademicYear::where('is_current', true)->first() ?: AcademicYear::first();

        try {
            $student = DB::transaction(function () use ($request, $schoolId, $activeYear) {
                // Generate a student ID number
                $studentIdNumber = 'STD-API-' . date('Y') . '-' . sprintf('%04d', mt_rand(1, 9999));

                $student = Student::create([
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
                    'address' => $request->address,
                    'region' => $request->region,
                    'district' => $request->district,
                    'current_class_id' => $request->current_class_id,
                    'current_stream_id' => $request->current_stream_id,
                    'enrollment_date' => $request->enrollment_date,
                    'nhis_number' => $request->nhis_number,
                    'status' => 'active',
                ]);

                // Create Guardian if details provided
                if ($request->filled('guardian_first_name') && $request->filled('guardian_last_name') && $request->filled('guardian_phone')) {
                    $guardian = Guardian::create([
                        'first_name' => $request->guardian_first_name,
                        'last_name' => $request->guardian_last_name,
                        'relationship' => $request->guardian_relationship ?: 'Parent',
                        'phone' => $request->guardian_phone,
                        'email' => $request->guardian_email,
                        'address' => $request->address,
                        'is_primary' => true,
                    ]);

                    $student->guardians()->attach($guardian->id, ['is_primary' => true]);
                }

                // Create Student Enrollment
                if ($activeYear) {
                    StudentEnrollment::create([
                        'student_id' => $student->id,
                        'academic_year_id' => $activeYear->id,
                        'class_id' => $request->current_class_id,
                        'stream_id' => $request->current_stream_id,
                        'enrollment_date' => $request->enrollment_date,
                        'status' => 'active',
                    ]);
                }

                return $student;
            });

            // Dispatch webhook if subscribed
            app(\App\Services\WebhookService::class)->dispatch('student.enrolled', $student->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Student enrolled successfully via API.',
                'data' => $student->load(['currentClass', 'currentStream', 'campus'])
            ], 201); // let's return 201 Created or 200, 201 is standard
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to enroll student: ' . $e->getMessage()
            ], 500);
        }
    }
}
