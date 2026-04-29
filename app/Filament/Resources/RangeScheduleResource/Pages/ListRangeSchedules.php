<?php

namespace App\Filament\Resources\RangeScheduleResource\Pages;

use App\Filament\Resources\RangeScheduleResource;
use App\Models\RangeSchedule;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListRangeSchedules extends ListRecords
{
    protected static string $resource = RangeScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generateQuarter')
                ->label('Kwartaalweken aanmaken')
                ->icon('heroicon-o-sparkles')
                ->form([
                    TextInput::make('year')
                        ->label('Jaar')
                        ->numeric()
                        ->required()
                        ->default((int) now()->year)
                        ->minValue(2020)
                        ->maxValue(2100),
                    Select::make('quarter')
                        ->label('Kwartaal')
                        ->required()
                        ->options([
                            1 => 'Q1',
                            2 => 'Q2',
                            3 => 'Q3',
                            4 => 'Q4',
                        ]),
                    Select::make('discipline')
                        ->label('Discipline')
                        ->required()
                        ->default('pistool')
                        ->options([
                            'pistool' => 'Pistool',
                            'geweer' => 'Geweer',
                        ]),
                    Select::make('day_of_week')
                        ->label('Dag')
                        ->required()
                        ->default('maandag')
                        ->options([
                            'maandag' => 'Maandag',
                            'dinsdag' => 'Dinsdag',
                            'woensdag' => 'Woensdag',
                            'donderdag' => 'Donderdag',
                            'vrijdag' => 'Vrijdag',
                            'zaterdag' => 'Zaterdag',
                            'zondag' => 'Zondag',
                        ]),
                ])
                ->action(function (array $data): void {
                    $createdCount = 0;

                    for ($week = 1; $week <= 13; $week++) {
                        $record = RangeSchedule::firstOrCreate(
                            [
                                'year' => (int) $data['year'],
                                'quarter' => (int) $data['quarter'],
                                'week_number' => $week,
                                'discipline' => $data['discipline'],
                                'day_of_week' => $data['day_of_week'],
                            ],
                            [
                                'is_open' => false,
                                'start_time' => null,
                                'end_time' => null,
                            ],
                        );

                        if ($record->wasRecentlyCreated) {
                            $createdCount++;
                        }
                    }

                    Notification::make()
                        ->title('Kwartaalweken aangemaakt')
                        ->body("{$createdCount} nieuwe weken toegevoegd voor {$data['discipline']} op {$data['day_of_week']} in Q{$data['quarter']} {$data['year']}.")
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make()
                ->label('Nieuwe planning week'),
        ];
    }
}
