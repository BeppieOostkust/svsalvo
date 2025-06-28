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
        Schema::create('competition_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Service Pistool, Kleinkaliber Geweer, etc.
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('weapon_type', ['pistool', 'geweer', 'luchtgeweer', 'boog'])->default('pistool');
            $table->enum('caliber', ['gkp', 'kkp', 'lucht', '22lr', 'centerfire'])->nullable();
            $table->json('rules')->nullable(); // Competition specific rules
            $table->json('scoring_system')->nullable(); // How scoring works
            $table->integer('max_shots')->nullable();
            $table->integer('time_limit')->nullable(); // in minutes
            $table->decimal('target_distance', 8, 2)->nullable(); // in meters
            $table->string('target_type')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['weapon_type', 'is_active']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_types');
    }
};
