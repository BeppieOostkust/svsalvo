<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use App\Models\User;
use App\Services\EmailService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateActivity extends CreateRecord
{
    protected static string $resource = ActivityResource::class;
    
    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Activiteit aanmaken');
    }
    
    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Annuleren');
    }

    protected function afterCreate(): void
    {
        $activity = $this->record;
        
        // Get all active members who want to receive activity notifications
        $users = User::where('is_active_member', true)
            ->whereNotNull('email_verified_at')
            ->get();

        if ($users->count() > 0) {
            $emailService = new EmailService();
            $sentCount = 0;
            $failedCount = 0;

            foreach ($users as $user) {
                $sent = $emailService->sendNewActivityEmail($user, $activity);
                if ($sent) {
                    $sentCount++;
                } else {
                    $failedCount++;
                }
            }

            // Show notification about email results
            if ($sentCount > 0) {
                Notification::make()
                    ->title('Activiteit aangemaakt en emails verzonden')
                    ->body("✅ {$sentCount} email(s) verzonden" . 
                           ($failedCount > 0 ? "\n⚠️ {$failedCount} email(s) mislukt" : ""))
                    ->success()
                    ->send();
            }
        }
    }
}
