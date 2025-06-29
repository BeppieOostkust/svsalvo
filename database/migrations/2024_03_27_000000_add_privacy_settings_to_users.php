<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('privacy_settings')->nullable()->after('password')->default(json_encode([
                'show_email' => false,
                'show_phone' => false,
                'show_disciplines' => true,
                'show_profile' => true,
                'show_name' => true
            ]));
            $table->string('phone')->nullable()->after('email');
            $table->json('disciplines')->nullable()->after('privacy_settings')->default(json_encode([]));
            $table->text('bio')->nullable()->after('disciplines');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['privacy_settings', 'phone', 'disciplines', 'bio']);
        });
    }
}; 