<?php

namespace App\Filament\Resources\CompetitionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use App\Models\CompetitionRound;

class ScoresRelationManager extends RelationManager
{
    protected static string $relationship = 'allScores';

    protected static ?string $recordTitleAttribute = 'user_id';

    protected static ?string $title = 'Scores';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Score Details')
                    ->schema([
                        Forms\Components\Select::make('competition_round_id')
                            ->label('Beurt')
                            ->options(function ($livewire) {
                                // Get the competition from the ownerRecord (the parent Competition model)
                                $competition = $livewire->ownerRecord;
                                if (!$competition) {
                                    return [];
                                }
                                $options = [];
                                foreach ($competition->rounds()->get() as $round) {
                                    $label = "{$competition->naam} - Beurt {$round->round_number}";
                                    if ($round->naam && $round->naam !== "Beurt {$round->round_number}") {
                                        $label .= " ({$round->naam})";
                                    }
                                    $options[$round->id] = $label;
                                }
                                return $options;
                            })
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('user_id')
                            ->label('Deelnemer')
                            ->relationship(
                                'user',
                                'name',
                                function ($query, $livewire) {
                                    // Only show users registered for this competition
                                    $competition = $livewire->ownerRecord;
                                    return $query->whereIn('id', 
                                        $competition->registrations()
                                            ->where('status', 'actief')
                                            ->pluck('user_id')
                                    );
                                }
                            )
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
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Linker Kaart (Schietkaart 1)')
                    ->description('Voer de score in')
                    ->schema([
                        Forms\Components\TextInput::make('linker_score')
                            ->label('Score')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->default(0)
                            ->live(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Rechter Kaart (Schietkaart 2)')
                    ->description('Voer de score in')
                    ->schema([
                        Forms\Components\TextInput::make('rechter_score')
                            ->label('Score')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->default(0)
                            ->live(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Totaal')
                    ->schema([
                        Forms\Components\TextInput::make('totale_punten')
                            ->label('Totale Punten (auto)')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                    ]),

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
                Tables\Columns\TextColumn::make('round.round_number')
                    ->label('Beurt')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Schutter')
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
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('linker_score')
                    ->label('Eerste Kaart')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rechter_score')
                    ->label('Tweede Kaart')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('totale_punten')
                    ->label('Totaal')
                    ->numeric()
                    ->weight(FontWeight::Bold)
                    ->sortable(),
            ])
            ->filters([
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
                Tables\Actions\CreateAction::make()
                    ->label('Score Toevoegen'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('round.round_number', 'asc');
    }
}
