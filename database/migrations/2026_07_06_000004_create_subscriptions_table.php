<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();
            $table->enum('status', [
                'trialing', 'active', 'past_due', 'canceled', 'expired'
            ])->default('trialing');
            $table->enum('billing_interval', ['monthly', 'yearly'])->default('monthly');

            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('canceled_at')->nullable();

            // Provider integration (Stripe, etc.) — placeholder for now
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable()->unique();
            $table->string('provider_status')->nullable();
            $table->json('provider_data')->nullable();

            $table->timestamps();

            $table->index(['organization_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};