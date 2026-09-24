<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tenant-owned tables that need organization_id
    private array $tables = [
        'tracks',
        'courses',
        'topics',
        'quizzes',
        'videos',
        'teams',
        'user_tracks',
        'user_course_progress',
        'user_topic_progress',
        'user_quiz_attempts',
        'assessment_results',
        'notifications',
        'user_level_analyses',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasColumn($table, 'organization_id')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    // Nullable + default 1 for existing data migration
                    $t->unsignedBigInteger('organization_id')->nullable()->after('id');
                    $t->index('organization_id', "idx_{$table}_org_id");
                });
            }
        }

        // Seed existing records with organization_id = 1 (default org)
        foreach ($this->tables as $table) {
            \Illuminate\Support\Facades\DB::table($table)
                ->whereNull('organization_id')
                ->update(['organization_id' => 1]);
        }
    }

   public function down(): void
{
    foreach ($this->tables as $table) {
        if (Schema::hasColumn($table, 'organization_id')) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (Schema::hasIndex($table, "idx_{$table}_org_id")) {
                    $t->dropIndex("idx_{$table}_org_id");
                }
                $t->dropColumn('organization_id');
            });
        }
    }
}
};