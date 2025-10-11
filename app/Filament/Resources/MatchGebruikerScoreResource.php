<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MatchGebruikerScoreResource\Pages;
use App\Filament\Resources\MatchGebruikerScoreResource\RelationManagers;
use App\Models\MatchGebruikerScore;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MatchGebruikerScoreResource extends Resource
{
    protected static ?string $model = MatchGebruikerScore::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Wedstrijd Scores';

    protected static ?string $navigationGroup = 'Wedstrijd Beheer';

    protected static ?string $modelLabel = 'Score';

    protected static ?string $pluralModelLabel = 'Scores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Wedstrijd Informatie')
                    ->schema([
                        Forms\Components\Select::make('wedstrijd_id')
                            ->relationship('matches', 'naam')
                            ->label('Wedstrijd')
                            ->required()
                            ->searchable()
                            ->preload(),
                            
                        Forms\Components\Select::make('gebruiker_id')
                            ->relationship('user', 'name')
                            ->label('Speler')
                            ->required()
                            ->searchable()
                            ->preload(),
                            
                        Forms\Components\Select::make('kaliber')
                            ->label('Kaliber')
                            ->options([
                                'gkp' => 'GKP (Groot Kaliber Pistool)',
                                'kkp' => 'KKP (Klein Kaliber Pistool)',
                            ])
                            ->required(),
                            
                        Forms\Components\TextInput::make('round_number')
                            ->label('Serie Nummer')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(10)
                            ->required(),
                            
                        Forms\Components\TextInput::make('baan_nummer')
                            ->label('Baan Nummer')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(20),
                            
                        Forms\Components\Toggle::make('is_official')
                            ->label('Officiële Serie')
                            ->default(true),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('Linker Kaart Scores')
                    ->schema([
                        Forms\Components\TextInput::make('linker_kaart_5')
                            ->label('Ring 5 (0 punten)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(10)
                            ->helperText('Telt niet mee voor punten'),
                            
                        Forms\Components\TextInput::make('linker_kaart_6')
                            ->label('Ring 6')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(10),
                            
                        Forms\Components\TextInput::make('linker_kaart_7')
                            ->label('Ring 7')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(10),
                            
                        Forms\Components\TextInput::make('linker_kaart_8')
                            ->label('Ring 8')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(10),
                            
                        Forms\Components\TextInput::make('linker_kaart_9')
                            ->label('Ring 9')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(10),
                            
                        Forms\Components\TextInput::make('linker_kaart_10')
                            ->label('Ring 10')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(10),
                    ])
                    ->columns(6),
                    
                Forms\Components\Section::make('Rechter Kaart Scores')
                    ->schema([
                        Forms\Components\TextInput::make('rechter_kaart_5')
                            ->label('Ring 5 (0 punten)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(10)
                            ->helperText('Telt niet mee voor punten'),
                            
                        Forms\Components\TextInput::make('rechter_kaart_6')
                            ->label('Ring 6')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(10),
                            
                        Forms\Components\TextInput::make('rechter_kaart_7')
                            ->label('Ring 7')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(10),
                            
                        Forms\Components\TextInput::make('rechter_kaart_8')
                            ->label('Ring 8')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(10),
                            
                        Forms\Components\TextInput::make('rechter_kaart_9')
                            ->label('Ring 9')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(10),
                            
                        Forms\Components\TextInput::make('rechter_kaart_10')
                            ->label('Ring 10')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(10),
                    ])
                    ->columns(6),
                    
                Forms\Components\Section::make('Extra Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('aantal_schoten_buiten_tijd')
                            ->label('Schoten Buiten Tijd')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                            
                        Forms\Components\TextInput::make('afwaarderingen')
                            ->label('Afwaarderingen')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                            
                        Forms\Components\TextInput::make('totale_punten')
                            ->label('Totale Punten')
                            ->numeric()
                            ->disabled()
                            ->helperText('Wordt automatisch berekend'),
                            
                        Forms\Components\Textarea::make('notes')
                            ->label('Opmerkingen')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('round_number')
            ->defaultGroup('round_number')
            ->columns([
                Tables\Columns\TextColumn::make('round_number')
                    ->label('Serie')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'success',
                        '2' => 'info', 
                        '3' => 'warning',
                        '4' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => "{$state}e Serie"),
                    
                Tables\Columns\TextColumn::make('matches.naam')
                    ->label('Wedstrijd')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Speler')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('kaliber')
                    ->label('Kaliber')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'gkp' => 'success',
                        'kkp' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                    
                Tables\Columns\TextColumn::make('baan_nummer')
                    ->label('Baan')
                    ->sortable()
                    ->placeholder('Niet toegewezen'),
                    
                Tables\Columns\TextColumn::make('totale_punten')
                    ->label('Totaal')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('linker_kaart_10')
                    ->label('L-10')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('linker_kaart_9')
                    ->label('L-9')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('rechter_kaart_10')
                    ->label('R-10')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('rechter_kaart_9')
                    ->label('R-9')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('linker_kaart_5')
                    ->label('L-5')
                    ->alignCenter()
                    ->color('gray')
                    ->tooltip('Ring 5 - telt niet mee voor punten')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('rechter_kaart_5')
                    ->label('R-5')
                    ->alignCenter()
                    ->color('gray')
                    ->tooltip('Ring 5 - telt niet mee voor punten')
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('is_official')
                    ->label('Officieel')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('round_number')
                    ->label('Serie Nummer')
                    ->options([
                        1 => '1e Serie',
                        2 => '2e Serie', 
                        3 => '3e Serie',
                        4 => '4e Serie',
                        5 => '5e Serie',
                        6 => '6e Serie',
                        7 => '7e Serie',
                        8 => '8e Serie',
                        9 => '9e Serie',
                        10 => '10e Serie',
                    ])
                    ->multiple(),
                    
                Tables\Filters\SelectFilter::make('kaliber')
                    ->label('Kaliber')
                    ->options([
                        'gkp' => 'GKP',
                        'kkp' => 'KKP',
                    ])
                    ->multiple(),
                    
                Tables\Filters\SelectFilter::make('wedstrijd_id')
                    ->label('Wedstrijd')
                    ->relationship('matches', 'naam')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\Filter::make('is_official')
                    ->label('Alleen Officiële Series')
                    ->query(fn (Builder $query): Builder => $query->where('is_official', true))
                    ->toggle(),
            ])
            ->groups([
                Tables\Grouping\Group::make('round_number')
                    ->label('Serie Nummer')
                    ->getTitleFromRecordUsing(fn ($record): string => "{$record->round_number}e Serie")
                    ->collapsible(),
                    
                Tables\Grouping\Group::make('kaliber')
                    ->label('Kaliber')
                    ->getTitleFromRecordUsing(fn ($record): string => strtoupper($record->kaliber))
                    ->collapsible(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('assign_lanes')
                        ->label('Banen Toewijzen')
                        ->icon('heroicon-m-map-pin')
                        ->form([
                            Forms\Components\TextInput::make('start_lane')
                                ->label('Start Baan Nummer')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            $startLane = $data['start_lane'];
                            foreach ($records as $index => $record) {
                                $record->update(['baan_nummer' => $startLane + $index]);
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMatchGebruikerScores::route('/'),
            'create' => Pages\CreateMatchGebruikerScore::route('/create'),
            'edit' => Pages\EditMatchGebruikerScore::route('/{record}/edit'),
        ];
    }
}
