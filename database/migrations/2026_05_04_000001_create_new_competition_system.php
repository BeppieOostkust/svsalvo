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
        // Create competitions table (jaarlijkse competitie)
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->year('jaar')->unique()->comment('Competition year (e.g., 2025, 2026)');
            $table->string('naam')->comment('Competition name');
            $table->text('beschrijving')->nullable();
            $table->enum('status', ['gepland', 'bezig', 'afgelopen', 'geannuleerd'])->default('gepland');
            $table->timestamps();
            
            $table->index('jaar');
            $table->index('status');
        });

        // Create competition rounds table (5 beurten per competitie)
        Schema::create('competition_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('competitions')->cascadeOnDelete();
            $table->integer('round_number')->comment('Round 1-5');
            $table->string('naam')->comment('e.g., "Beurt 1"');
            $table->date('datum')->nullable();
            $table->text('beschrijving')->nullable();
            $table->timestamps();
            
            $table->unique(['competition_id', 'round_number']);
            $table->index(['competition_id', 'round_number']);
        });

        // Create competition registrations table (deeln emers)
        Schema::create('competition_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('competitions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('kaliber', ['meesterkaart_zwaar', 'meesterkaart_licht', 'kk_geweer_open_50m', 'kk_geweer_optisch_100m', 'gk_precision_100m', 'militair_geweer', 'militair_geweer_optisch', 'veteranen_geweer'])->comment('Discipline/kaliber');
            $table->enum('status', ['actief', 'inactief', 'afgemeld'])->default('actief');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['competition_id', 'user_id', 'kaliber']);
            $table->index(['competition_id', 'user_id']);
            $table->index(['competition_id', 'status']);
        });

        // Create competition scores table (scores per beurt)
        Schema::create('competition_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_round_id')->constrained('competition_rounds')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('kaliber', ['meesterkaart_zwaar', 'meesterkaart_licht', 'kk_geweer_open_50m', 'kk_geweer_optisch_100m', 'gk_precision_100m', 'militair_geweer', 'militair_geweer_optisch', 'veteranen_geweer'])->comment('Which caliber scored');
            
            // Left card scores (2 input fields that add up to total)
            $table->integer('linker_score_1')->default(0)->comment('Left card score 1');
            $table->integer('linker_score_2')->default(0)->comment('Left card score 2');
            $table->integer('linker_totaal')->default(0)->comment('Auto-calculated: score_1 + score_2');
            
            // Right card scores (2 input fields that add up to total)
            $table->integer('rechter_score_1')->default(0)->comment('Right card score 1');
            $table->integer('rechter_score_2')->default(0)->comment('Right card score 2');
            $table->integer('rechter_totaal')->default(0)->comment('Auto-calculated: score_1 + score_2');
            
            // Combined scores
            $table->integer('totale_punten')->default(0)->comment('Auto-calculated total: linker + rechter - penalties');
            
            // Penalties and metadata
            $table->integer('aantal_schoten_buiten_tijd')->default(0)->comment('Shots outside time limit');
            $table->integer('afwaarderingen')->default(0)->comment('Deductions/disqualifications');
            $table->integer('baan_nummer')->nullable()->comment('Lane/range number');
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
        Schema::dropIfExists('competition_registrations');
        Schema::dropIfExists('competition_rounds');
        Schema::dropIfExists('competitions');
    }
};
