<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\WebsitePage;
use App\Models\PageRevision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteAddonBillingTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $admin;
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

        $this->admin = User::create([
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

    public function test_super_admin_can_update_website_price()
    {
        $response = $this->actingAs($this->superAdmin)->post('/super-admin/settings/update', [
            'website_builder_unlock_price' => '250.00',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('250.00', SystemSetting::getVal('website_builder_unlock_price'));
    }

    public function test_school_website_builder_is_gated_and_restricted_by_default()
    {
        // 1. Gated Route Access: Redirect to billing dashboard
        $response = $this->actingAs($this->admin)->get('/school/website/pages');
        $response->assertRedirect(route('school.billing.index'));
        $response->assertSessionHas('error');

        // 2. Public view serves under construction page
        $response = $this->withHeaders(['X-School-ID' => $this->school->id])
            ->get('/public-site');
        $response->assertStatus(200);
        $response->assertViewIs('school.website.offline_placeholder');
    }

    public function test_school_can_unlock_website_builder_addon()
    {
        // Set setting price
        SystemSetting::setVal('website_builder_unlock_price', '500.00');

        // Unlock
        $response = $this->actingAs($this->admin)->post('/school/billing/unlock-website', [
            'gateway' => 'paystack',
        ]);

        $response->assertRedirect(route('school.billing.index'));
        $response->assertSessionHas('success');

        $this->school->refresh();
        $settings = $this->school->settings ?: [];
        $this->assertTrue(isset($settings['website_builder_unlocked']) && $settings['website_builder_unlocked'] == true);

        // Subsequent access to website pages route succeeds
        $response = $this->actingAs($this->admin)->get('/school/website/pages');
        $response->assertStatus(200);

        // Accessing public page no longer renders under construction if home page exists and is published
        $page = WebsitePage::create([
            'school_id' => $this->school->id,
            'title' => 'Home Page',
            'slug' => 'home',
            'page_type' => 'home',
            'is_homepage' => true,
            'created_by' => $this->admin->id,
        ]);
        
        PageRevision::create([
            'website_page_id' => $page->id,
            'revision_number' => 1,
            'html_content' => '<p>Welcome</p>',
            'css_content' => '',
            'components_json' => '[]',
            'is_current_draft' => true,
            'is_published' => true,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->withHeaders(['X-School-ID' => $this->school->id])
            ->get('/public-site');
        $response->assertStatus(200);
        $response->assertViewIs('school.website.public_theme');
    }
}
