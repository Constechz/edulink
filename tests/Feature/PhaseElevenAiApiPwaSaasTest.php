<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use App\Models\ApiKey;
use App\Models\Webhook;
use App\Models\WebhookDeliveryLog;
use App\Models\SmsCreditLedger;
use App\Models\GradingScale;
use App\Models\GradingScaleItem;
use App\Models\ScoringConfiguration;
use App\Models\StudentScore;
use App\Services\AiAnalyticsService;
use App\Services\WebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PhaseElevenAiApiPwaSaasTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $admin;
    protected $plan;
    protected $campus;
    protected $class;
    protected $year;
    protected $term;
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup subscription plans
        $this->plan = Plan::create([
            'name' => 'Standard Plus',
            'price_monthly' => 150,
            'price_yearly' => 1500,
            'max_students' => 100,
            'max_staff' => 20,
            'max_campuses' => 3,
            'features' => ['academic', 'finance', 'ai_insights'],
            'is_active' => true,
        ]);

        $this->school = School::create([
            'name' => 'Legacy Academy',
            'school_code' => 'LAAC',
            'subdomain' => 'legacy',
            'plan_id' => $this->plan->id,
            'owner_name' => 'Director Asante',
            'owner_email' => 'director@legacy.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $this->campus = Campus::create([
            'school_id' => $this->school->id,
            'name' => 'Main Legacy',
            'is_main' => true,
            'is_active' => true,
        ]);

        $roleAdmin = Role::firstOrCreate(['slug' => 'school-admin'], ['name' => 'School Admin', 'is_system' => true]);
        $roleSuper = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin', 'is_system' => true]);

        $this->admin = User::create([
            'school_id' => $this->school->id,
            'name' => 'Admin Asante',
            'email' => 'admin@legacy.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);

        $this->year = AcademicYear::create([
            'school_id' => $this->school->id,
            'name' => '2026/2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-30',
            'is_current' => true,
        ]);

        $this->term = Term::create([
            'school_id' => $this->school->id,
            'academic_year_id' => $this->year->id,
            'name' => 'Term 1',
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-15',
            'is_current' => true,
        ]);

        $this->class = SchoolClass::create([
            'school_id' => $this->school->id,
            'academic_year_id' => $this->year->id,
            'name' => 'Form 1 Gold',
            'level' => 'SHS',
            'numeric_level' => 1,
        ]);

        $this->subject = Subject::create([
            'school_id' => $this->school->id,
            'name' => 'Core Mathematics',
            'code' => 'MTH-C',
            'level' => 'SHS',
            'is_core' => true,
            'is_active' => true,
        ]);
    }

    /**
     * Test AI Analytics Service logic
     */
    public function test_ai_analytics_comments_and_risk_triggers(): void
    {
        $aiService = app(AiAnalyticsService::class);

        // Test Comments Generation
        $this->assertEquals("Outstanding academic performance. Keep up the excellent work.", $aiService->suggestReportCardComment(85));
        $this->assertEquals("Satisfactory work, showing consistent effort throughout the term.", $aiService->suggestReportCardComment(65));
        $this->assertEquals("Average performance. Encouraged to pay closer attention to class assignments.", $aiService->suggestReportCardComment(45));
        $this->assertEquals("Below expectations. Recommend additional tutoring and extra revision exercises.", $aiService->suggestReportCardComment(30));

        // Test at risk flags trigger logic (by binding tenant first)
        app()->instance('tenant', $this->school);

        $student = Student::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'student_id_number' => 'STD-001',
            'first_name' => 'Kofi',
            'last_name' => 'Annan',
            'date_of_birth' => '2010-05-15',
            'gender' => 'Male',
            'nationality' => 'Ghanaian',
            'current_class_id' => $this->class->id,
            'enrollment_date' => '2026-09-01',
            'status' => 'active',
        ]);

        // Trigger AI flags run
        $flags = $aiService->flagAtRiskStudents($this->school->id);
        
        // Assert no flags yet since no attendance records exist
        $this->assertEmpty($flags);
    }

    /**
     * Test REST API Authentication and Token Validation
     */
    public function test_api_token_auth_middleware_blocks_and_grants(): void
    {
        // 1. Request without token should return 401 Unauthorized
        $response = $this->getJson('/api/v1/students');
        $response->assertStatus(401);
        $response->assertJsonFragment(['success' => false]);

        // 2. Generate an API Key
        $plainToken = 'edusphere_testing1234567890';
        $tokenHash = hash('sha256', $plainToken);

        $apiKey = ApiKey::create([
            'school_id' => $this->school->id,
            'name' => 'Integration Key',
            'token_hash' => $tokenHash,
            'is_active' => true,
        ]);

        // 3. Request with valid token should succeed
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $plainToken
        ])->getJson('/api/v1/students');

        $response->assertStatus(200);
        $response->assertJsonFragment(['success' => true]);
    }

    /**
     * Test REST API Student creation endpoints and webhook dispatching.
     */
    public function test_api_student_creation_and_webhooks(): void
    {
        Http::fake();

        // 1. Create API key
        $plainToken = 'edusphere_webhook_test_token';
        ApiKey::create([
            'school_id' => $this->school->id,
            'name' => 'Webhook Key',
            'token_hash' => hash('sha256', $plainToken),
            'is_active' => true,
        ]);

        // 2. Setup Webhook subscription
        Webhook::create([
            'school_id' => $this->school->id,
            'name' => 'Student Enrolled Hub',
            'url' => 'https://mock-receiver.com/webhook',
            'secret' => 'supersecret',
            'subscribed_events' => ['student.enrolled'],
            'is_active' => true,
        ]);

        // 3. Make POST request to register a student
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $plainToken
        ])->postJson('/api/v1/students', [
            'first_name' => 'Kwame',
            'last_name' => 'Nkrumah',
            'date_of_birth' => '2012-09-21',
            'gender' => 'Male',
            'nationality' => 'Ghanaian',
            'campus_id' => $this->campus->id,
            'current_class_id' => $this->class->id,
            'enrollment_date' => '2026-09-01',
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['success' => true]);

        // 4. Verify Student is stored in DB
        $this->assertDatabaseHas('students', [
            'school_id' => $this->school->id,
            'first_name' => 'Kwame',
            'last_name' => 'Nkrumah',
        ]);

        // 5. Verify Webhook delivery was logged
        $this->assertDatabaseHas('webhook_delivery_logs', [
            'event_type' => 'student.enrolled',
        ]);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://mock-receiver.com/webhook' &&
                   $request->hasHeader('X-EduSphere-Signature') &&
                   $request->hasHeader('X-EduSphere-Event', 'student.enrolled');
        });
    }

    /**
     * Test Scoring Setup & bulk score storage API.
     */
    public function test_api_scoring_configs_and_bulk_scores(): void
    {
        // 1. Create API key
        $plainToken = 'edusphere_scoring_api_token';
        ApiKey::create([
            'school_id' => $this->school->id,
            'name' => 'Scoring Key',
            'token_hash' => hash('sha256', $plainToken),
            'is_active' => true,
        ]);

        // 2. Setup Grading Scale for SHS
        $scale = GradingScale::create([
            'school_id' => $this->school->id,
            'name' => 'SHS Scale',
            'level' => 'SHS',
            'is_active' => true,
        ]);

        GradingScaleItem::create([
            'grading_scale_id' => $scale->id,
            'grade' => 'A1',
            'grade_point' => 4.0,
            'min_score' => 80.0,
            'max_score' => 100.0,
            'description' => 'Excellent',
        ]);

        // 3. POST Scoring Configuration
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $plainToken
        ])->postJson('/api/v1/scoring/configurations', [
            'level' => 'SHS',
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'name' => 'SHS Math Standard Config',
            'class_score_max' => 50.0,
            'class_score_weight' => 30.0,
            'exam_score_max' => 100.0,
            'exam_score_weight' => 70.0,
            'rounding_method' => 'ROUND',
            'decimal_places' => 2,
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['success' => true]);

        // 4. Enroll Student
        $student = Student::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'student_id_number' => 'STD-002',
            'first_name' => 'Ama',
            'last_name' => 'Ghana',
            'date_of_birth' => '2011-03-06',
            'gender' => 'Female',
            'nationality' => 'Ghanaian',
            'current_class_id' => $this->class->id,
            'enrollment_date' => '2026-09-01',
            'status' => 'active',
        ]);

        // 5. POST Bulk Scores
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $plainToken
        ])->postJson('/api/v1/scoring/scores/bulk', [
            'scores' => [
                [
                    'student_id' => $student->id,
                    'class_id' => $this->class->id,
                    'subject_id' => $this->subject->id,
                    'term_id' => $this->term->id,
                    'academic_year_id' => $this->year->id,
                    'raw_exam_score' => 90.0,
                    'component_scores' => [30.0, 15.0],
                ]
            ]
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('student_scores', [
            'student_id' => $student->id,
            'grand_total' => 90.00,
            'grade' => 'A1',
        ]);
    }

    /**
     * Test SaaS billing upgrade and Super Admin control endpoints.
     */
    public function test_saas_billing_and_super_admin_overrides(): void
    {
        // 1. Perform mock gateway subscription upgrade
        $planPremium = Plan::create([
            'name' => 'SaaS Premium Suite',
            'price_monthly' => 300,
            'price_yearly' => 3000,
            'max_students' => 1000,
            'max_staff' => 100,
            'max_campuses' => 10,
            'features' => ['academic', 'finance', 'ai_insights', 'lms'],
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post('/school/billing/checkout', [
            'plan_id' => $planPremium->id,
            'cycle' => 'monthly',
            'gateway' => 'paystack',
        ]);

        $response->assertRedirect();
        
        $this->school->refresh();
        $this->assertEquals($planPremium->id, $this->school->plan_id);
        $this->assertEquals('active', $this->school->subscription_status);

        // 2. Perform Super Admin manually overriding subscription plan
        $roleSuper = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin', 'is_system' => true]);
        $superAdmin = User::create([
            'name' => 'EduSphere Super User',
            'email' => 'super@edusphere.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleSuper->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($superAdmin)->post("/super-admin/billing/override/{$this->school->id}", [
            'plan_id' => $this->plan->id,
            'subscription_status' => 'suspended',
        ]);

        $response->assertRedirect();
        
        $this->school->refresh();
        $this->assertEquals($this->plan->id, $this->school->plan_id);
        $this->assertEquals('suspended', $this->school->subscription_status);

        // 3. Super Admin adjustments of SMS units
        $response = $this->actingAs($superAdmin)->post('/super-admin/billing/sms', [
            'school_id' => $this->school->id,
            'credits' => 500,
            'action_type' => 'purchase',
            'note' => 'Initial allotment',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('sms_credit_ledger', [
            'school_id' => $this->school->id,
            'type' => 'purchase',
            'credits' => 500,
            'balance_after' => 500,
        ]);
    }
}
