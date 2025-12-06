<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Services\EmailService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    if (!empty($data['password'])) {
                        $data['password'] = bcrypt($data['password']);
                    }
                    return $data;
                }),
            Actions\Action::make('createWithTempPassword')
                ->label('Aanmaken met tijdelijk wachtwoord')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->form([
                    Forms\Components\Section::make('Basis Informatie')
                        ->schema([
                            Forms\Components\TextInput::make('avg_name')
                                ->required()
                                ->label('AVG Ledennaam')
                                ->helperText('Naam zoals geregistreerd bij de Nederlandse Schietunie'),
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->label('Weergavenaam')
                                ->helperText('Naam zoals deze wordt weergegeven op de website'),
                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->required()
                                ->label('Email')
                                ->unique(User::class, 'email'),
                            Forms\Components\TextInput::make('first_name')
                                ->label('Voornaam'),
                            Forms\Components\TextInput::make('last_name')
                                ->label('Achternaam'),
                        ])->columns(2),
                    Forms\Components\Section::make('Account Instellingen')
                        ->schema([
                            Forms\Components\Toggle::make('is_admin')
                                ->label('Administrator')
                                ->default(false),
                            Forms\Components\Toggle::make('is_active_member')
                                ->label('Actief lid')
                                ->default(true),
                            Forms\Components\Toggle::make('show_contact_info')
                                ->label('Contactgegevens openbaar')
                                ->default(false),
                            Forms\Components\Toggle::make('show_scores_public')
                                ->label('Scores openbaar')
                                ->default(true),
                        ])->columns(2),
                ])
                ->action(function (array $data): void {
                    // Generate temporary password
                    $tempPassword = 'Temp' . Str::random(8);
                    
                    // Create user with temporary password
                    $user = User::create([
                        'avg_name' => $data['avg_name'],
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'first_name' => $data['first_name'] ?? null,
                        'last_name' => $data['last_name'] ?? null,
                        'password' => Hash::make($tempPassword),
                        'password_change_required' => true,
                        'is_admin' => $data['is_admin'] ?? false,
                        'is_active_member' => $data['is_active_member'] ?? true,
                        'show_contact_info' => $data['show_contact_info'] ?? false,
                        'show_scores_public' => $data['show_scores_public'] ?? true,
                        'show_in_organization' => false,
                        'show_in_participants' => true,
                        'email_verified_at' => now(),
                    ]);

                    // Send email with temporary password
                    $emailService = new EmailService();
                    $emailSent = $emailService->sendTemporaryPasswordEmail($user, $tempPassword);

                    // Show success notification with temporary password
                    Notification::make()
                        ->title('Gebruiker succesvol aangemaakt!')
                        ->body("Tijdelijk wachtwoord: **{$tempPassword}**\n\n" . 
                               ($emailSent 
                                   ? "✅ Email verzonden naar {$user->email}" 
                                   : "⚠️ Email kon niet worden verzonden. Geef het wachtwoord handmatig door.") . 
                               "\n\nDe gebruiker moet dit wachtwoord wijzigen bij eerste inlog.")
                        ->success()
                        ->persistent()
                        ->send();
                })
                ->modalHeading('Nieuwe gebruiker aanmaken met tijdelijk wachtwoord')
                ->modalDescription('Maak een nieuwe gebruiker aan met een automatisch gegenereerd tijdelijk wachtwoord. De gebruiker moet dit wachtwoord wijzigen bij eerste inlog.')
                ->modalSubmitActionLabel('Gebruiker aanmaken'),
            
            Actions\Action::make('bulkLogoutUsers')
                ->label('Bulk uitloggen')
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Gebruikers uitloggen')
                ->modalDescription('Selecteer de gebruikers die je wilt uitloggen van alle apparaten.')
                ->form([
                    Forms\Components\Select::make('user_ids')
                        ->label('Selecteer gebruikers')
                        ->multiple()
                        ->options(User::whereNotNull('email_verified_at')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $users = User::whereIn('id', $data['user_ids'])->get();
                    $loggedOutCount = 0;
                    
                    foreach ($users as $user) {
                        // Force logout by updating remember_token
                        $user->update(['remember_token' => null]);
                        $loggedOutCount++;
                    }
                    
                    Notification::make()
                        ->title('Gebruikers uitgelogd')
                        ->body("{$loggedOutCount} gebruiker(s) zijn uitgelogd van alle apparaten.")
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Hash password if provided, otherwise remove it from update data
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        
        $record->update($data);
        
        return $record;
    }
}
