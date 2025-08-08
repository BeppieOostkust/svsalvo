<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop membership applications table
        Schema::dropIfExists('membership_applications');
        
        // Remove membership application setting
        Setting::where('key', 'membership_applications_open')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate membership_applications table
        Schema::create('membership_applications', function (Blueprint $table) {
            $table->id();
            $table->string('voornaam');
            $table->string('achternaam');
            $table->string('email')->unique();
            $table->string('telefoonnummer');
            $table->date('geboortedatum');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
        
        // Recreate membership application setting
        Setting::create([
            'key' => 'membership_applications_open',
            'value' => '1',
            'type' => 'boolean',
            'description' => 'Geeft aan of lidmaatschap aanvragen open staan'
        ]);
    }
};
