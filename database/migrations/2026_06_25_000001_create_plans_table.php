<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Free, Standard, Premium, Enterprise
            $table->decimal('price_monthly', 10, 2);
            $table->decimal('price_yearly', 10, 2);
            $table->integer('max_students')->default(-1); // -1 = unlimited
            $table->integer('max_staff')->default(-1);
            $table->integer('max_campuses')->default(1);
            $table->json('features')->nullable();
            $table->integer('sms_credits_monthly')->default(0);
            $table->integer('storage_gb')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('plans');
    }
};
