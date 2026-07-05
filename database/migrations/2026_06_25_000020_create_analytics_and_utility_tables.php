<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Alumni Tables
        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->integer('graduation_year');
            $table->string('current_occupation')->nullable();
            $table->string('employer')->nullable();
            $table->string('higher_institution')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->timestamps();
        });

        Schema::create('alumni_donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('alumni_profile_id')->constrained('alumni_profiles')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('donation_date');
            $table->string('purpose')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamps();
        });

        Schema::create('alumni_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('event_date');
            $table->string('venue')->nullable();
            $table->timestamps();
        });

        Schema::create('mentoring_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->unsignedBigInteger('mentor_alumni_id');
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'completed'])->default('active');
            $table->timestamps();

            $table->foreign('mentor_alumni_id')->references('id')->on('alumni_profiles')->cascadeOnDelete();
        });

        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('posted_by_alumni_id')->nullable()->constrained('alumni_profiles')->nullOnDelete();
            $table->string('job_title');
            $table->string('company_name');
            $table->string('location')->nullable();
            $table->text('job_description');
            $table->string('application_url')->nullable();
            $table->date('closing_date')->nullable();
            $table->timestamps();
        });

        Schema::create('transcript_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('recipient_name');
            $table->string('recipient_address');
            $table->string('recipient_email')->nullable();
            $table->decimal('fee_amount', 8, 2)->default(0.00);
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->enum('status', ['pending', 'processing', 'sent', 'rejected'])->default('pending');
            $table->timestamps();
        });

        // AI & Analytics Tables
        Schema::create('ai_flag_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Academic Risk, Attendance Risk, Financial Default Risk
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('flag_type_id')->constrained('ai_flag_types')->cascadeOnDelete();
            $table->string('severity'); // low, medium, high
            $table->text('trigger_reason');
            $table->boolean('is_resolved')->default(false);
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->text('recommendation_text');
            $table->enum('status', ['pending', 'reviewed', 'implemented', 'dismissed'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        // Document Management
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('document_categories')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('current_file_path');
            $table->string('mime_type')->nullable();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->integer('version_number');
            $table->string('file_path');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('document_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action_taken'); // read, update, download
            $table->timestamp('accessed_at')->nullable();
        });

        // Helpdesk Tables
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // submitter
            $table->foreignId('category_id')->constrained('ticket_categories')->cascadeOnDelete();
            $table->string('subject');
            $table->text('body');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // sender
            $table->text('message');
            $table->timestamps();
        });

        Schema::create('knowledge_base_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->string('target_role')->nullable(); // null=all, or role names
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // Audit Logs (System actions)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // create, update, delete, login, logout
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('backup_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('frequency'); // daily, weekly, monthly
            $table->time('scheduled_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('file_path')->nullable();
            $table->string('file_size')->nullable();
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
        });

        Schema::create('consent_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('consent_type'); // e.g. photo release, medical treatment, trips
            $table->boolean('is_granted')->default(false);
            $table->foreignId('recorded_by_guardian_id')->constrained('guardians')->cascadeOnDelete();
            $table->timestamp('recorded_at')->nullable();
        });

        Schema::create('data_retention_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('data_type'); // student logs, chat history, system logs
            $table->integer('retention_years');
            $table->timestamps();
        });

        // API & Webhooks
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->string('token_hash', 64)->unique();
            $table->boolean('is_active')->default(true);
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->string('url');
            $table->string('secret')->nullable();
            $table->json('subscribed_events'); // e.g. ["student.enrolled", "score.published"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('webhook_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->constrained('webhooks')->cascadeOnDelete();
            $table->string('event_type');
            $table->json('payload');
            $table->integer('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->integer('attempt')->default(1);
            $table->timestamp('delivered_at')->nullable();
        });

        Schema::create('integration_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('provider'); // paystack, flutterwave, twilio
            $table->json('credentials_encrypted');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Events & Calendar
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->string('building')->nullable();
            $table->integer('capacity')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('location')->nullable();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['invited', 'accepted', 'declined', 'attended'])->default('invited');
            $table->timestamps();
        });

        Schema::create('room_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('booked_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->text('purpose')->nullable();
            $table->timestamps();
        });

        // Visitor Management
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('visitor_name');
            $table->string('id_type')->nullable(); // NHIS, Driver's License, National ID
            $table->string('id_number')->nullable();
            $table->string('phone_number');
            $table->string('purpose');
            $table->foreignId('whom_to_see_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('check_in_time');
            $table->dateTime('check_out_time')->nullable();
            $table->timestamps();
        });

        Schema::create('visitor_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_log_id')->constrained('visitor_logs')->cascadeOnDelete();
            $table->string('badge_number');
            $table->boolean('is_returned')->default(false);
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });

        Schema::create('pickup_authorizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('authorized_name');
            $table->string('phone_number');
            $table->string('relationship');
            $table->string('photo')->nullable();
            $table->timestamps();
        });

        // Safeguarding Cases (highly restricted)
        Schema::create('safeguarding_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
            $table->date('incident_date');
            $table->string('incident_type'); // abuse, neglect, self-harm, cyberbullying
            $table->longText('details'); // encrypted on app level
            $table->enum('status', ['open', 'under_review', 'referred_to_police', 'resolved'])->default('open');
            $table->timestamps();
        });

        Schema::create('safeguarding_escalations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('safeguarding_cases')->cascadeOnDelete();
            $table->string('agency_name'); // e.g. Police, DOVVSU, Social Welfare
            $table->date('escalated_date');
            $table->string('officer_in_charge')->nullable();
            $table->text('action_details')->nullable();
            $table->timestamps();
        });

        Schema::create('safeguarding_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('safeguarding_cases')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action_taken'); // read, update, download
            $table->timestamp('created_at')->nullable();
        });

        // Platform & Infrastructure Monitoring
        Schema::create('health_checks', function (Blueprint $table) {
            $table->id();
            $table->string('service_name'); // DB, Queue, Storage, Redis
            $table->string('status'); // OK, Warning, Down
            $table->integer('response_time_ms')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('checked_at')->nullable();
        });

        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained('schools')->cascadeOnDelete();
            $table->string('exception_class');
            $table->text('message');
            $table->longText('stack_trace')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('queue_monitor_logs', function (Blueprint $table) {
            $table->id();
            $table->string('job_class');
            $table->string('status'); // pending, running, success, failed
            $table->integer('execution_time_ms')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('incident_runbooks', function (Blueprint $table) {
            $table->id();
            $table->string('incident_type'); // DB connection fail, SMS Gateway fail
            $table->text('steps_to_resolve');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('incident_runbooks');
        Schema::dropIfExists('queue_monitor_logs');
        Schema::dropIfExists('error_logs');
        Schema::dropIfExists('health_checks');
        Schema::dropIfExists('safeguarding_audit_logs');
        Schema::dropIfExists('safeguarding_escalations');
        Schema::dropIfExists('safeguarding_cases');
        Schema::dropIfExists('pickup_authorizations');
        Schema::dropIfExists('visitor_badges');
        Schema::dropIfExists('visitor_logs');
        Schema::dropIfExists('room_bookings');
        Schema::dropIfExists('event_attendees');
        Schema::dropIfExists('events');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('integration_configs');
        Schema::dropIfExists('webhook_delivery_logs');
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('data_retention_policies');
        Schema::dropIfExists('consent_records');
        Schema::dropIfExists('backup_logs');
        Schema::dropIfExists('backup_schedules');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('knowledge_base_articles');
        Schema::dropIfExists('ticket_messages');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('ticket_categories');
        Schema::dropIfExists('document_access_logs');
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_categories');
        Schema::dropIfExists('ai_recommendations');
        Schema::dropIfExists('ai_flags');
        Schema::dropIfExists('ai_flag_types');
        Schema::dropIfExists('transcript_requests');
        Schema::dropIfExists('job_postings');
        Schema::dropIfExists('mentoring_matches');
        Schema::dropIfExists('alumni_events');
        Schema::dropIfExists('alumni_donations');
        Schema::dropIfExists('alumni_profiles');
    }
};
