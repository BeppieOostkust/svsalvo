<?php

namespace App\Filament\Resources\LegalDocumentResource\Pages;

use App\Filament\Resources\LegalDocumentResource;
use App\Models\LegalDocument;
use App\Models\User;
use App\Services\EmailService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditLegalDocument extends EditRecord
{
    protected static string $resource = LegalDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('notifyUsers')
                ->label('Notificeer Gebruikers')
                ->icon('heroicon-o-envelope')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Gebruikers Notificeren')
                ->modalDescription('Wil je alle actieve gebruikers notificeren over deze update van het juridisch document?')
                ->modalSubmitActionLabel('Ja, verstuur emails')
                ->action(function (LegalDocument $record) {
                    // Only for privacy policy updates
                    if ($record->type !== 'privacy_policy') {
                        Notification::make()
                            ->title('Alleen privacy verklaringen kunnen email notificaties versturen')
                            ->warning()
                            ->send();
                        return;
                    }

                    // Get all active members with verified emails
                    $users = User::where('is_active_member', true)
                        ->whereNotNull('email_verified_at')
                        ->get();

                    if ($users->count() > 0) {
                        $emailService = new EmailService();
                        $sentCount = 0;
                        $failedCount = 0;

                        foreach ($users as $user) {
                            $sent = $emailService->sendPrivacyPolicyUpdateEmail($user, $record);
                            if ($sent) {
                                $sentCount++;
                            } else {
                                $failedCount++;
                            }
                        }

                        // Show notification about email results
                        Notification::make()
                            ->title('Privacy verklaring update emails verzonden')
                            ->body("✅ {$sentCount} email(s) verzonden" . 
                                   ($failedCount > 0 ? "\n⚠️ {$failedCount} email(s) mislukt" : ""))
                            ->success()
                            ->persistent()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Geen gebruikers gevonden')
                            ->body('Er zijn geen actieve gebruikers met geverifieerde email adressen')
                            ->warning()
                            ->send();
                    }
                })
                ->visible(fn (LegalDocument $record): bool => $record->type === 'privacy_policy'),
            
            Actions\DeleteAction::make(),
        ];
    }
}
