<?php

namespace App\Filament\Resources\MatchesResource\Pages;

use App\Events\MatchUpdated;
use App\Filament\Resources\MatchesResource;
use App\Models\User;
use App\Services\EmailService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMatches extends CreateRecord
{
    protected static string $resource = MatchesResource::class;

    protected function afterCreate(): void
    {
        $match = $this->record;
        
        // Broadcast real-time update (only if Reverb is running)
        try {
            broadcast(new MatchUpdated('created', $match->id));
        } catch (\Exception $e) {
            // Silently fail if broadcasting is not available
        }
        
        // Check if emails should be sent
        if (!$this->data['send_emails'] ?? true) {
            Notification::make()
                ->title('Wedstrijd aangemaakt zonder emails')
                ->body('De wedstrijd is aangemaakt, maar er zijn geen emails verzonden.')
                ->info()
                ->send();
            return;
        }
        
        // Get all active members who want to receive match notifications
        $users = User::where('is_active_member', true)
            ->whereNotNull('email_verified_at')
            ->get();

        if ($users->count() > 0) {
            $emailService = new EmailService();
            $sentCount = 0;
            $failedCount = 0;

            foreach ($users as $user) {
                $sent = $emailService->sendNewMatchEmail($user, $match);
                if ($sent) {
                    $sentCount++;
                } else {
                    $failedCount++;
                }
            }

            // Show notification about email results
            if ($sentCount > 0) {
                Notification::make()
                    ->title('Wedstrijd aangemaakt en emails verzonden')
                    ->body("✅ {$sentCount} email(s) verzonden" . 
                           ($failedCount > 0 ? "\n⚠️ {$failedCount} email(s) mislukt" : ""))
                    ->success()
                    ->send();
            }
        } else {
            Notification::make()
                ->title('Wedstrijd aangemaakt')
                ->body('Geen actieve leden gevonden om emails naar te verzenden.')
                ->info()
                ->send();
        }
    }
}
