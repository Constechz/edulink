<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use App\Models\Message;
use App\Models\MessageTemplate;
use App\Models\MessageRecipient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolCommunicationEnhancementTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $admin;
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
            'name' => 'Communication Test Academy',
            'school_code' => 'CTA',
            'subdomain' => 'cta',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Kofi Comm',
            'owner_email' => 'kofi@cta.edu.gh',
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

        $this->admin = User::create([
            'school_id' => $this->school->id,
            'campus_id' => $campus->id,
            'name' => 'Ama Admin',
            'email' => 'admin@cta.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);
    }

    public function test_school_admin_can_access_communication_index_with_statistics()
    {
        // Create pre-existing message
        $message = Message::create([
            'school_id' => $this->school->id,
            'sender_user_id' => $this->admin->id,
            'channel' => 'email',
            'subject' => 'Exam Schedule',
            'body' => 'Examinations will start next week.',
            'status' => 'completed',
        ]);

        // Create recipient
        MessageRecipient::create([
            'message_id' => $message->id,
            'recipient_user_id' => $this->admin->id,
            'recipient_phone' => '+233240000000',
            'recipient_email' => $this->admin->email,
            'status' => 'sent',
        ]);

        $response = $this->actingAs($this->admin)->get(route('school.communication.index'));

        $response->assertStatus(200);
        $response->assertViewHas('totalSent', 1);
        $response->assertViewHas('emailCount', 1);
        $response->assertViewHas('smsCount', 0);
        $response->assertViewHas('totalRecipients', 1);

        $response->assertSee('Exam Schedule');
        $response->assertSee('Recipient Count:');
    }

    public function test_school_admin_can_store_and_destroy_templates()
    {
        // 1. Store template
        $response = $this->actingAs($this->admin)->post(route('school.communication.templates.store'), [
            'name' => 'General School Holidays template',
            'channel' => 'email',
            'subject' => 'Holidays Announcement',
            'body' => 'Please note that holidays will start from tomorrow.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('message_templates', [
            'school_id' => $this->school->id,
            'name' => 'General School Holidays template',
            'channel' => 'email',
            'subject' => 'Holidays Announcement',
        ]);

        $template = MessageTemplate::first();

        // 2. Destroy template
        $response = $this->actingAs($this->admin)->delete(route('school.communication.templates.destroy', $template->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('message_templates', [
            'id' => $template->id,
        ]);
    }
}
