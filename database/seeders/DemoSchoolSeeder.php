<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Campus;
use App\Models\User;
use App\Models\Role;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Models\Department;
use App\Models\Programme;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\ClassSubject;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\StudentGuardian;
use App\Models\StudentEnrollment;
use App\Models\Staff;
use App\Models\ScoringConfiguration;
use App\Models\ScoreComponent;
use App\Models\StudentScore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSchoolSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create School
        $school = School::updateOrCreate(
            ['school_code' => 'GVIS'],
            [
                'name' => 'Green Valley International School',
                'short_name' => 'Green Valley',
                'subdomain' => 'greenvalley',
                'plan_id' => 3, // Premium
                'subscription_status' => 'active',
                'owner_name' => 'Dr. Kofi Annan',
                'owner_email' => 'owner@greenvalley.edu.gh',
                'owner_phone' => '+233244112233',
                'branding' => json_encode([
                    'primary_color' => '#003366',
                    'secondary_color' => '#FFD700',
                    'heading_font' => 'Outfit',
                    'body_font' => 'Inter'
                ]),
                'is_active' => true,
                'onboarding_completed' => true
            ]
        );

        // Disable global scoping during seed so we can explicitly populate tables with school_id
        app()->instance('tenant', $school);

        // 2. Create Campuses
        $campus = Campus::updateOrCreate(
            ['school_id' => $school->id, 'code' => 'ACC'],
            [
                'name' => 'Accra Main Campus',
                'address' => 'Ring Road West, Accra',
                'phone' => '+233244001122',
                'email' => 'accra@greenvalley.edu.gh',
                'principal_name' => 'Mr. Samuel Boadi',
                'is_main' => true,
                'is_active' => true
            ]
        );

        // 3. Find System Roles
        $adminRole = Role::where('slug', 'school-admin')->first();
        $headRole = Role::where('slug', 'headteacher')->first();
        $hodRole = Role::where('slug', 'hod')->first();
        $teacherRole = Role::where('slug', 'class-teacher')->first();

        // 4. Create Users
        $adminUser = User::updateOrCreate(
            ['school_id' => $school->id, 'email' => 'schooladmin@demo.edu.gh'],
            [
                'campus_id' => $campus->id,
                'name' => 'Ama Serwaa (Admin)',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole ? $adminRole->id : null,
                'is_active' => true
            ]
        );

        $headUser = User::updateOrCreate(
            ['school_id' => $school->id, 'email' => 'headteacher@demo.edu.gh'],
            [
                'campus_id' => $campus->id,
                'name' => 'Dr. Samuel Boadi (Headteacher)',
                'password' => Hash::make('password123'),
                'role_id' => $headRole ? $headRole->id : null,
                'is_active' => true
            ]
        );

        $hodUser = User::updateOrCreate(
            ['school_id' => $school->id, 'email' => 'hod@demo.edu.gh'],
            [
                'campus_id' => $campus->id,
                'name' => 'Mrs. Beatrice Osei (HOD)',
                'password' => Hash::make('password123'),
                'role_id' => $hodRole ? $hodRole->id : null,
                'is_active' => true
            ]
        );

        $teacherUser = User::updateOrCreate(
            ['school_id' => $school->id, 'email' => 'teacher@demo.edu.gh'],
            [
                'campus_id' => $campus->id,
                'name' => 'Mr. Emmanuel Asante (Teacher)',
                'password' => Hash::make('password123'),
                'role_id' => $teacherRole ? $teacherRole->id : null,
                'is_active' => true
            ]
        );

        // 5. Create Academic Calendar
        $year = AcademicYear::updateOrCreate(
            ['school_id' => $school->id, 'name' => '2025/2026'],
            [
                'start_date' => '2025-09-01',
                'end_date' => '2026-07-31',
                'is_current' => true,
                'created_by' => $adminUser->id
            ]
        );

        $term1 = Term::updateOrCreate(
            ['school_id' => $school->id, 'academic_year_id' => $year->id, 'name' => 'Term 1'],
            ['start_date' => '2025-09-01', 'end_date' => '2025-12-15', 'is_current' => false]
        );
        $term2 = Term::updateOrCreate(
            ['school_id' => $school->id, 'academic_year_id' => $year->id, 'name' => 'Term 2'],
            ['start_date' => '2026-01-05', 'end_date' => '2026-04-10', 'is_current' => false]
        );
        $term3 = Term::updateOrCreate(
            ['school_id' => $school->id, 'academic_year_id' => $year->id, 'name' => 'Term 3'],
            ['start_date' => '2026-04-27', 'end_date' => '2026-07-25', 'is_current' => true]
        );

        // 6. Departments & Programmes
        $deptBasic = Department::updateOrCreate(
            ['school_id' => $school->id, 'name' => 'Basic School Dept'],
            ['campus_id' => $campus->id, 'code' => 'BSD', 'hod_user_id' => $hodUser->id]
        );

        $progPrimary = Programme::updateOrCreate(
            ['school_id' => $school->id, 'code' => 'PRIM'],
            ['department_id' => $deptBasic->id, 'name' => 'Primary School Programme', 'duration_years' => 6, 'level' => 'Primary']
        );

        // 7. Classes & Streams
        $class5 = SchoolClass::updateOrCreate(
            ['school_id' => $school->id, 'name' => 'Basic 5', 'academic_year_id' => $year->id],
            ['campus_id' => $campus->id, 'programme_id' => $progPrimary->id, 'level' => 'Primary', 'class_teacher_id' => $teacherUser->id]
        );

        $streamA = Stream::updateOrCreate(
            ['school_id' => $school->id, 'class_id' => $class5->id, 'name' => 'A'],
            ['class_teacher_id' => $teacherUser->id, 'capacity' => 45]
        );

        // 8. Subjects
        $subMath = Subject::updateOrCreate(
            ['school_id' => $school->id, 'code' => 'MATH-P'],
            ['department_id' => $deptBasic->id, 'name' => 'Mathematics', 'level' => 'Primary', 'is_core' => true]
        );

        $subSci = Subject::updateOrCreate(
            ['school_id' => $school->id, 'code' => 'SCI-P'],
            ['department_id' => $deptBasic->id, 'name' => 'Integrated Science', 'level' => 'Primary', 'is_core' => true]
        );

        // 9. Class Subjects Allocation
        ClassSubject::updateOrCreate(
            ['school_id' => $school->id, 'class_id' => $class5->id, 'subject_id' => $subMath->id],
            ['stream_id' => $streamA->id, 'teacher_id' => $teacherUser->id, 'academic_year_id' => $year->id, 'term_id' => $term3->id]
        );

        ClassSubject::updateOrCreate(
            ['school_id' => $school->id, 'class_id' => $class5->id, 'subject_id' => $subSci->id],
            ['stream_id' => $streamA->id, 'teacher_id' => $teacherUser->id, 'academic_year_id' => $year->id, 'term_id' => $term3->id]
        );

        // 10. Staff Records
        Staff::updateOrCreate(
            ['school_id' => $school->id, 'user_id' => $teacherUser->id],
            ['staff_number' => 'GVIS-STAFF-001', 'designation' => 'Class Teacher', 'department_id' => $deptBasic->id, 'date_joined' => '2023-01-01']
        );
        Staff::updateOrCreate(
            ['school_id' => $school->id, 'user_id' => $hodUser->id],
            ['staff_number' => 'GVIS-STAFF-002', 'designation' => 'HOD Basic School', 'department_id' => $deptBasic->id, 'date_joined' => '2020-01-01']
        );

        // 11. Scoring Configurations (50/50 scaling split)
        $scoreConfig = ScoringConfiguration::updateOrCreate(
            ['school_id' => $school->id, 'level' => 'Primary', 'is_default' => true],
            [
                'name' => 'GES Primary 50/50 Standard',
                'class_score_max' => 50,
                'class_score_weight' => 50,
                'exam_score_max' => 100,
                'exam_score_weight' => 50,
                'grand_total' => 100,
                'rounding_method' => 'ROUND',
                'decimal_places' => 0,
                'is_active' => true
            ]
        );

        // Core score components sum up to 50
        $components = [
            ['name' => 'Class Exercises', 'max_marks' => 10, 'display_order' => 1, 'is_required' => true],
            ['name' => 'Homeworks', 'max_marks' => 10, 'display_order' => 2, 'is_required' => true],
            ['name' => 'Project Work', 'max_marks' => 15, 'display_order' => 3, 'is_required' => true],
            ['name' => 'Class Test', 'max_marks' => 15, 'display_order' => 4, 'is_required' => true],
        ];

        foreach ($components as $comp) {
            ScoreComponent::updateOrCreate(
                ['school_id' => $school->id, 'scoring_configuration_id' => $scoreConfig->id, 'name' => $comp['name']],
                $comp
            );
        }

        // 12. Create Students & Guardians
        $studentNames = [
            ['first' => 'Kofi', 'last' => 'Mensah', 'dob' => '2016-05-12', 'gender' => 'Male'],
            ['first' => 'Ama', 'last' => 'Serwaa', 'dob' => '2016-09-22', 'gender' => 'Female'],
            ['first' => 'Yaw', 'last' => 'Boateng', 'dob' => '2015-11-04', 'gender' => 'Male'],
        ];

        $guardian = Guardian::updateOrCreate(
            ['school_id' => $school->id, 'phone' => '+233244998877'],
            ['first_name' => 'Robert', 'last_name' => 'Mensah', 'relationship' => 'Father', 'email' => 'robert@parent.com']
        );

        foreach ($studentNames as $idx => $st) {
            $student = Student::updateOrCreate(
                ['school_id' => $school->id, 'first_name' => $st['first'], 'last_name' => $st['last']],
                [
                    'student_id_number' => 'GVIS-2025-00' . ($idx + 1),
                    'date_of_birth' => $st['dob'],
                    'gender' => $st['gender'],
                    'current_class_id' => $class5->id,
                    'current_stream_id' => $streamA->id,
                    'enrollment_date' => '2025-09-01',
                    'status' => 'active'
                ]
            );

            StudentGuardian::updateOrCreate(
                ['student_id' => $student->id, 'guardian_id' => $guardian->id],
                ['is_primary' => true]
            );

            StudentEnrollment::updateOrCreate(
                ['school_id' => $school->id, 'student_id' => $student->id, 'academic_year_id' => $year->id, 'class_id' => $class5->id],
                ['stream_id' => $streamA->id, 'enrollment_date' => '2025-09-01', 'status' => 'active']
            );

            // Seed Mock Scores for Math & Science for Term 3
            // Math
            $mathRawClass = 35 + ($idx * 5); // 35, 40, 45 out of 50
            $mathRawExam = 60 + ($idx * 10); // 60, 70, 80 out of 100
            
            $scaledClass = ($mathRawClass / 50) * 50; // 35, 40, 45
            $scaledExam = ($mathRawExam / 100) * 50;  // 30, 35, 40
            $mathTotal = $scaledClass + $scaledExam;  // 65, 75, 85

            $mathGrade = $mathTotal >= 80 ? 'A1' : ($mathTotal >= 70 ? 'B2' : 'B3');
            $mathGP = $mathTotal >= 80 ? 1.00 : ($mathTotal >= 70 ? 2.00 : 3.00);

            StudentScore::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'student_id' => $student->id,
                    'subject_id' => $subMath->id,
                    'term_id' => $term3->id,
                    'academic_year_id' => $year->id
                ],
                [
                    'class_id' => $class5->id,
                    'stream_id' => $streamA->id,
                    'scoring_configuration_id' => $scoreConfig->id,
                    'teacher_id' => $teacherUser->id,
                    'component_scores' => json_encode(['Class Exercises' => 10, 'Homeworks' => 10, 'Project Work' => 10, 'Class Test' => ($mathRawClass - 30)]),
                    'raw_class_total' => $mathRawClass,
                    'scaled_class_score' => $scaledClass,
                    'raw_exam_score' => $mathRawExam,
                    'scaled_exam_score' => $scaledExam,
                    'grand_total' => $mathTotal,
                    'grade' => $mathGrade,
                    'grade_point' => $mathGP,
                    'status' => 'published'
                ]
            );

            // Science
            $sciRawClass = 42 - ($idx * 4); // 42, 38, 34 out of 50
            $sciRawExam = 74 - ($idx * 8);  // 74, 66, 58 out of 100
            
            $sciScaledClass = ($sciRawClass / 50) * 50;
            $sciScaledExam = ($sciRawExam / 100) * 50;
            $sciTotal = $sciScaledClass + $sciScaledExam;

            $sciGrade = $sciTotal >= 80 ? 'A1' : ($sciTotal >= 70 ? 'B2' : ($sciTotal >= 60 ? 'B3' : 'C4'));
            $sciGP = $sciTotal >= 80 ? 1.00 : ($sciTotal >= 70 ? 2.00 : ($sciTotal >= 60 ? 3.00 : 4.00));

            StudentScore::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'student_id' => $student->id,
                    'subject_id' => $subSci->id,
                    'term_id' => $term3->id,
                    'academic_year_id' => $year->id
                ],
                [
                    'class_id' => $class5->id,
                    'stream_id' => $streamA->id,
                    'scoring_configuration_id' => $scoreConfig->id,
                    'teacher_id' => $teacherUser->id,
                    'component_scores' => json_encode(['Class Exercises' => 10, 'Homeworks' => 10, 'Project Work' => 10, 'Class Test' => ($sciRawClass - 30)]),
                    'raw_class_total' => $sciRawClass,
                    'scaled_class_score' => $sciScaledClass,
                    'raw_exam_score' => $sciRawExam,
                    'scaled_exam_score' => $sciScaledExam,
                    'grand_total' => $sciTotal,
                    'grade' => $sciGrade,
                    'grade_point' => $sciGP,
                    'status' => 'published'
                ]
            );
        }

        // 13. Seed Default Chart of Accounts
        $coas = [
            ['account_code' => '1100', 'account_name' => 'Cash on Hand', 'account_type' => 'Asset'],
            ['account_code' => '1200', 'account_name' => 'Mobile Money Wallet', 'account_type' => 'Asset'],
            ['account_code' => '1300', 'account_name' => 'Bank Account', 'account_type' => 'Asset'],
            ['account_code' => '1400', 'account_name' => 'Accounts Receivable', 'account_type' => 'Asset'],
            ['account_code' => '4100', 'account_name' => 'Tuition Revenue', 'account_type' => 'Revenue'],
            ['account_code' => '5100', 'account_name' => 'General Expenses', 'account_type' => 'Expense'],
        ];

        $accountModels = [];
        foreach ($coas as $coa) {
            $accountModels[$coa['account_code']] = \App\Models\ChartOfAccount::updateOrCreate(
                ['school_id' => $school->id, 'account_code' => $coa['account_code']],
                ['account_name' => $coa['account_name'], 'account_type' => $coa['account_type'], 'is_active' => true]
            );
        }

        // 14. Seed Fee Structures
        $feeTuition = \App\Models\FeeStructure::updateOrCreate(
            ['school_id' => $school->id, 'name' => 'Tuition Fee - Basic 5'],
            [
                'campus_id' => $campus->id,
                'academic_year_id' => $year->id,
                'term_id' => $term3->id,
                'class_id' => $class5->id,
                'amount' => 1200.00,
                'due_date' => '2026-05-30',
                'is_mandatory' => true,
            ]
        );

        $feeIct = \App\Models\FeeStructure::updateOrCreate(
            ['school_id' => $school->id, 'name' => 'ICT Lab Fee - Basic 5'],
            [
                'campus_id' => $campus->id,
                'academic_year_id' => $year->id,
                'term_id' => $term3->id,
                'class_id' => $class5->id,
                'amount' => 150.00,
                'due_date' => '2026-05-30',
                'is_mandatory' => true,
            ]
        );

        // 15. Seed Invoices and Payments for students
        $students = Student::where('school_id', $school->id)->get();

        foreach ($students as $index => $student) {
            $invoiceNumber = 'INV-2026-000' . ($index + 1);
            $totalAmount = 1350.00; // 1200 + 150

            $invoice = \App\Models\Invoice::updateOrCreate(
                ['school_id' => $school->id, 'invoice_number' => $invoiceNumber],
                [
                    'campus_id' => $campus->id,
                    'student_id' => $student->id,
                    'academic_year_id' => $year->id,
                    'term_id' => $term3->id,
                    'total_amount' => $totalAmount,
                    'amount_paid' => 0.00,
                    'balance' => $totalAmount,
                    'status' => 'pending',
                    'due_date' => '2026-05-30',
                    'notes' => 'Term 3 standard billing.',
                    'created_by' => $adminUser->id,
                ]
            );

            \App\Models\InvoiceItem::updateOrCreate(
                ['invoice_id' => $invoice->id, 'fee_structure_id' => $feeTuition->id],
                [
                    'description' => 'Tuition Fee - Basic 5',
                    'amount' => 1200.00,
                    'discount_amount' => 0.00,
                    'net_amount' => 1200.00,
                ]
            );

            \App\Models\InvoiceItem::updateOrCreate(
                ['invoice_id' => $invoice->id, 'fee_structure_id' => $feeIct->id],
                [
                    'description' => 'ICT Lab Fee - Basic 5',
                    'amount' => 150.00,
                    'discount_amount' => 0.00,
                    'net_amount' => 150.00,
                ]
            );

            // Post double-entry for invoice issuance: Debit accounts receivable, Credit revenue
            $invoiceJe = \App\Models\JournalEntry::updateOrCreate(
                ['school_id' => $school->id, 'reference' => $invoiceNumber],
                [
                    'entry_date' => '2026-05-01',
                    'description' => "Invoice " . $invoiceNumber . " for student " . $student->first_name . " " . $student->last_name,
                    'created_by' => $adminUser->id,
                    'status' => 'posted',
                ]
            );

            \App\Models\JournalLine::updateOrCreate(
                ['journal_entry_id' => $invoiceJe->id, 'account_id' => $accountModels['1400']->id, 'debit' => $totalAmount],
                ['credit' => 0.00, 'description' => "Billing receivable"]
            );

            \App\Models\JournalLine::updateOrCreate(
                ['journal_entry_id' => $invoiceJe->id, 'account_id' => $accountModels['4100']->id, 'credit' => $totalAmount],
                ['debit' => 0.00, 'description' => "Billing revenue recognition"]
            );

            // Kofi Mensah pays partially (GHS 800) via Momo
            if ($index == 0) {
                $payAmount = 800.00;
                $receiptNumber = 'REC-2026-05-1001';

                $payment = \App\Models\Payment::updateOrCreate(
                    ['school_id' => $school->id, 'receipt_number' => $receiptNumber],
                    [
                        'invoice_id' => $invoice->id,
                        'student_id' => $student->id,
                        'amount' => $payAmount,
                        'payment_date' => '2026-05-15',
                        'method' => 'momo',
                        'reference_number' => 'REF-MOMO-8827',
                        'received_by' => $adminUser->id,
                        'is_reversed' => false,
                    ]
                );

                $invoice->update([
                    'amount_paid' => $payAmount,
                    'balance' => $totalAmount - $payAmount,
                    'status' => 'partial',
                ]);

                // Debit MoMo Wallet, Credit Accounts Receivable
                $paymentJe = \App\Models\JournalEntry::updateOrCreate(
                    ['school_id' => $school->id, 'reference' => $receiptNumber],
                    [
                        'entry_date' => '2026-05-15',
                        'description' => "Payment of GHS " . number_format($payAmount, 2) . " for Invoice " . $invoiceNumber,
                        'created_by' => $adminUser->id,
                        'status' => 'posted',
                    ]
                );

                \App\Models\JournalLine::updateOrCreate(
                    ['journal_entry_id' => $paymentJe->id, 'account_id' => $accountModels['1200']->id, 'debit' => $payAmount],
                    ['credit' => 0.00, 'description' => "Received Momo payment"]
                );

                \App\Models\JournalLine::updateOrCreate(
                    ['journal_entry_id' => $paymentJe->id, 'account_id' => $accountModels['1400']->id, 'credit' => $payAmount],
                    ['debit' => 0.00, 'description' => "Receivable reduction"]
                );
            }

            // Ama Serwaa pays fully (GHS 1350) via Bank Transfer
            if ($index == 1) {
                $payAmount = 1350.00;
                $receiptNumber = 'REC-2026-05-1002';

                $payment = \App\Models\Payment::updateOrCreate(
                    ['school_id' => $school->id, 'receipt_number' => $receiptNumber],
                    [
                        'invoice_id' => $invoice->id,
                        'student_id' => $student->id,
                        'amount' => $payAmount,
                        'payment_date' => '2026-05-16',
                        'method' => 'bank_transfer',
                        'reference_number' => 'REF-BANK-7729',
                        'received_by' => $adminUser->id,
                        'is_reversed' => false,
                    ]
                );

                $invoice->update([
                    'amount_paid' => $payAmount,
                    'balance' => 0.00,
                    'status' => 'paid',
                ]);

                // Debit Bank Account, Credit Accounts Receivable
                $paymentJe = \App\Models\JournalEntry::updateOrCreate(
                    ['school_id' => $school->id, 'reference' => $receiptNumber],
                    [
                        'entry_date' => '2026-05-16',
                        'description' => "Payment of GHS " . number_format($payAmount, 2) . " for Invoice " . $invoiceNumber,
                        'created_by' => $adminUser->id,
                        'status' => 'posted',
                    ]
                );

                \App\Models\JournalLine::updateOrCreate(
                    ['journal_entry_id' => $paymentJe->id, 'account_id' => $accountModels['1300']->id, 'debit' => $payAmount],
                    ['credit' => 0.00, 'description' => "Received Bank transfer payment"]
                );

                \App\Models\JournalLine::updateOrCreate(
                    ['journal_entry_id' => $paymentJe->id, 'account_id' => $accountModels['1400']->id, 'credit' => $payAmount],
                    ['debit' => 0.00, 'description' => "Receivable clearance"]
                );
            }
        }

        // 16. Seed default website pages and revisions for the demo school GVIS
        $blocks = \App\Models\WebsiteBlock::where('is_active', true)->orderBy('display_order')->get();
        $hero = $blocks->where('slug', 'hero-banner')->first()?->html_template ?? '';
        $about = $blocks->where('slug', 'about-us')->first()?->html_template ?? '';
        $stats = $blocks->where('slug', 'statistics-bar')->first()?->html_template ?? '';
        $cta = $blocks->where('slug', 'cta-banner')->first()?->html_template ?? '';
        $news = $blocks->where('slug', 'dynamic-news')->first()?->html_template ?? '';
        $events = $blocks->where('slug', 'dynamic-events')->first()?->html_template ?? '';
        $staff = $blocks->where('slug', 'dynamic-staff')->first()?->html_template ?? '';
        $gallery = $blocks->where('slug', 'dynamic-gallery')->first()?->html_template ?? '';
        $contact = $blocks->where('slug', 'contact-form')->first()?->html_template ?? '';

        $pagesData = [
            [
                'title' => 'Home',
                'slug' => 'home',
                'page_type' => 'home',
                'is_homepage' => true,
                'html' => $hero . $about . $stats . $news . $events . $staff . $gallery . $contact,
            ],
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'page_type' => 'about',
                'is_homepage' => false,
                'html' => $about . $stats,
            ],
            [
                'title' => 'Admissions',
                'slug' => 'admissions',
                'page_type' => 'admissions',
                'is_homepage' => false,
                'html' => $cta,
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact-us',
                'page_type' => 'contact',
                'is_homepage' => false,
                'html' => $contact,
            ],
        ];

        foreach ($pagesData as $index => $pData) {
            $page = \App\Models\WebsitePage::updateOrCreate(
                ['school_id' => $school->id, 'slug' => $pData['slug']],
                [
                    'title' => $pData['title'],
                    'page_type' => $pData['page_type'],
                    'is_homepage' => $pData['is_homepage'],
                    'is_published' => true,
                    'published_at' => now(),
                    'display_order' => $index + 1,
                    'created_by' => $adminUser->id,
                ]
            );

            // Create published revision (revision 1)
            \App\Models\PageRevision::updateOrCreate(
                ['website_page_id' => $page->id, 'revision_number' => 1],
                [
                    'html_content' => $pData['html'],
                    'css_content' => '',
                    'components_json' => '[]',
                    'is_current_draft' => false,
                    'is_published' => true,
                    'published_at' => now(),
                    'published_by' => $adminUser->id,
                    'created_by' => $adminUser->id,
                ]
            );

            // Create current draft revision for future edits (revision 2)
            \App\Models\PageRevision::updateOrCreate(
                ['website_page_id' => $page->id, 'revision_number' => 2],
                [
                    'html_content' => $pData['html'],
                    'css_content' => '',
                    'components_json' => '[]',
                    'is_current_draft' => true,
                    'is_published' => false,
                    'created_by' => $adminUser->id,
                ]
            );
        }

        // Ensure header and footer menus exist
        $headerMenu = \App\Models\WebsiteMenu::updateOrCreate(
            ['school_id' => $school->id, 'location' => 'header'],
            ['name' => 'Main Navigation']
        );
        $footerMenu = \App\Models\WebsiteMenu::updateOrCreate(
            ['school_id' => $school->id, 'location' => 'footer'],
            ['name' => 'Footer Links']
        );

        // Seed Header and Footer items
        $createdPages = \App\Models\WebsitePage::where('school_id', $school->id)->get();
        
        \App\Models\WebsiteMenuItem::where('menu_id', $headerMenu->id)->delete();
        \App\Models\WebsiteMenuItem::where('menu_id', $footerMenu->id)->delete();

        foreach ($createdPages as $index => $createdPage) {
            \App\Models\WebsiteMenuItem::create([
                'menu_id' => $headerMenu->id,
                'label' => $createdPage->title,
                'page_id' => $createdPage->id,
                'display_order' => $index,
            ]);

            \App\Models\WebsiteMenuItem::create([
                'menu_id' => $footerMenu->id,
                'label' => $createdPage->title,
                'page_id' => $createdPage->id,
                'display_order' => $index,
            ]);
        }
    }
}
