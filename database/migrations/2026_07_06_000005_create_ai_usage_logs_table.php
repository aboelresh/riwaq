<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('feature')->default('chat'); // chat, quiz_gen, report
            $table->integer('tokens_used')->default(0);
            $table->string('provider')->nullable();
            $table->boolean('was_fallback')->default(false);
            $table->timestamp('used_at');

            $table->index(['organization_id', 'used_at']);
            $table->index(['organization_id', 'feature', 'used_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};