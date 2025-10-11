<?php

namespace App\Filament\Resources;

use App\Filament\Actions\ExportScoresAction;
use App\Filament\Resources\MatchesResource\Pages;
use App\Filament\Resources\MatchesResource\RelationManagers;
use App\Models\Matches;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MatchesResource extends Resource
{
    protected static ?string $model = Matches::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationGroup = 'Wedstrijd Beheer';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && ($user->canAccessMatches() || $user->is_admin);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Wedstrijd Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('naam')
                            ->label('Wedstrijd Naam')
                            ->required(),
                        Forms\Components\TextInput::make('beschrijving')
                            ->label('Beschrijving'),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'binnenkort' => 'Binnenkort',
                                'bezig' => 'Bezig',
                                'geannuleerd' => 'Geannuleerd',
                                'afgelopen' => 'Afgelopen',
                            ])
                            ->required(),
                        Forms\Components\DateTimePicker::make('start_datum')
                            ->label('Start Datum')
                            ->required()
                            ->default(now())
                            ->displayFormat('d-m-Y H:i:s')
                            ->seconds(false)
                            ->timezone('Europe/Amsterdam'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Speler Scores (Georganiseerd per Serie)')
                    ->schema([
                        Forms\Components\Repeater::make('gebruikersScores')
                            ->relationship()
                            ->schema([
                                Forms\Components\Section::make('Speler & Serie Info')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\Select::make('gebruiker_id')
                                                    ->relationship('user', 'name')
                                                    ->label('Speler')
                                                    ->required()
                                                    ->searchable()
                                                    ->preload()
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Select::make('kaliber')
                                                    ->label('Kaliber')
                                                    ->options([
                                                        'gkp' => 'GKP',
                                                        'kkp' => 'KKP',
                                                    ])
                                                    ->required()
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\TextInput::make('round_number')
                                                    ->label('Serie #')
                                                    ->numeric()
                                                    ->default(1)
                                                    ->minValue(1)
                                                    ->maxValue(10)
                                                    ->required()
                                                    ->columnSpan(1),
                                            ]),
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('baan_nummer')
                                                    ->label('🎯 Baan Nummer')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->maxValue(20)
                                                    ->placeholder('1-20')
                                                    ->helperText('Wijs een baan toe (1-20)')
                                                    ->required()
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Toggle::make('is_official')
                                                    ->label('Officieel')
                                                    ->default(true)
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Placeholder::make('space')
                                                    ->columnSpan(1),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->collapsed(false),
                                    
                                Forms\Components\Grid::make(12)
                                    ->schema([
                                        Forms\Components\TextInput::make('linker_kaart_5')
                                            ->label('L-5')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1)
                                            ->helperText('0 pt'),
                                            
                                        Forms\Components\TextInput::make('linker_kaart_6')
                                            ->label('L-6')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('linker_kaart_7')
                                            ->label('L-7')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('linker_kaart_8')
                                            ->label('L-8')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('linker_kaart_9')
                                            ->label('L-9')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('linker_kaart_10')
                                            ->label('L-10')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('rechter_kaart_5')
                                            ->label('R-5')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1)
                                            ->helperText('0 pt'),
                                            
                                        Forms\Components\TextInput::make('rechter_kaart_6')
                                            ->label('R-6')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('rechter_kaart_7')
                                            ->label('R-7')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('rechter_kaart_8')
                                            ->label('R-8')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('rechter_kaart_9')
                                            ->label('R-9')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('rechter_kaart_10')
                                            ->label('R-10')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->columnSpan(1),
                                    ]),
                                    
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\TextInput::make('aantal_schoten_buiten_tijd')
                                            ->label('Buiten Tijd')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('afwaarderingen')
                                            ->label('Afwaarderingen')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('totale_punten')
                                            ->label('Totaal')
                                            ->numeric()
                                            ->disabled()
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('notes')
                                            ->label('Opmerkingen')
                                            ->columnSpan(1),
                                    ]),
                            ])
                            ->itemLabel(function (array $state): ?string {
                                $user = \App\Models\User::find($state['gebruiker_id'] ?? null);
                                $userName = $user ? $user->name : 'Onbekend';
                                $serie = $state['round_number'] ?? '?';
                                $kaliber = strtoupper($state['kaliber'] ?? 'onbekend');
                                return "{$userName} - {$serie}e Serie - {$kaliber}";
                            })
                            ->collapsible()
                            ->cloneable()
                            ->addActionLabel('Score Toevoegen')
                            ->reorderableWithButtons()
                            ->orderColumn('round_number')
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('naam')
                    ->label('Naam')
                    ->searchable(),
                Tables\Columns\TextColumn::make('beschrijving')
                    ->label('Beschrijving')
                    ->limit(50),
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'binnenkort' => 'Binnenkort',
                        'bezig' => 'Bezig',
                        'geannuleerd' => 'Geannuleerd',
                        'afgelopen' => 'Afgelopen',
                    ]),
                Tables\Columns\TextColumn::make('start_datum')
                    ->label('Start Datum')
                    ->dateTime('d-m-Y H:i'),
                Tables\Columns\TextColumn::make('gebruikersScores_count')
                    ->label('Aantal Scores')
                    ->counts('gebruikersScores'),
                Tables\Columns\TextColumn::make('aantal_spelers')
                    ->label('Spelers')
                    ->getStateUsing(function ($record) {
                        return $record->gebruikersScores()
                            ->distinct('gebruiker_id')
                            ->count('gebruiker_id');
                    }),
                Tables\Columns\TextColumn::make('aantal_series')
                    ->label('Series')
                    ->getStateUsing(function ($record) {
                        return $record->gebruikersScores()
                            ->distinct('round_number')
                            ->count('round_number');
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'binnenkort' => 'Binnenkort',
                        'bezig' => 'Bezig',
                        'geannuleerd' => 'Geannuleerd',
                        'afgelopen' => 'Afgelopen',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationLabel(): string
    {
        return 'Wedstrijden'; // Instead of "Matches"
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Wedstrijd Beheer';
    }

    public static function getModelLabel(): string
    {
        return 'Wedstrijd';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Wedstrijden';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['gebruikersScores' => function ($query) {
                $query->orderByDesc('totale_punten')
                    ->orderByRaw('linker_kaart_6 + linker_kaart_7 + linker_kaart_8 + linker_kaart_9 + linker_kaart_10 DESC');
            }]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMatches::route('/'),
            'create' => Pages\CreateMatches::route('/create'),
            'edit' => Pages\EditMatches::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RegistrationsRelationManager::class,
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->canAccessMatches() || $user->is_admin);
    }
}

