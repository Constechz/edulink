<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileSecurityTest extends TestCase
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
            'max_staff' => 50,
            'max_campuses' => 5,
            'is_active' => true,
        ]);

        $this->school = School::create([
            'name' => 'Adabraka Tech',
            'school_code' => 'ATB',
            'subdomain' => 'adabraka',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Akosua Owner',
            'owner_email' => 'akosua@adabraka.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $campus = Campus::create([
            'school_id' => $this->school->id,
            'name' => 'Central Adabraka',
            'is_main' => true,
            'is_active' => true,
        ]);

        $roleAdmin = Role::create(['name' => 'School Admin', 'slug' => 'school-admin', 'is_system' => true]);

        $this->user = User::create([
            'school_id' => $this->school->id,
            'campus_id' => $campus->id,
            'name' => 'Akosua Teacher',
            'email' => 'akosua@adabraka.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);
    }

    public function test_authenticated_user_can_access_profile_page()
    {
        $response = $this->actingAs($this->user)->get(route('profile'));

        $response->assertStatus(200);
        $response->assertSee('User Settings Dashboard');
        $response->assertSee('Akosua Teacher');
    }

    public function test_user_can_update_profile_name_and_email()
    {
        $response = $this->actingAs($this->user)->post(route('profile.update'), [
            'name' => 'Akosua Updated Name',
            'email' => 'akosua.new@adabraka.edu.gh',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('Akosua Updated Name', $this->user->name);
        $this->assertEquals('akosua.new@adabraka.edu.gh', $this->user->email);
    }

    public function test_user_can_update_profile_avatar_photo()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($this->user)->post(route('profile.update'), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'profile_photo' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertNotNull($this->user->profile_photo);
        
        Storage::disk('public')->assertExists($this->user->profile_photo);
    }

    public function test_user_can_change_password_with_correct_current_password()
    {
        $response = $this->actingAs($this->user)->post(route('profile.password'), [
            'current_password' => 'password123',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->user->password));
    }

    public function test_user_cannot_change_password_with_incorrect_current_password()
    {
        $response = $this->actingAs($this->user)->post(route('profile.password'), [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['current_password']);

        $this->user->refresh();
        $this->assertTrue(Hash::check('password123', $this->user->password));
    }
}
