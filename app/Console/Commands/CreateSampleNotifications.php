<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CreateSampleNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:create-samples {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sample notifications for testing';

    public function __construct(
        private NotificationService $notificationService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return 1;
            }
            $users = collect([$user]);
        } else {
            $users = User::where('is_active_member', true)->limit(5)->get();
            if ($users->isEmpty()) {
                $this->error("No active members found.");
                return 1;
            }
        }

        $this->info("Creating sample notifications for " . $users->count() . " user(s)...");

        foreach ($users as $user) {
            // Create sample notifications
            $this->notificationService->createForUser(
                $user,
                'activity',
                'Nieuwe activiteit: Zomertornooi 2025',
                'Er is een nieuw zomertornooi georganiseerd! Schrijf je in voor 15 juli om deel te nemen aan dit spannende evenement.',
                [
                    'activity_id' => 1,
                    'url' => '/activiteiten/1',
                ]
            );

            $this->notificationService->createForUser(
                $user,
                'match',
                'Nieuwe wedstrijd: Vrijdagavond competitie',
                'Er is een nieuwe wedstrijd toegevoegd voor aanstaande vrijdag. Meld je aan en laat zien wat je kunt!',
                [
                    'match_id' => 1,
                    'url' => '/wedstrijden/1',
                ]
            );

            $this->notificationService->createForUser(
                $user,
                'nieuws',
                'Nieuws: Clubkampioenschap aangekondigd',
                'Het jaarlijkse clubkampioenschap staat voor de deur. Lees meer over de regels en inschrijvingsprocedure.',
                [
                    'article_id' => 1,
                    'url' => '/nieuws/clubkampioenschap-2025',
                ]
            );

            $this->notificationService->createForUser(
                $user,
                'profile_updated',
                'Profiel bijgewerkt',
                'Je telefoonnummer en adres zijn bijgewerkt door een beheerder.',
                [
                    'updated_fields' => ['telefoonnummer', 'adres'],
                    'url' => '/dashboard/profile',
                ]
            );

            $this->notificationService->createForUser(
                $user,
                'general',
                'Welkom bij SSV De Moes!',
                'Bedankt voor je aanmelding bij onze schietvereniging. We hopen je snel op de schietbaan te zien!',
                [
                    'url' => '/dashboard',
                ]
            );

            $this->info("Created notifications for user: {$user->name}");
        }

        $this->info("Sample notifications created successfully!");
        return 0;
    }
}
