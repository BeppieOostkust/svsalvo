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
        Schema::table('users', function (Blueprint $table) {
            // Add indexes for registration form performance
            $table->index('is_active_member');
            $table->index('is_blocked');
            $table->index(['is_active_member', 'is_blocked']); // Composite index for the whereActive scope
            $table->index('name'); // For sorting by name
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active_member']);
            $table->dropIndex(['is_blocked']);
            $table->dropIndex(['is_active_member', 'is_blocked']);
            $table->dropIndex(['name']);
        });
    }
};
