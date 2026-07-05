<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            $table->string('student_id_number');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('nationality')->default('Ghanaian');
            $table->string('religion')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('photo')->nullable();
            $table->string('address')->nullable();
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->boolean('has_disability')->default(false);
            $table->text('disability_notes')->nullable();
            
            // Houses & scholarships (created later in operational/fin modules or soft-linked)
            $table->unsignedBigInteger('house_id')->nullable();
            $table->unsignedBigInteger('scholarship_id')->nullable();
            
            $table->string('previous_school')->nullable();
            $table->date('transfer_date')->nullable();
            $table->text('transfer_reason')->nullable();
            
            $table->foreignId('current_class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('current_stream_id')->nullable()->constrained('streams')->nullOnDelete();
            
            $table->date('enrollment_date');
            $table->enum('status', ['active', 'graduated', 'transferred', 'withdrawn', 'deceased'])->default('active');
            $table->string('nhis_number')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'student_id_number']);
        });

        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('relationship'); // Father, Mother, Uncle, Aunt, Guardian, etc.
            $table->string('phone');
            $table->string('alt_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('occupation')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->string('photo')->nullable();
            $table->boolean('can_pickup')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('student_guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('guardian_id')->constrained('guardians')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained('terms')->nullOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('stream_id')->nullable()->constrained('streams')->nullOnDelete();
            $table->date('enrollment_date');
            $table->string('status')->default('active'); // active, repeat, promoted
            $table->foreignId('promoted_from_class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('student_enrollments');
        Schema::dropIfExists('student_guardians');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('students');
    }
};
