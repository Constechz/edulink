<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            $table->string('staff_number');
            $table->string('designation');
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->enum('employment_type', ['permanent', 'contract', 'temporary'])->default('permanent');
            $table->string('qualification')->nullable();
            $table->string('specialization')->nullable();
            $table->string('professional_cert')->nullable();
            $table->date('date_joined');
            $table->date('date_left')->nullable();
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->string('salary_grade')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('ssnit_number')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('national_id_type')->nullable();
            $table->string('national_id_number')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'staff_number']);
        });

        Schema::create('staff_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->string('document_type'); // e.g. CV, Contract, Certificate
            $table->string('file_path');
            $table->dateTime('uploaded_at');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('staff_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->string('institution');
            $table->string('qualification'); // B.Ed, M.Sc, etc.
            $table->integer('year_obtained');
            $table->string('certificate_path')->nullable();
            $table->timestamps();
        });

        Schema::create('staff_appraisals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained('terms')->nullOnDelete();
            $table->foreignId('appraiser_id')->constrained('users')->cascadeOnDelete();
            $table->json('criteria'); // ratings for standard metrics
            $table->decimal('total_score', 5, 2);
            $table->string('grade');
            $table->text('comments')->nullable();
            $table->string('status')->default('draft'); // draft, submitted, completed
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('staff_appraisals');
        Schema::dropIfExists('staff_qualifications');
        Schema::dropIfExists('staff_documents');
        Schema::dropIfExists('staff');
    }
};
