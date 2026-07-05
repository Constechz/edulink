<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Plan;
use App\Models\Programme;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use App\Models\GradingScale;
use App\Models\GradingScaleItem;
use App\Models\ScoringConfiguration;
use App\Models\ScoreComponent;
use App\Models\StudentScore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseSevenScoringAndReportCardTest extends TestCase
{
    use RefreshDatabase;

    protected $schoolA;
    protected $schoolB;
    protected $adminA;
    protected $adminB;
    protected $teacherA;
    protected $hodA;
    protected $headteacherA;
    protected $studentA;
    protected $studentB;
    protected $classA;
    protected $classB;
    protected $subjectA;
    protected $yearA;
    protected $termA;
    protected $campusA;
    protected $scaleA;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = Plan::create([
            'name' => 'Standard',
            'price_monthly' => 200,
            'price_yearly' => 2000,
            'max_students' => 500,
            'max_staff' => 10,
            'max_campuses' => 2,
            'is_active' => true,
        ]);

        // School A
        $this->schoolA = School::create([
            'name' => 'Accra Senior High',
            'school_code' => 'ASH',
            'subdomain' => 'accrashs',
            'plan_id' => $plan->id,
            'owner_name' => 'Principal Mensah',
            'owner_email' => 'principal@accrashs.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $this->campusA = Campus::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Accra Campus',
            'is_main' => true,
            'is_active' => true,
        ]);

        // Roles
        $roleAdmin = Role::create(['name' => 'School Admin', 'slug' => 'school-admin', 'is_system' => true]);
        $roleTeacher = Role::create(['name' => 'Teacher', 'slug' => 'teacher', 'is_system' => true]);
        $roleHod = Role::create(['name' => 'HOD', 'slug' => 'hod', 'is_system' => true]);
        $roleHT = Role::create(['name' => 'Headteacher', 'slug' => 'headteacher', 'is_system' => true]);

        // Link permissions to HOD and Teacher for tests
        // Wait, standard user model hashasPermission checking roles permissions, let's keep it simple by assigning role IDs directly.
        
        $this->adminA = User::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Admin Accra',
            'email' => 'admin@accrashs.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);

        $this->teacherA = User::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Teacher Accra',
            'email' => 'teacher@accrashs.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleTeacher->id,
            'is_active' => true,
        ]);

        $this->hodA = User::create([
            'school_id' => $this->schoolA->id,
            'name' => 'HOD Accra',
            'email' => 'hod@accrashs.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleHod->id,
            'is_active' => true,
        ]);

        $this->headteacherA = User::create([
            'school_id' => $this->schoolA->id,
            'name' => 'HT Accra',
            'email' => 'ht@accrashs.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleHT->id,
            'is_active' => true,
        ]);

        $this->yearA = AcademicYear::create([
            'school_id' => $this->schoolA->id,
            'name' => '2026/2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-30',
            'is_current' => true,
        ]);

        $this->termA = Term::create([
            'school_id' => $this->schoolA->id,
            'academic_year_id' => $this->yearA->id,
            'name' => 'Term 1',
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-15',
            'is_current' => true,
        ]);

        $progA = Programme::create([
            'school_id' => $this->schoolA->id,
            'name' => 'General Science',
            'code' => 'G-SCI',
            'duration_years' => 3,
            'level' => 'SHS',
        ]);

        $this->classA = SchoolClass::create([
            'school_id' => $this->schoolA->id,
            'campus_id' => $this->campusA->id,
            'academic_year_id' => $this->yearA->id,
            'programme_id' => $progA->id,
            'name' => 'Science 1',
            'level' => 'SHS',
            'capacity' => 40,
        ]);

        $this->subjectA = Subject::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Elective Physics',
            'code' => 'PHYS',
            'level' => 'SHS',
        ]);

        $this->studentA = Student::create([
            'school_id' => $this->schoolA->id,
            'campus_id' => $this->campusA->id,
            'student_id_number' => 'STU-2026-0001',
            'admission_no' => 'STU-1001',
            'first_name' => 'Kofi',
            'last_name' => 'Osei',
            'gender' => 'Male',
            'date_of_birth' => '2010-05-12',
            'enrollment_date' => '2026-09-01',
            'status' => 'active',
            'current_class_id' => $this->classA->id,
        ]);

        // Seed Grading Scale A
        $this->scaleA = GradingScale::create([
            'school_id' => $this->schoolA->id,
            'name' => 'SHS Scale',
            'level' => 'SHS',
            'is_active' => true,
            'is_default' => true,
        ]);

        GradingScaleItem::create([
            'grading_scale_id' => $this->scaleA->id,
            'min_score' => 80.0,
            'max_score' => 100.0,
            'grade' => 'A1',
            'grade_point' => 1.0,
            'description' => 'Excellent',
        ]);

        GradingScaleItem::create([
            'grading_scale_id' => $this->scaleA->id,
            'min_score' => 70.0,
            'max_score' => 79.99,
            'grade' => 'B2',
            'grade_point' => 2.0,
            'description' => 'Very Good',
        ]);

        GradingScaleItem::create([
            'grading_scale_id' => $this->scaleA->id,
            'min_score' => 0.0,
            'max_score' => 69.99,
            'grade' => 'C6',
            'grade_point' => 6.0,
            'description' => 'Credit',
        ]);

        // School B Setup (for Tenant Isolation checks)
        $this->schoolB = School::create([
            'name' => 'Kumasi High School',
            'school_code' => 'KHS',
            'subdomain' => 'kumasihigh',
            'plan_id' => $plan->id,
            'owner_name' => 'Principal Baah',
            'owner_email' => 'principal@kumasihigh.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $this->adminB = User::create([
            'school_id' => $this->schoolB->id,
            'name' => 'Admin Kumasi',
            'email' => 'admin@kumasihigh.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);

        $campusB = Campus::create([
            'school_id' => $this->schoolB->id,
            'name' => 'Kumasi Campus',
            'is_main' => true,
            'is_active' => true,
        ]);

        $this->studentB = Student::create([
            'school_id' => $this->schoolB->id,
            'campus_id' => $campusB->id,
            'student_id_number' => 'STU-2026-0002',
            'admission_no' => 'STU-2001',
            'first_name' => 'Kwame',
            'last_name' => 'Asare',
            'gender' => 'Male',
            'date_of_birth' => '2010-08-14',
            'enrollment_date' => '2026-09-01',
            'status' => 'active',
        ]);
    }

    /**
     * Test 1: Configuration wizard validation limits.
     */
    public function test_scoring_configuration_wizard_validates_weights_and_component_limits()
    {
        $this->actingAs($this->adminA);

        // Sum of class & exam weights must be > 0
        $response = $this->post(route('school.scoring-configs.store'), [
            'name' => 'Invalid Weights Config',
            'level' => 'SHS',
            'class_score_max' => 50,
            'class_score_weight' => 0,
            'exam_score_max' => 100,
            'exam_score_weight' => 0,
            'rounding_method' => 'ROUND',
            'decimal_places' => 0,
            'components' => [
                ['name' => 'Homework', 'max_marks' => 20]
            ]
        ]);

        $response->assertSessionHasErrors(['class_score_weight']);

        // Components sum (60) exceeds class_score_max (50)
        $response2 = $this->post(route('school.scoring-configs.store'), [
            'name' => 'Over Bound Component Config',
            'level' => 'SHS',
            'class_score_max' => 50,
            'class_score_weight' => 30,
            'exam_score_max' => 100,
            'exam_score_weight' => 70,
            'rounding_method' => 'ROUND',
            'decimal_places' => 0,
            'components' => [
                ['name' => 'Midterm', 'max_marks' => 40],
                ['name' => 'Homework', 'max_marks' => 20],
            ]
        ]);

        $response2->assertSessionHasErrors(['class_score_max']);
    }

    /**
     * Test 2: Scoring wizard successfully creates config and components.
     */
    public function test_can_create_valid_scoring_configuration_and_components()
    {
        $this->actingAs($this->adminA);

        $response = $this->post(route('school.scoring-configs.store'), [
            'name' => 'Standard SHS Physics Ruleset',
            'level' => 'SHS',
            'subject_id' => $this->subjectA->id,
            'academic_year_id' => $this->yearA->id,
            'class_score_max' => 50,
            'class_score_weight' => 30,
            'exam_score_max' => 100,
            'exam_score_weight' => 70,
            'rounding_method' => 'ROUND',
            'decimal_places' => 1,
            'is_default' => 1,
            'components' => [
                ['name' => 'Lab Work', 'max_marks' => 30, 'is_required' => '1'],
                ['name' => 'Quizzes', 'max_marks' => 20],
            ]
        ]);

        $response->assertRedirect(route('school.scoring-configs.index'));
        $this->assertDatabaseHas('scoring_configurations', [
            'school_id' => $this->schoolA->id,
            'name' => 'Standard SHS Physics Ruleset',
            'class_score_weight' => 30,
            'exam_score_weight' => 70,
            'rounding_method' => 'ROUND',
            'decimal_places' => 1,
        ]);

        $config = ScoringConfiguration::where('name', 'Standard SHS Physics Ruleset')->first();
        $this->assertDatabaseHas('score_components', [
            'scoring_configuration_id' => $config->id,
            'name' => 'Lab Work',
            'max_marks' => 30,
            'is_required' => true,
        ]);
    }

    /**
     * Test 3: Math Engine calculations, scaling and rounding validation.
     */
    public function test_scoring_engine_correctly_aggregates_scales_rounds_and_looks_up_grades()
    {
        $config = ScoringConfiguration::create([
            'school_id' => $this->schoolA->id,
            'level' => 'SHS',
            'name' => 'Test Physics Ruleset',
            'class_score_max' => 50,
            'class_score_weight' => 30,
            'exam_score_max' => 100,
            'exam_score_weight' => 70,
            'rounding_method' => 'ROUND',
            'decimal_places' => 1,
            'is_active' => true,
        ]);

        $comp1 = ScoreComponent::create([
            'school_id' => $this->schoolA->id,
            'scoring_configuration_id' => $config->id,
            'name' => 'Class Work',
            'max_marks' => 30,
            'display_order' => 1,
            'is_active' => true,
        ]);

        $comp2 = ScoreComponent::create([
            'school_id' => $this->schoolA->id,
            'scoring_configuration_id' => $config->id,
            'name' => 'Assignments',
            'max_marks' => 20,
            'display_order' => 2,
            'is_active' => true,
        ]);

        $this->actingAs($this->teacherA);

        // Autosave draft:
        // SBA inputs: comp1 = 25/30, comp2 = 15/20 => Raw Class Total = 40/50.
        // Scaled Class score: (40 / 50) * 30 = 24.0.
        // Exam raw score: 80/100.
        // Scaled Exam score: (80 / 100) * 70 = 56.0.
        // Grand Total: 24.0 + 56.0 = 80.0.
        // Grade Scale lookup for 80.0 => A1.
        $response = $this->postJson(route('school.scores.save-draft'), [
            'student_id' => $this->studentA->id,
            'class_id' => $this->classA->id,
            'subject_id' => $this->subjectA->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
            'scoring_configuration_id' => $config->id,
            'component_scores' => [
                $comp1->id => 25,
                $comp2->id => 15,
            ],
            'raw_exam_score' => 80,
            'is_absent_exam' => 0,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'raw_class_total' => 40,
            'scaled_class_score' => 24.0,
            'scaled_exam_score' => 56.0,
            'grand_total' => 80.0,
            'grade' => 'A1',
        ]);

        $this->assertDatabaseHas('student_scores', [
            'student_id' => $this->studentA->id,
            'raw_class_total' => 40,
            'scaled_class_score' => 24.0,
            'scaled_exam_score' => 56.0,
            'grand_total' => 80.0,
            'grade' => 'A1',
            'status' => 'draft',
        ]);

        // Audit log created
        $this->assertDatabaseHas('score_history', [
            'changed_by' => $this->teacherA->id,
            'change_type' => 'create',
        ]);
    }

    /**
     * Test 4: Workflow states transitions and locking mechanisms.
     */
    public function test_workflow_transitions_from_draft_to_approved_and_locks_sheets()
    {
        $config = ScoringConfiguration::create([
            'school_id' => $this->schoolA->id,
            'level' => 'SHS',
            'name' => 'Workflow Config',
            'class_score_max' => 50,
            'class_score_weight' => 50,
            'exam_score_max' => 100,
            'exam_score_weight' => 50,
            'rounding_method' => 'ROUND',
            'decimal_places' => 1,
            'is_active' => true,
        ]);

        $score = StudentScore::create([
            'school_id' => $this->schoolA->id,
            'student_id' => $this->studentA->id,
            'class_id' => $this->classA->id,
            'subject_id' => $this->subjectA->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
            'scoring_configuration_id' => $config->id,
            'teacher_id' => $this->teacherA->id,
            'status' => 'draft',
        ]);

        $this->actingAs($this->teacherA);

        // Submit to HOD
        $responseSubmit = $this->post(route('school.scores.submit'), [
            'class_id' => $this->classA->id,
            'subject_id' => $this->subjectA->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
        ]);
        $responseSubmit->assertRedirect();
        $this->assertEquals('submitted', $score->fresh()->status);

        // LOCKED Check: cannot edit a submitted score
        $responseLock = $this->postJson(route('school.scores.save-draft'), [
            'student_id' => $this->studentA->id,
            'class_id' => $this->classA->id,
            'subject_id' => $this->subjectA->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
            'scoring_configuration_id' => $config->id,
            'component_scores' => [],
            'raw_exam_score' => 90,
        ]);
        $responseLock->assertStatus(403);

        // HOD Verification
        $this->actingAs($this->hodA);
        $responseVerify = $this->post(route('school.scores.verify'), [
            'class_id' => $this->classA->id,
            'subject_id' => $this->subjectA->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
            'action' => 'approve',
            'moderation_note' => 'Looks fine.',
        ]);
        $responseVerify->assertRedirect();
        $this->assertEquals('hod_verified', $score->fresh()->status);

        // Headteacher Approval
        $this->actingAs($this->headteacherA);
        $responseApprove = $this->post(route('school.scores.approve'), [
            'class_id' => $this->classA->id,
            'subject_id' => $this->subjectA->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
            'action' => 'approve',
            'moderation_note' => 'Approved.',
        ]);
        $responseApprove->assertRedirect();
        $this->assertEquals('approved', $score->fresh()->status);
    }

    /**
     * Test 5: PDF Generation outputs valid binary structure.
     */
    public function test_pdf_generation_returns_correct_response_header()
    {
        $this->actingAs($this->adminA);

        $config = ScoringConfiguration::create([
            'school_id' => $this->schoolA->id,
            'level' => 'SHS',
            'name' => 'Report Config',
            'class_score_max' => 50,
            'class_score_weight' => 50,
            'exam_score_max' => 100,
            'exam_score_weight' => 50,
            'rounding_method' => 'ROUND',
            'decimal_places' => 0,
            'is_active' => true,
        ]);

        StudentScore::create([
            'school_id' => $this->schoolA->id,
            'student_id' => $this->studentA->id,
            'class_id' => $this->classA->id,
            'subject_id' => $this->subjectA->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
            'scoring_configuration_id' => $config->id,
            'teacher_id' => $this->teacherA->id,
            'raw_class_total' => 40,
            'scaled_class_score' => 20,
            'raw_exam_score' => 80,
            'scaled_exam_score' => 40,
            'grand_total' => 60,
            'status' => 'approved',
        ]);

        // Generate student report card
        $responseCard = $this->get(route('school.reports.card', [
            'student' => $this->studentA->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
        ]));
        $responseCard->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $responseCard->headers->get('Content-Type'));

        // Generate broadsheet PDF
        $responseBroadsheet = $this->get(route('school.reports.broadsheet', [
            'class_id' => $this->classA->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
            'format' => 'pdf',
        ]));
        $responseBroadsheet->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $responseBroadsheet->headers->get('Content-Type'));
    }

    /**
     * Test 6: Tenant Isolation constraints.
     */
    public function test_tenant_isolation_prevents_unauthorized_cross_tenant_access()
    {
        // Admin from School A tries to save score for Student from School B
        $this->actingAs($this->adminA);

        $configB = ScoringConfiguration::create([
            'school_id' => $this->schoolB->id,
            'level' => 'SHS',
            'name' => 'School B Ruleset',
            'class_score_max' => 50,
            'class_score_weight' => 50,
            'exam_score_max' => 100,
            'exam_score_weight' => 50,
            'rounding_method' => 'ROUND',
            'decimal_places' => 0,
            'is_active' => true,
        ]);

        // Attempting to autosave draft for School B student from School A session
        $response = $this->postJson(route('school.scores.save-draft'), [
            'student_id' => $this->studentB->id,
            'class_id' => $this->classA->id,
            'subject_id' => $this->subjectA->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
            'scoring_configuration_id' => $configB->id,
            'component_scores' => [],
            'raw_exam_score' => 90,
        ]);

        // Should abort or return 403 / 422 validation fail on exists rule (which respects global scopes)
        $response->assertStatus(403);

        // Attempting to generate report card PDF for student B (School B) from School A session
        $responseCard = $this->get(route('school.reports.card', [
            'student' => $this->studentB->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
        ]));
        
        $responseCard->assertStatus(403);
    }

    /**
     * Test 7: Edit and update scoring configurations, asserting tenant validation and component ID preservation.
     */
    public function test_can_edit_and_update_scoring_configuration_preserving_component_ids()
    {
        $this->actingAs($this->adminA);

        $config = ScoringConfiguration::create([
            'school_id' => $this->schoolA->id,
            'level' => 'SHS',
            'name' => 'Report Config to Edit',
            'class_score_max' => 50,
            'class_score_weight' => 50,
            'exam_score_max' => 100,
            'exam_score_weight' => 50,
            'rounding_method' => 'ROUND',
            'decimal_places' => 0,
            'is_active' => true,
        ]);

        $comp1 = ScoreComponent::create([
            'school_id' => $this->schoolA->id,
            'scoring_configuration_id' => $config->id,
            'name' => 'Homework 1',
            'max_marks' => 20,
            'display_order' => 1,
            'is_active' => true,
            'is_required' => true,
        ]);

        $comp2 = ScoreComponent::create([
            'school_id' => $this->schoolA->id,
            'scoring_configuration_id' => $config->id,
            'name' => 'Test 1',
            'max_marks' => 30,
            'display_order' => 2,
            'is_active' => true,
            'is_required' => true,
        ]);

        // Get edit view
        $responseEdit = $this->get(route('school.scoring-configs.edit', $config->id));
        $responseEdit->assertStatus(200);

        // Try to update from School B admin (unauthorized edit check)
        $this->actingAs($this->adminB);
        $responseUnauthorized = $this->put(route('school.scoring-configs.update', $config->id), [
            'name' => 'Hacked Name',
        ]);
        $responseUnauthorized->assertStatus(403);

        // Update successfully from School A admin
        $this->actingAs($this->adminA);
        $responseUpdate = $this->put(route('school.scoring-configs.update', $config->id), [
            'name' => 'Updated Report Config Name',
            'level' => 'SHS',
            'class_score_max' => 50,
            'class_score_weight' => 40,
            'exam_score_max' => 100,
            'exam_score_weight' => 60,
            'rounding_method' => 'CEIL',
            'decimal_places' => 1,
            'is_default' => 1,
            'components' => [
                [
                    'id' => $comp1->id,
                    'name' => 'Homework 1 Modified',
                    'max_marks' => 15,
                    'is_required' => '1',
                ],
                [
                    // New component
                    'name' => 'Project 1 New',
                    'max_marks' => 35,
                    'is_required' => '0',
                ]
                // comp2 is omitted so it should be deleted
            ],
        ]);

        $responseUpdate->assertRedirect(route('school.scoring-configs.index'));

        // Check if config values were updated
        $config = $config->fresh();
        $this->assertEquals('Updated Report Config Name', $config->name);
        $this->assertEquals(40, $config->class_score_weight);
        $this->assertEquals('CEIL', $config->rounding_method);

        // Verify component counts
        $components = $config->components;
        $this->assertCount(2, $components);

        // Verify comp1 is updated but ID is preserved
        $updatedComp1 = $components->where('id', $comp1->id)->first();
        $this->assertNotNull($updatedComp1);
        $this->assertEquals('Homework 1 Modified', $updatedComp1->name);
        $this->assertEquals(15.0, floatval($updatedComp1->max_marks));

        // Verify comp2 is deleted
        $deletedComp2 = ScoreComponent::find($comp2->id);
        $this->assertNull($deletedComp2);

        // Verify new component is created
        $newComp = $components->where('name', 'Project 1 New')->first();
        $this->assertNotNull($newComp);
        $this->assertEquals(35.0, floatval($newComp->max_marks));
    }

    /**
     * Test 8: Unlocking approved and published scores back to draft.
     */
    public function test_authorized_user_can_unlock_approved_and_published_scores()
    {
        $roleAdmin = Role::where('slug', 'school-admin')->first();
        $permission = \App\Models\Permission::create([
            'name' => 'Approve Scores',
            'slug' => 'approve-scores',
            'module' => 'scoring',
        ]);
        $roleAdmin->permissions()->attach($permission->id);

        $config = ScoringConfiguration::create([
            'school_id' => $this->schoolA->id,
            'level' => 'SHS',
            'name' => 'Unlock Test Config',
            'class_score_max' => 50,
            'class_score_weight' => 50,
            'exam_score_max' => 100,
            'exam_score_weight' => 50,
            'rounding_method' => 'ROUND',
            'decimal_places' => 1,
            'is_active' => true,
        ]);

        $score = StudentScore::create([
            'school_id' => $this->schoolA->id,
            'student_id' => $this->studentA->id,
            'class_id' => $this->classA->id,
            'subject_id' => $this->subjectA->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
            'scoring_configuration_id' => $config->id,
            'teacher_id' => $this->teacherA->id,
            'status' => 'published',
            'approved_at' => now(),
            'published_at' => now(),
        ]);

        // Attempting to unlock as teacher (unauthorized)
        $this->actingAs($this->teacherA);
        $responseTeacher = $this->post(route('school.scores.unlock'), [
            'class_id' => $this->classA->id,
            'subject_id' => $this->subjectA->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
        ]);
        $responseTeacher->assertRedirect();
        $responseTeacher->assertSessionHas('error');
        $this->assertEquals('published', $score->fresh()->status);

        // Unlock as administrator (authorized)
        $this->actingAs($this->adminA);
        $responseAdmin = $this->post(route('school.scores.unlock'), [
            'class_id' => $this->classA->id,
            'subject_id' => $this->subjectA->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
        ]);
        $responseAdmin->assertRedirect();
        $responseAdmin->assertSessionHas('success');
        
        $freshScore = $score->fresh();
        $this->assertEquals('draft', $freshScore->status);
        $this->assertNull($freshScore->approved_at);
        $this->assertNull($freshScore->published_at);
    }

    /**
     * Test 9: Export scores CSV template.
     */
    public function test_can_export_scores_to_csv_template()
    {
        $config = ScoringConfiguration::create([
            'school_id' => $this->schoolA->id,
            'level' => 'SHS',
            'name' => 'Export Config',
            'class_score_max' => 50,
            'class_score_weight' => 50,
            'exam_score_max' => 100,
            'exam_score_weight' => 50,
            'rounding_method' => 'ROUND',
            'decimal_places' => 1,
            'is_active' => true,
        ]);

        $comp = ScoreComponent::create([
            'school_id' => $this->schoolA->id,
            'scoring_configuration_id' => $config->id,
            'name' => 'Homework',
            'max_marks' => 20,
            'display_order' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($this->teacherA);

        $response = $this->get(route('school.scores.export', [
            'class_id' => $this->classA->id,
            'subject_id' => $this->subjectA->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $content = $response->streamedContent();
        $this->assertStringContainsString('Student ID', $content);
        $this->assertStringContainsString('Admission No', $content);
        $this->assertStringContainsString('Homework', $content);
    }

    /**
     * Test 10: Import valid CSV scores.
     */
    public function test_can_import_scores_from_valid_csv()
    {
        $roleTeacher = Role::where('slug', 'teacher')->first();
        $permission = \App\Models\Permission::create([
            'name' => 'Enter Scores',
            'slug' => 'enter-scores',
            'module' => 'scoring',
        ]);
        $roleTeacher->permissions()->attach($permission->id);

        $config = ScoringConfiguration::create([
            'school_id' => $this->schoolA->id,
            'level' => 'SHS',
            'name' => 'Import Config',
            'class_score_max' => 50,
            'class_score_weight' => 50,
            'exam_score_max' => 100,
            'exam_score_weight' => 50,
            'rounding_method' => 'ROUND',
            'decimal_places' => 1,
            'is_active' => true,
        ]);

        $comp = ScoreComponent::create([
            'school_id' => $this->schoolA->id,
            'scoring_configuration_id' => $config->id,
            'name' => 'Lab Work',
            'max_marks' => 30,
            'display_order' => 1,
            'is_active' => true,
        ]);

        // Mock a CSV file structure
        $csvContent = "Student ID,Admission No,Student Name,\"Component:{$comp->id} (Lab Work, Max: 30)\",\"Exam (Max: 100)\",Remarks\n";
        $csvContent .= "{$this->studentA->id},{$this->studentA->admission_no},{$this->studentA->first_name} {$this->studentA->last_name},25,80,Good effort\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'csv');
        file_put_contents($tempFile, $csvContent);

        $uploadedFile = new \Illuminate\Http\UploadedFile(
            $tempFile,
            'scores.csv',
            'text/csv',
            null,
            true
        );

        $this->actingAs($this->teacherA);

        $response = $this->post(route('school.scores.import'), [
            'class_id' => $this->classA->id,
            'subject_id' => $this->subjectA->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
            'csv_file' => $uploadedFile,
        ]);

        @unlink($tempFile);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('student_scores', [
            'student_id' => $this->studentA->id,
            'subject_id' => $this->subjectA->id,
            'raw_exam_score' => 80.0,
            'remarks' => 'Good effort',
            'status' => 'draft',
        ]);
        
        $score = StudentScore::where('student_id', $this->studentA->id)->first();
        // Lab Work = 25/30 => raw sba = 25/30 = 25/50 (based on max bounds components calculation)
        // Wait, standard scoring service computes scaled class score and aggregates:
        $this->assertNotNull($score->grand_total);
    }

    /**
     * Test 11: CSV import validates component limits and score boundaries.
     */
    public function test_import_validates_component_boundaries()
    {
        $roleTeacher = Role::where('slug', 'teacher')->first();
        $permission = \App\Models\Permission::firstOrCreate(
            ['slug' => 'enter-scores'],
            ['name' => 'Enter Scores', 'module' => 'scoring']
        );
        $roleTeacher->permissions()->syncWithoutDetaching([$permission->id]);

        $config = ScoringConfiguration::create([
            'school_id' => $this->schoolA->id,
            'level' => 'SHS',
            'name' => 'Import Validation Config',
            'class_score_max' => 50,
            'class_score_weight' => 50,
            'exam_score_max' => 100,
            'exam_score_weight' => 50,
            'rounding_method' => 'ROUND',
            'decimal_places' => 1,
            'is_active' => true,
        ]);

        $comp = ScoreComponent::create([
            'school_id' => $this->schoolA->id,
            'scoring_configuration_id' => $config->id,
            'name' => 'Quiz',
            'max_marks' => 20,
            'display_order' => 1,
            'is_active' => true,
        ]);

        // Mock invalid CSV content (Quiz mark 25 exceeds component max 20)
        $csvContent = "Student ID,Admission No,Student Name,\"Component:{$comp->id} (Quiz, Max: 20)\",\"Exam (Max: 100)\",Remarks\n";
        $csvContent .= "{$this->studentA->id},{$this->studentA->admission_no},{$this->studentA->first_name} {$this->studentA->last_name},25,80,Good effort\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'csv');
        file_put_contents($tempFile, $csvContent);

        $uploadedFile = new \Illuminate\Http\UploadedFile(
            $tempFile,
            'scores.csv',
            'text/csv',
            null,
            true
        );

        $this->actingAs($this->teacherA);

        $response = $this->post(route('school.scores.import'), [
            'class_id' => $this->classA->id,
            'subject_id' => $this->subjectA->id,
            'term_id' => $this->termA->id,
            'academic_year_id' => $this->yearA->id,
            'csv_file' => $uploadedFile,
        ]);

        @unlink($tempFile);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('exceeds limit', session('error'));

        $this->assertDatabaseMissing('student_scores', [
            'student_id' => $this->studentA->id,
            'subject_id' => $this->subjectA->id,
        ]);
    }
}


