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
        Schema::table('match_gebruikers_scores', function (Blueprint $table) {
            $table->integer('baan_nummer')->nullable()->after('kaliber');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_gebruikers_scores', function (Blueprint $table) {
            $table->dropColumn('baan_nummer');
        });
    }
};
