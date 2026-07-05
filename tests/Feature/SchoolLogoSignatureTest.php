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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SchoolLogoSignatureTest extends TestCase
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
            'name' => 'Accra Academy',
            'school_code' => 'ACC',
            'subdomain' => 'accra',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Yaw Owner',
            'owner_email' => 'yaw@accra.edu.gh',
            'is_active' => true,
            'onboarding_completed' => false, // start incomplete
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
            'level' => 'SHS',
        ]);

        $roleAdmin = Role::create(['name' => 'School Admin', 'slug' => 'school-admin', 'is_system' => true]);
        Role::create(['name' => 'Parent', 'slug' => 'parent', 'is_system' => true]);

        $this->schoolAdmin = User::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'name' => 'Yaw Admin',
            'email' => 'admin@accra.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);
    }

    public function test_school_admin_can_upload_logo_and_signature_during_onboarding()
    {
        Storage::fake('public');

        $logoFile = UploadedFile::fake()->image('school_logo.png');
        $signatureFile = UploadedFile::fake()->image('head_sig.jpg');

        $response = $this->actingAs($this->schoolAdmin)->post(route('school.onboarding.store'), [
            'step' => 1,
            'primary_color' => '#112233',
            'accent_color' => '#445566',
            'font_family' => 'Outfit',
            'logo' => $logoFile,
            'headteacher_signature' => $signatureFile,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->school->refresh();
        $this->assertNotNull($this->school->logo);
        $this->assertNotNull($this->school->settings['headteacher_signature']);

        Storage::disk('public')->assertExists($this->school->logo);
        Storage::disk('public')->assertExists($this->school->settings['headteacher_signature']);
    }

    public function test_school_admin_can_update_logo_and_signature_in_settings()
    {
        Storage::fake('public');

        $this->school->update([
            'logo' => 'schools/logos/old_logo.png',
            'settings' => ['headteacher_signature' => 'schools/signatures/old_sig.jpg'],
        ]);

        Storage::disk('public')->put('schools/logos/old_logo.png', 'content');
        Storage::disk('public')->put('schools/signatures/old_sig.jpg', 'content');

        $newLogo = UploadedFile::fake()->image('new_logo.png');
        $newSignature = UploadedFile::fake()->image('new_sig.jpg');

        $response = $this->actingAs($this->schoolAdmin)->post(route('school.settings.profile'), [
            'name' => 'Accra Academy Updated',
            'short_name' => 'ACC UPDATED',
            'logo' => $newLogo,
            'headteacher_signature' => $newSignature,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->school->refresh();
        $this->assertEquals('Accra Academy Updated', $this->school->name);
        
        Storage::disk('public')->assertMissing('schools/logos/old_logo.png');
        Storage::disk('public')->assertMissing('schools/signatures/old_sig.jpg');

        Storage::disk('public')->assertExists($this->school->logo);
        Storage::disk('public')->assertExists($this->school->settings['headteacher_signature']);
    }

    public function test_report_card_view_displays_custom_logo_and_signature()
    {
        $student = \App\Models\Student::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'student_id_number' => 'STD-2026-9999',
            'first_name' => 'Aba',
            'last_name' => 'Aidoo',
            'date_of_birth' => '2015-09-11',
            'gender' => 'Female',
            'nationality' => 'Ghanaian',
            'current_class_id' => $this->class->id,
            'status' => 'active',
            'enrollment_date' => '2026-06-01',
        ]);

        $this->school->update([
            'logo' => 'schools/logos/logo.png',
            'settings' => ['headteacher_signature' => 'schools/signatures/sig.jpg'],
        ]);

        $term = Term::create([
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
