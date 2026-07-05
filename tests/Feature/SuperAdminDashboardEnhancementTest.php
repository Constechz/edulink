<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminDashboardEnhancementTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $schoolAdmin;
    protected $superAdmin;
    protected $plan;

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
            'name' => 'Accra Academy Branch',
            'school_code' => 'AAB',
            'subdomain' => 'accra-academy',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Yaw Branch',
            'owner_email' => 'yaw@branch.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $campus = Campus::create([
            'school_id' => $this->school->id,
            'name' => 'Main Accra Campus',
            'is_main' => true,
            'is_active' => true,
        ]);

        $roleAdmin = Role::create(['name' => 'School Admin', 'slug' => 'school-admin', 'is_system' => true]);
        $roleSuper = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin', 'is_system' => true]);

        $this->schoolAdmin = User::create([
            'school_id' => $this->school->id,
            'campus_id' => $campus->id,
            'name' => 'Kofi Admin',
            'email' => 'admin@branch.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Platform Master User',
            'email' => 'master@edusphere.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleSuper->id,
            'is_active' => true,
        ]);
    }

    public function test_super_admin_can_access_dashboard_with_schools_and_users_listings()
    {
        $response = $this->actingAs($this->superAdmin)->get('/super-admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('super-admin.dashboard');
        
        $response->assertViewHas('schoolsList');
        $response->assertViewHas('usersList');
        $response->assertViewHas('rolesList');

        $response->assertSee('Accra Academy Branch');
        $response->assertSee('Kofi Admin');
    }

    public function test_super_admin_can_toggle_user_active_status()
    {
        // Assert initial status is active (true)
        $this->assertTrue($this->schoolAdmin->is_active);

        $response = $this->actingAs($this->superAdmin)->post(route('super-admin.users.toggle-status', $this->schoolAdmin->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Refresh and assert status changed to inactive (false)
        $this->schoolAdmin->refresh();
        $this->assertFalse($this->schoolAdmin->is_active);
    }

    public function test_super_admin_can_toggle_school_activation_state()
    {
        // Assert initial status is active (true)
        $this->assertTrue($this->school->is_active);

        $response = $this->actingAs($this->superAdmin)->post(route('super-admin.schools.toggle-status', $this->school->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Refresh and assert status changed to suspended (false)
        $this->school->refresh();
        $this->assertFalse($this->school->is_active);

        // Deactivating school should automatically cascade block its user accounts
        $this->schoolAdmin->refresh();
        $this->assertFalse($this->schoolAdmin->is_active);
    }

    public function test_non_super_admin_is_forbidden_from_dashboard_and_toggles()
    {
        // Get dashboard
        $response = $this->actingAs($this->schoolAdmin)->get('/super-admin/dashboard');
        $response->assertStatus(403);

        // Post toggles
        $response = $this->actingAs($this->schoolAdmin)->post(route('super-admin.users.toggle-status', $this->schoolAdmin->id));
        $response->assertStatus(403);

        $response = $this->actingAs($this->schoolAdmin)->post(route('super-admin.schools.toggle-status', $this->school->id));
        $response->assertStatus(403);
    }
}
