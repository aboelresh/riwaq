<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 191)->unique();       // 191 = max for utf8mb4 on older MySQL
            $table->string('subdomain', 191)->unique()->nullable();  // unique() already creates index
            $table->string('custom_domain', 191)->unique()->nullable();
            $table->string('logo')->nullable();
            $table->string('primary_color', 7)->default('#4f46e5');
            $table->string('timezone')->default('UTC');
            $table->string('locale')->default('en');
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Note: slug, subdomain, custom_domain already indexed via ->unique() above
            // Only add explicit index for non-unique columns
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};