<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all existing users to have show_scores_public = false
        DB::table('users')->update(['show_scores_public' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally restore to true if needed
        DB::table('users')->update(['show_scores_public' => true]);
    }
};
