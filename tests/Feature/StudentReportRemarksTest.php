<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\Term;
use App\Models\Student;
use App\Models\GradingScale;
use App\Models\GradingScaleItem;
use App\Models\StudentReportDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentReportRemarksTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $schoolAdmin;
    protected $plan;
    protected $campus;
    protected $class;
    protected $academicYear;
    protected $term;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::create([
            'name' => 'Premium',
            'price_monthly' => 500,
            'price_yearly' => 5000,
            'max_students' => 1000,
            'max_staff' => 50,
            'max_campuses' => 5,
            'is_active' => true,
        ]);

        $this->school = School::create([
            'name' => 'Accra Academy',
            'school_code' => 'ACC',
            'subdomain' => 'accra',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Yaw Owner',
            'owner_email' => 'yaw@accra.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $this->campus = Campus::create([
            'school_id' => $this->school->id,
            'name' => 'Main Accra',
            'is_main' => true,
            'is_active' => true,
        ]);

        $this->academicYear = AcademicYear::create([
            'school_id' => $this->school->id,
            'name' => '2026/2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-30',
            'is_current' => true,
        ]);

        $this->class = SchoolClass::create([
            'school_id' => $this->school->id,
            'name' => 'Form 1 A',
            'campus_id' => $this->campus->id,
            'academic_year_id' => $this->academicYear->id,
            'level' => 'Primary', // standards-based curriculum level
        ]);

        $this->student = Student::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'student_id_number' => 'STD-2026-8888',
            'first_name' => 'Abena',
            'last_name' => 'Boateng',
            'date_of_birth' => '2016-04-12',
            'gender' => 'Female',
            'nationality' => 'Ghanaian',
            'current_class_id' => $this->class->id,
            'status' => 'active',
            'enrollment_date' => '2026-06-01',
        ]);

        $this->term = Term::create([
            'school_id' => $this->school->id,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Term 1',
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-18',
            'is_current' => true,
        ]);

        $roleAdmin = Role::create(['name' => 'School Admin', 'slug' => 'school-admin', 'is_system' => true]);

        $this->schoolAdmin = User::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'name' => 'Yaw Admin',
            'email' => 'admin@accra.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);

        $this->class->class_teacher_id = $this->schoolAdmin->id;
        $this->class->save();

        // Seed default scales
        $scale = GradingScale::create([
            'school_id' => $this->school->id,
            'name' => 'Primary Standards-Based',
            'level' => 'Primary',
            'is_active' => true,
            'is_default' => true,
        ]);

        GradingScaleItem::create([
            'grading_scale_id' => $scale->id,
            'grade' => 'Adv',
            'min_score' => 80.00,
            'max_score' => 100.00,
            'grade_point' => 4.00,
            'description' => 'Advanced',
            'display_order' => 1,
        ]);
    }

    public function test_school_admin_can_save_student_report_card_remarks_and_details()
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('school.reports.details.store', $this->student->id), [
            'term_id' => $this->term->id,
            'academic_year_id' => $this->academicYear->id,
            'conduct' => 'Very Respectful',
            'attitude' => 'Attentive & Hardworking',
            'interest' => 'Creative Writing',
            'remarks' => 'Outstanding academic focus shown.',
            'reopening_date' => '2027-01-10',
            'attendance_present' => 88,
            'attendance_total' => 90,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('student_report_details', [
            'student_id' => $this->student->id,
            'conduct' => 'Very Respectful',
            'attitude' => 'Attentive & Hardworking',
            'interest' => 'Creative Writing',
            'remarks' => 'Outstanding academic focus shown.',
            'attendance_present' => 88,
            'attendance_total' => 90,
        ]);
    }

    public function test_school_admin_can_update_grading_scale_items_in_settings()
    {
        $scale = GradingScale::where('school_id', $this->school->id)->first();
        $item = $scale->items->first();

        $response = $this->actingAs($this->schoolAdmin)->post(route('school.settings.grading-scale.update', $scale->id), [
            'items' => [
                [
                    'id' => $item->id,
                    'grade' => 'Adv+',
                    'min_score' => 85.00,
                    'max_score' => 100.00,
                    'grade_point' => 4.50,
                    'description' => 'Super Advanced',
                ]
            ]
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $item->refresh();
        $this->assertEquals('Adv+', $item->grade);
        $this->assertEquals(85.00, floatval($item->min_score));
        $this->assertEquals(4.50, floatval($item->grade_point));
        $this->assertEquals('Super Advanced', $item->description);
    }

    public function test_report_card_view_compiles_successfully_with_custom_remarks_and_grading_legend()
    {
        StudentReportDetail::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'academic_year_id' => $this->academicYear->id,
            'conduct' => 'Exceptional',
            'attitude' => 'Superb',
            'interest' => 'Reading & Art',
            'remarks' => 'Brilliant performance all round.',
            'reopening_date' => '2027-01-10',
            'attendance_present' => 90,
            'attendance_total' => 90,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('school.reports.card', [
            'student' => $this->student->id,
            'term_id' => $this->term->id,
            'academic_year_id' => $this->academicYear->id,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_user_can_upload_personal_signature_on_profile()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->image('teacher_signature.png');

        $response = $this->actingAs($this->schoolAdmin)->post(route('profile.update'), [
            'name' => 'Yaw Admin',
            'email' => 'admin@accra.edu.gh',
            'signature' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->schoolAdmin->refresh();
        $this->assertNotNull($this->schoolAdmin->signature);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($this->schoolAdmin->signature);
    }

    public function test_teacher_role_restricted_to_assigned_class_only()
    {
        $roleTeacher = Role::create(['name' => 'Teacher', 'slug' => 'teacher', 'is_system' => true]);

        $teacherUser = User::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'name' => 'Kofi Teacher',
            'email' => 'kofi@accra.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleTeacher->id,
            'is_active' => true,
        ]);

        // Create second class where Kofi is not the teacher
        $otherClass = SchoolClass::create([
            'school_id' => $this->school->id,
            'name' => 'Form 1 B',
            'campus_id' => $this->campus->id,
            'academic_year_id' => $this->academicYear->id,
            'level' => 'Primary',
        ]);

        $otherStudent = Student::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'student_id_number' => 'STD-2026-9999',
            'first_name' => 'Kojo',
            'last_name' => 'Mensah',
            'date_of_birth' => '2016-05-12',
            'gender' => 'Male',
            'nationality' => 'Ghanaian',
            'current_class_id' => $otherClass->id,
            'status' => 'active',
            'enrollment_date' => '2026-06-01',
        ]);

        // Kofi views dashboard, sees no classes (since he is not class teacher of either yet)
        $response1 = $this->actingAs($teacherUser)->get(route('school.reports.index'));
        $response1->assertStatus(200);
        $response1->assertDontSee($this->class->name);

        // Assign Kofi as teacher of class A
        $this->class->class_teacher_id = $teacherUser->id;
        $this->class->save();

        // Kofi views dashboard now, sees class A but not class B
        $response2 = $this->actingAs($teacherUser)->get(route('school.reports.index'));
        $response2->assertStatus(200);
        $response2->assertSee($this->class->name);
        $response2->assertDontSee($otherClass->name);

        // Kofi saves details of student in class A (Success)
        $response3 = $this->actingAs($teacherUser)->post(route('school.reports.details.store', $this->student->id), [
            'term_id' => $this->term->id,
            'academic_year_id' => $this->academicYear->id,
            'conduct' => 'Helpful',
            'attitude' => 'Hardworking',
            'interest' => 'Art',
            'remarks' => 'Good job.',
        ]);
        $response3->assertRedirect();

        // Kofi saves details of student in class B (Fails 403)
        $response4 = $this->actingAs($teacherUser)->post(route('school.reports.details.store', $otherStudent->id), [
            'term_id' => $this->term->id,
            'academic_year_id' => $this->academicYear->id,
            'conduct' => 'Helpful',
            'attitude' => 'Hardworking',
            'interest' => 'Art',
            'remarks' => 'Good job.',
        ]);
        $response4->assertStatus(403);
    }
}
