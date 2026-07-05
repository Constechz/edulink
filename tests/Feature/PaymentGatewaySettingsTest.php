<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentGatewaySettingsTest extends TestCase
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
            'name' => 'Accra High Tech',
            'school_code' => 'AHT',
            'subdomain' => 'accra-high',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Ama Owner',
            'owner_email' => 'ama@accra-high.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $campus = Campus::create([
            'school_id' => $this->school->id,
            'name' => 'Central Accra',
            'is_main' => true,
            'is_active' => true,
        ]);

        $roleAdmin = Role::create(['name' => 'School Admin', 'slug' => 'school-admin', 'is_system' => true]);
        $roleSuper = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin', 'is_system' => true]);

        $this->schoolAdmin = User::create([
            'school_id' => $this->school->id,
            'campus_id' => $campus->id,
            'name' => 'Kojo Principal',
            'email' => 'kojo@accra-high.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Platform Operator',
            'email' => 'operator@edusphere.local',
            'password' => bcrypt('password123'),
            'role_id' => $roleSuper->id,
            'is_active' => true,
        ]);
    }

    public function test_super_admin_can_save_platform_payment_gateway_settings()
    {
        $response = $this->actingAs($this->superAdmin)->post(route('super-admin.settings.update'), [
            'website_builder_unlock_price' => '300.00',
            'platform_paystack_public_key' => 'pk_test_platform_paystack',
            'platform_paystack_secret_key' => 'sk_test_platform_paystack',
            'platform_paystack_enabled' => '1',
            'platform_flutterwave_public_key' => 'pk_test_platform_flutterwave',
            'platform_flutterwave_secret_key' => 'sk_test_platform_flutterwave',
            'platform_flutterwave_enabled' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('pk_test_platform_paystack', SystemSetting::getVal('platform_paystack_public_key'));
        $this->assertEquals('1', SystemSetting::getVal('platform_paystack_enabled'));
        $this->assertEquals('pk_test_platform_flutterwave', SystemSetting::getVal('platform_flutterwave_public_key'));
        $this->assertEquals('1', SystemSetting::getVal('platform_flutterwave_enabled'));
    }

    public function test_school_admin_can_save_tenant_payment_gateway_settings()
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('school.settings.payments'), [
            'paystack_public_key' => 'pk_live_school_paystack',
            'paystack_secret_key' => 'sk_live_school_paystack',
            'paystack_enabled' => '1',
            'flutterwave_public_key' => 'pk_live_school_flutterwave',
            'flutterwave_secret_key' => 'sk_live_school_flutterwave',
            'flutterwave_enabled' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->school->refresh();
        $gateways = $this->school->settings['payment_gateways'] ?? [];

        $this->assertEquals('pk_live_school_paystack', $gateways['paystack']['public_key'] ?? null);
        $this->assertEquals('1', $gateways['paystack']['enabled'] ?? null);
        $this->assertEquals('pk_live_school_flutterwave', $gateways['flutterwave']['public_key'] ?? null);
        $this->assertEquals('1', $gateways['flutterwave']['enabled'] ?? null);
    }
}
