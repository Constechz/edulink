<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->decimal('amount_paid', 8, 2);
            $table->string('currency')->default('GHS');
            $table->string('payment_reference')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('status')->default('active'); // active, expired, canceled
            $table->boolean('auto_renew')->default(true);
            $table->timestamps();
        });

        Schema::create('sms_credit_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->enum('type', ['purchase', 'usage', 'refund']);
            $table->integer('credits');
            $table->integer('balance_after');
            $table->string('reference')->nullable();
            $table->string('note')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('feature_key');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'feature_key']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('feature_flags');
        Schema::dropIfExists('sms_credit_ledger');
        Schema::dropIfExists('subscriptions');
    }
};
