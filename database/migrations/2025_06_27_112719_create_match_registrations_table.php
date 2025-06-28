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
        Schema::create('match_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['aangemeld', 'bevestigd', 'geweigerd', 'geannuleerd', 'aanwezig', 'afwezig'])->default('aangemeld');
            $table->enum('caliber', ['kkp', 'gkp'])->comment('Klein Kaliber Pistool or Groot Kaliber Pistool');
            $table->text('notes')->nullable();
            $table->datetime('registered_at');
            $table->decimal('paid_amount', 8, 2)->default(0.00);
            $table->boolean('payment_confirmed')->default(false);
            $table->boolean('converted_to_participant')->default(false)->comment('Whether this registration has been converted to a MatchGebruikerScore');
            $table->json('additional_data')->nullable(); // For custom fields
            $table->timestamps();
            
            $table->unique(['match_id', 'user_id']);
            $table->index(['match_id', 'status']);
            $table->index(['match_id', 'converted_to_participant']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_registrations');
    }
};
