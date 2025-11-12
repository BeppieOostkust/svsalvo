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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Friendly name
            $table->string('slug')->unique(); // Unique identifier (e.g., 'new_user_temp_password')
            $table->string('subject');
            $table->text('html_content');
            $table->text('text_content')->nullable();
            $table->json('available_variables')->nullable(); // Variables that can be used in this template
            $table->boolean('is_active')->default(true);
            $table->string('category')->default('system'); // system, user, activity, match, feedback, legal
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
