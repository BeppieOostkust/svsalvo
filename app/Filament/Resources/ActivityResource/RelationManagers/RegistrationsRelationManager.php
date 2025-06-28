<?php

namespace App\Filament\Resources\ActivityResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Gebruiker')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'ingeschreven' => 'Ingeschreven',
                        'bevestigd' => 'Bevestigd',
                        'geannuleerd' => 'Geannuleerd',
                        'afgewezen' => 'Afgewezen',
                    ])
                    ->required()
                    ->default('ingeschreven'),
                Forms\Components\TextInput::make('paid_amount')
                    ->label('Betaald bedrag (€)')
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01),
                Forms\Components\Toggle::make('payment_confirmed')
                    ->label('Betaling bevestigd')
                    ->default(false),
                Forms\Components\Textarea::make('notes')
                    ->label('Opmerkingen')
                    ->rows(3),
                Forms\Components\KeyValue::make('additional_data')
                    ->label('Extra gegevens')
                    ->keyLabel('Veld')
                    ->valueLabel('Waarde'),
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
                Tables\Columns\TextColumn::make('user.email')
                    ->label('E-mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ingeschreven' => 'warning',
                        'bevestigd' => 'success',
                        'geannuleerd' => 'danger',
                        'afgewezen' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ingeschreven' => 'Ingeschreven',
                        'bevestigd' => 'Bevestigd',
                        'geannuleerd' => 'Geannuleerd',
                        'afgewezen' => 'Afgewezen',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('payment_confirmed')
                    ->label('Betaald')
                    ->boolean(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Bedrag')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('registered_at')
                    ->label('Ingeschreven op')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Opmerkingen')
                    ->limit(50)
                    ->tooltip(fn (?string $state): ?string => $state),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'ingeschreven' => 'Ingeschreven',
                        'bevestigd' => 'Bevestigd',
                        'geannuleerd' => 'Geannuleerd',
                        'afgewezen' => 'Afgewezen',
                    ]),
                Tables\Filters\TernaryFilter::make('payment_confirmed')
                    ->label('Betaling bevestigd'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Inschrijving toevoegen'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('confirm')
                    ->label('Bevestigen')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn ($record) => $record->update(['status' => 'bevestigd']))
                    ->visible(fn ($record) => $record->status === 'ingeschreven'),
                Tables\Actions\Action::make('cancel')
                    ->label('Annuleren')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(fn ($record) => $record->update(['status' => 'geannuleerd']))
                    ->visible(fn ($record) => in_array($record->status, ['ingeschreven', 'bevestigd'])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('confirm')
                        ->label('Bevestigen')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['status' => 'bevestigd'])),
                    Tables\Actions\BulkAction::make('cancel')
                        ->label('Annuleren')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['status' => 'geannuleerd'])),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('registered_at', 'desc');
    }
}
