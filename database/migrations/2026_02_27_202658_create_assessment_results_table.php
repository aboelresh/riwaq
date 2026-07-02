<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recommended_track_id')->nullable()->constrained('tracks')->onDelete('set null');
            $table->json('answers'); // Store all answers
            $table->json('scores'); // Store scores per track
            $table->text('analysis')->nullable(); // AI-generated analysis (future)
            $table->timestamps();
            
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_results');
    }
};