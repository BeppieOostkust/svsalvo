<?php

namespace App\Filament\Resources\CompetitionScoresResource\Pages;

use App\Filament\Resources\CompetitionScoresResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompetitionScore extends EditRecord
{
    protected static string $resource = CompetitionScoresResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
