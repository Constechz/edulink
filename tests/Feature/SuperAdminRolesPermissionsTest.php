<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Permission;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminRolesPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $schoolAdmin;
    protected $superAdmin;
    protected $plan;
    protected $testPermission;

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
            'name' => 'Legacy Academy',
            'school_code' => 'LGC',
            'subdomain' => 'legacy',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Kwame Legacy',
            'owner_email' => 'kwame@legacy.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $campus = Campus::create([
            'school_id' => $this->school->id,
            'name' => 'Main Legacy Campus',
            'is_main' => true,
            'is_active' => true,
        ]);

        $roleAdmin = Role::create(['name' => 'School Admin', 'slug' => 'school-admin', 'is_system' => true]);
        $roleSuper = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin', 'is_system' => true]);

        $this->schoolAdmin = User::create([
            'school_id' => $this->school->id,
            'campus_id' => $campus->id,
            'name' => 'Efe Admin',
            'email' => 'efe@legacy.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Master Admin',
            'email' => 'master@edusphere.local',
            'password' => bcrypt('password123'),
            'role_id' => $roleSuper->id,
            'is_active' => true,
        ]);

        $this->testPermission = Permission::create([
            'name' => 'Manage Platform Settings',
            'slug' => 'manage-platform-settings',
            'module' => 'System',
            'description' => 'Change platform SMTP and gateways',
        ]);
    }

    public function test_super_admin_can_access_roles_directory()
    {
        $response = $this->actingAs($this->superAdmin)->get(route('super-admin.roles.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Super Admin');
        $response->assertSee('School Admin');
    }

    public function test_super_admin_can_create_custom_role_with_permissions()
    {
        $response = $this->actingAs($this->superAdmin)->get(route('super-admin.roles.create'));
        $response->assertStatus(200);
        $response->assertSee('Create Custom Global Role');

        $response = $this->actingAs($this->superAdmin)->post(route('super-admin.roles.store'), [
            'name' => 'Platform Support Staff',
            'slug' => 'platform-support',
            'description' => 'Handles support queries and basic logs inspection',
            'permissions' => [$this->testPermission->id],
        ]);

        $response->assertRedirect(route('super-admin.roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', [
            'name' => 'Platform Support Staff',
            'slug' => 'platform-support',
            'school_id' => null,
        ]);

        $role = Role::withoutGlobalScopes()->where('slug', 'platform-support')->first();
        $this->assertTrue($role->permissions->contains($this->testPermission));
    }

    public function test_super_admin_can_edit_and_update_role_permissions()
    {
        $customRole = Role::create([
            'school_id' => null,
            'name' => 'Temporary Help',
            'slug' => 'temp-help',
            'description' => 'Temporary operational helpers',
            'is_system' => false,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('super-admin.roles.edit', $customRole->id));
        $response->assertStatus(200);
        $response->assertSee('Edit Global Role and Permissions');

        $response = $this->actingAs($this->superAdmin)->put(route('super-admin.roles.update', $customRole->id), [
            'name' => 'Temporary Helper Updated',
            'slug' => 'temp-help',
            'description' => 'Updated temporary description',
            'permissions' => [$this->testPermission->id],
        ]);

        $response->assertRedirect(route('super-admin.roles.index'));
        
        $customRole->refresh();
        $this->assertEquals('Temporary Helper Updated', $customRole->name);
        $this->assertTrue($customRole->permissions->contains($this->testPermission));
    }

    public function test_super_admin_cannot_delete_system_roles()
    {
        $systemRole = Role::withoutGlobalScopes()->where('slug', 'super-admin')->first();
        $this->assertTrue($systemRole->is_system);

        $response = $this->actingAs($this->superAdmin)->delete(route('super-admin.roles.destroy', $systemRole->id));
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['error']);
        
        $this->assertDatabaseHas('roles', ['id' => $systemRole->id]);
    }

    public function test_super_admin_can_delete_custom_roles()
    {
        $customRole = Role::create([
            'school_id' => null,
            'name' => 'Redundant Role',
            'slug' => 'redundant',
            'is_system' => false,
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('super-admin.roles.destroy', $customRole->id));

        $response->assertRedirect(route('super-admin.roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('roles', ['id' => $customRole->id]);
    }

    public function test_school_admin_is_forbidden_from_managing_global_roles()
    {
        $response = $this->actingAs($this->schoolAdmin)->get(route('super-admin.roles.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->schoolAdmin)->post(route('super-admin.roles.store'), [
            'name' => 'Hack Role',
            'slug' => 'hack',
        ]);
        $response->assertStatus(403);
    }
}
