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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('location')->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('type', ['training', 'wedstrijd', 'evenement', 'vergadering', 'cursus', 'toernooi', 'competitie'])->default('evenement');
            $table->enum('status', ['gepland', 'bevestigd', 'geannuleerd', 'uitgesteld', 'afgelopen'])->default('gepland');
            $table->integer('max_participants')->nullable();
            $table->integer('current_participants')->default(0);
            $table->decimal('entry_fee', 8, 2)->default(0.00);
            $table->boolean('requires_registration')->default(false);
            $table->datetime('registration_deadline')->nullable();
            $table->text('requirements')->nullable(); // Equipment, skill level, etc.
            $table->text('contact_info')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('additional_info')->nullable(); // For flexible data
            $table->foreignId('organizer_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            $table->index(['type', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
