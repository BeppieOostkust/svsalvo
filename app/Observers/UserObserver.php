<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\UserBlockedNotification;
use App\Services\NotificationService;
use Filament\Notifications\Notification;

class UserObserver
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Handle the User "updating" event.
     */
    public function updating(User $user): void
    {
        // Set blocked_at timestamp when user is being blocked
        if ($user->isDirty('is_blocked')) {
            if ($user->is_blocked && !$user->getOriginal('is_blocked')) {
                // User is being blocked
                $user->blocked_at = now();
                $user->remember_token = null; // Force logout
            } elseif (!$user->is_blocked && $user->getOriginal('is_blocked')) {
                // User is being unblocked
                $user->forceFill([
                    'blocked_at' => null,
                    'blocked_reason' => null,
                ]);
            }
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Send notifications when block status changes
        if ($user->wasChanged('is_blocked')) {
            if ($user->is_blocked) {
                // User was blocked
                Notification::make()
                    ->title('Account Status Gewijzigd')
                    ->body("Gebruiker **{$user->name}** is geblokkeerd en uitgelogd van alle apparaten.")
                    ->warning()
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->label('Bekijken')
                            ->url(route('filament.admin.resources.users.index'))
                            ->button(),
                    ])
                    ->sendToDatabase(auth()->user());
                    
                // Send email notification to all admins
                if (auth()->check()) {
                    $admins = User::where('is_admin', true)->where('id', '!=', $user->id)->get();
                    foreach ($admins as $admin) {
                        $admin->notify(new UserBlockedNotification($user, auth()->user()));
                    }
                }
            } else {
                // User was unblocked
                Notification::make()
                    ->title('Account Status Gewijzigd')
                    ->body("Gebruiker **{$user->name}** is gedeblokkeerd en kan nu weer inloggen.")
                    ->success()
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->label('Bekijken')
                            ->url(route('filament.admin.resources.users.index'))
                            ->button(),
                    ])
                    ->sendToDatabase(auth()->user());
            }
        }

        // Notify user when their important personal information is updated by an admin
        if (auth()->check() && auth()->user()->isAdmin() && auth()->user()->id !== $user->id) {
            $changedFields = [];
            
            // Only notify for these critical personal information fields
            $importantFields = [
                'first_name' => 'voornaam',
                'last_name' => 'achternaam', 
                'name' => 'volledige naam',
                'email' => 'e-mailadres',
                'phone' => 'telefoonnummer',
                'address' => 'adres',
                'city' => 'woonplaats',
                'postal_code' => 'postcode',
                'country' => 'land',
                'date_of_birth' => 'geboortedatum',
                'membership_number' => 'lidnummer',
                'license_number' => 'licentienummer',
                'license_expiry' => 'licentie vervaldatum',
                'membership_status' => 'lidmaatschap status',
                'is_active_member' => 'actief lidmaatschap',
                'roles' => 'rollen/functies',
                'is_admin' => 'admin rechten',
            ];

            foreach ($importantFields as $field => $displayName) {
                if ($user->wasChanged($field)) {
                    $changedFields[] = $displayName;
                }
            }

            if (!empty($changedFields)) {
                $this->notificationService->notifyProfileUpdated($user, $changedFields);
            }
        }
    }
}
