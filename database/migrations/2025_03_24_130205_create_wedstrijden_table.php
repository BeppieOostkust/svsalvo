<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWedstrijdenTable extends Migration
{
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('naam'); // Nieuw veld voor de naam van de wedstrijd
            $table->text('beschrijving')->nullable(); // Nieuw veld voor de beschrijving
            $table->string('categorie')->nullable(); // Nieuw veld voor de categorie
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('matches');
    }
}
