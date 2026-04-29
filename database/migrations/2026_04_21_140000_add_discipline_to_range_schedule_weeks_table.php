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
        Schema::table('range_schedule_weeks', function (Blueprint $table) {
            $table->string('discipline', 20)
                ->default('pistool')
                ->after('week_number');

            $table->dropUnique('range_schedule_unique_week_per_quarter');
            $table->unique(['year', 'quarter', 'week_number', 'discipline'], 'range_schedule_unique_week_per_quarter_discipline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('range_schedule_weeks', function (Blueprint $table) {
            $table->dropUnique('range_schedule_unique_week_per_quarter_discipline');
            $table->dropColumn('discipline');
            $table->unique(['year', 'quarter', 'week_number'], 'range_schedule_unique_week_per_quarter');
        });
    }
};
