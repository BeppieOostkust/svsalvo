<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Leden Beheer';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->is_admin || $user->canAccessAll());
    }
    
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->canAccessAll() || $user->is_admin);
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && ($user->canAccessAll() || $user->is_admin);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Account Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('avg_name')
                            ->required()
                            ->label('AVG Ledennaam'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Weergavenaam'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->label('Email'),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->label('Wachtwoord')
                            ->helperText('Laat leeg om het huidige wachtwoord te behouden (alleen bij bewerken)'),
                        Forms\Components\Toggle::make('is_active_member')
                            ->label('Actief lid')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Persoonlijke Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label('Voornaam'),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Achternaam'),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->label('Geboortedatum'),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefoon'),
                        Forms\Components\Textarea::make('address')
                            ->label('Adres'),
                        Forms\Components\TextInput::make('city')
                            ->label('Stad'),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Postcode'),
                        Forms\Components\TextInput::make('country')
                            ->label('Land')
                            ->default('Nederland'),
                    ])->columns(2),

                Forms\Components\Section::make('Vereniging Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('position')
                            ->label('Functie'),
                        Forms\Components\Textarea::make('bio')
                            ->label('Biografie'),
                        Forms\Components\FileUpload::make('profile_image')
                            ->label('Profielfoto')
                            ->image()
                            ->disk('public')
                            ->directory('profile-images')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                            ->maxSize(1024) // 1MB max
                            ->helperText('Maximaal 1MB. Formaten: JPEG, JPG, PNG')
                            ->enableOpen(false)
                            ->enableDownload(false)
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                            ])
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('300')
                            ->imageResizeTargetHeight('300')
                            ->orientImagesFromExif(false),
                        Forms\Components\Toggle::make('show_in_organization')
                            ->label('Toon in organisatie overzicht'),
                        Forms\Components\TextInput::make('organization_sort_order')
                            ->label('Volgorde in organisatie')
                            ->numeric()
                            ->default(0),
                        Forms\Components\DatePicker::make('member_since')
                            ->label('Lid sinds'),
                    ])->columns(2),

                Forms\Components\Section::make('Schiet Informatie')
                    ->schema([
                        Forms\Components\Select::make('preferred_discipline')
                            ->label('Voorkeur discipline')
                            ->options([
                                'gkp' => 'GKP',
                                'kkp' => 'KKP',
                                'gkg' => 'GKG',
                                'kkg' => 'KKG',
                                'luchtpistool' => 'Luchtpistool',
                                'luchtwapen' => 'Luchtwapen',
                            ]),
                        Forms\Components\TextInput::make('license_number')
                            ->label('Licentienummer'),
                        Forms\Components\DatePicker::make('license_expiry')
                            ->label('Licentie verloopt'),
                    ])->columns(2),

                Forms\Components\Section::make('Privacy Instellingen')
                    ->schema([
                        Forms\Components\Toggle::make('show_contact_info')
                            ->label('Contactgegevens openbaar'),
                        Forms\Components\Toggle::make('show_scores_public')
                            ->label('Scores openbaar')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Account Beveiliging')
                    ->schema([
                        Forms\Components\Toggle::make('is_blocked')
                            ->label('Account geblokkeerd')
                            ->helperText('Geblokkeerde gebruikers kunnen niet inloggen'),
                        Forms\Components\Textarea::make('blocked_reason')
                            ->label('Reden blokkering')
                            ->visible(fn (Forms\Get $get) => $get('is_blocked'))
                            ->required(fn (Forms\Get $get) => $get('is_blocked'))
                            ->placeholder('Geef een reden op voor de blokkering...'),
                        Forms\Components\DateTimePicker::make('blocked_at')
                            ->label('Geblokkeerd op')
                            ->disabled()
                            ->visible(fn (Forms\Get $get) => $get('is_blocked')),
                    ])->columns(1),

                Forms\Components\Section::make('Rollen & Rechten')
                    ->schema([
                        Forms\Components\Toggle::make('is_admin')
                            ->label('Administrator')
                            ->helperText('Admins hebben toegang tot alle functies'),
                        Forms\Components\CheckboxList::make('roles')
                            ->label('Specifieke Rollen')
                            ->options(\App\Models\User::ROLES)
                            ->descriptions([
                                'wedstrijdcommisie' => 'Toegang tot wedstrijd beheer',
                                'secretaris' => 'Volledige toegang tot alle functies',
                                'webmaster' => 'Volledige toegang tot alle functies',
                                'activiteitencommisie' => 'Toegang tot activiteiten beheer',
                                'kascommisie' => 'Toegang tot financieel beheer (prijzen)',
                                'voorzitter' => 'Volledige toegang tot alle functies',
                            ])
                            ->columns(2)
                            ->helperText('Selecteer specifieke rollen voor deze gebruiker'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(name: 'avg_name')
                    ->label('AVG Ledennaam')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Weergavenaam')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Volledige naam')
                    ->getStateUsing(fn ($record) => $record->full_name)
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_admin')
                    ->label('Admin')
                    ->boolean(),
                Tables\Columns\IconColumn::make('password_change_required')
                    ->label('Wachtwoord wijziging vereist')
                    ->boolean()
                    ->color(fn ($state): string => $state ? 'warning' : 'success')
                    ->icon(fn ($state): string => $state ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle'),
                Tables\Columns\IconColumn::make('is_active_member')
                    ->label('Actief lid')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_blocked')
                    ->label('Geblokkeerd')
                    ->boolean()
                    ->color(fn ($state): string => $state ? 'danger' : 'success')
                    ->icon(fn ($state): string => $state ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open'),
                Tables\Columns\TextColumn::make('blocked_reason')
                    ->label('Reden blokkering')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->blocked_reason)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('position')
                    ->label('Functie')
                    ->searchable(),
                Tables\Columns\TextColumn::make('preferred_discipline')
                    ->label('Discipline')
                    ->badge(),
                Tables\Columns\TextColumn::make('member_since')
                    ->label('Lid sinds')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Account aangemaakt')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('formatted_roles')
                    ->label('Rollen')
                    ->badge()
                    ->separator(',')
                    ->getStateUsing(fn ($record) => collect($record->roles ?? [])
                        ->map(fn($role) => \App\Models\User::ROLES[$role] ?? $role)
                        ->toArray())
                    ->color(fn ($state) => empty($state) ? 'gray' : 'primary')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label('Administrators'),
                Tables\Filters\TernaryFilter::make('is_active_member')
                    ->label('Actieve leden'),
                Tables\Filters\TernaryFilter::make('is_blocked')
                    ->label('Geblokkeerd')
                    ->placeholder('Alle gebruikers')
                    ->trueLabel('Alleen geblokkeerde')
                    ->falseLabel('Alleen niet-geblokkeerde'),
                Tables\Filters\TernaryFilter::make('show_in_organization')
                    ->label('Zichtbaar in organisatie'),
                Tables\Filters\SelectFilter::make('preferred_discipline')
                    ->label('Discipline')
                    ->options([
                        'gkp' => 'GKP',
                        'kkp' => 'KKP',
                        'gkg' => 'GKG',
                        'kkg' => 'KKG',
                        'luchtpistool' => 'Luchtpistool',
                        'luchtwapen' => 'Luchtwapen',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('toggleBlock')
                    ->label(fn ($record) => $record->is_blocked ? 'Deblokkeren' : 'Blokkeren')
                    ->icon(fn ($record) => $record->is_blocked ? 'heroicon-o-lock-open' : 'heroicon-o-lock-closed')
                    ->color(fn ($record) => $record->is_blocked ? 'success' : 'danger')
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => $record->is_blocked ? 'Gebruiker deblokkeren' : 'Gebruiker blokkeren')
                    ->modalDescription(fn ($record) => $record->is_blocked 
                        ? 'Weet je zeker dat je deze gebruiker wilt deblokkeren?' 
                        : 'Weet je zeker dat je deze gebruiker wilt blokkeren? De gebruiker kan dan niet meer inloggen.')
                    ->form(fn ($record) => $record->is_blocked ? [] : [
                        Forms\Components\Textarea::make('blocked_reason')
                            ->label('Reden voor blokkering')
                            ->required()
                            ->placeholder('Geef een reden op waarom deze gebruiker wordt geblokkeerd...')
                    ])
                    ->action(function ($record, array $data): void {
                        if ($record->is_blocked) {
                            // Unblock user
                            $record->update([
                                'is_blocked' => false,
                                'blocked_reason' => null,
                                'blocked_at' => null,
                            ]);
                            
                            Notification::make()
                                ->title('Gebruiker gedeblokkeerd')
                                ->body("**{$record->name}** is gedeblokkeerd en kan nu weer inloggen.")
                                ->success()
                                ->duration(5000)
                                ->send();
                        } else {
                            // Block user and force logout
                            $record->update([
                                'is_blocked' => true,
                                'blocked_reason' => $data['blocked_reason'],
                                'blocked_at' => now(),
                                'remember_token' => null, // Force logout
                            ]);
                            
                            Notification::make()
                                ->title('Gebruiker geblokkeerd')
                                ->body("**{$record->name}** is geblokkeerd en automatisch uitgelogd.\n\n**Reden:** {$data['blocked_reason']}")
                                ->warning()
                                ->duration(8000)
                                ->send();
                        }
                    }),
                
                Tables\Actions\Action::make('forceLogout')
                    ->label('Uitloggen')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Gebruiker uitloggen')
                    ->modalDescription('Weet je zeker dat je deze gebruiker wilt uitloggen van alle apparaten?')
                    ->action(function ($record): void {
                        $record->update(['remember_token' => null]);
                        
                        Notification::make()
                            ->title('Gebruiker uitgelogd')
                            ->body("**{$record->name}** is succesvol uitgelogd van alle apparaten.")
                            ->success()
                            ->duration(4000)
                            ->send();
                    })
                    ->visible(fn ($record) => !$record->is_blocked),
                
                Tables\Actions\Action::make('viewBlockReason')
                    ->label('Bekijk reden')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading('Reden voor blokkering')
                    ->modalContent(fn ($record) => new \Illuminate\Support\HtmlString("
                        <div class='p-4'>
                            <h3 class='text-lg font-semibold mb-2'>Gebruiker: {$record->name}</h3>
                            <p class='text-sm text-gray-600 mb-4'>Geblokkeerd op: " . $record->blocked_at?->format('d-m-Y H:i') . "</p>
                            <div class='bg-red-50 border border-red-200 rounded-lg p-4'>
                                <h4 class='font-medium text-red-800 mb-2'>Reden voor blokkering:</h4>
                                <p class='text-red-700'>" . ($record->blocked_reason ?? 'Geen reden opgegeven') . "</p>
                            </div>
                        </div>
                    "))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Sluiten')
                    ->visible(fn ($record) => $record->is_blocked),
                
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('blockUsers')
                        ->label('Blokkeren')
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Geselecteerde gebruikers blokkeren')
                        ->modalDescription('Weet je zeker dat je de geselecteerde gebruikers wilt blokkeren?')
                        ->form([
                            Forms\Components\Textarea::make('blocked_reason')
                                ->label('Reden voor blokkering')
                                ->required()
                                ->placeholder('Geef een reden op waarom deze gebruikers worden geblokkeerd...')
                        ])
                        ->action(function (array $data, $records): void {
                            $count = 0;
                            foreach ($records as $record) {
                                if (!$record->is_blocked) {
                                    $record->update([
                                        'is_blocked' => true,
                                        'blocked_reason' => $data['blocked_reason'],
                                        'blocked_at' => now(),
                                        'remember_token' => null,
                                    ]);
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Gebruikers geblokkeerd')
                                ->body("{$count} gebruiker(s) zijn geblokkeerd en uitgelogd.\n\n**Reden:** {$data['blocked_reason']}")
                                ->warning()
                                ->duration(8000)
                                ->send();
                        }),
                    
                    Tables\Actions\BulkAction::make('unblockUsers')
                        ->label('Deblokkeren')
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Geselecteerde gebruikers deblokkeren')
                        ->modalDescription('Weet je zeker dat je de geselecteerde gebruikers wilt deblokkeren?')
                        ->action(function ($records): void {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->is_blocked) {
                                    $record->update([
                                        'is_blocked' => false,
                                        'blocked_reason' => null,
                                        'blocked_at' => null,
                                    ]);
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Gebruikers gedeblokkeerd')
                                ->body("{$count} gebruiker(s) zijn gedeblokkeerd.")
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\BulkAction::make('forceLogoutUsers')
                        ->label('Uitloggen')
                        ->icon('heroicon-o-arrow-right-on-rectangle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Geselecteerde gebruikers uitloggen')
                        ->modalDescription('Weet je zeker dat je de geselecteerde gebruikers wilt uitloggen van alle apparaten?')
                        ->action(function ($records): void {
                            $count = 0;
                            foreach ($records as $record) {
                                $record->update(['remember_token' => null]);
                                $count++;
                            }
                            
                            Notification::make()
                                ->title('Gebruikers uitgelogd')
                                ->body("{$count} gebruiker(s) zijn uitgelogd van alle apparaten.")
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationLabel(): string
    {
        return 'Leden';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Leden Beheer';
    }

    public static function getModelLabel(): string
    {
        return 'Lid';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Leden';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
