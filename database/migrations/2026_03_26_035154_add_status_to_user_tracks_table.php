<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_tracks', function (Blueprint $table) {
            // active = currently studying, waitlist = enrolled but not active, completed = done
            $table->enum('status', ['active', 'waitlist', 'completed'])->default('active')->after('track_id');
            // Min score percentage to unlock next track (admin-configurable later, default 25%)
            $table->integer('unlock_threshold')->default(25)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('user_tracks', function (Blueprint $table) {
            $table->dropColumn(['status', 'unlock_threshold']);
        });
    }
};
