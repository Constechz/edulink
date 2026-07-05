<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Assignments Tables
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('stream_id')->nullable()->constrained('streams')->nullOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('due_date');
            $table->decimal('max_marks', 8, 2)->default(100.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->dateTime('submitted_at');
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->string('status')->default('submitted'); // submitted, graded, late
            $table->timestamps();
        });

        Schema::create('assignment_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('attachable_type'); // Assignment or AssignmentSubmission
            $table->unsignedBigInteger('attachable_id');
            $table->string('file_path');
            $table->string('file_name');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('assignment_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('assignment_submissions')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->text('comments');
            $table->timestamp('created_at')->nullable();
        });

        // LMS Tables
        Schema::create('lms_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('lms_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('lms_courses')->cascadeOnDelete();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('lms_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lms_lessons')->cascadeOnDelete();
            $table->string('title');
            $table->string('resource_type'); // file, video_link, doc, pdf, url
            $table->string('file_path')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('lms_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('lms_courses')->cascadeOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained('lms_lessons')->nullOnDelete();
            $table->string('title');
            $table->integer('duration_minutes')->default(30);
            $table->decimal('passing_percentage', 5, 2)->default(50.00);
            $table->timestamps();
        });

        Schema::create('lms_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('lms_quizzes')->cascadeOnDelete();
            $table->text('question_text');
            $table->string('question_type'); // single_choice, multiple_choice, boolean, short_answer
            $table->json('options_json')->nullable();
            $table->text('correct_answer');
            $table->integer('points')->default(1);
            $table->timestamps();
        });

        Schema::create('lms_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('lms_quizzes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->boolean('is_passed')->default(false);
            $table->timestamp('attempted_at')->nullable();
        });

        Schema::create('lms_forums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('lms_courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('lms_forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_id')->constrained('lms_forums')->cascadeOnDelete();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('lms_forum_posts')->nullOnDelete();
        });

        Schema::create('lms_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('lms_courses')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('certificate_number')->unique();
            $table->date('issued_at');
            $table->timestamps();
        });

        Schema::create('lms_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('lms_courses')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('enrolled_at');
            $table->timestamps();

            $table->unique(['course_id', 'student_id']);
        });

        Schema::create('lms_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained('lms_lessons')->cascadeOnDelete();
            $table->dateTime('completed_at');
            $table->timestamps();

            $table->unique(['student_id', 'lesson_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('lms_progress');
        Schema::dropIfExists('lms_enrollments');
        Schema::dropIfExists('lms_certificates');
        Schema::dropIfExists('lms_forum_posts');
        Schema::dropIfExists('lms_forums');
        Schema::dropIfExists('lms_quiz_attempts');
        Schema::dropIfExists('lms_quiz_questions');
        Schema::dropIfExists('lms_quizzes');
        Schema::dropIfExists('lms_resources');
        Schema::dropIfExists('lms_lessons');
        Schema::dropIfExists('lms_courses');
        Schema::dropIfExists('assignment_feedback');
        Schema::dropIfExists('assignment_attachments');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
    }
};
