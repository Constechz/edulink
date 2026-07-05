<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\ChartOfAccount;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseEightFinanceTest extends TestCase
{
    use RefreshDatabase;

    protected $schoolA;
    protected $schoolB;
    protected $adminA;
    protected $adminB;
    protected $studentA1;
    protected $studentA2;
    protected $studentB1;
    protected $classA;
    protected $classB;
    protected $yearA;
    protected $yearB;
    protected $termA;
    protected $termB;
    protected $campusA;
    protected $campusB;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup subscription plan
        $plan = Plan::create([
            'name' => 'Premium',
            'price_monthly' => 500,
            'price_yearly' => 5000,
            'max_students' => 1000,
            'max_staff' => 50,
            'max_campuses' => 5,
            'is_active' => true,
        ]);

        // School A setup
        $this->schoolA = School::create([
            'name' => 'Accra Prep School',
            'school_code' => 'APS',
            'subdomain' => 'accraprep',
            'plan_id' => $plan->id,
            'owner_name' => 'Adwoa Mensah',
            'owner_email' => 'adwoa@accraprep.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $this->campusA = Campus::create([
            'school_id' => $this->schoolA->id,
            'name' => 'East Legon Campus',
            'is_main' => true,
            'is_active' => true,
        ]);

        $roleAdmin = Role::create(['name' => 'School Admin', 'slug' => 'school-admin', 'is_system' => true]);

        $this->adminA = User::create([
            'school_id' => $this->schoolA->id,
            'campus_id' => $this->campusA->id,
            'name' => 'Ama Admin',
            'email' => 'admin@accraprep.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);

        $this->yearA = AcademicYear::create([
            'school_id' => $this->schoolA->id,
            'name' => '2026/2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-07-31',
            'is_current' => true,
            'created_by' => $this->adminA->id,
        ]);

        $this->termA = Term::create([
            'school_id' => $this->schoolA->id,
            'academic_year_id' => $this->yearA->id,
            'name' => 'Term 1',
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-15',
            'is_current' => true,
        ]);

        $this->classA = SchoolClass::create([
            'school_id' => $this->schoolA->id,
            'campus_id' => $this->campusA->id,
            'academic_year_id' => $this->yearA->id,
            'name' => 'Class 1',
            'level' => 'Primary',
        ]);

        $this->studentA1 = Student::create([
            'school_id' => $this->schoolA->id,
            'campus_id' => $this->campusA->id,
            'first_name' => 'Kofi',
            'last_name' => 'Osei',
            'student_id_number' => 'APS-001',
            'date_of_birth' => '2020-05-10',
            'gender' => 'Male',
            'current_class_id' => $this->classA->id,
            'status' => 'active',
            'enrollment_date' => '2026-09-01',
        ]);

        $this->studentA2 = Student::create([
            'school_id' => $this->schoolA->id,
            'campus_id' => $this->campusA->id,
            'first_name' => 'Yaw',
            'last_name' => 'Appiah',
            'student_id_number' => 'APS-002',
            'date_of_birth' => '2020-08-12',
            'gender' => 'Male',
            'current_class_id' => $this->classA->id,
            'status' => 'active',
            'enrollment_date' => '2026-09-01',
        ]);

        // School B setup (for Isolation tests)
        $this->schoolB = School::create([
            'name' => 'Kumasi Academy',
            'school_code' => 'KUM',
            'subdomain' => 'kumasiacademy',
            'plan_id' => $plan->id,
            'owner_name' => 'Dr. Kwame Boateng',
            'owner_email' => 'kwame@kumasi.edu.gh',
            'is_active' => true,
            'onboarding_completed' => true,
        ]);

        $this->campusB = Campus::create([
            'school_id' => $this->schoolB->id,
            'name' => 'Kumasi Campus',
            'is_main' => true,
            'is_active' => true,
        ]);

        $this->adminB = User::create([
            'school_id' => $this->schoolB->id,
            'campus_id' => $this->campusB->id,
            'name' => 'Kojo Admin',
            'email' => 'admin@kumasi.edu.gh',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);

        $this->yearB = AcademicYear::create([
            'school_id' => $this->schoolB->id,
            'name' => '2026/2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-07-31',
            'is_current' => true,
            'created_by' => $this->adminB->id,
        ]);

        $this->termB = Term::create([
            'school_id' => $this->schoolB->id,
            'academic_year_id' => $this->yearB->id,
            'name' => 'Term 1',
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-15',
            'is_current' => true,
        ]);

        $this->classB = SchoolClass::create([
            'school_id' => $this->schoolB->id,
            'campus_id' => $this->campusB->id,
            'academic_year_id' => $this->yearB->id,
            'name' => 'Class 1',
            'level' => 'Primary',
        ]);

        $this->studentB1 = Student::create([
            'school_id' => $this->schoolB->id,
            'campus_id' => $this->campusB->id,
            'first_name' => 'Ama',
            'last_name' => 'Boaten',
            'student_id_number' => 'KUM-001',
            'date_of_birth' => '2020-03-04',
            'gender' => 'Female',
            'current_class_id' => $this->classB->id,
            'status' => 'active',
            'enrollment_date' => '2026-09-01',
        ]);
    }

    /**
     * Test Fee Structure CRUD operations.
     */
    public function test_fee_structure_crud()
    {
        $this->actingAs($this->adminA);

        // 1. Create structure
        $response = $this->post(route('school.finance.fee-structures.store'), [
            'name' => 'Primary Tuition',
            'amount' => 850.00,
            'due_date' => '2026-10-31',
            'academic_year_id' => $this->yearA->id,
            'term_id' => $this->termA->id,
            'class_id' => $this->classA->id,
            'campus_id' => $this->campusA->id,
            'is_mandatory' => true,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('fee_structures', [
            'school_id' => $this->schoolA->id,
            'name' => 'Primary Tuition',
            'amount' => 850.00,
        ]);

        $structure = FeeStructure::where('name', 'Primary Tuition')->firstOrFail();

        // 2. Update structure
        $response = $this->put(route('school.finance.fee-structures.update', $structure->id), [
            'name' => 'Primary Tuition (Revised)',
            'amount' => 900.00,
            'due_date' => '2026-10-31',
            'academic_year_id' => $this->yearA->id,
            'term_id' => $this->termA->id,
            'class_id' => $this->classA->id,
            'campus_id' => $this->campusA->id,
            'is_mandatory' => true,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('fee_structures', [
            'id' => $structure->id,
            'name' => 'Primary Tuition (Revised)',
            'amount' => 900.00,
        ]);

        // 3. Delete structure
        $response = $this->delete(route('school.finance.fee-structures.destroy', $structure->id));
        $response->assertStatus(302);
        
        $this->assertSoftDeleted('fee_structures', [
            'id' => $structure->id,
        ]);
    }

    /**
     * Test single student invoice generation with automatic double-entry posting.
     */
    public function test_individual_invoice_generation()
    {
        $this->actingAs($this->adminA);

        $fee = FeeStructure::create([
            'school_id' => $this->schoolA->id,
            'campus_id' => $this->campusA->id,
            'academic_year_id' => $this->yearA->id,
            'term_id' => $this->termA->id,
            'class_id' => $this->classA->id,
            'name' => 'Canteen Levy',
            'amount' => 200.00,
            'due_date' => '2026-11-30',
        ]);

        $response = $this->post(route('school.finance.invoices.store'), [
            'student_id' => $this->studentA1->id,
            'academic_year_id' => $this->yearA->id,
            'term_id' => $this->termA->id,
            'due_date' => '2026-11-30',
            'notes' => 'Test notes',
            'fees' => [
                [
                    'id' => $fee->id,
                    'discount' => 30.00,
                ]
            ]
        ]);

        $response->assertStatus(302);

        // Assert invoice is generated
        $invoice = Invoice::where('student_id', $this->studentA1->id)->firstOrFail();
        $this->assertEquals(170.00, $invoice->total_amount); // 200 - 30
        $this->assertEquals(170.00, $invoice->balance);
        $this->assertEquals('pending', $invoice->status);

        // Assert invoice item was stored correctly
        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'amount' => 200.00,
            'discount_amount' => 30.00,
            'net_amount' => 170.00,
        ]);

        // Assert double-entry posting was triggered: Debit Accounts Receivable, Credit Revenue
        $this->assertDatabaseHas('journal_entries', [
            'school_id' => $this->schoolA->id,
            'reference' => $invoice->invoice_number,
            'status' => 'posted',
        ]);

        $je = JournalEntry::where('reference', $invoice->invoice_number)->firstOrFail();
        $this->assertCount(2, $je->lines);

        $lineDeb = $je->lines()->where('debit', '>', 0)->firstOrFail();
        $lineCred = $je->lines()->where('credit', '>', 0)->firstOrFail();

        $this->assertEquals(170.00, $lineDeb->debit);
        $this->assertEquals('1400', $lineDeb->account->account_code); // Accounts Receivable

        $this->assertEquals(170.00, $lineCred->credit);
        $this->assertEquals('4100', $lineCred->account->account_code); // Tuition Revenue
    }

    /**
     * Test bulk billing generation for active students in a class.
     */
    public function test_bulk_invoice_generation()
    {
        $this->actingAs($this->adminA);

        $fee1 = FeeStructure::create([
            'school_id' => $this->schoolA->id,
            'campus_id' => $this->campusA->id,
            'academic_year_id' => $this->yearA->id,
            'term_id' => $this->termA->id,
            'class_id' => $this->classA->id,
            'name' => 'Tuition Part A',
            'amount' => 500.00,
            'due_date' => '2026-11-30',
        ]);

        $fee2 = FeeStructure::create([
            'school_id' => $this->schoolA->id,
            'campus_id' => $this->campusA->id,
            'academic_year_id' => $this->yearA->id,
            'term_id' => $this->termA->id,
            'class_id' => $this->classA->id,
            'name' => 'Tuition Part B',
            'amount' => 300.00,
            'due_date' => '2026-11-30',
        ]);

        $response = $this->post(route('school.finance.invoices.bulk-store'), [
            'class_id' => $this->classA->id,
            'academic_year_id' => $this->yearA->id,
            'term_id' => $this->termA->id,
            'due_date' => '2026-11-30',
            'fee_structure_ids' => [$fee1->id, $fee2->id],
        ]);

        $response->assertStatus(302);

        // Class A has 2 students ($this->studentA1, $this->studentA2). Bulk billing should generate invoices for both.
        $this->assertCount(2, Invoice::where('academic_year_id', $this->yearA->id)->get());

        foreach ([$this->studentA1, $this->studentA2] as $student) {
            $invoice = Invoice::where('student_id', $student->id)->firstOrFail();
            $this->assertEquals(800.00, $invoice->total_amount);
            $this->assertEquals(800.00, $invoice->balance);

            // Verify journal postings
            $je = JournalEntry::where('reference', $invoice->invoice_number)->firstOrFail();
            $this->assertEquals(800.00, $je->lines()->sum('debit'));
            $this->assertEquals(800.00, $je->lines()->sum('credit'));
        }
    }

    /**
     * Test recording payments and balance updates.
     */
    public function test_payment_allocation()
    {
        $this->actingAs($this->adminA);

        $invoice = Invoice::create([
            'school_id' => $this->schoolA->id,
            'campus_id' => $this->campusA->id,
            'student_id' => $this->studentA1->id,
            'academic_year_id' => $this->yearA->id,
            'term_id' => $this->termA->id,
            'invoice_number' => 'INV-2026-T1',
            'total_amount' => 1000.00,
            'amount_paid' => 0.00,
            'balance' => 1000.00,
            'status' => 'pending',
            'due_date' => '2026-11-30',
            'created_by' => $this->adminA->id,
        ]);

        // Log partial payment
        $response = $this->post(route('school.finance.payments.store'), [
            'invoice_id' => $invoice->id,
            'amount' => 400.00,
            'payment_date' => '2026-10-15',
            'method' => 'momo',
            'reference_number' => 'MOMO-7729',
            'notes' => 'Partial payment',
        ]);

        $response->assertStatus(302);

        // Check invoice balances
        $invoice->refresh();
        $this->assertEquals(400.00, $invoice->amount_paid);
        $this->assertEquals(600.00, $invoice->balance);
        $this->assertEquals('partial', $invoice->status);

        $payment = Payment::where('invoice_id', $invoice->id)->firstOrFail();
        $this->assertEquals(400.00, $payment->amount);
        $this->assertEquals('momo', $payment->method);
        $this->assertFalse($payment->is_reversed);

        // Check journal postings: Debit Momo (1200), Credit Receivable (1400)
        $je = JournalEntry::where('reference', $payment->receipt_number)->firstOrFail();
        
        $lineDeb = $je->lines()->where('debit', '>', 0)->firstOrFail();
        $lineCred = $je->lines()->where('credit', '>', 0)->firstOrFail();

        $this->assertEquals(400.00, $lineDeb->debit);
        $this->assertEquals('1200', $lineDeb->account->account_code); // Momo Wallet

        $this->assertEquals(400.00, $lineCred->credit);
        $this->assertEquals('1400', $lineCred->account->account_code); // Receivable
    }

    /**
     * Test payment reversals and balance restorations.
     */
    public function test_payment_reversal()
    {
        $this->actingAs($this->adminA);

        // Setup accounts manually
        $coas = [
            ['account_code' => '1100', 'account_name' => 'Cash on Hand', 'account_type' => 'Asset'],
            ['account_code' => '1200', 'account_name' => 'Mobile Money Wallet', 'account_type' => 'Asset'],
            ['account_code' => '1300', 'account_name' => 'Bank Account', 'account_type' => 'Asset'],
            ['account_code' => '1400', 'account_name' => 'Accounts Receivable', 'account_type' => 'Asset'],
            ['account_code' => '4100', 'account_name' => 'Tuition Revenue', 'account_type' => 'Revenue'],
            ['account_code' => '5100', 'account_name' => 'General Expenses', 'account_type' => 'Expense'],
        ];
        foreach ($coas as $coa) {
            ChartOfAccount::create([
                'school_id' => $this->schoolA->id,
                'account_code' => $coa['account_code'],
                'account_name' => $coa['account_name'],
                'account_type' => $coa['account_type'],
                'is_active' => true
            ]);
        }

        $invoice = Invoice::create([
            'school_id' => $this->schoolA->id,
            'campus_id' => $this->campusA->id,
            'student_id' => $this->studentA1->id,
            'academic_year_id' => $this->yearA->id,
            'term_id' => $this->termA->id,
            'invoice_number' => 'INV-2026-T1',
            'total_amount' => 1000.00,
            'amount_paid' => 600.00,
            'balance' => 400.00,
            'status' => 'partial',
            'due_date' => '2026-11-30',
            'created_by' => $this->adminA->id,
        ]);

        $payment = Payment::create([
            'school_id' => $this->schoolA->id,
            'invoice_id' => $invoice->id,
            'student_id' => $this->studentA1->id,
            'amount' => 600.00,
            'payment_date' => '2026-10-15',
            'method' => 'cash',
            'received_by' => $this->adminA->id,
            'receipt_number' => 'REC-2026-0099',
            'is_reversed' => false,
        ]);

        // Perform reversal
        $response = $this->post(route('school.finance.payments.reverse', $payment->id));
        $response->assertStatus(302);

        $payment->refresh();
        $this->assertTrue($payment->is_reversed);

        $invoice->refresh();
        $this->assertEquals(0.00, $invoice->amount_paid);
        $this->assertEquals(1000.00, $invoice->balance);
        $this->assertEquals('pending', $invoice->status);

        // Check reversal double entry: Debit Receivable (1400), Credit Cash (1100)
        $je = JournalEntry::where('reference', 'REV-' . $payment->receipt_number)->firstOrFail();
        $lineDeb = $je->lines()->where('debit', '>', 0)->firstOrFail();
        $lineCred = $je->lines()->where('credit', '>', 0)->firstOrFail();

        $this->assertEquals(600.00, $lineDeb->debit);
        $this->assertEquals('1400', $lineDeb->account->account_code); // Accounts Receivable

        $this->assertEquals(600.00, $lineCred->credit);
        $this->assertEquals('1100', $lineCred->account->account_code); // Cash
    }

    /**
     * Test tenant isolation for all financial transactions.
     */
    public function test_finance_tenant_isolation()
    {
        // Add accounts for School B
        $coas = [
            ['account_code' => '1100', 'account_name' => 'Cash on Hand', 'account_type' => 'Asset'],
            ['account_code' => '1200', 'account_name' => 'Mobile Money Wallet', 'account_type' => 'Asset'],
            ['account_code' => '1300', 'account_name' => 'Bank Account', 'account_type' => 'Asset'],
            ['account_code' => '1400', 'account_name' => 'Accounts Receivable', 'account_type' => 'Asset'],
            ['account_code' => '4100', 'account_name' => 'Tuition Revenue', 'account_type' => 'Revenue'],
            ['account_code' => '5100', 'account_name' => 'General Expenses', 'account_type' => 'Expense'],
        ];
        foreach ($coas as $coa) {
            ChartOfAccount::create([
                'school_id' => $this->schoolB->id,
                'account_code' => $coa['account_code'],
                'account_name' => $coa['account_name'],
                'account_type' => $coa['account_type'],
                'is_active' => true
            ]);
        }

        // Invoice under School B
        $invoiceB = Invoice::create([
            'school_id' => $this->schoolB->id,
            'campus_id' => $this->campusB->id,
            'student_id' => $this->studentB1->id,
            'academic_year_id' => $this->yearB->id,
            'term_id' => $this->termB->id,
            'invoice_number' => 'INV-KUMASI-01',
            'total_amount' => 500.00,
            'amount_paid' => 0.00,
            'balance' => 500.00,
            'status' => 'pending',
            'due_date' => '2026-11-30',
            'created_by' => $this->adminB->id,
        ]);

        // Login as School A
        $this->actingAs($this->adminA);

        // 1. Attempt to view School B's invoice details - should yield 403 Forbidden or 404 Not Found
        $response = $this->get(route('school.finance.invoices.show', $invoiceB->id));
        $response->assertStatus(403);

        // 2. Attempt to view School B's invoice PDF
        $response = $this->get(route('school.finance.invoices.pdf', $invoiceB->id));
        $response->assertStatus(403);

        // 3. Attempt to record payment against School B's invoice - should yield 404 (since query searches within tenant scope)
        $response = $this->post(route('school.finance.payments.store'), [
            'invoice_id' => $invoiceB->id,
            'amount' => 100.00,
            'payment_date' => '2026-10-15',
            'method' => 'cash',
        ]);
        $response->assertStatus(404);
    }
}
