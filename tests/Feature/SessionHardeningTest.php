<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SessionHardeningTest extends TestCase
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
            'onboarding_completed' => true,
        ]);

        $this->user = User::create([
            'school_id' => $this->school->id,
            'name' => 'School Owner',
            'email' => 'owner@test.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        Route::middleware(['web', 'auth'])->get('/_test/protected', function () {
            return 'Access Granted';
        });
    }

    public function test_session_remains_active_when_ip_and_user_agent_do_not_change(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession([
                'pinned_ip' => '127.0.0.1',
                'pinned_user_agent' => 'Symfony'
            ])
            ->withServerVariables([
                'REMOTE_ADDR' => '127.0.0.1',
                'HTTP_USER_AGENT' => 'Symfony'
            ])
            ->get('/_test/protected');

        $response->assertStatus(200);
        $this->assertTrue(\Auth::check());
    }

    public function test_session_terminates_when_ip_changes(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession([
                'pinned_ip' => '127.0.0.1',
                'pinned_user_agent' => 'Symfony'
            ])
            ->withServerVariables([
                'REMOTE_ADDR' => '192.168.1.1', // Changed IP
                'HTTP_USER_AGENT' => 'Symfony'
            ])
            ->get('/_test/protected');

        $response->assertRedirect('/login');
        $this->assertFalse(\Auth::check());
    }

    public function test_session_terminates_when_user_agent_changes(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession([
                'pinned_ip' => '127.0.0.1',
                'pinned_user_agent' => 'Symfony'
            ])
            ->withServerVariables([
                'REMOTE_ADDR' => '127.0.0.1',
                'HTTP_USER_AGENT' => 'Mozilla/5.0' // Changed UA
            ])
            ->get('/_test/protected');

        $response->assertRedirect('/login');
        $this->assertFalse(\Auth::check());
    }
}
