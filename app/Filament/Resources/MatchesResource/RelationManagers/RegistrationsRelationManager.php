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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
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
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Naam')
                    ->searchable()
                    ->sortable(),
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
                        $record->matches->gebruikersScores()->create([
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
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('convertSelected')
                        ->label('Geselecteerde toevoegen als deelnemers')
                        ->icon('heroicon-o-users')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $addedCount = 0;
                            
                            foreach ($records as $record) {
                                if (!$record->converted_to_participant) {
                                    // Voeg de gebruiker toe als deelnemer met een lege score
                                    $record->matches->gebruikersScores()->create([
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
                                }
                            }

                            Notification::make()
                                ->title($addedCount . ' deelnemer(s) toegevoegd')
                                ->success()
                                ->send();
                        })
                ]),
            ]);
    }
}
