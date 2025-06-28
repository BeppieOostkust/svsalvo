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
        Schema::create('activity_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['aangemeld', 'bevestigd', 'geannuleerd', 'afwezig', 'aanwezig'])->default('aangemeld');
            $table->text('notes')->nullable();
            $table->datetime('registered_at');
            $table->decimal('paid_amount', 8, 2)->default(0.00);
            $table->boolean('payment_confirmed')->default(false);
            $table->json('additional_data')->nullable(); // For custom fields
            $table->timestamps();
            
            $table->unique(['activity_id', 'user_id']);
            $table->index(['activity_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_registrations');
    }
};
