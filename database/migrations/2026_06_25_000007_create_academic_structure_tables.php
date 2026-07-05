<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name'); // e.g. "2024/2025"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('name'); // e.g. "Term 1", "Term 2", "Term 3"
            $table->date('start_date');
            $table->date('end_date');
            $table->date('reopening_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->unsignedBigInteger('hod_user_id')->nullable(); // FK to users
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('name');
            $table->string('code');
            $table->integer('duration_years')->default(3);
            $table->enum('level', ['Nursery', 'KG', 'Primary', 'JHS', 'SHS', 'TVET', 'Tertiary']);
            $table->timestamps();
        });

        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('programme_id')->nullable()->constrained('programmes')->nullOnDelete();
            $table->string('name'); // e.g. "Class 3", "Form 1"
            $table->enum('level', ['Nursery', 'KG', 'Primary', 'JHS', 'SHS', 'TVET', 'Tertiary']);
            $table->unsignedBigInteger('class_teacher_id')->nullable(); // FK to users
            $table->integer('capacity')->default(40);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('name'); // e.g. "A", "B", "Gold", "Blue"
            $table->unsignedBigInteger('class_teacher_id')->nullable(); // FK to users
            $table->integer('capacity')->default(40);
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('name');
            $table->string('code');
            $table->enum('level', ['Nursery', 'KG', 'Primary', 'JHS', 'SHS', 'TVET', 'Tertiary']);
            $table->boolean('is_core')->default(true);
            $table->boolean('is_elective')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('stream_id')->nullable()->constrained('streams')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->unsignedBigInteger('teacher_id')->nullable(); // FK to users
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained('terms')->cascadeOnDelete();
            $table->integer('periods_per_week')->default(4);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('class_subjects');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('streams');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('programmes');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('terms');
        Schema::dropIfExists('academic_years');
    }
};
