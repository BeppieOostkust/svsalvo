<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE competition_registrations MODIFY kaliber ENUM('gkp', 'kkp', 'meesterkaart_zwaar', 'meesterkaart_licht', 'kk_geweer_open_50m', 'kk_geweer_optisch_100m', 'gk_precision_100m', 'militair_geweer', 'militair_geweer_optisch', 'veteranen_geweer') NOT NULL");
        DB::statement("ALTER TABLE competition_scores MODIFY kaliber ENUM('gkp', 'kkp', 'meesterkaart_zwaar', 'meesterkaart_licht', 'kk_geweer_open_50m', 'kk_geweer_optisch_100m', 'gk_precision_100m', 'militair_geweer', 'militair_geweer_optisch', 'veteranen_geweer') NOT NULL");

        DB::table('competition_registrations')
            ->where('kaliber', 'gkp')
            ->update(['kaliber' => 'gk_precision_100m']);

        DB::table('competition_registrations')
            ->where('kaliber', 'kkp')
            ->update(['kaliber' => 'kk_geweer_open_50m']);

        DB::table('competition_scores')
            ->where('kaliber', 'gkp')
            ->update(['kaliber' => 'gk_precision_100m']);

        DB::table('competition_scores')
            ->where('kaliber', 'kkp')
            ->update(['kaliber' => 'kk_geweer_open_50m']);

        DB::statement("ALTER TABLE competition_registrations MODIFY kaliber ENUM('meesterkaart_zwaar', 'meesterkaart_licht', 'kk_geweer_open_50m', 'kk_geweer_optisch_100m', 'gk_precision_100m', 'militair_geweer', 'militair_geweer_optisch', 'veteranen_geweer') NOT NULL");
        DB::statement("ALTER TABLE competition_scores MODIFY kaliber ENUM('meesterkaart_zwaar', 'meesterkaart_licht', 'kk_geweer_open_50m', 'kk_geweer_optisch_100m', 'gk_precision_100m', 'militair_geweer', 'militair_geweer_optisch', 'veteranen_geweer') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE competition_registrations MODIFY kaliber ENUM('gkp', 'kkp', 'meesterkaart_zwaar', 'meesterkaart_licht', 'kk_geweer_open_50m', 'kk_geweer_optisch_100m', 'gk_precision_100m', 'militair_geweer', 'militair_geweer_optisch', 'veteranen_geweer') NOT NULL");
        DB::statement("ALTER TABLE competition_scores MODIFY kaliber ENUM('gkp', 'kkp', 'meesterkaart_zwaar', 'meesterkaart_licht', 'kk_geweer_open_50m', 'kk_geweer_optisch_100m', 'gk_precision_100m', 'militair_geweer', 'militair_geweer_optisch', 'veteranen_geweer') NOT NULL");

        DB::table('competition_registrations')
            ->where('kaliber', 'gk_precision_100m')
            ->update(['kaliber' => 'gkp']);

        DB::table('competition_registrations')
            ->where('kaliber', 'kk_geweer_open_50m')
            ->update(['kaliber' => 'kkp']);

        DB::table('competition_scores')
            ->where('kaliber', 'gk_precision_100m')
            ->update(['kaliber' => 'gkp']);

        DB::table('competition_scores')
            ->where('kaliber', 'kk_geweer_open_50m')
            ->update(['kaliber' => 'kkp']);

        DB::statement("ALTER TABLE competition_registrations MODIFY kaliber ENUM('gkp', 'kkp') NOT NULL");
        DB::statement("ALTER TABLE competition_scores MODIFY kaliber ENUM('gkp', 'kkp') NOT NULL");
    }
};