<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();      // note is ABOUT this user
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete(); // note was WRITTEN BY this user
            $table->text('content');
            $table->boolean('is_private')->default(false); // private = only leader sees it
            $table->timestamps();

            $table->index(['team_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_notes');
    }
};
