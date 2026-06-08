<?php

namespace App\Filament\Resources\CompetitionResource\Pages;

use App\Filament\Resources\CompetitionResource;
use App\Models\Competition;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompetitions extends ListRecords
{
    protected static string $resource = CompetitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nieuwe Competitie')
                ->after(fn (Competition $record) => $this->createDefaultRounds($record)),
        ];
    }

    /**
     * Create 5 default rounds when a competition is created
     */
    private function createDefaultRounds(Competition $competition): void
    {
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
