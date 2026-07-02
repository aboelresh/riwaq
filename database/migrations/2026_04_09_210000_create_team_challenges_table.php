<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('target_type'); // topics_viewed, quizzes_passed, xp_earned, tasks_completed
            $table->integer('target_value');
            $table->integer('current_value')->default(0);
            $table->date('week_start'); // Monday
            $table->date('week_end');   // Sunday
            $table->boolean('is_completed')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['team_id', 'week_start', 'week_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_challenges');
    }
};
