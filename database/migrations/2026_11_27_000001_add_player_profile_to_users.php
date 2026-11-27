<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('age_group')->nullable();
            $table->string('platform')->nullable();
            $table->json('games')->nullable();
            $table->string('level')->nullable();
            $table->string('play_style')->nullable();
            $table->string('availability', 240)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['age_group', 'platform', 'games', 'level', 'play_style', 'availability']);
        });
    }
};
