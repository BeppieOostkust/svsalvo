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

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Inschrijvingen';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Gebruiker')
                    ->options(User::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'aangemeld' => 'Aangemeld',
                        'bevestigd' => 'Bevestigd',
                        'geweigerd' => 'Geweigerd',
                        'geannuleerd' => 'Geannuleerd',
                        'aanwezig' => 'Aanwezig',
                        'afwezig' => 'Afwezig',
                    ])
                    ->required(),
                Forms\Components\Select::make('caliber')
                    ->label('Kaliber')
                    ->options([
                        'kkp' => 'KKP (Klein Kaliber Pistool)',
                        'gkp' => 'GKP (Groot Kaliber Pistool)',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Opmerkingen')
                    ->rows(3),
                Forms\Components\Toggle::make('payment_confirmed')
                    ->label('Betaling bevestigd'),
                Forms\Components\TextInput::make('paid_amount')
                    ->label('Betaald bedrag')
                    ->numeric()
                    ->default(0.00),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Gebruiker')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'aangemeld',
                        'success' => 'bevestigd',
                        'danger' => 'geweigerd',
                        'secondary' => 'geannuleerd',
                        'info' => 'aanwezig',
                        'gray' => 'afwezig',
                    ]),
                Tables\Columns\TextColumn::make('caliber')
                    ->label('Kaliber')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'kkp' => 'info',
                        'gkp' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('converted_to_participant')
                    ->label('Deelnemer')
                    ->boolean()
                    ->tooltip(fn ($record) => $record->converted_to_participant ? 'Al toegevoegd als deelnemer' : 'Nog niet toegevoegd als deelnemer'),
                Tables\Columns\IconColumn::make('payment_confirmed')
                    ->label('Betaald')
                    ->boolean(),
                Tables\Columns\TextColumn::make('registered_at')
                    ->label('Ingeschreven op')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Opmerkingen')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'aangemeld' => 'Aangemeld',
                        'bevestigd' => 'Bevestigd',
                        'geweigerd' => 'Geweigerd',
                        'geannuleerd' => 'Geannuleerd',
                        'aanwezig' => 'Aanwezig',
                        'afwezig' => 'Afwezig',
                    ]),
                Tables\Filters\SelectFilter::make('caliber')
                    ->label('Kaliber')
                    ->options([
                        'kkp' => 'KKP',
                        'gkp' => 'GKP',
                    ]),
                Tables\Filters\TernaryFilter::make('converted_to_participant')
                    ->label('Al deelnemer'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['registered_at'] = now();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('convert_to_participant')
                    ->label('Voeg toe als deelnemer')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->visible(fn (MatchRegistration $record): bool => !$record->converted_to_participant && in_array($record->status, ['bevestigd', 'aangemeld']))
                    ->requiresConfirmation()
                    ->modalHeading('Voeg toe als deelnemer')
                    ->modalDescription(fn (MatchRegistration $record): string => "Weet je zeker dat je {$record->user->name} wilt toevoegen als deelnemer voor deze wedstrijd?")
                    ->action(function (MatchRegistration $record): void {
                        try {
                            $participant = $record->convertToParticipant();
                            
                            Notification::make()
                                ->title('Deelnemer toegevoegd')
                                ->body("{$record->user->name} is succesvol toegevoegd als deelnemer.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Fout bij toevoegen')
                                ->body('Er is een fout opgetreden bij het toevoegen van de deelnemer: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('convert_selected')
                        ->label('Voeg geselecteerden toe als deelnemers')
                        ->icon('heroicon-o-plus-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Geselecteerde registraties converteren')
                        ->modalDescription('Weet je zeker dat je alle geselecteerde registraties wilt converteren naar deelnemers?')
                        ->action(function ($records) {
                            $converted = 0;
                            $errors = [];
                            
                            foreach ($records as $record) {
                                if (!$record->converted_to_participant && in_array($record->status, ['bevestigd', 'aangemeld'])) {
                                    try {
                                        $record->convertToParticipant();
                                        $converted++;
                                    } catch (\Exception $e) {
                                        $errors[] = "{$record->user->name}: {$e->getMessage()}";
                                    }
                                }
                            }
                            
                            if ($converted > 0) {
                                Notification::make()
                                    ->title("Deelnemers toegevoegd")
                                    ->body("{$converted} registraties zijn succesvol geconverteerd naar deelnemers.")
                                    ->success()
                                    ->send();
                            }
                            
                            if (!empty($errors)) {
                                Notification::make()
                                    ->title('Enkele fouten opgetreden')
                                    ->body(implode(', ', $errors))
                                    ->warning()
                                    ->send();
                            }
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
