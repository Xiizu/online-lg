<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            // 'longText' pour stocker beaucoup de contenu
            // 'nullable' est CRUCIAL pour ne pas faire planter les lignes existantes
            $table->longText('notes')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
