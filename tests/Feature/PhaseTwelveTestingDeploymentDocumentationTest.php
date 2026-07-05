<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class PhaseTwelveTestingDeploymentDocumentationTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $user;
    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::create([
            'name' => 'Enterprise Plan',
            'price_monthly' => 1000,
            'price_yearly' => 10000,
            'max_students' => 10000,
            'max_staff' => 500,
            'max_campuses' => 10,
            'is_active' => true,
        ]);

        $this->school = School::create([
            'name' => 'Accra Technical Academy',
            'school_code' => 'ATA',
            'subdomain' => 'ata',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Adow Akufo',
            'owner_email' => 'admin@ata.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $this->user = User::create([
            'school_id' => $this->school->id,
            'name' => 'Adow Akufo',
            'email' => 'admin@ata.edu.gh',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
    }

    /**
     * Test that guests are redirected from docs routes.
     */
    public function test_guest_is_redirected_from_docs()
    {
        $response = $this->get(route('school.docs.deployment'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('school.docs.testing'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that authenticated users can access the deployment and testing hubs.
     */
    public function test_authenticated_user_can_access_docs()
    {
        $response = $this->actingAs($this->user)->get(route('school.docs.deployment'));
        $response->assertStatus(200);
        $response->assertSee('Production Server Requirements');
        $response->assertSee('Nginx Virtual Host Setup');

        $response = $this->actingAs($this->user)->get(route('school.docs.testing'));
        $response->assertStatus(200);
        $response->assertSee('Automated Test Suites');
        $response->assertSee('OWASP Security Guidelines');
    }

    /**
     * Test that db:backup artisan command executes and outputs files successfully.
     */
    public function test_backup_artisan_command()
    {
        Storage::fake('local');

        $this->artisan('db:backup')
            ->expectsOutput('Starting database backup process...')
            ->assertExitCode(0);

        $files = Storage::disk('local')->files('backups');
        $this->assertNotEmpty($files);
        $this->assertTrue(str_contains($files[0], 'backup_'));
    }

    /**
     * Test that sys:health artisan command executes successfully.
     */
    public function test_health_artisan_command()
    {
        $this->artisan('sys:health')
            ->expectsOutput('Starting system health check...')
            ->expectsOutput('System Health Check Completed.')
            ->assertExitCode(0);
    }

    /**
     * Test scheduler registrations for Phase 12 console commands.
     */
    public function test_scheduler_registrations()
    {
        $schedule = app(Schedule::class);
        $events = collect($schedule->events());

        // Check db:backup registration
        $backupEvent = $events->first(function ($event) {
            return str_contains($event->command, 'db:backup');
        });
        $this->assertNotNull($backupEvent, 'db:backup is not registered in scheduler');
        $this->assertEquals('0 2 * * *', $backupEvent->expression, 'db:backup is not scheduled daily at 02:00');

        // Check sys:health registration
        $healthEvent = $events->first(function ($event) {
            return str_contains($event->command, 'sys:health');
        });
        $this->assertNotNull($healthEvent, 'sys:health is not registered in scheduler');
        $this->assertEquals('*/5 * * * *', $healthEvent->expression, 'sys:health is not scheduled every five minutes');
    }

    /**
     * Test secure session defaults in config.
     */
    public function test_session_security_settings()
    {
        $this->assertTrue(config('session.http_only'), 'Session cookie HttpOnly setting should be active.');
    }
}
