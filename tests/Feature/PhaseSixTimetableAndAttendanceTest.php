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
use App\Models\Timetable;
use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseSixTimetableAndAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected $schoolA;
    protected $schoolB;
    protected $adminA;
    protected $adminB;
    protected $teacher;
    protected $student;
    protected $class;
    protected $stream;
    protected $subject;
    protected $year;
    protected $term;
    protected $campusA;

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

        // School A Setup
        $this->schoolA = School::create([
            'name' => 'Accra High School',
            'school_code' => 'AHS',
            'subdomain' => 'accrahigh',
            'plan_id' => $plan->id,
            'owner_name' => 'Principal Appiah',
            'owner_email' => 'principal@accrahigh.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $this->campusA = Campus::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Main Campus',
            'is_main' => true,
            'is_active' => true,
        ]);

        $roleAdmin = Role::create([
            'name' => 'School Admin',
            'slug' => 'school-admin',
            'is_system' => true,
        ]);

        $roleTeacher = Role::create([
            'name' => 'Teacher',
            'slug' => 'teacher',
            'is_system' => true,
        ]);

        $this->adminA = User::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Admin Accra',
            'email' => 'admin@accrahigh.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);

        $this->teacher = User::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Yaw Mensah',
            'email' => 'yaw@accrahigh.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleTeacher->id,
            'is_active' => true,
        ]);

        $this->year = AcademicYear::create([
            'school_id' => $this->schoolA->id,
            'name' => '2026/2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-30',
            'is_current' => true,
        ]);

        $this->term = Term::create([
            'school_id' => $this->schoolA->id,
            'academic_year_id' => $this->year->id,
            'name' => 'Term 1',
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-15',
            'is_current' => true,
        ]);

        $prog = Programme::create([
            'school_id' => $this->schoolA->id,
            'name' => 'General Arts',
            'code' => 'G-ARTS',
            'duration_years' => 3,
            'level' => 'SHS',
        ]);

        $this->class = SchoolClass::create([
            'school_id' => $this->schoolA->id,
            'campus_id' => $this->campusA->id,
            'academic_year_id' => $this->year->id,
            'programme_id' => $prog->id,
            'name' => 'Arts 1',
            'level' => 'SHS',
            'capacity' => 40,
        ]);

        $this->stream = Stream::create([
            'school_id' => $this->schoolA->id,
            'class_id' => $this->class->id,
            'name' => 'Stream A',
        ]);

        $this->subject = Subject::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Core English',
            'code' => 'ENG',
            'level' => 'SHS',
        ]);

        $this->student = Student::create([
            'school_id' => $this->schoolA->id,
            'campus_id' => $this->campusA->id,
            'student_id_number' => 'STU-2026-0001',
            'first_name' => 'Kwame',
            'last_name' => 'Osei',
            'date_of_birth' => '2010-02-14',
            'gender' => 'Male',
            'current_class_id' => $this->class->id,
            'current_stream_id' => $this->stream->id,
            'enrollment_date' => '2026-09-01',
        ]);

        // School B Setup for Isolation
        $this->schoolB = School::create([
            'name' => 'Kumasi High School',
            'school_code' => 'KHS',
            'subdomain' => 'kumasihigh',
            'plan_id' => $plan->id,
            'owner_name' => 'Principal Boateng',
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
    }

    /**
     * Test Timetable scheduling and clash checks.
     */
    public function test_timetable_scheduling_and_clash_detection(): void
    {
        // 1. Visit Timetable Dashboard page
        $response = $this->actingAs($this->adminA)
            ->get('/school/timetable');
        $response->assertStatus(200);

        // 2. Add normal timetable slot
        $response = $this->actingAs($this->adminA)->post('/school/timetable', [
            'class_id' => $this->class->id,
            'stream_id' => $this->stream->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'room' => 'Room 101',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('timetables', [
            'school_id' => $this->schoolA->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday',
            'start_time' => '09:00',
        ]);

        // 3. Test Teacher Clash Detection: Same teacher, overlapping time slot
        $responseOverlap = $this->actingAs($this->adminA)->post('/school/timetable', [
            'class_id' => $this->class->id,
            'stream_id' => null, // different stream/class configuration
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id, // conflict teacher
            'day_of_week' => 'Monday',
            'start_time' => '09:30', // overlaps 09:00 - 10:00
            'end_time' => '10:30',
            'room' => 'Room 102',
        ]);

        $responseOverlap->assertSessionHasErrors('teacher_id');

        // 4. Test Class Overlap Detection: Same class/stream, overlapping time slot
        $anotherTeacher = User::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Ama Serwaa',
            'email' => 'ama@accrahigh.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $this->teacher->role_id,
            'is_active' => true,
        ]);

        $responseClassOverlap = $this->actingAs($this->adminA)->post('/school/timetable', [
            'class_id' => $this->class->id,
            'stream_id' => $this->stream->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $anotherTeacher->id,
            'day_of_week' => 'Monday',
            'start_time' => '08:30',
            'end_time' => '09:30', // overlaps 09:00 - 10:00
            'room' => 'Room 103',
        ]);

        $responseClassOverlap->assertSessionHasErrors('class_id');

        // 5. Verify and delete timetable slot
        $slot = Timetable::where('school_id', $this->schoolA->id)
            ->where('teacher_id', $this->teacher->id)
            ->first();

        $responseDelete = $this->actingAs($this->adminA)->delete("/school/timetable/{$slot->id}");
        $responseDelete->assertRedirect();
        $this->assertDatabaseMissing('timetables', ['id' => $slot->id]);
    }

    /**
     * Test Manual Attendance entry and Parent simulated alert log.
     */
    public function test_attendance_manual_submission_and_logs(): void
    {
        // 1. Visit Attendance page
        $response = $this->actingAs($this->adminA)
            ->get('/school/attendance');
        $response->assertStatus(200);

        // 2. Submit attendance register (Manual)
        $response = $this->actingAs($this->adminA)->post('/school/attendance', [
            'class_id' => $this->class->id,
            'stream_id' => $this->stream->id,
            'date' => '2026-06-25',
            'attendance' => [
                $this->student->id => 'absent',
            ]
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('attendance_records', [
            'school_id' => $this->schoolA->id,
            'student_id' => $this->student->id,
            'status' => 'absent',
            'date' => '2026-06-25 00:00:00',
            'method' => 'manual',
        ]);
    }

    /**
     * Test QR Kiosk check-in verification (on-time and late calculation checks).
     */
    public function test_attendance_qr_checkin_kiosk_scan(): void
    {
        // 1. View Kiosk UI
        $response = $this->actingAs($this->adminA)
            ->get('/school/attendance/qr-kiosk');
        $response->assertStatus(200);

        // 2. Perform mock check-in via QR controller endpoint
        $responseCheck = $this->actingAs($this->adminA)->postJson('/school/attendance/qr-checkin', [
            'student_id_number' => 'STU-2026-0001',
        ]);

        $responseCheck->assertStatus(200)
            ->assertJson([
                'success' => true,
                'student_name' => 'Kwame Osei',
            ]);

        $this->assertDatabaseHas('attendance_records', [
            'school_id' => $this->schoolA->id,
            'student_id' => $this->student->id,
            'method' => 'qr',
        ]);
    }

    /**
     * Test QR Kiosk check-in verification with custom cutoff configuration.
     */
    public function test_attendance_qr_checkin_with_custom_cutoff(): void
    {
        // 1. Post settings to make sure updating settings via controller profile route works
        $responseSettings = $this->actingAs($this->adminA)->post('/school/settings/profile', [
            'name' => 'Accra High School Edited',
            'attendance_cutoff_time' => '05:00', // 05:00 AM
        ]);
        $responseSettings->assertSessionHasNoErrors();
        $responseSettings->assertRedirect();
        
        $this->schoolA->refresh();
        $this->assertEquals('05:00:00', $this->schoolA->settings['attendance_cutoff_time']);

        // 2. Now update it directly to 00:01:00 to force a late check-in in the test
        $settings = $this->schoolA->settings;
        $settings['attendance_cutoff_time'] = '00:01:00';
        $this->schoolA->update(['settings' => $settings]);

        // 3. Perform mock check-in via QR controller endpoint
        $responseCheck = $this->actingAs($this->adminA)->postJson('/school/attendance/qr-checkin', [
            'student_id_number' => 'STU-2026-0001',
        ]);

        $responseCheck->assertStatus(200)
            ->assertJson([
                'success' => true,
                'student_name' => 'Kwame Osei',
                'status' => 'Late',
            ]);

        $this->assertDatabaseHas('attendance_records', [
            'school_id' => $this->schoolA->id,
            'student_id' => $this->student->id,
            'status' => 'late',
            'method' => 'qr',
        ]);
    }

    /**
     * Test statistical reports calculations.
     */
    public function test_attendance_reports_and_statistics(): void
    {
        // Setup direct database checks for report calculations
        AttendanceRecord::create([
            'school_id' => $this->schoolA->id,
            'campus_id' => $this->campusA->id,
            'class_id' => $this->class->id,
            'stream_id' => $this->stream->id,
            'student_id' => $this->student->id,
            'academic_year_id' => $this->year->id,
            'term_id' => $this->term->id,
            'date' => '2026-06-25',
            'status' => 'present',
            'method' => 'qr',
            'recorded_by' => $this->adminA->id,
        ]);

        $response = $this->actingAs($this->adminA)
            ->get('/school/attendance/reports?date=2026-06-25');
        
        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['present'] === 1 && $stats['absent'] === 0;
        });
    }

    /**
     * Test tenant data isolation.
     */
    public function test_tenant_isolation_for_timetables_and_attendance(): void
    {
        // Create School A Timetable Slot
        $slotA = Timetable::create([
            'school_id' => $this->schoolA->id,
            'campus_id' => $this->campusA->id,
            'class_id' => $this->class->id,
            'stream_id' => $this->stream->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday',
            'start_time' => '11:00',
            'end_time' => '12:00',
            'academic_year_id' => $this->year->id,
            'term_id' => $this->term->id,
        ]);

        // Attempt to delete slotA as School B Admin
        $response = $this->actingAs($this->adminB)
            ->delete("/school/timetable/{$slotA->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('timetables', ['id' => $slotA->id]);
    }
}
