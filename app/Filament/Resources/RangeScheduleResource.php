<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RangeScheduleResource\Pages;
use App\Models\RangeSchedule;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RangeScheduleResource extends Resource
{
    protected static ?string $model = RangeSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Baanplanning';

    protected static ?string $modelLabel = 'Baanplanning week';

    protected static ?string $pluralModelLabel = 'Baanplanning weken';

    protected static ?string $navigationGroup = 'Wedstrijd Beheer';

    protected static ?int $navigationSort = 11;

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && ($user->canAccessMatches() || $user->is_admin);
    }

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && ($user->canAccessMatches() || $user->is_admin);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('year')
                    ->label('Jaar')
                    ->required()
                    ->numeric()
                    ->default((int) now()->year)
                    ->minValue(2020)
                    ->maxValue(2100),
                Forms\Components\Select::make('quarter')
                    ->label('Kwartaal')
                    ->options([
                        1 => 'Q1',
                        2 => 'Q2',
                        3 => 'Q3',
                        4 => 'Q4',
                    ])
                    ->required(),
                Forms\Components\Select::make('week_number')
                    ->label('Week in kwartaal')
                    ->required()
                    ->options(function (): array {
                        $weeks = [];

                        for ($week = 1; $week <= 13; $week++) {
                            $weeks[$week] = "Week {$week}";
                        }

                        return $weeks;
                    }),
                Forms\Components\Select::make('discipline')
                    ->label('Discipline')
                    ->required()
                    ->options([
                        'pistool' => 'Pistool',
                        'geweer' => 'Geweer',
                    ])
                    ->default('pistool'),
                Forms\Components\Select::make('day_of_week')
                    ->label('Dag')
                    ->required()
                    ->options([
                        'maandag' => 'Maandag',
                        'dinsdag' => 'Dinsdag',
                        'woensdag' => 'Woensdag',
                        'donderdag' => 'Donderdag',
                        'vrijdag' => 'Vrijdag',
                        'zaterdag' => 'Zaterdag',
                        'zondag' => 'Zondag',
                    ])
                    ->default('maandag'),
                Forms\Components\Toggle::make('is_open')
                    ->label('Open')
                    ->default(false)
                    ->live(),
                Forms\Components\TimePicker::make('start_time')
                    ->label('Starttijd')
                    ->seconds(false)
                    ->visible(fn (Forms\Get $get): bool => (bool) $get('is_open'))
                    ->dehydrated(fn (Forms\Get $get): bool => (bool) $get('is_open'))
                    ->required(fn (Forms\Get $get): bool => (bool) $get('is_open')),
                Forms\Components\TimePicker::make('end_time')
                    ->label('Eindtijd')
                    ->seconds(false)
                    ->visible(fn (Forms\Get $get): bool => (bool) $get('is_open'))
                    ->dehydrated(fn (Forms\Get $get): bool => (bool) $get('is_open'))
                    ->required(fn (Forms\Get $get): bool => (bool) $get('is_open'))
                    ->after('start_time'),
                Forms\Components\Textarea::make('notes')
                    ->label('Opmerking')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query
                ->orderByDesc('year')
                ->orderByDesc('quarter')
                ->orderBy('week_number'))
            ->columns([
                Tables\Columns\TextColumn::make('year')
                    ->label('Jaar')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quarter_label')
                    ->label('Kwartaal')
                    ->badge()
                    ->color('primary')
                    ->sortable(query: fn ($query, string $direction) => $query
                        ->orderBy('year', $direction)
                        ->orderBy('quarter', $direction)),
                Tables\Columns\TextColumn::make('week_number')
                    ->label('Week')
                    ->formatStateUsing(fn (int $state): string => "Week {$state}")
                    ->sortable(),
                Tables\Columns\TextColumn::make('discipline_label')
                    ->label('Discipline')
                    ->badge()
                    ->color(fn (RangeSchedule $record): string => $record->discipline === 'geweer' ? 'info' : 'warning'),
                Tables\Columns\TextColumn::make('day_of_week_label')
                    ->label('Dag')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\IconColumn::make('is_open')
                    ->label('Open')
                    ->boolean(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start')
                    ->time('H:i')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Einde')
                    ->time('H:i')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Bijgewerkt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('Jaar')
                    ->options(fn () => RangeSchedule::query()
                        ->select('year')
                        ->distinct()
                        ->orderByDesc('year')
                        ->pluck('year', 'year')
                        ->toArray()),
                Tables\Filters\SelectFilter::make('quarter')
                    ->label('Kwartaal')
                    ->options([
                        1 => 'Q1',
                        2 => 'Q2',
                        3 => 'Q3',
                        4 => 'Q4',
                    ]),
                Tables\Filters\SelectFilter::make('discipline')
                    ->label('Discipline')
                    ->options([
                        'pistool' => 'Pistool',
                        'geweer' => 'Geweer',
                    ]),
                Tables\Filters\SelectFilter::make('day_of_week')
                    ->label('Dag')
                    ->options([
                        'maandag' => 'Maandag',
                        'dinsdag' => 'Dinsdag',
                        'woensdag' => 'Woensdag',
                        'donderdag' => 'Donderdag',
                        'vrijdag' => 'Vrijdag',
                        'zaterdag' => 'Zaterdag',
                        'zondag' => 'Zondag',
                    ]),
                Tables\Filters\TernaryFilter::make('is_open')
                    ->label('Open'),
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
            'index' => Pages\ListRangeSchedules::route('/'),
            'create' => Pages\CreateRangeSchedule::route('/create'),
            'edit' => Pages\EditRangeSchedule::route('/{record}/edit'),
        ];
    }
}
