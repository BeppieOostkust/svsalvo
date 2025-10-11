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
            // Add 5-ring columns after the existing ring columns
            $table->integer('linker_kaart_5')->default(0)->after('linker_kaart_6');
            $table->integer('rechter_kaart_5')->default(0)->after('rechter_kaart_6');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_gebruikers_scores', function (Blueprint $table) {
            $table->dropColumn(['linker_kaart_5', 'rechter_kaart_5']);
        });
    }
};
