<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\PlatformAdmin;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MfaAuthenticationTest extends TestCase
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
            'mfa_enabled' => true,
            'is_active' => true,
        ]);
    }

    public function test_mfa_redirects_to_verification_page(): void
    {
        $response = $this->post('/login', [
            'email' => 'owner@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login.mfa'));
        $this->assertFalse(\Auth::check()); // User not fully logged in yet
    }

    public function test_mfa_fails_with_invalid_otp(): void
    {
        // Initiate login
        $this->post('/login', [
            'email' => 'owner@test.com',
            'password' => 'password123',
        ]);

        // Submit wrong OTP code
        $response = $this->withSession([
            'mfa_user_id' => $this->user->id,
            'mfa_guard' => 'web'
        ])->post('/login/mfa', [
            'code' => '000000',
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertFalse(\Auth::check());
    }

    public function test_mfa_succeeds_with_valid_otp(): void
    {
        // Initiate login
        $this->post('/login', [
            'email' => 'owner@test.com',
            'password' => 'password123',
        ]);

        // Capture cached OTP
        $cacheKey = "mfa_otp_web_{$this->user->id}";
        $otp = Cache::get($cacheKey);
        $this->assertNotNull($otp);

        // Submit correct OTP code
        $response = $this->withSession([
            'mfa_user_id' => $this->user->id,
            'mfa_guard' => 'web'
        ])->post('/login/mfa', [
            'code' => $otp,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertTrue(\Auth::check());
        $this->assertEquals($this->user->id, \Auth::id());
    }

    public function test_platform_admin_mfa_verification(): void
    {
        $admin = PlatformAdmin::create([
            'name' => 'Platform Admin',
            'email' => 'platform@admin.com',
            'password' => bcrypt('adminpass'),
            'mfa_enabled' => true,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'platform@admin.com',
            'password' => 'adminpass',
        ]);

        $response->assertRedirect(route('login.mfa'));
        
        $cacheKey = "mfa_otp_platform_admin_{$admin->id}";
        $otp = Cache::get($cacheKey);
        $this->assertNotNull($otp);

        $verifyResponse = $this->withSession([
            'mfa_user_id' => $admin->id,
            'mfa_guard' => 'platform_admin'
        ])->post('/login/mfa', [
            'code' => $otp,
        ]);

        $verifyResponse->assertRedirect('/super-admin/dashboard');
        $this->assertTrue(\Auth::guard('platform_admin')->check());
    }
}
