<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUserWithTempPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-temp {email} {name} {--first-name=} {--last-name=} {--avg-name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Maak een nieuwe gebruiker aan met een tijdelijk wachtwoord dat gewijzigd moet worden';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name');
        $firstName = $this->option('first-name');
        $lastName = $this->option('last-name');
        $avgName = $this->option('avg-name') ?? $name;

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error("Gebruiker met email '{$email}' bestaat al!");
            return Command::FAILURE;
        }

        // Generate a temporary password
        $tempPassword = 'Temp' . Str::random(8) . '!';

        // Create user
        $user = User::create([
            'name' => $name,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'avg_name' => $avgName,
            'email' => $email,
            'password' => Hash::make($tempPassword),
            'password_change_required' => true,
            'is_admin' => false,
            'show_in_organization' => false,
            'show_contact_info' => false,
            'show_scores_public' => false,
            'is_active_member' => true,
            'email_verified_at' => now(),
        ]);

        $this->info('Gebruiker succesvol aangemaakt!');
        $this->line('');
        $this->line("Email: {$email}");
        $this->line("Naam: {$name}");
        if ($firstName) $this->line("Voornaam: {$firstName}");
        if ($lastName) $this->line("Achternaam: {$lastName}");
        $this->line("AVG Naam: {$avgName}");
        $this->line('');
        $this->warn("TIJDELIJK WACHTWOORD: {$tempPassword}");
        $this->line('');
        $this->info('De gebruiker moet dit wachtwoord wijzigen bij eerste inlog.');
        $this->info('Geef dit wachtwoord veilig door aan de gebruiker.');

        return Command::SUCCESS;
    }
}
