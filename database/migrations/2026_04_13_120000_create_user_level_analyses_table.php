<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_level_analyses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->json('stats');
            $t->json('analysis');
            $t->string('model_used')->nullable();
            $t->timestamp('analyzed_at');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_level_analyses');
    }
};
