<?php

namespace App\Filament\Resources\MatchGebruikerScoreResource\Pages;

use App\Filament\Resources\MatchGebruikerScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMatchGebruikerScores extends ListRecords
{
    protected static string $resource = MatchGebruikerScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
