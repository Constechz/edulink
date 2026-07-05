<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $schoolAdmin;
    protected $plan;
    protected $campus;
    protected $class;
    protected $academicYear;

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
            'name' => 'Kumasi Academy',
            'school_code' => 'KUM',
            'subdomain' => 'kumasi',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Kwame Owner',
            'owner_email' => 'kwame@kumasi.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $this->campus = Campus::create([
            'school_id' => $this->school->id,
            'name' => 'Main Kumasi',
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
            'name' => 'Form 1 B',
            'campus_id' => $this->campus->id,
            'academic_year_id' => $this->academicYear->id,
            'level' => 'SHS',
        ]);

        $roleAdmin = Role::create(['name' => 'School Admin', 'slug' => 'school-admin', 'is_system' => true]);
        Role::create(['name' => 'Parent', 'slug' => 'parent', 'is_system' => true]);

        $this->schoolAdmin = User::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'name' => 'Kwame Admin',
            'email' => 'admin@kumasi.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);
    }

    public function test_school_admin_can_register_student_with_photo()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('student_avatar.jpg');

        $response = $this->actingAs($this->schoolAdmin)->post(route('school.students.store'), [
            'first_name' => 'Kofi',
            'last_name' => 'Mensah',
            'date_of_birth' => '2015-05-12',
            'gender' => 'Male',
            'nationality' => 'Ghanaian',
            'campus_id' => $this->campus->id,
            'current_class_id' => $this->class->id,
            'enrollment_date' => '2026-06-01',
            'guardian_first_name' => 'Emmanuel',
            'guardian_last_name' => 'Mensah',
            'guardian_phone' => '+233244123456',
            'guardian_relationship' => 'Father',
            'photo' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $student = Student::where('first_name', 'Kofi')->first();
        $this->assertNotNull($student);
        $this->assertNotNull($student->photo);

        Storage::disk('public')->assertExists($student->photo);
    }

    public function test_school_admin_can_update_student_photo()
    {
        Storage::fake('public');

        $student = Student::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'student_id_number' => 'STD-2026-0001',
            'first_name' => 'Yaa',
            'last_name' => 'Asantewaa',
            'date_of_birth' => '2014-03-24',
            'gender' => 'Female',
            'nationality' => 'Ghanaian',
            'current_class_id' => $this->class->id,
            'status' => 'active',
            'photo' => 'student-photos/old_photo.jpg',
            'enrollment_date' => '2026-06-01',
        ]);

        // Place old fake file in storage
        Storage::disk('public')->put('student-photos/old_photo.jpg', 'content');

        $newFile = UploadedFile::fake()->image('new_avatar.jpg');

        $response = $this->actingAs($this->schoolAdmin)->put(route('school.students.update', $student->id), [
            'first_name' => 'Yaa',
            'last_name' => 'Asantewaa Updated',
            'date_of_birth' => '2014-03-24',
            'gender' => 'Female',
            'nationality' => 'Ghanaian',
            'campus_id' => $this->campus->id,
            'current_class_id' => $this->class->id,
            'status' => 'active',
            'photo' => $newFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $student->refresh();
        $this->assertEquals('Asantewaa Updated', $student->last_name);
        $this->assertNotEquals('student-photos/old_photo.jpg', $student->photo);
        
        Storage::disk('public')->assertMissing('student-photos/old_photo.jpg');
        Storage::disk('public')->assertExists($student->photo);
    }

    public function test_report_card_view_compiles_successfully_with_photo()
    {
        $student = Student::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'student_id_number' => 'STD-2026-0002',
            'first_name' => 'Kweku',
            'last_name' => 'Ananse',
            'date_of_birth' => '2016-07-15',
            'gender' => 'Male',
            'nationality' => 'Ghanaian',
            'current_class_id' => $this->class->id,
            'status' => 'active',
            'photo' => 'student-photos/ananse.jpg',
            'enrollment_date' => '2026-06-01',
        ]);

        $term = \App\Models\Term::create([
            'school_id' => $this->school->id,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Term 1',
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-18',
            'is_current' => true,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('school.reports.card', [
            'student' => $student->id,
            'term_id' => $term->id,
            'academic_year_id' => $this->academicYear->id,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
