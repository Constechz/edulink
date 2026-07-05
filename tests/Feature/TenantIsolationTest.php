<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Plan;
use App\Models\School;
use App\Http\Middleware\IdentifyTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a plan for testing
        $this->plan = Plan::create([
            'name' => 'Premium',
            'price_monthly' => 500,
            'price_yearly' => 5000,
            'max_students' => 1000,
            'max_staff' => 100,
            'max_campuses' => 5,
            'sms_credits_monthly' => 5000,
            'storage_gb' => 100,
            'is_active' => true,
        ]);
    }

    public function test_belongs_to_school_trait_auto_fills_school_id(): void
    {
        $school = School::create([
            'name' => 'School A',
            'school_code' => 'SCHA',
            'subdomain' => 'schoola',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Owner A',
            'owner_email' => 'owner@schoola.com',
            'is_active' => true,
        ]);

        app()->instance('tenant', $school);

        $campus = Campus::create([
            'name' => 'Main Campus',
            'code' => 'MC',
        ]);

        $this->assertEquals($school->id, $campus->school_id);
    }

    public function test_school_scope_isolates_tenant_queries(): void
    {
        $schoolA = School::create([
            'name' => 'School A',
            'school_code' => 'SCHA',
            'subdomain' => 'schoola',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Owner A',
            'owner_email' => 'owner@schoola.com',
            'is_active' => true,
        ]);

        $schoolB = School::create([
            'name' => 'School B',
            'school_code' => 'SCHB',
            'subdomain' => 'schoolb',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Owner B',
            'owner_email' => 'owner@schoolb.com',
            'is_active' => true,
        ]);

        // Create campus for School A
        app()->instance('tenant', $schoolA);
        $campusA = Campus::create([
            'name' => 'Campus A',
            'code' => 'CA',
        ]);

        // Create campus for School B
        app()->instance('tenant', $schoolB);
        $campusB = Campus::create([
            'name' => 'Campus B',
            'code' => 'CB',
        ]);

        // Retrieve while School A is active tenant
        app()->instance('tenant', $schoolA);
        $campusesForA = Campus::all();

        $this->assertCount(1, $campusesForA);
        $this->assertEquals('Campus A', $campusesForA->first()->name);

        // Retrieve while School B is active tenant
        app()->instance('tenant', $schoolB);
        $campusesForB = Campus::all();

        $this->assertCount(1, $campusesForB);
        $this->assertEquals('Campus B', $campusesForB->first()->name);
    }

    public function test_tenant_middleware_resolves_school_from_domain_and_headers(): void
    {
        $school = School::create([
            'name' => 'School A',
            'school_code' => 'SCHA',
            'subdomain' => 'schoola',
            'custom_domain' => 'schoola.edu.gh',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Owner A',
            'owner_email' => 'owner@schoola.com',
            'is_active' => true,
        ]);

        $middleware = new IdentifyTenant();
        $request = Request::create('/school/dashboard', 'GET');
        $request->setLaravelSession(app('session')->driver('array'));
        $request->headers->set('X-School-ID', $school->id);

        $middleware->handle($request, function ($req) {
            $this->assertTrue(app()->bound('tenant'));
            $this->assertEquals('School A', app('tenant')->name);
            return response('OK');
        });

        // Test custom domain resolution
        $requestCustomDomain = Request::create('/school/dashboard', 'GET');
        $requestCustomDomain->setLaravelSession(app('session')->driver('array'));
        $requestCustomDomain->server->set('HTTP_HOST', 'schoola.edu.gh');

        $middleware->handle($requestCustomDomain, function ($req) {
            $this->assertTrue(app()->bound('tenant'));
            $this->assertEquals('School A', app('tenant')->name);
            return response('OK');
        });
    }
}
