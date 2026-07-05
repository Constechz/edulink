<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('grading_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name'); // e.g. "GES Basic School Standard", "WAEC SHS Standard"
            $table->string('level'); // e.g. "Primary", "JHS", "SHS", "ALL"
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('grading_scale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_scale_id')->constrained('grading_scales')->cascadeOnDelete();
            $table->string('grade'); // A1, B2, B3, C4, C5, C6, D7, E8, F9
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->decimal('grade_point', 4, 2); // e.g. 1.00 for A1, 9.00 for F9
            $table->string('description')->nullable(); // e.g. Excellent, Very Good, Fail
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('grading_scale_items');
        Schema::dropIfExists('grading_scales');
    }
};
