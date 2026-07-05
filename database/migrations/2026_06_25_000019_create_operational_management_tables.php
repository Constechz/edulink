<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // HR Leave & Payroll
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name'); // Annual, Sick, Maternity, etc.
            $table->integer('days_allowed');
            $table->timestamps();
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->date('date');
            $table->time('clock_in');
            $table->time('clock_out')->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->timestamps();

            $table->unique(['school_id', 'staff_id', 'date']);
        });

        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name'); // June 2026
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['open', 'processed', 'closed'])->default('open');
            $table->timestamps();
        });

        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->cascadeOnDelete();
            $table->date('run_date');
            $table->foreignId('run_by')->constrained('users')->cascadeOnDelete();
            $table->decimal('total_gross', 12, 2);
            $table->decimal('total_deductions', 12, 2);
            $table->decimal('total_net', 12, 2);
            $table->timestamps();
        });

        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('payroll_run_id')->constrained('payroll_runs')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('gross_salary', 10, 2);
            $table->decimal('total_deductions', 10, 2);
            $table->decimal('net_salary', 10, 2);
            $table->enum('status', ['draft', 'paid'])->default('draft');
            $table->date('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamps();
        });

        Schema::create('payslip_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payslip_id')->constrained('payslips')->cascadeOnDelete();
            $table->string('name'); // Housing Allowance, SSNIT Deduction, Tax
            $table->enum('type', ['allowance', 'deduction']);
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

        Schema::create('staff_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->integer('repayment_term_months');
            $table->decimal('monthly_installment', 10, 2);
            $table->decimal('balance', 10, 2);
            $table->enum('status', ['pending', 'active', 'completed'])->default('pending');
            $table->timestamps();
        });

        Schema::create('staff_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->boolean('is_recurring')->default(true);
            $table->timestamps();
        });

        // Communication Tables
        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->enum('channel', ['sms', 'email']);
            $table->string('subject')->nullable();
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('sender_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('channel', ['sms', 'email']);
            $table->string('subject')->nullable();
            $table->text('body');
            $table->enum('status', ['draft', 'sending', 'completed', 'failed'])->default('draft');
            $table->timestamps();
        });

        Schema::create('message_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('messages')->cascadeOnDelete();
            $table->foreignId('recipient_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_email')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->enum('type', ['sms', 'email', 'push'])->default('push');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('sms_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('phone_number');
            $table->text('message_body');
            $table->integer('credits_used')->default(1);
            $table->string('status');
            $table->string('reference')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->string('target_audience')->default('all'); // all, students, staff, parents
            $table->boolean('is_pinned')->default(false);
            $table->dateTime('expires_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Health Tables
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('blood_group')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->timestamps();
        });

        Schema::create('health_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('visit_date');
            $table->text('symptoms');
            $table->text('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->string('medication_given')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->integer('quantity_in_stock')->default(0);
            $table->integer('reorder_level')->default(10);
            $table->timestamps();
        });

        Schema::create('allergies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('allergen');
            $table->enum('severity', ['low', 'medium', 'high']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('immunizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('vaccine_name');
            $table->date('date_administered');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Discipline Tables
        Schema::create('discipline_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
            $table->date('incident_date');
            $table->enum('category', ['minor', 'major', 'critical'])->default('minor');
            $table->text('description');
            $table->enum('status', ['pending', 'under_investigation', 'resolved'])->default('pending');
            $table->timestamps();
        });

        Schema::create('warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('discipline_cases')->cascadeOnDelete();
            $table->string('warning_letter_path')->nullable();
            $table->date('date_issued');
            $table->text('details')->nullable();
            $table->timestamps();
        });

        Schema::create('suspensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('discipline_cases')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'completed'])->default('active');
            $table->timestamps();
        });

        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('reward_type'); // Certificate, Prize, Badge
            $table->text('description');
            $table->date('date_awarded');
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('reporter_name');
            $table->string('reporter_phone')->nullable();
            $table->string('incident_type');
            $table->text('description');
            $table->date('date_reported');
            $table->timestamps();
        });

        Schema::create('counseling_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('counselor_user_id')->constrained('users')->cascadeOnDelete();
            $table->date('session_date');
            $table->longText('notes'); // Encrypted or restricted on application level
            $table->boolean('follow_up_required')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('counseling_sessions');
        Schema::dropIfExists('incident_reports');
        Schema::dropIfExists('rewards');
        Schema::dropIfExists('suspensions');
        Schema::dropIfExists('warnings');
        Schema::dropIfExists('discipline_cases');
        Schema::dropIfExists('immunizations');
        Schema::dropIfExists('allergies');
        Schema::dropIfExists('medications');
        Schema::dropIfExists('health_visits');
        Schema::dropIfExists('health_records');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('sms_delivery_logs');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('message_recipients');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('message_templates');
        Schema::dropIfExists('staff_deductions');
        Schema::dropIfExists('staff_loans');
        Schema::dropIfExists('payslip_items');
        Schema::dropIfExists('payslips');
        Schema::dropIfExists('payroll_runs');
        Schema::dropIfExists('payroll_periods');
        Schema::dropIfExists('timesheets');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('leave_types');
    }
};
