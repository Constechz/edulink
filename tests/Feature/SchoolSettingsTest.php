<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $user;
    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::create([
            'name' => 'Standard',
            'price_monthly' => 200,
            'price_yearly' => 2000,
            'max_students' => 500,
            'max_staff' => 50,
            'max_campuses' => 2,
            'features' => ['dashboard', 'academics', 'students', 'finance', 'website_builder'],
            'is_active' => true,
        ]);

        $this->school = School::create([
            'name' => 'Original School Name',
            'school_code' => 'OSCH',
            'subdomain' => 'originalschool',
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

    public function test_can_view_settings_page(): void
    {
        $response = $this->actingAs($this->user)->get('/school/settings');
        $response->assertStatus(200);
    }

    public function test_can_update_general_profile(): void
    {
        $response = $this->actingAs($this->user)->post('/school/settings/profile', [
            'name' => 'Updated School Name',
            'short_name' => 'USCH',
            'phone' => '+233 24 555 1234',
            'email' => 'updated@school.edu.gh',
            'address' => 'Accra High Street',
            'region' => 'Greater Accra',
            'district' => 'Accra Metropolitan',
        ]);

        $response->assertRedirect();
        $this->school->refresh();
        $this->assertEquals('Updated School Name', $this->school->name);
        $this->assertEquals('USCH', $this->school->short_name);
        $this->assertEquals('Greater Accra', $this->school->region);
    }

    public function test_can_update_gateways(): void
    {
        $response = $this->actingAs($this->user)->post('/school/settings/gateway', [
            'smtp_host' => 'smtp.test.com',
            'smtp_port' => 587,
            'smtp_username' => 'testuser',
            'smtp_password' => 'testpass',
            'smtp_encryption' => 'tls',
            'smtp_from_address' => 'no-reply@school.edu.gh',
            'smtp_from_name' => 'School Alerts',
            'sms_provider' => 'arkesel',
            'sms_api_key' => 'arkesel-key-12345',
            'sms_sender_id' => 'SCHALERTS',
        ]);

        $response->assertRedirect();
        $this->school->refresh();

        $this->assertEquals('smtp.test.com', $this->school->email_config['host']);
        $this->assertEquals('arkesel', $this->school->sms_gateway_config['provider']);
        $this->assertEquals('SCHALERTS', $this->school->sms_gateway_config['sender_id']);
    }

    public function test_can_toggle_features_within_plan_limits(): void
    {
        // standard plan only allows: dashboard, academics, students, finance, website_builder
        // we try to enable: finance (allowed) and lms (not allowed by plan)
        $response = $this->actingAs($this->user)->post('/school/settings/features', [
            'modules' => [
                'finance' => 1,
                'lms' => 1, // requires Premium/Enterprise, should be filtered out
            ]
        ]);

        $response->assertRedirect();
        $this->school->refresh();

        $enabledModules = $this->school->settings['enabled_modules'] ?? [];
        
        $this->assertTrue(in_array('finance', $enabledModules));
        $this->assertFalse(in_array('lms', $enabledModules)); // blocked
    }
}
