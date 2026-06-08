<?php

namespace App\Filament\Resources\CompetitionResource\Pages;

use App\Filament\Resources\CompetitionResource;
use App\Models\Competition;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCompetition extends CreateRecord
{
    protected static string $resource = CompetitionResource::class;

    protected function afterCreate(): void
    {
        // Create 5 default rounds
        $competition = $this->record;
        for ($i = 1; $i <= 5; $i++) {
            $competition->rounds()->firstOrCreate(
                ['round_number' => $i],
                [
                    'naam' => "Beurt {$i}",
                    'beschrijving' => null,
                    'datum' => null,
                ]
            );
        }
    }
}
