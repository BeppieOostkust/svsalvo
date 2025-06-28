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
        Schema::create('membership_applications', function (Blueprint $table) {
            $table->id();
            $table->string('voornaam'); // First Name
            $table->string('achternaam'); // Last Name
            $table->string('email')->unique();
            $table->string('telefoonnummer'); // Phone Number
            $table->date('geboortedatum'); // Date of Birth
            $table->integer('leeftijd'); // Age (calculated)
            $table->enum('status', ['nieuw', 'in_behandeling', 'goedgekeurd', 'afgewezen'])->default('nieuw');
            $table->text('opmerkingen')->nullable(); // Admin notes
            $table->timestamp('aangemeld_op')->useCurrent(); // Applied at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_applications');
    }
};
