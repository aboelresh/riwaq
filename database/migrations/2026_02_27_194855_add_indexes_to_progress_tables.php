<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // User Course Progress Indexes
        Schema::table('user_course_progress', function (Blueprint $table) {
            $table->index('user_id', 'idx_ucp_user');
            $table->index('course_id', 'idx_ucp_course');
            $table->index(['user_id', 'course_id'], 'idx_ucp_user_course');
            $table->index('is_unlocked', 'idx_ucp_unlocked');
            $table->index('is_completed', 'idx_ucp_completed');
        });

        // User Topic Progress Indexes
        Schema::table('user_topic_progress', function (Blueprint $table) {
            $table->index('user_id', 'idx_utp_user');
            $table->index('topic_id', 'idx_utp_topic');
            $table->index(['user_id', 'topic_id'], 'idx_utp_user_topic');
            $table->index('is_unlocked', 'idx_utp_unlocked');
            $table->index('is_viewed', 'idx_utp_viewed');
        });

        // User Quiz Attempts Indexes
        Schema::table('user_quiz_attempts', function (Blueprint $table) {
            $table->index('user_id', 'idx_uqa_user');
            $table->index('quiz_id', 'idx_uqa_quiz');
            $table->index(['user_id', 'quiz_id'], 'idx_uqa_user_quiz');
            $table->index('passed', 'idx_uqa_passed');
            $table->index('can_retry_at', 'idx_uqa_retry');
            $table->index('attempted_at', 'idx_uqa_attempted');
        });

        // User Tracks Indexes
        Schema::table('user_tracks', function (Blueprint $table) {
            $table->index('user_id', 'idx_ut_user');
            $table->index('track_id', 'idx_ut_track');
            $table->index(['user_id', 'track_id'], 'idx_ut_user_track');
        });

        // Team Members Indexes
        Schema::table('team_members', function (Blueprint $table) {
            $table->index('user_id', 'idx_tm_user');
            $table->index('team_id', 'idx_tm_team');
            $table->index('track_id', 'idx_tm_track');
            $table->index(['user_id', 'team_id'], 'idx_tm_user_team');
        });
    }

    public function down(): void
    {
        Schema::table('user_course_progress', function (Blueprint $table) {
            $table->dropIndex('idx_ucp_user');
            $table->dropIndex('idx_ucp_course');
            $table->dropIndex('idx_ucp_user_course');
            $table->dropIndex('idx_ucp_unlocked');
            $table->dropIndex('idx_ucp_completed');
        });

        Schema::table('user_topic_progress', function (Blueprint $table) {
            $table->dropIndex('idx_utp_user');
            $table->dropIndex('idx_utp_topic');
            $table->dropIndex('idx_utp_user_topic');
            $table->dropIndex('idx_utp_unlocked');
            $table->dropIndex('idx_utp_viewed');
        });

        Schema::table('user_quiz_attempts', function (Blueprint $table) {
            $table->dropIndex('idx_uqa_user');
            $table->dropIndex('idx_uqa_quiz');
            $table->dropIndex('idx_uqa_user_quiz');
            $table->dropIndex('idx_uqa_passed');
            $table->dropIndex('idx_uqa_retry');
            $table->dropIndex('idx_uqa_attempted');
        });

        Schema::table('user_tracks', function (Blueprint $table) {
            $table->dropIndex('idx_ut_user');
            $table->dropIndex('idx_ut_track');
            $table->dropIndex('idx_ut_user_track');
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->dropIndex('idx_tm_user');
            $table->dropIndex('idx_tm_team');
            $table->dropIndex('idx_tm_track');
            $table->dropIndex('idx_tm_user_team');
        });
    }
};