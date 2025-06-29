<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'key' => 'membership_registration_enabled',
            'value' => '1', // Standaard aan
            'type' => 'boolean',
            'group' => 'membership',
            'label' => 'Ledenregistratie',
            'description' => 'Schakel de mogelijkheid om nieuwe lidmaatschapsaanvragen in te dienen in of uit.',
            'is_public' => true,
            'is_editable' => true,
            'validation_rules' => ['boolean'],
            'sort_order' => 0,
        ]);
    }
} 