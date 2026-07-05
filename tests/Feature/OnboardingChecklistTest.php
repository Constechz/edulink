<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingChecklistTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $user;
    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::create([
            'name' => 'Standard',
            'price_monthly' => 200,
            'price_yearly' => 2000,
            'max_students' => 500,
            'max_staff' => 50,
            'max_campuses' => 2,
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
            'name' => 'School Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
    }

    public function test_can_view_checklist_page(): void
    {
        $response = $this->actingAs($this->user)->get('/school/checklist');
        
        $response->assertStatus(200);
        $response->assertViewHas('progressPercent');
        $response->assertViewHas('completedItems');
        $response->assertViewHas('totalItems');
    }
}
