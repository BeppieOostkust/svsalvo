<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user that needs to change password
        User::updateOrCreate(
            ['email' => 'dion@example.com'],
            [
                'name' => 'Dion Test',
                'first_name' => 'Dion',
                'last_name' => 'Test',
                'avg_name' => 'Dion Test',
                'email' => 'dion@example.com',
                'password' => Hash::make('TempPass123!'), // Tijdelijk wachtwoord
                'password_change_required' => true, // Moet wachtwoord wijzigen
                'is_admin' => false,
                'show_in_organization' => false,
                'show_contact_info' => false,
                'show_scores_public' => false,
                'is_active_member' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Test gebruiker aangemaakt:');
        $this->command->info('Email: dion@example.com');
        $this->command->info('Tijdelijk wachtwoord: TempPass123!');
        $this->command->info('Deze gebruiker moet het wachtwoord wijzigen bij eerste inlog.');
    }
}
