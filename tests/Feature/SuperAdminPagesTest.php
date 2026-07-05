<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminPagesTest extends TestCase
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
            'name' => 'Legacy High School',
            'school_code' => 'LHS',
            'subdomain' => 'legacy',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Kofi Legacy',
            'owner_email' => 'kofi@legacy.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $campus = Campus::create([
            'school_id' => $this->school->id,
            'name' => 'Accra Campus',
            'is_main' => true,
            'is_active' => true,
        ]);

        $roleAdmin = Role::create(['name' => 'School Admin', 'slug' => 'school-admin', 'is_system' => true]);
        $roleSuper = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin', 'is_system' => true]);

        $this->schoolAdmin = User::create([
            'school_id' => $this->school->id,
            'campus_id' => $campus->id,
            'name' => 'Ama Admin',
            'email' => 'admin@legacy.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);

        $this->superAdmin = User::create([
            'name' => 'EduSphere Super User',
            'email' => 'super@edusphere.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleSuper->id,
            'is_active' => true,
        ]);
    }

    public function test_non_super_admin_is_blocked_from_admin_panels()
    {
        // Gated dedicated paths
        $response = $this->actingAs($this->schoolAdmin)->get('/super-admin/sms-credits');
        $response->assertStatus(403);

        $response = $this->actingAs($this->schoolAdmin)->get('/super-admin/access-logs');
        $response->assertStatus(403);

        $response = $this->actingAs($this->schoolAdmin)->get('/super-admin/settings');
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_all_dedicated_panels()
    {
        // 1. SMS credits view
        $response = $this->actingAs($this->superAdmin)->get('/super-admin/sms-credits');
        $response->assertStatus(200);
        $response->assertViewIs('super-admin.sms-credits');

        // 2. Access logs view
        // Log a mock audit entry to verify list rendering
        AuditLog::create([
            'school_id' => $this->school->id,
            'user_id' => $this->schoolAdmin->id,
            'action' => 'login',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit testing agent',
        ]);

        $response = $this->actingAs($this->superAdmin)->get('/super-admin/access-logs');
        $response->assertStatus(200);
        $response->assertViewIs('super-admin.access-logs');
        $response->assertSee('Legacy High School');
        $response->assertSee('Ama Admin');

        // 3. Settings view
        $response = $this->actingAs($this->superAdmin)->get('/super-admin/settings');
        $response->assertStatus(200);
        $response->assertViewIs('super-admin.settings');
    }

    public function test_super_admin_can_toggle_all_settings_switches()
    {
        $response = $this->actingAs($this->superAdmin)->post('/super-admin/settings/update', [
            'website_builder_unlock_price' => '399.99',
            'maintenance_mode' => '1',
            'self_registration_enabled' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('399.99', SystemSetting::getVal('website_builder_unlock_price'));
        $this->assertEquals('1', SystemSetting::getVal('maintenance_mode'));
        $this->assertEquals('1', SystemSetting::getVal('self_registration_enabled'));
    }

    public function test_super_admin_can_approve_school_registration()
    {
        \Illuminate\Support\Facades\Mail::fake();

        // 1. Create a school in inactive (pending) state
        $pendingSchool = School::create([
            'name' => 'Pending Academy',
            'school_code' => 'PND1',
            'subdomain' => 'pending',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Kweku Owner',
            'owner_email' => 'kweku@pending.edu.gh',
            'is_active' => false,
        ]);

        $pendingAdmin = User::create([
            'school_id' => $pendingSchool->id,
            'name' => 'Kweku Owner',
            'email' => 'kweku@pending.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => Role::where('slug', 'school-admin')->first()->id,
            'is_active' => false,
        ]);

        // 2. Super Admin approves it
        $response = $this->actingAs($this->superAdmin)->post("/super-admin/schools/{$pendingSchool->id}/approve");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $pendingSchool->refresh();
        $pendingAdmin->refresh();

        $this->assertTrue($pendingSchool->is_active);
        $this->assertTrue($pendingAdmin->is_active);

        // Assert Mail was sent to Kweku
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\SchoolApprovedMail::class, function ($mail) use ($pendingSchool) {
            return $mail->hasTo($pendingSchool->owner_email);
        });
    }

    public function test_registration_sends_emails_and_sets_pending_state()
    {
        \Illuminate\Support\Facades\Mail::fake();

        $response = $this->post('/register', [
            'school_name' => 'New Test School',
            'subdomain' => 'newtest',
            'admin_name' => 'New Admin',
            'admin_email' => 'newadmin@newtest.edu.gh',
            'admin_password' => 'password123',
            'admin_password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // Verify Mail sent to Super Admin
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\SchoolRegisteredSuperAdminMail::class, function ($mail) {
            return $mail->hasTo('admin@edusphere.com');
        });

        // Verify Mail sent to user
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\SchoolRegistrationPendingMail::class, function ($mail) {
            return $mail->hasTo('newadmin@newtest.edu.gh');
        });
    }
}
