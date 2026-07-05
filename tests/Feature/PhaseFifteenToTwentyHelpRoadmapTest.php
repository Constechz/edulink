<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseFifteenToTwentyHelpRoadmapTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $user;
    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::create([
            'name' => 'Roadmap Standard',
            'price_monthly' => 200,
            'price_yearly' => 2000,
            'max_students' => 500,
            'max_staff' => 30,
            'max_campuses' => 1,
            'is_active' => true,
        ]);

        $this->school = School::create([
            'name' => 'Kumasi High School',
            'school_code' => 'KHS',
            'subdomain' => 'kumasihigh',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Headmaster Boateng',
            'owner_email' => 'admin@khs.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $this->user = User::create([
            'school_id' => $this->school->id,
            'name' => 'Help Desk Officer',
            'email' => 'admin@khs.edu.gh',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
    }

    /**
     * Test guest is redirected from help portal.
     */
    public function test_guest_is_redirected_from_help_portal()
    {
        $response = $this->get(route('school.docs.help'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that staff users can access the Help Center and view manuals.
     */
    public function test_authenticated_user_can_access_help_center()
    {
        $response = $this->actingAs($this->user)->get(route('school.docs.help'));
        
        $response->assertStatus(200);
        $response->assertSee('Help');
        $response->assertSee('Super Admin Manual');
        $response->assertSee('GES Standard Grading Scale');
        $response->assertSee('Milestones Roadmap');
        $response->assertSee('Videos Training Scripts');
    }
}
