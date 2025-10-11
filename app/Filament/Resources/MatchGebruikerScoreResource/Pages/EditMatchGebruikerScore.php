<?php

namespace App\Filament\Resources\MatchGebruikerScoreResource\Pages;

use App\Filament\Resources\MatchGebruikerScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMatchGebruikerScore extends EditRecord
{
    protected static string $resource = MatchGebruikerScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
