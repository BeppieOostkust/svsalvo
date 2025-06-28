<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\User;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizer = User::where('is_admin', true)->first();
        
        if (!$organizer) {
            $this->command->error('No admin user found to assign as organizer');
            return;
        }

        Activity::create([
            'title' => 'Schiettraining Beginners',
            'slug' => 'schiettraining-beginners',
            'description' => 'Basis schiettraining voor beginners. Alle benodigde materialen worden verstrekt.',
            'type' => 'training',
            'status' => 'bevestigd',
            'location' => 'Schietbaan Raamsdonksveer',
            'start_date' => now()->addDays(7),
            'start_time' => '19:00',
            'end_time' => '21:00',
            'max_participants' => 10,
            'current_participants' => 0,
            'entry_fee' => 15.00,
            'requires_registration' => true,
            'registration_deadline' => now()->addDays(5),
            'organizer_id' => $organizer->id,
            'contact_info' => 'Voor vragen: info@example.com',
        ]);

        Activity::create([
            'title' => 'Interne Wedstrijd GKP',
            'slug' => 'interne-wedstrijd-gkp',
            'description' => 'Maandelijkse interne wedstrijd Groot Kaliber Pistool.',
            'type' => 'wedstrijd',
            'status' => 'bevestigd',
            'location' => 'Schietbaan Raamsdonksveer',
            'start_date' => now()->addDays(14),
            'start_time' => '18:30',
            'end_time' => '22:00',
            'max_participants' => 20,
            'current_participants' => 5,
            'entry_fee' => 10.00,
            'requires_registration' => true,
            'registration_deadline' => now()->addDays(10),
            'organizer_id' => $organizer->id,
            'contact_info' => 'Wedstrijdleider: john@example.com',
        ]);

        Activity::create([
            'title' => 'Algemene Ledenvergadering',
            'slug' => 'algemene-ledenvergadering',
            'description' => 'Jaarlijkse algemene ledenvergadering. Alle leden zijn welkom.',
            'type' => 'vergadering',
            'status' => 'bevestigd',
            'location' => 'Clubhuis',
            'start_date' => now()->addDays(30),
            'start_time' => '20:00',
            'end_time' => '22:30',
            'max_participants' => null,
            'current_participants' => 0,
            'entry_fee' => 0.00,
            'requires_registration' => false,
            'organizer_id' => $organizer->id,
            'contact_info' => 'Secretaris: secretaris@example.com',
        ]);
    }
}
