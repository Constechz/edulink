<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $user;
    protected $plan;
    protected $campus;
    protected $role;

    protected function setUp(): void
    {
        parent::setUp();

        // Plan with 2 max staff
        $this->plan = Plan::create([
            'name' => 'Standard',
            'price_monthly' => 200,
            'price_yearly' => 2000,
            'max_students' => 500,
            'max_staff' => 2,
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
            'onboarding_completed' => true,
        ]);

        $this->role = Role::create([
            'name' => 'Teacher',
            'slug' => 'teacher',
            'school_id' => null,
            'is_system' => true,
        ]);

        $this->campus = Campus::create([
            'school_id' => $this->school->id,
            'name' => 'Main Campus',
            'is_main' => true,
            'is_active' => true,
        ]);

        $this->user = User::create([
            'school_id' => $this->school->id,
            'name' => 'School Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
    }

    public function test_can_view_staff_directory(): void
    {
        $response = $this->actingAs($this->user)->get('/school/staff');
        $response->assertStatus(200);
    }

    public function test_can_register_staff_member(): void
    {
        $response = $this->actingAs($this->user)->post('/school/staff', [
            'name' => 'John Teacher',
            'email' => 'john@school.edu.gh',
            'password' => 'password1234',
            'role_id' => $this->role->id,
            'campus_id' => $this->campus->id,
            'designation' => 'Maths Tutor',
            'staff_number' => 'STF-001',
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('users', [
            'school_id' => $this->school->id,
            'email' => 'john@school.edu.gh',
            'name' => 'John Teacher',
        ]);

        $this->assertDatabaseHas('staff', [
            'school_id' => $this->school->id,
            'designation' => 'Maths Tutor',
            'staff_number' => 'STF-001',
        ]);
    }

    public function test_cannot_exceed_staff_plan_limits(): void
    {
        $this->actingAs($this->user);

        // Register Staff 1
        $this->post('/school/staff', [
            'name' => 'Staff 1',
            'email' => 'staff1@school.edu.gh',
            'password' => 'password1234',
            'role_id' => $this->role->id,
            'campus_id' => $this->campus->id,
            'designation' => 'Tutor',
            'staff_number' => 'STF-1',
        ]);

        // Register Staff 2
        $this->post('/school/staff', [
            'name' => 'Staff 2',
            'email' => 'staff2@school.edu.gh',
            'password' => 'password1234',
            'role_id' => $this->role->id,
            'campus_id' => $this->campus->id,
            'designation' => 'Tutor',
            'staff_number' => 'STF-2',
        ]);

        $this->assertEquals(2, Staff::where('school_id', $this->school->id)->count());

        // Try registering Staff 3 (exceeding limit of 2)
        $response = $this->post('/school/staff', [
            'name' => 'Staff 3',
            'email' => 'staff3@school.edu.gh',
            'password' => 'password1234',
            'role_id' => $this->role->id,
            'campus_id' => $this->campus->id,
            'designation' => 'Tutor',
            'staff_number' => 'STF-3',
        ]);

        $response->assertSessionHasErrors('limit');
        $this->assertEquals(2, Staff::where('school_id', $this->school->id)->count());
    }

    public function test_can_toggle_staff_status(): void
    {
        // Register a staff member first
        $user = User::create([
            'school_id' => $this->school->id,
            'name' => 'John Teacher',
            'email' => 'john@school.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $this->role->id,
            'is_active' => true,
        ]);
        $staff = Staff::create([
            'user_id' => $user->id,
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'staff_number' => 'STF-001',
            'designation' => 'Maths Tutor',
            'date_joined' => now(),
        ]);

        $response = $this->actingAs($this->user)->post("/school/staff/{$staff->id}/toggle");
        
        $response->assertRedirect();
        $user->refresh();
        $this->assertFalse($user->is_active); // Should toggle to false

        // Toggle back to true
        $this->post("/school/staff/{$staff->id}/toggle");
        $user->refresh();
        $this->assertTrue($user->is_active);
    }

    public function test_can_delete_staff_profile(): void
    {
        $user = User::create([
            'school_id' => $this->school->id,
            'name' => 'John Teacher',
            'email' => 'john@school.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $this->role->id,
            'is_active' => true,
        ]);
        $staff = Staff::create([
            'user_id' => $user->id,
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'staff_number' => 'STF-001',
            'designation' => 'Maths Tutor',
            'date_joined' => now(),
        ]);

        $response = $this->actingAs($this->user)->delete("/school/staff/{$staff->id}");
        $response->assertRedirect();

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertSoftDeleted('staff', ['id' => $staff->id]);
    }
}
