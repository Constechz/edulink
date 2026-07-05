<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('promotion_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->enum('level', ['nursery', 'kg', 'primary', 'jhs', 'shs', 'tertiary']);
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->enum('method', ['annual_average', 'two_of_three', 'subject_pass_count'])->default('annual_average');
            $table->json('term_weights_json')->nullable(); // e.g. {"term1":1,"term2":1,"term3":1}
            $table->decimal('promotion_threshold', 5, 2)->default(40.00);
            $table->decimal('conditional_threshold', 5, 2)->nullable()->default(35.00);
            $table->integer('min_subjects_to_pass')->nullable();
            $table->decimal('per_subject_pass_mark', 5, 2)->nullable();
            $table->integer('repeat_limit')->default(1);
            $table->boolean('exclude_terminal_year')->default(true);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('promotion_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('level');
            $table->foreignId('run_by')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['draft', 'teacher_reviewed', 'approved', 'published'])->default('draft');
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('student_promotion_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('promotion_run_id')->constrained('promotion_runs')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('from_class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('to_class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->decimal('term1_score', 5, 2)->nullable();
            $table->decimal('term2_score', 5, 2)->nullable();
            $table->decimal('term3_score', 5, 2)->nullable();
            $table->decimal('computed_average', 5, 2)->nullable();
            $table->string('method_used');
            $table->json('rule_snapshot_json'); // frozen configuration fields
            $table->enum('decision', ['promoted', 'conditional', 'repeat', 'bece_candidate', 'wassce_candidate']);
            $table->boolean('is_override')->default(false);
            $table->text('override_reason')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });

        Schema::create('student_repeat_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->integer('repeat_count_at_this_class')->default(1);
            $table->text('reason')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('student_repeat_history');
        Schema::dropIfExists('student_promotion_records');
        Schema::dropIfExists('promotion_runs');
        Schema::dropIfExists('promotion_configurations');
    }
};
