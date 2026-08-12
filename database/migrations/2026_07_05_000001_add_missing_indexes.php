<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('username', 'idx_users_username');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->index('created_by', 'idx_teams_created_by');
        });

        Schema::table('team_tasks', function (Blueprint $table) {
            $table->index('status', 'idx_team_tasks_status');
            $table->index('priority', 'idx_team_tasks_priority');
            $table->index(['team_id', 'status'], 'idx_team_tasks_team_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_username');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropIndex('idx_teams_created_by');
        });

        Schema::table('team_tasks', function (Blueprint $table) {
            $table->dropIndex('idx_team_tasks_status');
            $table->dropIndex('idx_team_tasks_priority');
            $table->dropIndex('idx_team_tasks_team_status');
        });
    }
};