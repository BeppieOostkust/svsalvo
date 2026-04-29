<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('marketplace_listings', function (Blueprint $table) {
            $table->json('images')->nullable()->after('image');
        });

        DB::table('marketplace_listings')
            ->whereNotNull('image')
            ->whereNull('images')
            ->orderBy('id')
            ->get(['id', 'image'])
            ->each(function ($row): void {
                DB::table('marketplace_listings')
                    ->where('id', $row->id)
                    ->update(['images' => json_encode([$row->image])]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketplace_listings', function (Blueprint $table) {
            $table->dropColumn('images');
        });
    }
};
