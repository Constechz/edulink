<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $user;
    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::create([
            'name' => 'Premium',
            'price_monthly' => 500,
            'price_yearly' => 5000,
            'max_students' => 1000,
            'max_staff' => 100,
            'max_campuses' => 5,
            'is_active' => true,
        ]);

        $this->school = School::create([
            'name' => 'Test School',
            'school_code' => 'TSCH',
            'subdomain' => 'testschool',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Owner',
            'owner_email' => 'owner@test.com',
            'is_active' => true,
            'onboarding_completed' => false,
        ]);

        $this->user = User::create([
            'school_id' => $this->school->id,
            'name' => 'School Owner',
            'email' => 'owner@test.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
    }

    public function test_user_can_access_onboarding_page(): void
    {
        $response = $this->actingAs($this->user)->get('/school/onboarding');
        $response->assertStatus(200);
    }

    public function test_complete_five_step_onboarding_wizard(): void
    {
        $this->actingAs($this->user);

        // Step 1: Branding
        $response1 = $this->postJson('/school/onboarding', [
            'step' => 1,
            'primary_color' => '#003366',
            'accent_color' => '#ffd700',
            'font_family' => 'Outfit',
        ]);
        $response1->assertJson(['success' => true]);

        // Step 2: Calendar
        $response2 = $this->postJson('/school/onboarding', [
            'step' => 2,
            'academic_year' => '2026/2027',
            'term_name' => 'Term 1',
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-15',
        ]);
        $response2->assertJson(['success' => true]);

        // Step 3: Admin User
        $response3 = $this->postJson('/school/onboarding', [
            'step' => 3,
            'admin_name' => 'Principal Mensah',
            'admin_email' => 'principal@test.com',
            'admin_password' => 'password1234',
        ]);
        $response3->assertJson(['success' => true]);

        // Step 4: Scoring Weights
        $response4 = $this->postJson('/school/onboarding', [
            'step' => 4,
            'class_weight' => 40,
            'exam_weight' => 60,
        ]);
        $response4->assertJson(['success' => true]);

        // Step 5: Finalize & Website Template
        $response5 = $this->postJson('/school/onboarding', [
            'step' => 5,
        ]);
        $response5->assertJson([
            'success' => true,
            'redirect' => '/dashboard',
        ]);

        $this->school->refresh();
        $this->assertTrue($this->school->onboarding_completed);
    }
}
