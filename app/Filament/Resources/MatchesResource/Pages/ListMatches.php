<?php

namespace App\Filament\Resources\MatchesResource\Pages;

use App\Filament\Actions\ExportScoresAction;
use App\Filament\Resources\MatchesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMatches extends ListRecords
{
    protected static string $resource = MatchesResource::class;

    // Poll every 3 seconds to refresh the list
    protected static ?string $pollingInterval = '3s';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
