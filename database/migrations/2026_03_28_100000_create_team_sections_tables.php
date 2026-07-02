<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_general')->default(false);
            $table->string('color', 50)->nullable();
            $table->string('icon', 30)->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['team_id', 'is_general']);
        });

        Schema::create('team_section_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('team_sections')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 20)->nullable(); // lead, member
            $table->timestamps();

            $table->unique(['section_id', 'user_id']);
        });

        // Add section_id to team_tasks
        Schema::table('team_tasks', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->after('team_id')->constrained('team_sections')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('team_tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('section_id');
        });
        Schema::dropIfExists('team_section_members');
        Schema::dropIfExists('team_sections');
    }
};
