<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseFourteenSecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $user;
    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::create([
            'name' => 'Enterprise Secure',
            'price_monthly' => 1500,
            'price_yearly' => 15000,
            'max_students' => 50000,
            'max_staff' => 1000,
            'max_campuses' => 20,
            'is_active' => true,
        ]);

        $this->school = School::create([
            'name' => 'Accra National College',
            'school_code' => 'ANC',
            'subdomain' => 'anc',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Principal Akufo',
            'owner_email' => 'sec@anc.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $this->user = User::create([
            'school_id' => $this->school->id,
            'name' => 'Security Audit Officer',
            'email' => 'sec@anc.edu.gh',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
    }

    /**
     * Test that guest users are redirected.
     */
    public function test_guest_redirected_from_security_hub()
    {
        $response = $this->get(route('school.docs.security'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that staff users can access the Security Audit Center.
     */
    public function test_authenticated_user_can_access_security_hub()
    {
        $response = $this->actingAs($this->user)->get(route('school.docs.security'));
        
        $response->assertStatus(200);
        $response->assertSee('Security Audit');
        $response->assertSee('Overall Compliance Rating');
        $response->assertSee('Application Debug Mode');
        $response->assertSee('HttpOnly Session Cookies');
    }

    /**
     * Test dynamic debug mode diagnostic rating logic.
     */
    public function test_debug_mode_evaluation_results()
    {
        // 1. With Debug Mode Enabled
        config(['app.debug' => true]);
        $response = $this->actingAs($this->user)->get(route('school.docs.security'));
        $response->assertSee('Enabled (APP_DEBUG=true)');

        // 2. With Debug Mode Disabled
        config(['app.debug' => false]);
        $response = $this->actingAs($this->user)->get(route('school.docs.security'));
        $response->assertSee('Disabled (APP_DEBUG=false)');
    }
}
