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
        Schema::table('match_registrations', function (Blueprint $table) {
            // Drop the old unique constraint that only considers match_id and user_id
            $table->dropUnique(['match_id', 'user_id']);
            
            // Add new unique constraint that includes caliber
            // This allows one registration per user per match per caliber
            $table->unique(['match_id', 'user_id', 'caliber'], 'match_user_caliber_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_registrations', function (Blueprint $table) {
            // Drop the new constraint
            $table->dropUnique('match_user_caliber_unique');
            
            // Restore the old constraint
            $table->unique(['match_id', 'user_id']);
        });
    }
};
