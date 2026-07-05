<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AdmissionApplication;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Plan;
use App\Models\Programme;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Staff;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhaseFiveAcademicAndStudentTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $user;
    protected $plan;
    protected $campus;
    protected $role;
    protected $parentRole;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->plan = Plan::create([
            'name' => 'Standard',
            'price_monthly' => 200,
            'price_yearly' => 2000,
            'max_students' => 500,
            'max_staff' => 10,
            'max_campuses' => 2,
            'is_active' => true,
        ]);

        $this->school = School::create([
            'name' => 'Accra High School',
            'school_code' => 'AHS',
            'subdomain' => 'accrahigh',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Principal Appiah',
            'owner_email' => 'principal@accrahigh.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $this->role = Role::create([
            'name' => 'Teacher',
            'slug' => 'teacher',
            'school_id' => null,
            'is_system' => true,
        ]);

        $this->parentRole = Role::create([
            'name' => 'Parent',
            'slug' => 'parent',
            'school_id' => null,
            'is_system' => true,
        ]);

        $this->campus = Campus::create([
            'school_id' => $this->school->id,
            'name' => 'Main Campus',
            'is_main' => true,
            'is_active' => true,
        ]);

        $this->user = User::create([
            'school_id' => $this->school->id,
            'name' => 'School Admin',
            'email' => 'admin@accrahigh.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $this->role->id,
            'is_active' => true,
        ]);
    }

    /**
     * Test Student Registry CRUD & Guardian Portal Auto-creation.
     */
    public function test_student_registry_crud_and_guardian_portal(): void
    {
        $year = AcademicYear::create([
            'school_id' => $this->school->id,
            'name' => '2026/2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-30',
            'is_current' => true,
        ]);

        $prog = Programme::create([
            'school_id' => $this->school->id,
            'name' => 'General Science',
            'code' => 'G-SCI',
            'duration_years' => 3,
            'level' => 'SHS',
        ]);

        $class = SchoolClass::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'academic_year_id' => $year->id,
            'programme_id' => $prog->id,
            'name' => 'Form 1 Sci A',
            'level' => 'SHS',
            'capacity' => 45,
        ]);

        // 1. View students directory
        $response = $this->actingAs($this->user)->get('/school/students');
        $response->assertStatus(200);

        // 2. Register Student & Guardian
        $response = $this->post('/school/students', [
            'first_name' => 'Kwame',
            'middle_name' => 'Kofi',
            'last_name' => 'Mensah',
            'date_of_birth' => '2012-05-15',
            'gender' => 'Male',
            'nationality' => 'Ghanaian',
            'campus_id' => $this->campus->id,
            'current_class_id' => $class->id,
            'enrollment_date' => '2026-09-01',
            'guardian_first_name' => 'Edward',
            'guardian_last_name' => 'Mensah',
            'guardian_phone' => '+233241112222',
            'guardian_email' => 'edward.mensah@email.com',
            'guardian_relationship' => 'Father',
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('students', [
            'school_id' => $this->school->id,
            'first_name' => 'Kwame',
            'last_name' => 'Mensah',
        ]);

        $this->assertDatabaseHas('guardians', [
            'school_id' => $this->school->id,
            'email' => 'edward.mensah@email.com',
        ]);

        // Assert Parent portal user account was auto-created
        $this->assertDatabaseHas('users', [
            'school_id' => $this->school->id,
            'email' => 'edward.mensah@email.com',
            'role_id' => $this->parentRole->id,
        ]);
    }

    /**
     * Test Staff HR qualifications & documents.
     */
    public function test_staff_hr_qualifications_and_documents(): void
    {
        $staffUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Grace Teacher',
            'email' => 'grace@school.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $this->role->id,
            'is_active' => true,
        ]);

        $staff = Staff::create([
            'user_id' => $staffUser->id,
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'staff_number' => 'STF-099',
            'designation' => 'Science Tutor',
            'date_joined' => '2026-01-10',
        ]);

        // 1. View Staff HR Show Page
        $response = $this->actingAs($this->user)->get("/school/staff-hr/{$staff->id}");
        $response->assertStatus(200);

        // 2. Update HR Financial & Identifications
        $response = $this->put("/school/staff-hr/{$staff->id}", [
            'employment_type' => 'permanent',
            'salary_grade' => 'Grade A',
            'bank_name' => 'GCB Bank',
            'bank_account' => '1234567890',
            'bank_branch' => 'High Street',
            'ssnit_number' => 'SSNIT-999-XYZ',
            'tin_number' => 'TIN-111-222',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('staff', [
            'id' => $staff->id,
            'ssnit_number' => 'SSNIT-999-XYZ',
        ]);

        // 3. Add Qualification
        $mockCert = UploadedFile::fake()->create('degree.pdf', 500);
        $response = $this->post("/school/staff-hr/{$staff->id}/qualification", [
            'institution' => 'University of Cape Coast',
            'qualification' => 'Bachelor of Education',
            'year_obtained' => 2020,
            'certificate' => $mockCert,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('staff_qualifications', [
            'staff_id' => $staff->id,
            'qualification' => 'Bachelor of Education',
        ]);

        // 4. Upload HR Document
        $mockDoc = UploadedFile::fake()->create('contract.pdf', 1000);
        $response = $this->post("/school/staff-hr/{$staff->id}/upload", [
            'document_type' => 'Contract Agreement',
            'document' => $mockDoc,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('staff_documents', [
            'staff_id' => $staff->id,
            'document_type' => 'Contract Agreement',
        ]);
    }

    /**
     * Test Academic Calendar structures.
     */
    public function test_academic_structure_setup(): void
    {
        $this->actingAs($this->user);

        // 1. View Academic structure page
        $response = $this->get('/school/academics');
        $response->assertStatus(200);

        // 2. Add Academic Year
        $response = $this->post('/school/academics/years', [
            'name' => '2026/2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-30',
            'is_current' => 1,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('academic_years', [
            'school_id' => $this->school->id,
            'name' => '2026/2027',
            'is_current' => 1,
        ]);
        $yearId = AcademicYear::first()->id;

        // 3. Add Term
        $response = $this->post('/school/academics/terms', [
            'academic_year_id' => $yearId,
            'name' => 'Term 1',
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-18',
            'is_current' => 1,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('terms', [
            'school_id' => $this->school->id,
            'name' => 'Term 1',
        ]);

        // 4. Add Programme
        $response = $this->post('/school/academics/programmes', [
            'name' => 'General Arts',
            'code' => 'G-ARTS',
            'duration_years' => 3,
            'level' => 'SHS',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('programmes', [
            'school_id' => $this->school->id,
            'code' => 'G-ARTS',
        ]);
        $progId = Programme::first()->id;

        // 5. Create Class
        $response = $this->post('/school/academics/classes', [
            'campus_id' => $this->campus->id,
            'academic_year_id' => $yearId,
            'programme_id' => $progId,
            'name' => 'Form 1 Arts B',
            'level' => 'SHS',
            'capacity' => 50,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('classes', [
            'school_id' => $this->school->id,
            'name' => 'Form 1 Arts B',
        ]);
        $classId = SchoolClass::first()->id;

        // 6. Create Stream
        $response = $this->post('/school/academics/streams', [
            'class_id' => $classId,
            'name' => 'Stream Gold',
            'capacity' => 25,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('streams', [
            'school_id' => $this->school->id,
            'name' => 'Stream Gold',
        ]);
    }

    /**
     * Test Subjects allocations.
     */
    public function test_subject_registry_and_allocations(): void
    {
        $this->actingAs($this->user);

        // 1. View subjects page
        $response = $this->get('/school/subjects');
        $response->assertStatus(200);

        // 2. Create Subject
        $response = $this->post('/school/subjects', [
            'name' => 'Social Studies',
            'code' => 'S-STUD',
            'level' => 'SHS',
            'type' => 'core',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('subjects', [
            'school_id' => $this->school->id,
            'code' => 'S-STUD',
            'is_core' => true,
        ]);
        $subjId = Subject::first()->id;

        // Setup class context
        $year = AcademicYear::create([
            'school_id' => $this->school->id,
            'name' => '2026/2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-30',
            'is_current' => true,
        ]);
        $prog = Programme::create([
            'school_id' => $this->school->id,
            'name' => 'General Science',
            'code' => 'G-SCI',
            'duration_years' => 3,
            'level' => 'SHS',
        ]);
        $class = SchoolClass::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'academic_year_id' => $year->id,
            'programme_id' => $prog->id,
            'name' => 'Form 1 Sci A',
            'level' => 'SHS',
            'capacity' => 45,
        ]);

        // 3. Allocate subject teacher to class
        $response = $this->post('/school/subjects/allocate', [
            'class_id' => $class->id,
            'subject_id' => $subjId,
            'teacher_id' => $this->user->id,
            'periods_per_week' => 5,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('class_subjects', [
            'school_id' => $this->school->id,
            'class_id' => $class->id,
            'subject_id' => $subjId,
            'teacher_id' => $this->user->id,
        ]);
    }

    /**
     * Test Admissions CRM online application form, review workflow, and registration.
     */
    public function test_admissions_crm_pipeline_and_approval(): void
    {
        // Setup target class context
        $year = AcademicYear::create([
            'school_id' => $this->school->id,
            'name' => '2026/2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-30',
            'is_current' => true,
        ]);
        $prog = Programme::create([
            'school_id' => $this->school->id,
            'name' => 'General Science',
            'code' => 'G-SCI',
            'duration_years' => 3,
            'level' => 'SHS',
        ]);
        $class = SchoolClass::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'academic_year_id' => $year->id,
            'programme_id' => $prog->id,
            'name' => 'Form 1 Sci A',
            'level' => 'SHS',
            'capacity' => 45,
        ]);

        // 1. Access public Admissions page
        $response = $this->get("/admissions/apply?school_id={$this->school->id}");
        $response->assertStatus(200);

        // 2. Submit Public Application
        $mockBirthCert = UploadedFile::fake()->create('birth.jpg', 800);
        $response = $this->post('/admissions/apply', [
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'first_name' => 'Aba',
            'last_name' => 'Appiah',
            'date_of_birth' => '2013-11-20',
            'gender' => 'Female',
            'guardian_name' => 'Charles Appiah',
            'guardian_phone' => '+233550009999',
            'guardian_email' => 'charles@email.com',
            'class_id' => $class->id,
            'birth_certificate' => $mockBirthCert,
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('admission_applications', [
            'school_id' => $this->school->id,
            'first_name' => 'Aba',
            'status' => 'reviewing',
        ]);
        
        $app = AdmissionApplication::first();

        // 3. View Admissions CRM Dashboard (School portal)
        $response = $this->actingAs($this->user)->get('/school/admissions');
        $response->assertStatus(200);

        // 4. Update Application Status to Interview
        $response = $this->post("/school/admissions/{$app->id}/status", [
            'status' => 'interview',
            'interview_notes' => 'Scheduled interview for July 5th',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('admission_applications', [
            'id' => $app->id,
            'status' => 'interview',
            'interview_notes' => 'Scheduled interview for July 5th',
        ]);

        // 5. Approve application and promote candidate to registered Student
        $response = $this->post("/school/admissions/{$app->id}/approve");
        $response->assertRedirect();

        // Check application status is Approved
        $this->assertDatabaseHas('admission_applications', [
            'id' => $app->id,
            'status' => 'approved',
        ]);

        // Check Student profile is created
        $this->assertDatabaseHas('students', [
            'school_id' => $this->school->id,
            'first_name' => 'Aba',
            'last_name' => 'Appiah',
        ]);

        // Check Guardian is created
        $this->assertDatabaseHas('guardians', [
            'school_id' => $this->school->id,
            'email' => 'charles@email.com',
        ]);

        // Check Parent portal account was created
        $this->assertDatabaseHas('users', [
            'school_id' => $this->school->id,
            'email' => 'charles@email.com',
            'role_id' => $this->parentRole->id,
        ]);
    }
}
