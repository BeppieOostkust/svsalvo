<?php

namespace App\Filament\Resources\CompetitionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RoundsRelationManager extends RelationManager
{
    protected static string $relationship = 'rounds';

    protected static ?string $recordTitleAttribute = 'naam';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('naam')
                    ->label('Beurt Naam')
                    ->required(),
                Forms\Components\DatePicker::make('datum')
                    ->label('Datum'),
                Forms\Components\Textarea::make('beschrijving')
                    ->label('Beschrijving')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('naam')
            ->columns([
                Tables\Columns\TextColumn::make('round_number')
                    ->label('Ronde')
                    ->sortable(),
                Tables\Columns\TextColumn::make('naam')
                    ->label('Naam')
                    ->searchable(),
                Tables\Columns\TextColumn::make('datum')
                    ->label('Datum')
                    ->date('d-m-Y'),
                Tables\Columns\TextColumn::make('scores_count')
                    ->label('Scores')
                    ->counts('scores'),
            ])
            ->filters([])
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
