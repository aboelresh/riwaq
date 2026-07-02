<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_quiz_attempts', function (Blueprint $table) {
            $table->integer('attempt_number')->default(1)->after('quiz_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_quiz_attempts', function (Blueprint $table) {
            $table->dropColumn('attempt_number');
        });
    }
};