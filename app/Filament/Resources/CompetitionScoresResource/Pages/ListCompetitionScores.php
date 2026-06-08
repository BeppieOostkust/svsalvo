<?php

namespace App\Filament\Resources\CompetitionScoresResource\Pages;

use App\Filament\Resources\CompetitionScoresResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompetitionScores extends ListRecords
{
    protected static string $resource = CompetitionScoresResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
