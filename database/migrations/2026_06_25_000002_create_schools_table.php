<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('school_code')->unique();
            $table->string('logo')->nullable();
            $table->string('address')->nullable();
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website_domain')->nullable();
            $table->string('custom_domain')->nullable()->unique();
            $table->string('subdomain')->unique();
            $table->foreignId('plan_id')->constrained('plans');
            $table->string('subscription_status')->default('trial'); // trial, active, suspended, expired
            $table->timestamp('trial_ends_at')->nullable();
            $table->string('owner_name');
            $table->string('owner_email');
            $table->string('owner_phone')->nullable();
            $table->json('branding')->nullable(); // primary_color, secondary_color, heading_font, etc.
            $table->json('settings')->nullable(); // general settings
            $table->text('sms_gateway_config')->nullable(); // encrypted JSON
            $table->text('email_config')->nullable(); // encrypted JSON
            $table->boolean('is_active')->default(true);
            $table->boolean('onboarding_completed')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('schools');
    }
};
