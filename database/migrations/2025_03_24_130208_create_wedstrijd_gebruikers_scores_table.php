<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWedstrijdGebruikersScoresTable extends Migration
{
    public function up()
    {
        Schema::create('match_gebruikers_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wedstrijd_id')->constrained('matches')->onDelete('cascade');
            $table->foreignId('gebruiker_id')->constrained('users')->onDelete('cascade');
            $table->string('kaliber');
            $table->integer('linker_kaart_6')->default(0);
            $table->integer('linker_kaart_7')->default(0);
            $table->integer('linker_kaart_8')->default(0);
            $table->integer('linker_kaart_9')->default(0);
            $table->integer('linker_kaart_10')->default(0);
            $table->integer('rechter_kaart_6')->default(0);
            $table->integer('rechter_kaart_7')->default(0);
            $table->integer('rechter_kaart_8')->default(0);
            $table->integer('rechter_kaart_9')->default(0);
            $table->integer('rechter_kaart_10')->default(0);
            $table->integer('aantal_schoten_buiten_tijd')->default(0);
            $table->integer('afwaarderingen')->default(0);
            $table->integer('totale_punten')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('matches_gebruikers_scores');
    }
}

