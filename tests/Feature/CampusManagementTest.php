<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampusManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $user;
    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();

        // Plan with 2 max campuses
        $this->plan = Plan::create([
            'name' => 'Standard',
            'price_monthly' => 200,
            'price_yearly' => 2000,
            'max_students' => 500,
            'max_staff' => 50,
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

        $this->user = User::create([
            'school_id' => $this->school->id,
            'name' => 'School Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
    }

    public function test_can_view_campuses(): void
    {
        $response = $this->actingAs($this->user)->get('/school/campuses');
        $response->assertStatus(200);
    }

    public function test_can_create_campus(): void
    {
        $response = $this->actingAs($this->user)->post('/school/campuses', [
            'name' => 'Main Campus',
            'code' => 'MC',
            'address' => 'Accra',
            'phone' => '+233 24 123 4567',
            'email' => 'main@school.edu.gh',
            'principal_name' => 'Dr. Kwame',
            'is_main' => 1,
            'is_active' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('campuses', [
            'school_id' => $this->school->id,
            'name' => 'Main Campus',
            'is_main' => true,
        ]);
    }

    public function test_cannot_exceed_campus_plan_limits(): void
    {
        $this->actingAs($this->user);

        // Add campus 1
        $this->post('/school/campuses', [
            'name' => 'Campus 1',
            'code' => 'C1',
            'is_main' => 1,
            'is_active' => 1,
        ]);

        // Add campus 2
        $this->post('/school/campuses', [
            'name' => 'Campus 2',
            'code' => 'C2',
            'is_main' => 0,
            'is_active' => 1,
        ]);

        // Assert 2 campuses in DB
        $this->assertEquals(2, Campus::where('school_id', $this->school->id)->count());

        // Try to add campus 3 (exceeding limit of 2)
        $response = $this->post('/school/campuses', [
            'name' => 'Campus 3',
            'code' => 'C3',
            'is_main' => 0,
            'is_active' => 1,
        ]);

        // Should return with validation error about limits
        $response->assertSessionHasErrors('limit');
        $this->assertEquals(2, Campus::where('school_id', $this->school->id)->count());
    }

    public function test_can_update_campus(): void
    {
        $campus = Campus::create([
            'school_id' => $this->school->id,
            'name' => 'Old Name',
            'is_main' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->put("/school/campuses/{$campus->id}", [
            'name' => 'New Name',
            'code' => 'NC',
            'address' => 'Kumasi',
            'phone' => '+233 24 999 9999',
            'email' => 'new@school.edu.gh',
            'principal_name' => 'Mrs. Boateng',
            'is_main' => 1,
            'is_active' => 1,
        ]);

        $response->assertRedirect();
        $campus->refresh();
        $this->assertEquals('New Name', $campus->name);
        $this->assertEquals('Kumasi', $campus->address);
    }

    public function test_can_delete_campus(): void
    {
        $campus = Campus::create([
            'school_id' => $this->school->id,
            'name' => 'ToDelete',
            'is_main' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->delete("/school/campuses/{$campus->id}");
        $response->assertRedirect();
        $this->assertSoftDeleted('campuses', ['id' => $campus->id]);
    }
}
