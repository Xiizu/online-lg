<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Convertit la colonne user_id (BIGINT) en VARCHAR(36) pour supporter les UUID
        DB::statement('ALTER TABLE sessions MODIFY user_id VARCHAR(36) NULL');
    }

    public function down(): void
    {
        // Revient à BIGINT UNSIGNED NULL si besoin
        DB::statement('ALTER TABLE sessions MODIFY user_id BIGINT UNSIGNED NULL');
    }
};
