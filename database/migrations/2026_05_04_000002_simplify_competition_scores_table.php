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
        // Drop and recreate the competition_scores table with simplified schema
        Schema::dropIfExists('competition_scores');

        Schema::create('competition_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_round_id')->constrained('competition_rounds')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('kaliber', ['meesterkaart_zwaar', 'meesterkaart_licht', 'kk_geweer_open_50m', 'kk_geweer_optisch_100m', 'gk_precision_100m', 'militair_geweer', 'militair_geweer_optisch', 'veteranen_geweer'])->comment('Which caliber scored');
            
            // Single score per card
            $table->integer('linker_score')->default(0)->comment('Left card score');
            $table->integer('rechter_score')->default(0)->comment('Right card score');
            
            // Total (auto-calculated)
            $table->integer('totale_punten')->default(0)->comment('Auto-calculated: linker + rechter');
            
            // Metadata
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->unique(['competition_round_id', 'user_id', 'kaliber']);
            $table->index(['competition_round_id', 'user_id']);
            $table->index(['user_id', 'kaliber']);
            $table->index(['competition_round_id', 'kaliber']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_scores');

        // Recreate old schema if needed
        Schema::create('competition_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_round_id')->constrained('competition_rounds')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('kaliber', ['meesterkaart_zwaar', 'meesterkaart_licht', 'kk_geweer_open_50m', 'kk_geweer_optisch_100m', 'gk_precision_100m', 'militair_geweer', 'militair_geweer_optisch', 'veteranen_geweer']);
            
            $table->integer('linker_score_1')->default(0);
            $table->integer('linker_score_2')->default(0);
            $table->integer('linker_totaal')->default(0);
            $table->integer('rechter_score_1')->default(0);
            $table->integer('rechter_score_2')->default(0);
            $table->integer('rechter_totaal')->default(0);
            $table->integer('totale_punten')->default(0);
            $table->integer('aantal_schoten_buiten_tijd')->default(0);
            $table->integer('afwaarderingen')->default(0);
            $table->integer('baan_nummer')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->unique(['competition_round_id', 'user_id', 'kaliber']);
        });
    }
};
