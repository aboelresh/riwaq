<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dm_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_one_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_two_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_one_id', 'user_two_id', 'team_id']);
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('channel_type'); // team, section, dm
            $table->unsignedBigInteger('channel_id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->string('type')->default('text'); // text, system
            $table->unsignedBigInteger('reply_to_id')->nullable();
            $table->timestamps();

            $table->index(['channel_type', 'channel_id', 'created_at']);
            $table->foreign('reply_to_id')->references('id')->on('chat_messages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('dm_channels');
    }
};
