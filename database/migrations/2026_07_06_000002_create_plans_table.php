<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->boolean('is_public')->default(true);
            $table->boolean('is_active')->default(true);

            // Entitlements — what this plan allows
            $table->integer('max_students')->default(50);
            $table->integer('max_instructors')->default(3);
            $table->integer('max_courses')->default(10);
            $table->integer('max_tracks')->default(5);
            $table->integer('max_storage_gb')->default(5);
            $table->integer('max_ai_calls_per_month')->default(500);
            $table->boolean('allow_custom_domain')->default(false);
            $table->boolean('allow_white_label')->default(false);
            $table->boolean('allow_api_access')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};