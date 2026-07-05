<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_report_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            
            $table->string('conduct')->nullable();
            $table->string('attitude')->nullable();
            $table->string('interest')->nullable();
            $table->text('remarks')->nullable();
            $table->date('reopening_date')->nullable();
            
            $table->integer('attendance_present')->nullable();
            $table->integer('attendance_total')->nullable();
            
            $table->timestamps();

            $table->unique(['school_id', 'student_id', 'term_id', 'academic_year_id'], 'unique_student_term_report');
            $table->index(['school_id', 'student_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('student_report_details');
    }
};
