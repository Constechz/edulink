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
use App\Models\Guardian;
use App\Models\User;
use App\Models\Subject;
use App\Models\Term;
use App\Models\Timetable;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\StudentScore;
use App\Models\Invoice;
use App\Models\AttendanceRecord;
use App\Models\LmsCourse;
use App\Models\LmsLesson;
use App\Models\LmsQuiz;
use App\Models\LmsQuizQuestion;
use App\Models\LmsQuizAttempt;
use App\Models\LmsForum;
use App\Models\LmsForumPost;
use App\Models\LibraryCategory;
use App\Models\LibraryBook;
use App\Models\LibraryLoan;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\StockTransaction;
use App\Models\Dormitory;
use App\Models\DormitoryRoom;
use App\Models\DormitoryBed;
use App\Models\HostelAllocation;
use App\Models\TransportRoute;
use App\Models\Vehicle;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Models\Payslip;
use App\Models\HealthVisit;
use App\Models\DisciplineCase;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseTenPortalsLmsOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $campus;
    protected $admin;
    protected $teacher;
    protected $studentUser;
    protected $parentUser;
    
    protected $student;
    protected $guardian;
    protected $class;
    protected $stream;
    protected $subject;
    protected $year;
    protected $term;
    protected $scoringConfig;

    protected function setUp(): void
    {
        parent::setUp();

        if (\Illuminate\Support\Facades\DB::connection() instanceof \Illuminate\Database\SQLiteConnection) {
            \Illuminate\Support\Facades\DB::connection()->getPdo()->sqliteCreateFunction('CONCAT', function (...$args) {
                return implode('', $args);
            });
        }

        $plan = Plan::create([
            'name' => 'Premium',
            'price_monthly' => 500,
            'price_yearly' => 5000,
            'max_students' => 1000,
            'max_staff' => 50,
            'max_campuses' => 5,
            'is_active' => true,
        ]);

        $this->school = School::create([
            'name' => 'Phase 10 Test Academy',
            'school_code' => 'P10A',
            'subdomain' => 'p10a',
            'plan_id' => $plan->id,
            'owner_name' => 'Kofi Tester',
            'owner_email' => 'kofi@tester.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $this->campus = Campus::create([
            'school_id' => $this->school->id,
            'name' => 'Main Campus',
            'is_main' => true,
            'is_active' => true,
        ]);

        // Roles
        $roleAdmin = Role::create(['name' => 'School Admin', 'slug' => 'school-admin', 'is_system' => true]);
        $roleTeacher = Role::create(['name' => 'Teacher', 'slug' => 'teacher', 'is_system' => true]);
        $roleStudent = Role::create(['name' => 'Student', 'slug' => 'student', 'is_system' => true]);
        $roleParent = Role::create(['name' => 'Parent', 'slug' => 'parent', 'is_system' => true]);

        // Admins and Staff
        $this->admin = User::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'name' => 'Admin Kofi',
            'email' => 'admin@tester.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);

        $this->teacher = User::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'name' => 'Teacher Ama',
            'email' => 'teacher@tester.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleTeacher->id,
            'is_active' => true,
        ]);

        // Academic settings
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
            'name' => 'Basic 7',
            'level' => 'JHS',
            'numeric_level' => 7,
            'is_active' => true,
        ]);

        $this->stream = Stream::create([
            'school_id' => $this->school->id,
            'class_id' => $this->class->id,
            'name' => 'A',
            'is_active' => true,
        ]);

        $this->subject = Subject::create([
            'school_id' => $this->school->id,
            'name' => 'Mathematics',
            'code' => 'MATH7',
            'type' => 'core',
            'level' => 'JHS',
            'is_active' => true,
        ]);

        // Student registry
        $this->student = Student::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'student_id_number' => 'STD001',
            'first_name' => 'Yaw',
            'last_name' => 'Student',
            'gender' => 'Male',
            'date_of_birth' => '2015-05-10',
            'enrollment_date' => '2026-09-01',
            'current_class_id' => $this->class->id,
            'current_stream_id' => $this->stream->id,
            'status' => 'active',
        ]);

        // Student User record for portal login (links via name / employee_id mapping)
        $this->studentUser = User::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'name' => 'Yaw Student',
            'email' => 'yaw@student.edu.gh',
            'phone' => '+233241112222',
            'password' => bcrypt('password123'),
            'role_id' => $roleStudent->id,
            'employee_id' => 'STD001', // maps to student_id_number in resolving
            'is_active' => true,
        ]);

        // Guardian registry
        $this->guardian = Guardian::create([
            'school_id' => $this->school->id,
            'first_name' => 'Kwame',
            'last_name' => 'Parent',
            'email' => 'kwame@parent.edu.gh',
            'phone' => '+233243334444',
            'relationship' => 'father',
        ]);
        
        // Link Guardian to Student
        $this->guardian->students()->attach($this->student->id);

        // Guardian User record for portal login
        $this->parentUser = User::create([
            'school_id' => $this->school->id,
            'campus_id' => $this->campus->id,
            'name' => 'Kwame Parent',
            'email' => 'kwame@parent.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleParent->id,
            'is_active' => true,
        ]);

        $this->scoringConfig = \App\Models\ScoringConfiguration::create([
            'school_id' => $this->school->id,
            'name' => 'Default Config',
            'class_score_max' => 50,
            'class_score_weight' => 50,
            'exam_score_max' => 50,
            'exam_score_weight' => 50,
            'grand_total' => 100,
        ]);
    }

    /**
     * Test Student Portal
     */
    public function test_student_portal()
    {
        $this->actingAs($this->studentUser);

        // 1. Timetable Setup
        Timetable::create([
            'school_id' => $this->school->id,
            'academic_year_id' => $this->year->id,
            'class_id' => $this->class->id,
            'stream_id' => $this->stream->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday', // Use static day to avoid weekend test failures
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'room' => 'Classroom 7A',
        ]);

        // 2. Assignment Setup
        $assignment = Assignment::create([
            'school_id' => $this->school->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Algebra Exercise 1',
            'description' => 'Solve equations 1 to 10.',
            'due_date' => now()->addDays(2),
            'is_active' => true,
        ]);

        // 3. Score Setup
        StudentScore::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'term_id' => $this->term->id,
            'academic_year_id' => $this->year->id,
            'scoring_configuration_id' => $this->scoringConfig->id,
            'teacher_id' => $this->teacher->id,
            'class_score' => 25,
            'exam_score' => 45,
            'grand_total' => 70,
            'grade' => 'B',
            'remarks' => 'Good effort.',
            'status' => 'published',
        ]);

        // Test dashboard
        $response = $this->get(route('school.student-portal.dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('student');
        $response->assertSee('Yaw Student');

        // Test ID card
        $response = $this->get(route('school.student-portal.id-card'));
        $response->assertStatus(200);
        $response->assertSee('STD001');

        // Test timetable
        $response = $this->get(route('school.student-portal.timetable'));
        $response->assertStatus(200);
        $response->assertSee('Mathematics');

        // Test assignments desk
        $response = $this->get(route('school.student-portal.assignments.index'));
        $response->assertStatus(200);
        $response->assertSee('Algebra Exercise 1');

        // Test results desk
        $response = $this->get(route('school.student-portal.results.index'));
        $response->assertStatus(200);
        $response->assertSee('Mathematics');
        $response->assertSee('70');
    }

    /**
     * Test Parent Portal
     */
    public function test_parent_portal()
    {
        $this->actingAs($this->parentUser);

        // 1. Setup attendance
        AttendanceRecord::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'class_id' => $this->class->id,
            'stream_id' => $this->stream->id,
            'term_id' => $this->term->id,
            'academic_year_id' => $this->year->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'present',
            'recorded_by' => $this->teacher->id,
        ]);

        // 2. Setup fees invoice
        Invoice::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'academic_year_id' => $this->year->id,
            'term_id' => $this->term->id,
            'invoice_number' => 'INV-001',
            'total_amount' => 450.00,
            'amount_paid' => 150.00,
            'balance' => 300.00,
            'due_date' => now()->addDays(30),
            'status' => 'partial',
            'created_by' => $this->admin->id,
        ]);

        // 3. Setup student published scores
        StudentScore::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'term_id' => $this->term->id,
            'academic_year_id' => $this->year->id,
            'scoring_configuration_id' => $this->scoringConfig->id,
            'teacher_id' => $this->teacher->id,
            'class_score' => 20,
            'exam_score' => 50,
            'grand_total' => 70,
            'grade' => 'B',
            'remarks' => 'Satisfactory.',
            'status' => 'published',
        ]);

        // Dashboard load
        $response = $this->get(route('school.parent-portal.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Yaw Student');
        $response->assertSee('GHS 300.00'); // balance

        // Select child
        $response = $this->get(route('school.parent-portal.select-child', $this->student->id));
        $response->assertStatus(302);

        // Child Attendance
        $response = $this->get(route('school.parent-portal.attendance'));
        $response->assertStatus(200);
        $response->assertSee('present');

        // Child Fees
        $response = $this->get(route('school.parent-portal.fees'));
        $response->assertStatus(200);
        $response->assertSee('INV-001');

        // Child Report Cards
        $response = $this->get(route('school.parent-portal.reports'));
        $response->assertStatus(200);
        $response->assertSee('70');
    }

    /**
     * Test Learning Management System (LMS)
     */
    public function test_lms_portal()
    {
        $this->actingAs($this->studentUser);

        // Create course
        $course = LmsCourse::create([
            'school_id' => $this->school->id,
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subject->id,
            'title' => 'Introductory Mathematics Basic 7',
            'description' => 'LMS Course description',
            'is_active' => true,
        ]);

        // Create lesson
        $lesson = LmsLesson::create([
            'course_id' => $course->id,
            'title' => 'Chapter 1: Number Systems',
            'content' => 'Introduction to whole numbers and fractions.',
            'display_order' => 1,
            'is_active' => true,
        ]);

        // Create quiz
        $quiz = LmsQuiz::create([
            'course_id' => $course->id,
            'title' => 'Quick Check 1.1',
            'passing_percentage' => 70,
            'time_limit_minutes' => 15,
            'is_active' => true,
        ]);

        $question = LmsQuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'What is 5 + 7?',
            'question_type' => 'multiple_choice',
            'options' => json_encode(['10', '11', '12', '13']),
            'correct_answer' => '12',
            'points' => 10,
        ]);

        // Create forum
        $forum = LmsForum::create([
            'course_id' => $course->id,
            'title' => 'Q&A Chat Room',
            'description' => 'Discuss anything mathematical.',
        ]);

        // LMS list courses
        $response = $this->get(route('school.lms.courses.index'));
        $response->assertStatus(200);
        $response->assertSee('Introductory Mathematics Basic 7');

        // Course show
        $response = $this->get(route('school.lms.courses.show', $course->id));
        $response->assertStatus(200);
        $response->assertSee('Chapter 1: Number Systems');
        $response->assertSee('Quick Check 1.1');

        // Lesson show
        $response = $this->get(route('school.lms.lessons.show', $lesson->id));
        $response->assertStatus(200);
        $response->assertSee('Introduction to whole numbers and fractions.');

        // Complete lesson
        $response = $this->post(route('school.lms.lessons.complete', $lesson->id));
        $response->assertStatus(302);
        $this->assertDatabaseHas('lms_progress', [
            'student_id' => $this->student->id,
            'lesson_id' => $lesson->id,
        ]);

        // Quiz show
        $response = $this->get(route('school.lms.quizzes.show', $quiz->id));
        $response->assertStatus(200);
        $response->assertSee('What is 5 + 7?');

        // Submit quiz (correct answer)
        $response = $this->post(route('school.lms.quizzes.submit', $quiz->id), [
            'answers' => [
                $question->id => '12'
            ]
        ]);
        $response->assertStatus(200);
        $response->assertSee('100%'); // Percentage score
        $response->assertSee('passed');

        // Forum Post Store
        $response = $this->post(route('school.lms.forums.post.store', $forum->id), [
            'content' => 'I love basic mathematics.',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('lms_forum_posts', [
            'forum_id' => $forum->id,
            'user_id' => $this->studentUser->id,
            'content' => 'I love basic mathematics.',
        ]);
    }

    /**
     * Test Operations Dashboard and Modules
     */
    public function test_operations_modules()
    {
        $this->actingAs($this->admin);

        // 1. Operations dashboard loads
        $response = $this->get(route('school.operations.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Operations Center');

        // 2. Library Tests
        $libCat = LibraryCategory::create([
            'school_id' => $this->school->id,
            'name' => 'General Science',
        ]);
        $book = LibraryBook::create([
            'school_id' => $this->school->id,
            'category_id' => $libCat->id,
            'title' => 'Intro to Physics JHS 1',
            'author' => 'Dr. K. Mensah',
            'copies_total' => 5,
            'copies_available' => 5,
        ]);

        $response = $this->get(route('school.operations.library.index'));
        $response->assertStatus(200);
        $response->assertSee('Intro to Physics JHS 1');

        // Borrow book
        $response = $this->post(route('school.operations.library.borrow'), [
            'book_id' => $book->id,
            'student_id' => $this->student->id,
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('library_loans', [
            'book_id' => $book->id,
            'status' => 'active',
        ]);

        $loan = LibraryLoan::where('book_id', $book->id)->firstOrFail();
        
        // Return book
        $response = $this->post(route('school.operations.library.return', $loan->id));
        $response->assertStatus(302);
        $this->assertDatabaseHas('library_loans', [
            'id' => $loan->id,
            'status' => 'returned',
        ]);

        // 3. Inventory Tests
        $invCat = InventoryCategory::create([
            'school_id' => $this->school->id,
            'name' => 'Stationery',
        ]);
        $item = InventoryItem::create([
            'school_id' => $this->school->id,
            'category_id' => $invCat->id,
            'name' => 'A4 Sheet Packs',
            'code' => 'A4-PKS',
            'quantity_in_stock' => 50,
            'reorder_level' => 10,
        ]);

        $response = $this->get(route('school.operations.inventory.index'));
        $response->assertStatus(200);
        $response->assertSee('A4 Sheet Packs');

        // Inventory Transaction
        $response = $this->post(route('school.operations.inventory.transaction'), [
            'inventory_item_id' => $item->id,
            'quantity' => 20,
            'type' => 'out',
            'description' => 'Office disbursement',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('stock_transactions', [
            'inventory_item_id' => $item->id,
            'quantity' => 20,
            'type' => 'out',
        ]);

        // 4. Hostel Tests
        $dorm = Dormitory::create([
            'school_id' => $this->school->id,
            'name' => 'Bonsu House',
            'capacity' => 100,
            'gender_allowed' => 'Male',
        ]);
        $room = DormitoryRoom::create([
            'dormitory_id' => $dorm->id,
            'room_number' => 'Room 101',
            'capacity' => 4,
        ]);
        $bed = DormitoryBed::create([
            'room_id' => $room->id,
            'bed_number' => 'Bed 1',
            'is_occupied' => false,
        ]);

        $response = $this->get(route('school.operations.hostel.index'));
        $response->assertStatus(200);
        $response->assertSee('Bonsu House');

        // Allocate bed
        $response = $this->post(route('school.operations.hostel.allocate'), [
            'student_id' => $this->student->id,
            'dormitory_id' => $dorm->id,
            'room_id' => $room->id,
            'bed_id' => $bed->id,
            'academic_year_id' => $this->year->id,
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('hostel_allocations', [
            'student_id' => $this->student->id,
            'bed_id' => $bed->id,
        ]);

        // 5. Transport Tests
        $trRoute = TransportRoute::create([
            'school_id' => $this->school->id,
            'route_name' => 'Adenta Shuttle',
            'start_point' => 'Madina',
            'end_point' => 'Campus',
        ]);
        $vehicle = Vehicle::create([
            'school_id' => $this->school->id,
            'plate_number' => 'GR-1010-26',
            'model' => 'Coaster Bus',
            'capacity' => 30,
            'status' => 'active',
        ]);

        $response = $this->get(route('school.operations.transport.index'));
        $response->assertStatus(200);
        $response->assertSee('Adenta Shuttle');
        $response->assertSee('GR-1010-26');

        // 6. HR & Payroll Tests
        $leaveType = LeaveType::create([
            'school_id' => $this->school->id,
            'name' => 'Sick Leave',
            'days_allowed' => 14,
        ]);
        $staff = \App\Models\Staff::create([
            'school_id' => $this->school->id,
            'user_id' => $this->admin->id, // map admin user
            'staff_number' => 'STF001',
            'designation' => 'Administrator',
            'date_joined' => '2025-01-01',
            'status' => 'active',
        ]);

        $response = $this->get(route('school.operations.hr.index'));
        $response->assertStatus(200);
        $response->assertSee('Sick Leave');

        // Submit leave request
        $response = $this->post(route('school.operations.hr.leave'), [
            'staff_id' => $staff->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(8)->format('Y-m-d'),
            'reason' => 'Doctor scheduled appointment',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('leave_requests', [
            'leave_type_id' => $leaveType->id,
            'reason' => 'Doctor scheduled appointment',
        ]);

        // Run payroll period & payslip view
        $period = PayrollPeriod::create([
            'school_id' => $this->school->id,
            'name' => 'June 2026',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30',
            'status' => 'open',
        ]);

        $response = $this->post(route('school.operations.hr.payroll.run'), [
            'payroll_period_id' => $period->id,
        ]);
        $response->assertStatus(302);

        $payslip = Payslip::whereHas('payrollRun', function ($query) use ($period) {
            $query->where('payroll_period_id', $period->id);
        })->firstOrFail();
        $response = $this->get(route('school.operations.hr.payslip', $payslip->id));
        $response->assertStatus(200);
        $response->assertSee('June 2026');

        // 7. Health & Discipline Views
        $response = $this->get(route('school.operations.health-discipline.index'));
        $response->assertStatus(200);
        $response->assertSee('Clinic');
        $response->assertSee('Discipline');

        // Log health visit
        $response = $this->post(route('school.operations.health.visit.store'), [
            'student_id' => $this->student->id,
            'visit_date' => now()->format('Y-m-d H:i:s'),
            'symptoms' => 'Fever and cold',
            'diagnosis' => 'Early malaria symptoms',
            'treatment' => 'Administered initial dose',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('health_visits', [
            'student_id' => $this->student->id,
            'diagnosis' => 'Early malaria symptoms',
        ]);

        // Log discipline case
        $response = $this->post(route('school.operations.discipline.case.store'), [
            'student_id' => $this->student->id,
            'incident_date' => now()->format('Y-m-d'),
            'category' => 'major',
            'description' => 'Unexcused class absenteeism',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('discipline_cases', [
            'student_id' => $this->student->id,
            'category' => 'major',
            'description' => 'Unexcused class absenteeism',
        ]);
    }

    /**
     * Test Communication Blasts
     */
    public function test_communication_blasts()
    {
        $this->actingAs($this->admin);

        // Notice blast center index loads
        $response = $this->get(route('school.communication.index'));
        $response->assertStatus(200);
        $response->assertSee('School Communication Center');

        // Post a notice blast campaign
        $response = $this->post(route('school.communication.send-blast'), [
            'channel' => 'email',
            'target_audience' => 'all',
            'subject' => 'Notice: Founders Day Holidays',
            'body' => 'Please note that the campus will remain closed during the holidays.',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('messages', [
            'channel' => 'email',
            'subject' => 'Notice: Founders Day Holidays',
        ]);
        
        $this->assertDatabaseHas('announcements', [
            'title' => 'Notice: Founders Day Holidays',
            'target_audience' => 'all',
        ]);

        // Assert that the email campaign actually sent emails to target recipients and was captured by global log listener
        $this->assertDatabaseHas('email_logs', [
            'recipient_email' => 'teacher@tester.edu.gh',
            'subject' => 'Notice: Founders Day Holidays',
            'status' => 'sent',
        ]);

        $this->assertDatabaseHas('email_logs', [
            'recipient_email' => 'yaw@student.edu.gh',
            'subject' => 'Notice: Founders Day Holidays',
            'status' => 'sent',
        ]);
    }
}
