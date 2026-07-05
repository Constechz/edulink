<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('stream_id')->nullable()->constrained('streams')->nullOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('scoring_configuration_id')->constrained('scoring_configurations')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            
            $table->json('component_scores')->nullable(); // e.g. {"1": 8, "2": 9}
            $table->decimal('raw_class_total', 8, 2)->nullable();
            $table->decimal('scaled_class_score', 8, 2)->nullable();
            $table->decimal('raw_exam_score', 8, 2)->nullable();
            $table->decimal('scaled_exam_score', 8, 2)->nullable();
            $table->decimal('grand_total', 8, 2)->nullable();
            $table->string('grade', 5)->nullable();
            $table->decimal('grade_point', 4, 2)->nullable();
            
            $table->integer('subject_position')->nullable();
            $table->integer('total_students')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_absent_exam')->default(false);
            $table->text('moderation_note')->nullable();
            
            $table->enum('status', ['draft', 'submitted', 'hod_verified', 'approved', 'published'])->default('draft');
            
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('hod_verified_at')->nullable();
            $table->foreignId('hod_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'student_id', 'subject_id', 'term_id', 'academic_year_id'], 'unique_student_term_score');
            $table->index(['school_id', 'class_id', 'term_id']);
        });

        Schema::create('score_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_score_id')->constrained('student_scores')->cascadeOnDelete();
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->string('change_type'); // create, update, delete
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('reason')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('score_history');
        Schema::dropIfExists('student_scores');
    }
};
