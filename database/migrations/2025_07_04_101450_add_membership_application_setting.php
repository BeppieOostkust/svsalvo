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
        // Add membership application setting
        Setting::create([
            'key' => 'membership_applications_open',
            'value' => '1', // Default to open
            'type' => 'boolean',
            'group' => 'Lidmaatschap',
            'label' => 'Lidmaatschap Aanvragen Open',
            'description' => 'Bepaalt of nieuwe leden zich kunnen aanmelden via de website',
            'is_public' => true,
            'is_editable' => true,
            'sort_order' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('key', 'membership_applications_open')->delete();
    }
};
