<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->boolean('is_alive')->default(true);
            $table->char('token', 36)->unique();
            $table->text('comment')->nullable();
            $table->string('aura')->nullable();
            $table->string('apparence')->nullable();

            // Relations
            $table->foreignId('camp_id')->nullable()->constrained('camps')->onDelete('set null');
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
