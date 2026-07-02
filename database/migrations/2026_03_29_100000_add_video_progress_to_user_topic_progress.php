<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_topic_progress', function (Blueprint $table) {
            $table->integer('video_watched_seconds')->default(0)->after('is_viewed');
            $table->integer('video_total_seconds')->default(0)->after('video_watched_seconds');
            $table->integer('video_last_position')->default(0)->after('video_total_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('user_topic_progress', function (Blueprint $table) {
            $table->dropColumn(['video_watched_seconds', 'video_total_seconds', 'video_last_position']);
        });
    }
};
