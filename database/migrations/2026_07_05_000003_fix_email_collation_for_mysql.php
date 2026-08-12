<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix email case-sensitivity on MySQL production.
     *
     * On SQLite (sandbox): email comparison is case-sensitive by default.
     * We handle this with LOWER(email) in LoginController — works on both.
     *
     * On MySQL (production): the default collation is usually utf8mb4_0900_ai_ci
     * which IS case-insensitive, so this migration is a safety net to ensure
     * the collation is explicitly set to utf8mb4_unicode_ci across environments.
     *
     * This migration is safe to run on SQLite (it skips the ALTER TABLE).
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE users MODIFY email VARCHAR(255)
                 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL"
            );
        }
        // SQLite: no action needed — LOWER(email) in LoginController handles it
    }

    public function down(): void
    {
        // No rollback needed — collation change is safe to keep
    }
};