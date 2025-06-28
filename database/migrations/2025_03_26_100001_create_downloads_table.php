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
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type'); // pdf, doc, xls, etc.
            $table->integer('file_size'); // in bytes
            $table->enum('category', ['formulieren', 'reglementen', 'resultaten', 'documenten', 'fotos'])->default('documenten');
            $table->boolean('is_public')->default(true);
            $table->boolean('requires_login')->default(false);
            $table->integer('download_count')->default(0);
            $table->json('allowed_roles')->nullable(); // For role-based access
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            $table->index(['category', 'is_public']);
            $table->index('file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
