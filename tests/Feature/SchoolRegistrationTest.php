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
        $response = $this->post('/register', [
            'school_name' => 'Legacy High School',
            'subdomain' => 'legacyhigh',
            'admin_name' => 'Principal Asante',
            'admin_email' => 'principal@legacy.edu.gh',
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
            'admin_name' => 'Principal Asante',
            'admin_email' => 'principal@legacy.edu.gh',
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
            'admin_name' => 'Principal Asante',
            'admin_email' => 'principal@legacy.edu.gh',
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
            'admin_name' => 'Principal Asante',
            'admin_email' => 'principal@legacy.edu.gh',
            'admin_password' => 'password123',
            'admin_password_confirmation' => 'differentpassword',
        ]);

        $response->assertSessionHasErrors('admin_password');
        $this->assertFalse(Auth::check());
    }
}
