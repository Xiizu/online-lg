<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // $table->string('aura');
        // $table->string('apparence');
        DB::statement('ALTER TABLE players ADD aura VARCHAR(255) NULL');
        DB::statement('ALTER TABLE players ADD apparence VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
