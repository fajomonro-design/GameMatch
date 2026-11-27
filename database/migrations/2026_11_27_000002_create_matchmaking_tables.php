<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('language')->nullable();
            $table->text('bio')->nullable();
            $table->json('availability_slots')->nullable();
        });

        Schema::create('player_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_one_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('player_two_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->unique(['player_one_id', 'player_two_id']);
        });

        Schema::create('player_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('player_id')->constrained('users')->cascadeOnDelete();
            $table->string('reason');
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_reports');
        Schema::dropIfExists('player_contacts');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['language', 'bio', 'availability_slots']);
        });
    }
};
