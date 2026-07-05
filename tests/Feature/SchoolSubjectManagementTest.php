<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolSubjectManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $schoolAdmin;
    protected $plan;
    protected $subject;

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
            'name' => 'Accra Prep School',
            'school_code' => 'APS',
            'subdomain' => 'accra-prep',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Owuraku Owner',
            'owner_email' => 'owuraku@accra-prep.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $campus = Campus::create([
            'school_id' => $this->school->id,
            'name' => 'Main Prep',
            'is_main' => true,
            'is_active' => true,
        ]);

        $roleAdmin = Role::create(['name' => 'School Admin', 'slug' => 'school-admin', 'is_system' => true]);

        $this->schoolAdmin = User::create([
            'school_id' => $this->school->id,
            'campus_id' => $campus->id,
            'name' => 'Owuraku Admin',
            'email' => 'admin@accra-prep.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);

        $this->subject = Subject::create([
            'school_id' => $this->school->id,
            'name' => 'Integrated Science',
            'code' => 'I-SCI',
            'level' => 'JHS',
            'is_core' => true,
            'is_elective' => false,
        ]);
    }

    public function test_authenticated_user_can_access_subjects_index()
    {
        $response = $this->actingAs($this->schoolAdmin)->get(route('school.subjects'));

        $response->assertStatus(200);
        $response->assertSee('Integrated Science');
        $response->assertSee('I-SCI');
    }

    public function test_school_admin_can_update_subject()
    {
        $response = $this->actingAs($this->schoolAdmin)->put(route('school.subjects.update', $this->subject->id), [
            'name' => 'Updated Science',
            'code' => 'U-SCI',
            'level' => 'SHS',
            'type' => 'elective',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->subject->refresh();
        $this->assertEquals('Updated Science', $this->subject->name);
        $this->assertEquals('U-SCI', $this->subject->code);
        $this->assertEquals('SHS', $this->subject->level);
        $this->assertFalse($this->subject->is_core);
        $this->assertTrue($this->subject->is_elective);
    }

    public function test_school_admin_can_delete_subject()
    {
        $response = $this->actingAs($this->schoolAdmin)->delete(route('school.subjects.destroy', $this->subject->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertNull(Subject::find($this->subject->id));
    }

    public function test_school_admin_can_update_allocation()
    {
        $academicYear = \App\Models\AcademicYear::create([
            'school_id' => $this->school->id,
            'name' => '2026/2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-30',
            'is_current' => true,
        ]);
        
        $class = \App\Models\SchoolClass::create([
            'school_id' => $this->school->id,
            'name' => 'Form 1 A',
            'campus_id' => $this->schoolAdmin->campus_id,
            'academic_year_id' => $academicYear->id,
            'level' => 'SHS',
        ]);

        $allocation = \App\Models\ClassSubject::create([
            'school_id' => $this->school->id,
            'class_id' => $class->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->schoolAdmin->id,
            'academic_year_id' => $academicYear->id,
            'periods_per_week' => 4,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->put(route('school.subjects.allocate.update', $allocation->id), [
            'class_id' => $class->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->schoolAdmin->id,
            'periods_per_week' => 6,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $allocation->refresh();
        $this->assertEquals(6, $allocation->periods_per_week);
    }

    public function test_school_admin_can_delete_allocation()
    {
        $academicYear = \App\Models\AcademicYear::create([
            'school_id' => $this->school->id,
            'name' => '2026/2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-30',
            'is_current' => true,
        ]);
        
        $class = \App\Models\SchoolClass::create([
            'school_id' => $this->school->id,
            'name' => 'Form 1 A',
            'campus_id' => $this->schoolAdmin->campus_id,
            'academic_year_id' => $academicYear->id,
            'level' => 'SHS',
        ]);

        $allocation = \App\Models\ClassSubject::create([
            'school_id' => $this->school->id,
            'class_id' => $class->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->schoolAdmin->id,
            'academic_year_id' => $academicYear->id,
            'periods_per_week' => 4,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->delete(route('school.subjects.allocate.destroy', $allocation->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertNull(\App\Models\ClassSubject::find($allocation->id));
    }
}
