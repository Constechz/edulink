<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Plan;
use App\Models\PlatformAdmin;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RbacMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $plan;
    protected $permission;

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

        $this->permission = Permission::create([
            'name' => 'Configure Scoring',
            'slug' => 'configure-scoring',
            'module' => 'Scoring',
            'description' => 'Test permission',
        ]);

        // Register a temporary test route for testing the permission middleware
        Route::middleware(['web', 'permission:configure-scoring'])->get('/_test/configure-scoring', function () {
            return 'Access Granted';
        });
    }

    public function test_user_without_permission_is_forbidden(): void
    {
        $role = Role::create([
            'name' => 'Teacher',
            'slug' => 'teacher',
            'school_id' => $this->school->id,
            'is_system' => false,
        ]);

        $user = User::create([
            'school_id' => $this->school->id,
            'name' => 'Teacher User',
            'email' => 'teacher@test.com',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/_test/configure-scoring');
        $response->assertStatus(403);
    }

    public function test_user_with_permission_can_access(): void
    {
        $role = Role::create([
            'name' => 'Teacher',
            'slug' => 'teacher',
            'school_id' => $this->school->id,
            'is_system' => false,
        ]);
        $role->permissions()->attach($this->permission->id);

        $user = User::create([
            'school_id' => $this->school->id,
            'name' => 'Teacher User',
            'email' => 'teacher@test.com',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/_test/configure-scoring');
        $response->assertStatus(200);
        $response->assertSee('Access Granted');
    }

    public function test_super_admin_bypasses_permission_check(): void
    {
        $role = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'school_id' => $this->school->id,
            'is_system' => true,
        ]);

        $user = User::create([
            'school_id' => $this->school->id,
            'name' => 'Super User',
            'email' => 'super@test.com',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/_test/configure-scoring');
        $response->assertStatus(200);
    }

    public function test_platform_admin_bypasses_permission_check(): void
    {
        $admin = PlatformAdmin::create([
            'name' => 'Platform Admin',
            'email' => 'platform@admin.com',
            'password' => bcrypt('adminpass'),
        ]);

        $response = $this->actingAs($admin, 'platform_admin')->get('/_test/configure-scoring');
        $response->assertStatus(200);
    }
}
