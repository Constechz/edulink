<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use App\Models\ApiKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseThirteenApiDocumentationTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $user;
    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::create([
            'name' => 'Developer Standard',
            'price_monthly' => 300,
            'price_yearly' => 3000,
            'max_students' => 1000,
            'max_staff' => 50,
            'max_campuses' => 2,
            'is_active' => true,
        ]);

        $this->school = School::create([
            'name' => 'Tema International School',
            'school_code' => 'TIS',
            'subdomain' => 'tema',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Headmistress Mensah',
            'owner_email' => 'admin@tis.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $this->user = User::create([
            'school_id' => $this->school->id,
            'name' => 'Admin Mensah',
            'email' => 'admin@tis.edu.gh',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
    }

    /**
     * Test that guests cannot access the API keys management / docs index.
     */
    public function test_guest_is_redirected_from_developer_portal()
    {
        $response = $this->get(route('school.api-keys.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that authenticated school users can render the developer docs.
     */
    public function test_authenticated_user_can_access_developer_docs()
    {
        $response = $this->actingAs($this->user)->get(route('school.api-keys.index'));
        
        $response->assertStatus(200);
        $response->assertSee('API Credentials');
        $response->assertSee('Interactive API Reference');
        $response->assertSee('/scoring/scores/bulk');
        $response->assertSee('Authorization: Bearer');
    }

    /**
     * Test validation checks against endpoint routing.
     */
    public function test_api_endpoint_authentication_gates()
    {
        // 1. Without Token
        $response = $this->getJson('/api/v1/students');
        $response->assertStatus(401);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'Unauthorized: Missing or invalid token format.'
        ]);

        // 2. With Invalid Token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer edusphere_invalidkey12345'
        ])->getJson('/api/v1/students');
        
        $response->assertStatus(401);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'Unauthorized: Invalid or expired API key.'
        ]);

        // 3. With Valid Token
        $plainToken = 'edusphere_validtoken987654321';
        $tokenHash = hash('sha256', $plainToken);

        ApiKey::create([
            'school_id' => $this->school->id,
            'name' => 'Production Key',
            'token_hash' => $tokenHash,
            'is_active' => true,
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$plainToken}"
        ])->getJson('/api/v1/students');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => true
        ]);
    }
}
