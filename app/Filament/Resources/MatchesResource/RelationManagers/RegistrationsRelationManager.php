<?php

namespace App\Filament\Resources\MatchesResource\RelationManagers;

use App\Models\MatchRegistration;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Aanmeldingen';

    protected static ?string $recordTitleAttribute = 'id';

    public function getTableHeading(): string
    {
        $totalCount = $this->getOwnerRecord()->registrations()->count();
        $pendingCount = $this->getOwnerRecord()
            ->registrations()
            ->whereIn('status', ['aangemeld', 'bevestigd'])
            ->where('converted_to_participant', false)
            ->count();
        $participantCount = $this->getOwnerRecord()
            ->registrations()
            ->where('converted_to_participant', true)
            ->count();
            
        return "Aanmeldingen ({$totalCount} totaal, {$pendingCount} wachtend, {$participantCount} deelnemers)";
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->dehydrated(false),
                Forms\Components\Select::make('caliber')
                    ->label('Kaliber')
                    ->options([
                        'kkp' => 'KKP (Klein Kaliber Pistool)',
                        'gkp' => 'GKP (Groot Kaliber Pistool)',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'aangemeld' => 'Aangemeld',
                        'bevestigd' => 'Bevestigd',
                        'afgemeld' => 'Afgemeld',
                        'aanwezig' => 'Aanwezig',
                        'afwezig' => 'Afwezig',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['user', 'match']))
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Naam')
                    ->searchable(['users.name'])
                    ->sortable(['users.name']),
                Tables\Columns\TextColumn::make('caliber')
                    ->label('Kaliber')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'kkp' => 'KKP',
                        'gkp' => 'GKP',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aangemeld' => 'gray',
                        'bevestigd' => 'warning',
                        'afgemeld' => 'danger',
                        'aanwezig' => 'success',
                        'afwezig' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('registered_at')
                    ->label('Aangemeld op')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('converted_to_participant')
                    ->label('Deelnemer')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
            ])
            ->defaultSort('registered_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aangemeld' => 'Aangemeld',
                        'bevestigd' => 'Bevestigd',
                        'afgemeld' => 'Afgemeld',
                        'aanwezig' => 'Aanwezig',
                        'afwezig' => 'Afwezig',
                    ]),
                Tables\Filters\SelectFilter::make('caliber')
                    ->label('Kaliber')
                    ->options([
                        'kkp' => 'KKP',
                        'gkp' => 'GKP',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['registered_at'] = now();
                        return $data;
                    }),
                
                Tables\Actions\Action::make('convertAllPending')
                    ->label('🚀 Alle aangemelde toevoegen als deelnemers')
                    ->icon('heroicon-o-user-group')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Alle aangemelde gebruikers toevoegen als deelnemers')
                    ->modalDescription('Dit zal alle aangemelde gebruikers (status: aangemeld of bevestigd) die nog geen deelnemer zijn toevoegen als deelnemers met lege scores.')
                    ->modalSubmitActionLabel('Ja, voeg alle toe')
                    ->action(function () {
                        $pendingRegistrations = $this->getOwnerRecord()
                            ->registrations()
                            ->whereIn('status', ['aangemeld', 'bevestigd'])
                            ->where('converted_to_participant', false)
                            ->get();
                        
                        $addedCount = 0;
                        $addedNames = [];
                        
                        foreach ($pendingRegistrations as $registration) {
                            // Voeg de gebruiker toe als deelnemer met een lege score
                            \App\Models\MatchGebruikerScore::create([
                                'wedstrijd_id' => $registration->match_id,
                                'gebruiker_id' => $registration->user_id,
                                'kaliber' => $registration->caliber,
                                'totale_punten' => 0,
                                'linker_kaart_6' => 0,
                                'linker_kaart_7' => 0,
                                'linker_kaart_8' => 0,
                                'linker_kaart_9' => 0,
                                'linker_kaart_10' => 0,
                                'rechter_kaart_6' => 0,
                                'rechter_kaart_7' => 0,
                                'rechter_kaart_8' => 0,
                                'rechter_kaart_9' => 0,
                                'rechter_kaart_10' => 0,
                                'aantal_schoten_buiten_tijd' => 0,
                                'afwaarderingen' => 0,
                            ]);
                            
                            // Update de registratie status
                            $registration->update([
                                'status' => 'aanwezig',
                                'converted_to_participant' => true,
                            ]);
                            
                            $addedCount++;
                            $addedNames[] = $registration->user->name;
                        }
                        
                        if ($addedCount > 0) {
                            Notification::make()
                                ->title('Alle aangemelde deelnemers toegevoegd')
                                ->body($addedCount . ' deelnemer(s) toegevoegd: ' . implode(', ', $addedNames))
                                ->success()
                                ->persistent()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Geen aanmeldingen om toe te voegen')
                                ->body('Alle aangemelde gebruikers zijn al deelnemers.')
                                ->warning()
                                ->send();
                        }
                    })
                    ->visible(fn () => $this->getOwnerRecord()
                        ->registrations()
                        ->whereIn('status', ['aangemeld', 'bevestigd'])
                        ->where('converted_to_participant', false)
                        ->exists()
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('convert')
                    ->label('Toevoegen als deelnemer')
                    ->icon('heroicon-o-user-plus')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // Voeg de gebruiker toe als deelnemer met een lege score
                        \App\Models\MatchGebruikerScore::create([
                            'wedstrijd_id' => $record->match_id,
                            'gebruiker_id' => $record->user_id,
                            'kaliber' => $record->caliber,
                            'totale_punten' => 0,
                            'linker_kaart_6' => 0,
                            'linker_kaart_7' => 0,
                            'linker_kaart_8' => 0,
                            'linker_kaart_9' => 0,
                            'linker_kaart_10' => 0,
                            'rechter_kaart_6' => 0,
                            'rechter_kaart_7' => 0,
                            'rechter_kaart_8' => 0,
                            'rechter_kaart_9' => 0,
                            'rechter_kaart_10' => 0,
                            'aantal_schoten_buiten_tijd' => 0,
                            'afwaarderingen' => 0,
                        ]);
                        
                        // Update de registratie status
                        $record->update([
                            'status' => 'aanwezig',
                            'converted_to_participant' => true,
                        ]);

                        Notification::make()
                            ->title('Deelnemer toegevoegd')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => !$record->converted_to_participant),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('convertSelected')
                        ->label('✅ Toevoegen als deelnemers')
                        ->icon('heroicon-o-user-plus')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Geselecteerde aanmeldingen toevoegen als deelnemers')
                        ->modalDescription('Weet je zeker dat je de geselecteerde aanmeldingen wilt toevoegen als deelnemers? Ze krijgen lege scores die je later kunt invullen.')
                        ->modalSubmitActionLabel('Ja, toevoegen als deelnemers')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $addedCount = 0;
                            $skippedCount = 0;
                            $addedNames = [];
                            
                            foreach ($records as $record) {
                                if (!$record->converted_to_participant) {
                                    // Voeg de gebruiker toe als deelnemer met een lege score
                                    \App\Models\MatchGebruikerScore::create([
                                        'wedstrijd_id' => $record->match_id,
                                        'gebruiker_id' => $record->user_id,
                                        'kaliber' => $record->caliber,
                                        'totale_punten' => 0,
                                        'linker_kaart_6' => 0,
                                        'linker_kaart_7' => 0,
                                        'linker_kaart_8' => 0,
                                        'linker_kaart_9' => 0,
                                        'linker_kaart_10' => 0,
                                        'rechter_kaart_6' => 0,
                                        'rechter_kaart_7' => 0,
                                        'rechter_kaart_8' => 0,
                                        'rechter_kaart_9' => 0,
                                        'rechter_kaart_10' => 0,
                                        'aantal_schoten_buiten_tijd' => 0,
                                        'afwaarderingen' => 0,
                                    ]);
                                    
                                    // Update de registratie status
                                    $record->update([
                                        'status' => 'aanwezig',
                                        'converted_to_participant' => true,
                                    ]);
                                    
                                    $addedCount++;
                                    $addedNames[] = $record->user->name;
                                } else {
                                    $skippedCount++;
                                }
                            }

                            $message = $addedCount . ' deelnemer(s) toegevoegd';
                            if ($skippedCount > 0) {
                                $message .= ' (' . $skippedCount . ' overgeslagen omdat ze al deelnemers waren)';
                            }
                            
                            if ($addedCount > 0) {
                                $message .= ': ' . implode(', ', $addedNames);
                            }

                            Notification::make()
                                ->title('Deelnemers toegevoegd')
                                ->body($message)
                                ->success()
                                ->persistent()
                                ->send();
                        }),
                    
                    Tables\Actions\BulkAction::make('bulkUpdateStatus')
                        ->label('📝 Status wijzigen')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Nieuwe status')
                                ->options([
                                    'aangemeld' => 'Aangemeld',
                                    'bevestigd' => 'Bevestigd',
                                    'afgemeld' => 'Afgemeld',
                                    'aanwezig' => 'Aanwezig',
                                    'afwezig' => 'Afwezig',
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $updatedCount = 0;
                            
                            foreach ($records as $record) {
                                $record->update(['status' => $data['status']]);
                                $updatedCount++;
                            }

                            Notification::make()
                                ->title($updatedCount . ' aanmelding(en) bijgewerkt')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    
                    Tables\Actions\BulkAction::make('selectAll')
                        ->label('🎯 Alle niet-geconverteerde selecteren')
                        ->icon('heroicon-o-check-circle')
                        ->color('info')
                        ->action(function () {
                            // This will be handled by JavaScript to select all non-converted records
                            $this->dispatch('selectNonConverted');
                        }),
                        
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('🗑️ Verwijderen')
                        ->requiresConfirmation()
                        ->modalHeading('Geselecteerde aanmeldingen verwijderen')
                        ->modalDescription('Weet je zeker dat je deze aanmeldingen wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.'),
                ]),
            ]);
    }
}
