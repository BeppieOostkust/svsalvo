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
            // Add round number (1-4 for the 4 series)
            $table->integer('round_number')->default(1)->after('kaliber');
            
            // Add official score flag (only one score per player per caliber counts officially)
            $table->boolean('is_official')->default(true)->after('round_number');
            
            // Add notes for additional context
            $table->text('notes')->nullable()->after('totale_punten');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_gebruikers_scores', function (Blueprint $table) {
            $table->dropColumn(['round_number', 'is_official', 'notes']);
        });
    }
};
