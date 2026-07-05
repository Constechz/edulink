<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('scoring_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            $table->enum('level', ['ALL', 'Nursery', 'KG', 'Primary', 'JHS', 'SHS', 'TVET', 'Tertiary'])->default('ALL');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('name');
            $table->decimal('class_score_max', 8, 2)->default(50);
            $table->decimal('class_score_weight', 8, 2)->default(50);
            $table->decimal('exam_score_max', 8, 2)->default(100);
            $table->decimal('exam_score_weight', 8, 2)->default(50);
            $table->decimal('grand_total', 8, 2)->default(100);
            $table->enum('rounding_method', ['ROUND', 'FLOOR', 'CEIL'])->default('ROUND');
            $table->tinyInteger('decimal_places')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'level', 'is_active']);
        });

        Schema::create('score_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('scoring_configuration_id')->constrained('scoring_configurations')->cascadeOnDelete();
            $table->string('name'); // e.g. "Exercise 1", "Homework", "Project Work"
            $table->decimal('max_marks', 8, 2);
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_required')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['scoring_configuration_id', 'display_order']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('score_components');
        Schema::dropIfExists('scoring_configurations');
    }
};
