<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompetitionResource\Pages;
use App\Filament\Resources\CompetitionResource\RelationManagers;
use App\Models\Competition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompetitionResource extends Resource
{
    protected static ?string $model = Competition::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Competities';

    protected static ?string $navigationGroup = 'Wedstrijdlieden';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && $user->is_admin;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Competitie Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('jaar')
                            ->label('Jaar')
                            ->numeric()
                            ->required()
                            ->minValue(2020)
                            ->maxValue(2100)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('naam')
                            ->label('Competitie Naam')
                            ->required()
                            ->default(fn ($record) => $record?->jaar ? "Competitie {$record->jaar}" : null),
                        Forms\Components\Textarea::make('beschrijving')
                            ->label('Beschrijving')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'gepland' => 'Gepland',
                                'bezig' => 'Bezig',
                                'afgelopen' => 'Afgelopen',
                                'geannuleerd' => 'Geannuleerd',
                            ])
                            ->required()
                            ->default('gepland'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jaar')
                    ->label('Jaar')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('naam')
                    ->label('Naam')
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'gepland' => 'Gepland',
                        'bezig' => 'Bezig',
                        'afgelopen' => 'Afgelopen',
                        'geannuleerd' => 'Geannuleerd',
                    ]),
                Tables\Columns\TextColumn::make('rounds_count')
                    ->label('Beurten')
                    ->counts('rounds'),
                Tables\Columns\TextColumn::make('registrations_count')
                    ->label('Deelnemers')
                    ->counts('registrations'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'gepland' => 'Gepland',
                        'bezig' => 'Bezig',
                        'afgelopen' => 'Afgelopen',
                        'geannuleerd' => 'Geannuleerd',
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\RoundsRelationManager::class,
            RelationManagers\RegistrationsRelationManager::class,
            RelationManagers\ScoresRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompetitions::route('/'),
            'create' => Pages\CreateCompetition::route('/create'),
            'edit' => Pages\EditCompetition::route('/{record}/edit'),
        ];
    }
}
