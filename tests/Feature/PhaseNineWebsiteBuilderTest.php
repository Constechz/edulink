<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\PageRevision;
use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use App\Models\WebsitePage;
use App\Models\WebsiteSettings;
use App\Models\WebsiteMenu;
use App\Models\WebsiteMenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseNineWebsiteBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected $schoolA;
    protected $schoolB;
    protected $adminA;
    protected $adminB;
    protected $pageA;
    protected $pageB;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = Plan::create([
            'name' => 'Premium',
            'price_monthly' => 500,
            'price_yearly' => 5000,
            'max_students' => 1000,
            'max_staff' => 50,
            'max_campuses' => 5,
            'is_active' => true,
        ]);

        // School A
        $this->schoolA = School::create([
            'name' => 'Legacy High School',
            'school_code' => 'LHS',
            'subdomain' => 'legacy',
            'plan_id' => $plan->id,
            'owner_name' => 'Kofi Legacy',
            'owner_email' => 'kofi@legacy.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
            'settings' => ['website_builder_unlocked' => true],
        ]);

        $campusA = Campus::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Accra Campus',
            'is_main' => true,
            'is_active' => true,
        ]);

        $roleAdmin = Role::create(['name' => 'School Admin', 'slug' => 'school-admin', 'is_system' => true]);

        $this->adminA = User::create([
            'school_id' => $this->schoolA->id,
            'campus_id' => $campusA->id,
            'name' => 'Ama Admin',
            'email' => 'admin@legacy.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);

        // School B
        $this->schoolB = School::create([
            'name' => 'Apex Academy',
            'school_code' => 'APX',
            'subdomain' => 'apex',
            'plan_id' => $plan->id,
            'owner_name' => 'Ekow Apex',
            'owner_email' => 'ekow@apex.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
            'settings' => ['website_builder_unlocked' => true],
        ]);

        $campusB = Campus::create([
            'school_id' => $this->schoolB->id,
            'name' => 'Kumasi Campus',
            'is_main' => true,
            'is_active' => true,
        ]);

        $this->adminB = User::create([
            'school_id' => $this->schoolB->id,
            'campus_id' => $campusB->id,
            'name' => 'Yaw Admin',
            'email' => 'admin@apex.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);

        // Set up initial homepage for School A
        $this->pageA = WebsitePage::create([
            'school_id' => $this->schoolA->id,
            'title' => 'Home Page LHS',
            'slug' => 'home',
            'page_type' => 'home',
            'is_published' => false,
            'is_homepage' => true,
            'created_by' => $this->adminA->id,
        ]);

        PageRevision::create([
            'website_page_id' => $this->pageA->id,
            'revision_number' => 1,
            'html_content' => '<p>Welcome</p>',
            'css_content' => '',
            'components_json' => '[]',
            'is_current_draft' => true,
            'is_published' => false,
            'created_by' => $this->adminA->id,
        ]);

        // Set up initial homepage for School B
        $this->pageB = WebsitePage::create([
            'school_id' => $this->schoolB->id,
            'title' => 'Home Page Apex',
            'slug' => 'home',
            'page_type' => 'home',
            'is_published' => false,
            'is_homepage' => true,
            'created_by' => $this->adminB->id,
        ]);

        PageRevision::create([
            'website_page_id' => $this->pageB->id,
            'revision_number' => 1,
            'html_content' => '<p>Apex Home</p>',
            'css_content' => '',
            'components_json' => '[]',
            'is_current_draft' => true,
            'is_published' => false,
            'created_by' => $this->adminB->id,
        ]);
    }

    /**
     * Test pages CRUD operations.
     */
    public function test_pages_crud()
    {
        $this->actingAs($this->adminA);

        // 1. Create page
        $response = $this->post(route('school.website.pages.store'), [
            'title' => 'Admissions Guide',
            'slug' => 'admissions-guide',
            'page_type' => 'admissions',
            'meta_description' => 'Admissions guide details.',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('website_pages', [
            'school_id' => $this->schoolA->id,
            'title' => 'Admissions Guide',
            'slug' => 'admissions-guide',
        ]);

        $page = WebsitePage::where('slug', 'admissions-guide')->firstOrFail();

        // Assert initial blank revision was created
        $this->assertDatabaseHas('page_revisions', [
            'website_page_id' => $page->id,
            'revision_number' => 1,
            'is_current_draft' => true,
        ]);

        // 2. Attempt to delete homepage - should fail
        $response = $this->delete(route('school.website.pages.destroy', $this->pageA->id));
        $response->assertStatus(302);
        $this->assertDatabaseHas('website_pages', ['id' => $this->pageA->id]);

        // 3. Delete non-homepage page - should succeed
        $response = $this->delete(route('school.website.pages.destroy', $page->id));
        $response->assertStatus(302);
        $this->assertSoftDeleted('website_pages', ['id' => $page->id]);
    }

    /**
     * Test AJAX builder save drafts.
     */
    public function test_save_builder_draft()
    {
        $this->actingAs($this->adminA);

        $response = $this->post(route('school.website.pages.save', $this->pageA->id), [
            'html' => '<h1>New Content</h1>',
            'css' => 'h1 { color: red; }',
            'components' => '[{"type":"text","content":"New Content"}]',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('page_revisions', [
            'website_page_id' => $this->pageA->id,
            'revision_number' => 1,
            'html_content' => '<h1>New Content</h1>',
            'css_content' => 'h1 { color: red; }',
            'is_current_draft' => true,
        ]);
    }

    /**
     * Test publishing a page.
     */
    public function test_publish_page()
    {
        $this->actingAs($this->adminA);

        $response = $this->post(route('school.website.pages.publish', $this->pageA->id));
        $response->assertStatus(302);

        $this->pageA->refresh();
        $this->assertTrue($this->pageA->is_published);

        // The published revision is no longer current draft (it is live)
        $this->assertDatabaseHas('page_revisions', [
            'website_page_id' => $this->pageA->id,
            'revision_number' => 1,
            'is_published' => true,
            'is_current_draft' => false,
        ]);

        // A new draft revision is created dynamically to buffer future edits
        $this->assertDatabaseHas('page_revisions', [
            'website_page_id' => $this->pageA->id,
            'revision_number' => 2,
            'is_current_draft' => true,
            'is_published' => false,
        ]);
    }

    /**
     * Test draft rollback.
     */
    public function test_rollback_revision()
    {
        $this->actingAs($this->adminA);

        // Edit draft (revision 1)
        $response1 = $this->post(route('school.website.pages.save', $this->pageA->id), [
            'html' => '<h1>Revision 1</h1>',
            'components' => '[]',
        ]);
        $response1->assertStatus(200);

        // Publish to freeze revision 1 and trigger revision 2 draft creation
        $responsePublish = $this->post(route('school.website.pages.publish', $this->pageA->id));
        $responsePublish->assertStatus(302);

        // Edit new draft (revision 2)
        $response2 = $this->post(route('school.website.pages.save', $this->pageA->id), [
            'html' => '<h1>Revision 2 (Draft)</h1>',
            'components' => '[]',
        ]);
        $response2->assertStatus(200);

        $rev1 = PageRevision::where('website_page_id', $this->pageA->id)->where('revision_number', 1)->firstOrFail();

        // Rollback draft to revision 1 components
        $response = $this->post(route('school.website.revisions.rollback', $rev1->id));
        $response->assertStatus(302);

        // Assert a new draft (revision 3) exists matching revision 1 HTML content
        $this->assertDatabaseHas('page_revisions', [
            'website_page_id' => $this->pageA->id,
            'revision_number' => 3,
            'html_content' => '<h1>Revision 1</h1>',
            'is_current_draft' => true,
        ]);
    }

    /**
     * Test branding and settings updates.
     */
    public function test_settings_update()
    {
        $this->actingAs($this->adminA);

        $settings = WebsiteSettings::create([
            'school_id' => $this->schoolA->id,
            'site_name' => 'Old Name',
        ]);

        $response = $this->post(route('school.website.settings.update'), [
            'site_name' => 'Legacy Academy',
            'site_tagline' => 'Excellence Always',
            'primary_color' => '#112233',
            'secondary_color' => '#445566',
            'accent_color' => '#778899',
            'text_color' => '#000000',
            'bg_color' => '#FFFFFF',
            'heading_font' => 'Outfit',
            'body_font' => 'Inter',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('website_settings', [
            'school_id' => $this->schoolA->id,
            'site_name' => 'Legacy Academy',
            'site_tagline' => 'Excellence Always',
            'primary_color' => '#112233',
        ]);
    }

    /**
     * Test navigation builder tree updates.
     */
    public function test_navigation_update()
    {
        $this->actingAs($this->adminA);

        $menu = WebsiteMenu::create([
            'school_id' => $this->schoolA->id,
            'location' => 'header',
            'name' => 'Header Menu',
        ]);

        $response = $this->post(route('school.website.navigation.update'), [
            'menu_id' => $menu->id,
            'items' => [
                [
                    'label' => 'Homepage',
                    'page_id' => $this->pageA->id,
                    'url' => null,
                ],
                [
                    'label' => 'Google link',
                    'page_id' => null,
                    'url' => 'https://google.com',
                    'open_new_tab' => 1,
                ]
            ]
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('website_menu_items', [
            'menu_id' => $menu->id,
            'label' => 'Homepage',
            'page_id' => $this->pageA->id,
        ]);

        $this->assertDatabaseHas('website_menu_items', [
            'menu_id' => $menu->id,
            'label' => 'Google link',
            'url' => 'https://google.com',
            'open_new_tab' => true,
        ]);
    }

    /**
     * Test multi-tenant isolation.
     */
    public function test_website_tenant_isolation()
    {
        // Login as School A
        $this->actingAs($this->adminA);

        // 1. Attempt to view School B's builder page - forbidden
        $response = $this->get(route('school.website.pages.builder', $this->pageB->id));
        $response->assertStatus(403);

        // 2. Attempt to trigger GrapesJS save draft on School B's page
        $response = $this->post(route('school.website.pages.save', $this->pageB->id), [
            'html' => '<h1>Hacked</h1>',
            'components' => '[]',
        ]);
        $response->assertStatus(403);

        // 3. Attempt to publish School B's page
        $response = $this->post(route('school.website.pages.publish', $this->pageB->id));
        $response->assertStatus(403);
    }

    /**
     * Test public rendering without credentials.
     */
    public function test_public_rendering_no_auth()
    {
        // First publish the home page so it's live
        $this->actingAs($this->adminA);
        $this->post(route('school.website.pages.publish', $this->pageA->id));

        // Logout and hit visitor url
        auth()->logout();

        $response = $this->get(route('public.site.page', 'home') . '?school_id=' . $this->schoolA->id);
        
        $response->assertStatus(200);
        $response->assertSee('Home Page LHS'); // Page title
        $response->assertSee('Welcome'); // Revision html content
    }
}
