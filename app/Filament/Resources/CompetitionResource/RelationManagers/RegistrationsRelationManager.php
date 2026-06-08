<?php

namespace App\Filament\Resources\CompetitionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $recordTitleAttribute = 'user_id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Deelnemer')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('kaliber')
                    ->label('Discipline')
                    ->options([
                        'meesterkaart_zwaar' => 'Meesterkaart zwaar',
                        'meesterkaart_licht' => 'Meesterkaart licht',
                        'kk_geweer_open_50m' => 'KK geweer open richtmiddelen 50m',
                        'kk_geweer_optisch_100m' => 'KK geweer optisch 100m',
                        'gk_precision_100m' => 'Groot kaliber precisiegeweer target 100m',
                        'militair_geweer' => 'Militair geweer',
                        'militair_geweer_optisch' => 'Militair geweer optisch',
                        'veteranen_geweer' => 'Veteranen geweer',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'actief' => 'Actief',
                        'inactief' => 'Inactief',
                        'afgemeld' => 'Afgemeld',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Opmerkingen')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Deelnemer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kaliber')
                    ->label('Discipline')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'meesterkaart_zwaar' => 'Meesterkaart zwaar',
                        'meesterkaart_licht' => 'Meesterkaart licht',
                        'kk_geweer_open_50m' => 'KK geweer open richtmiddelen 50m',
                        'kk_geweer_optisch_100m' => 'KK geweer optisch 100m',
                        'gk_precision_100m' => 'Groot kaliber precisiegeweer target 100m',
                        'militair_geweer' => 'Militair geweer',
                        'militair_geweer_optisch' => 'Militair geweer optisch',
                        'veteranen_geweer' => 'Veteranen geweer',
                        default => $state,
                    }),
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'actief' => 'Actief',
                        'inactief' => 'Inactief',
                        'afgemeld' => 'Afgemeld',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ingeschreven')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'actief' => 'Actief',
                        'inactief' => 'Inactief',
                        'afgemeld' => 'Afgemeld',
                    ]),
                Tables\Filters\SelectFilter::make('kaliber')
                    ->options([
                        'meesterkaart_zwaar' => 'Meesterkaart zwaar',
                        'meesterkaart_licht' => 'Meesterkaart licht',
                        'kk_geweer_open_50m' => 'KK geweer open richtmiddelen 50m',
                        'kk_geweer_optisch_100m' => 'KK geweer optisch 100m',
                        'gk_precision_100m' => 'Groot kaliber precisiegeweer target 100m',
                        'militair_geweer' => 'Militair geweer',
                        'militair_geweer_optisch' => 'Militair geweer optisch',
                        'veteranen_geweer' => 'Veteranen geweer',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
