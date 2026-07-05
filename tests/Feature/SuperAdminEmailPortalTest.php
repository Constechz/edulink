<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\EmailLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SuperAdminEmailPortalTest extends TestCase
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

    public function test_non_super_admin_is_blocked_from_email_settings()
    {
        $response = $this->actingAs($this->schoolAdmin)->get('/super-admin/email-settings');
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_email_settings_and_logs()
    {
        $response = $this->actingAs($this->superAdmin)->get('/super-admin/email-settings');
        $response->assertStatus(200);
        $response->assertViewIs('super-admin.email-settings');
    }

    public function test_super_admin_can_update_smtp_gateway_settings()
    {
        $response = $this->actingAs($this->superAdmin)->post('/super-admin/email-settings/update', [
            'smtp_host' => 'smtp.mailtrap.io',
            'smtp_port' => 2525,
            'smtp_encryption' => 'tls',
            'smtp_username' => 'testuser',
            'smtp_password' => 'testpass',
            'mail_from_address' => 'system@edusphere.com',
            'mail_from_name' => 'EduSphere System',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('smtp.mailtrap.io', SystemSetting::getVal('smtp_host'));
        $this->assertEquals('2525', SystemSetting::getVal('smtp_port'));
        $this->assertEquals('tls', SystemSetting::getVal('smtp_encryption'));
        $this->assertEquals('testuser', SystemSetting::getVal('smtp_username'));
        $this->assertEquals('testpass', SystemSetting::getVal('smtp_password'));
        $this->assertEquals('system@edusphere.com', SystemSetting::getVal('mail_from_address'));
        $this->assertEquals('EduSphere System', SystemSetting::getVal('mail_from_name'));
    }

    public function test_outgoing_emails_are_logged_automatically_in_database()
    {
        // Assert email logs table is empty initially
        $this->assertEquals(0, EmailLog::count());

        // Send a test mail via standard log driver
        Mail::raw('This is a test notification body', function ($message) {
            $message->to('recipient@edusphere.local')
                    ->subject('Global Log Interceptor Test');
        });

        // Assert that the event listener captured the dispatch and created an EmailLog entry
        $this->assertEquals(1, EmailLog::count());
        $this->assertDatabaseHas('email_logs', [
            'recipient_email' => 'recipient@edusphere.local',
            'subject' => 'Global Log Interceptor Test',
            'status' => 'sent',
        ]);
    }

    public function test_super_admin_can_send_broadcast_to_all_school_admins()
    {
        // Assert initial logs count is 0
        $this->assertEquals(0, EmailLog::count());

        $response = $this->actingAs($this->superAdmin)->post('/super-admin/email-settings/send', [
            'target_type' => 'all_admins',
            'subject' => 'Platform Maintenance Window',
            'body' => 'We will perform scheduled updates this Saturday at 22:00 GMT.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify the email log was created in DB automatically by the listener
        $this->assertEquals(1, EmailLog::count());
        $this->assertDatabaseHas('email_logs', [
            'recipient_email' => 'admin@legacy.edu.gh',
            'subject' => 'Platform Maintenance Window',
            'status' => 'sent',
        ]);
    }

    public function test_super_admin_can_send_direct_email_to_specific_address()
    {
        $this->assertEquals(0, EmailLog::count());

        $response = $this->actingAs($this->superAdmin)->post('/super-admin/email-settings/send', [
            'target_type' => 'specific_user',
            'specific_email' => 'custom-target@external.com',
            'subject' => 'Direct Warning Notice',
            'body' => 'Your subscription is expiring tomorrow.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(1, EmailLog::count());
        $this->assertDatabaseHas('email_logs', [
            'recipient_email' => 'custom-target@external.com',
            'subject' => 'Direct Warning Notice',
            'status' => 'sent',
        ]);
    }

    public function test_non_super_admin_cannot_send_broadcast_emails()
    {
        $response = $this->actingAs($this->schoolAdmin)->post('/super-admin/email-settings/send', [
            'target_type' => 'all_users',
            'subject' => 'Unauthorized Broadcast Attempt',
            'body' => 'This should be blocked.',
        ]);

        $response->assertStatus(403);
    }
}
