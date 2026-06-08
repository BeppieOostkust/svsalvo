<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompetitionScoresResource\Pages;
use App\Models\CompetitionScore;
use App\Models\CompetitionRound;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompetitionScoresResource extends Resource
{
    protected static ?string $model = CompetitionScore::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationLabel = 'Wedstrijdscores';

    protected static ?string $navigationGroup = 'Wedstrijdlieden';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && $user->is_admin;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Score Informatie')
                    ->schema([
                        Forms\Components\Select::make('competition_round_id')
                            ->label('Beurt')
                            ->options(function () {
                                return CompetitionRound::with('competition')
                                    ->get()
                                    ->pluck('competition.naam', 'id')
                                    ->toArray();
                            })
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->label('Deelnemer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
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
                    ]),

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
                    ]),

                Forms\Components\Section::make('Totaal Score')
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('round.competition.jaar')
                    ->label('Jaar')
                    ->sortable(),
                Tables\Columns\TextColumn::make('round.round_number')
                    ->label('Beurt')
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('linker_score')
                    ->label('Eerste Kaart')
                    ->numeric(),
                Tables\Columns\TextColumn::make('rechter_score')
                    ->label('Tweede Kaart')
                    ->numeric(),
                Tables\Columns\TextColumn::make('totale_punten')
                    ->label('Totaal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('round.competition_id')
                    ->label('Competitie')
                    ->relationship('round.competition', 'naam'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompetitionScores::route('/'),
            'create' => Pages\CreateCompetitionScore::route('/create'),
            'edit' => Pages\EditCompetitionScore::route('/{record}/edit'),
        ];
    }
}
