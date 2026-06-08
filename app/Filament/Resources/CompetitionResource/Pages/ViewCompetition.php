<?php

namespace App\Filament\Resources\CompetitionResource\Pages;

use App\Filament\Resources\CompetitionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCompetition extends ViewRecord
{
    protected static string $resource = CompetitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
