<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SchoolRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected $plan;
    protected $role;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed a plan and role required for registration
        $this->plan = Plan::create([
            'name' => 'Free Trial Plan',
            'price_monthly' => 0.00,
            'price_yearly' => 0.00,
            'max_students' => 50,
            'max_staff' => 10,
            'max_campuses' => 1,
            'features' => ['dashboard', 'academics'],
            'is_active' => true,
        ]);

        $this->role = Role::create([
            'name' => 'School Admin',
            'slug' => 'school-admin',
            'is_system' => true,
        ]);
    }

    public function test_guest_can_access_registration_page(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function test_school_registration_success(): void
    {
        \Illuminate\Support\Facades\Queue::fake();

        $response = $this->post('/register', [
            'school_name' => 'Legacy High School',
            'subdomain' => 'legacyhigh',
            'region' => 'Greater Accra',
            'admin_name' => 'Principal Asante',
            'admin_email' => 'principal@legacy.edu.gh',
            'admin_phone' => '0241234567',
            'admin_password' => 'password123',
            'admin_password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');
        
        // Assert user is not automatically logged in (pending approval)
        $this->assertFalse(Auth::check());
        
        // Assert school exists in database as inactive (pending)
        $this->assertDatabaseHas('schools', [
            'name' => 'Legacy High School',
            'subdomain' => 'legacyhigh',
            'region' => 'Greater Accra',
            'plan_id' => $this->plan->id,
            'subscription_status' => 'trial',
            'onboarding_completed' => false,
            'is_active' => false,
        ]);

        // Assert user exists as inactive
        $this->assertDatabaseHas('users', [
            'name' => 'Principal Asante',
            'email' => 'principal@legacy.edu.gh',
            'role_id' => $this->role->id,
            'is_active' => false,
        ]);
    }

    public function test_registration_fails_due_to_duplicate_subdomain(): void
    {
        // Create an existing school with the same subdomain
        School::create([
            'name' => 'Other School',
            'school_code' => 'OTH1',
            'subdomain' => 'legacyhigh',
            'plan_id' => $this->plan->id,
            'subscription_status' => 'trial',
            'owner_name' => 'Other Owner',
            'owner_email' => 'owner@other.edu.gh',
            'is_active' => true,
            'onboarding_completed' => false,
        ]);

        $response = $this->post('/register', [
            'school_name' => 'Legacy High School',
            'subdomain' => 'legacyhigh',
            'region' => 'Greater Accra',
            'admin_name' => 'Principal Asante',
            'admin_email' => 'principal@legacy.edu.gh',
            'admin_phone' => '0241234567',
            'admin_password' => 'password123',
            'admin_password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('subdomain');
        $this->assertFalse(Auth::check());
    }

    public function test_registration_fails_due_to_invalid_subdomain_characters(): void
    {
        $response = $this->post('/register', [
            'school_name' => 'Legacy High School',
            'subdomain' => 'legacy_high!',
            'region' => 'Greater Accra',
            'admin_name' => 'Principal Asante',
            'admin_email' => 'principal@legacy.edu.gh',
            'admin_phone' => '0241234567',
            'admin_password' => 'password123',
            'admin_password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('subdomain');
        $this->assertFalse(Auth::check());
    }

    public function test_registration_fails_due_to_unconfirmed_password(): void
    {
        $response = $this->post('/register', [
            'school_name' => 'Legacy High School',
            'subdomain' => 'legacyhigh',
            'region' => 'Greater Accra',
            'admin_name' => 'Principal Asante',
            'admin_email' => 'principal@legacy.edu.gh',
            'admin_phone' => '0241234567',
            'admin_password' => 'password123',
            'admin_password_confirmation' => 'differentpassword',
        ]);

        $response->assertSessionHasErrors('admin_password');
        $this->assertFalse(Auth::check());
    }

    public function test_school_registration_dispatches_auto_approval_job(): void
    {
        \Illuminate\Support\Facades\Queue::fake();

        $response = $this->post('/register', [
            'school_name' => 'Legacy High School',
            'subdomain' => 'legacyhigh',
            'region' => 'Greater Accra',
            'admin_name' => 'Principal Asante',
            'admin_email' => 'principal@legacy.edu.gh',
            'admin_phone' => '0241234567',
            'admin_password' => 'password123',
            'admin_password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\AutoApproveSchoolAndUser::class, function ($job) {
            return $job->school->name === 'Legacy High School' && $job->delay !== null;
        });
    }

    public function test_auto_approve_job_activates_school_and_user_and_sends_email(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        // Create an inactive school and admin user
        $school = School::create([
            'name' => 'Pending School',
            'school_code' => 'PEND1',
            'subdomain' => 'pendingschool',
            'plan_id' => $this->plan->id,
            'subscription_status' => 'trial',
            'owner_name' => 'Owner Name',
            'owner_email' => 'owner@pending.edu.gh',
            'is_active' => false,
            'onboarding_completed' => false,
        ]);

        $user = User::create([
            'school_id' => $school->id,
            'name' => 'Owner Name',
            'email' => 'owner@pending.edu.gh',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role_id' => $this->role->id,
            'is_active' => false,
        ]);

        $job = new \App\Jobs\AutoApproveSchoolAndUser($school);
        $job->handle();

        // Assert school is activated
        $school->refresh();
        $this->assertTrue($school->is_active);
        $this->assertEquals('active', $school->subscription_status);
        $this->assertNull($school->trial_ends_at);

        // Assert user is activated
        $user->refresh();
        $this->assertTrue($user->is_active);

        // Assert email is sent
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\SchoolApprovedMail::class, function ($mail) use ($school) {
            return $mail->hasTo($school->owner_email) && $mail->school->id === $school->id;
        });
    }
}

