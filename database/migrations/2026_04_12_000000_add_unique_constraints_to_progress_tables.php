<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("DELETE FROM user_tracks WHERE id NOT IN (SELECT MIN(id) FROM user_tracks GROUP BY user_id, track_id)");
        DB::statement("DELETE FROM user_course_progress WHERE id NOT IN (SELECT MIN(id) FROM user_course_progress GROUP BY user_id, course_id)");
        DB::statement("DELETE FROM user_topic_progress WHERE id NOT IN (SELECT MIN(id) FROM user_topic_progress GROUP BY user_id, topic_id)");

        Schema::table('user_tracks', function (Blueprint $table) {
            $table->unique(['user_id', 'track_id'], 'user_tracks_user_track_unique');
        });

        Schema::table('user_course_progress', function (Blueprint $table) {
            $table->unique(['user_id', 'course_id'], 'user_course_progress_user_course_unique');
        });

        Schema::table('user_topic_progress', function (Blueprint $table) {
            $table->unique(['user_id', 'topic_id'], 'user_topic_progress_user_topic_unique');
        });
    }

    public function down(): void
    {
        Schema::table('user_tracks', function (Blueprint $table) {
            $table->dropUnique('user_tracks_user_track_unique');
        });

        Schema::table('user_course_progress', function (Blueprint $table) {
            $table->dropUnique('user_course_progress_user_course_unique');
        });

        Schema::table('user_topic_progress', function (Blueprint $table) {
            $table->dropUnique('user_topic_progress_user_topic_unique');
        });
    }
};
