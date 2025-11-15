<?php

namespace App\Filament\Resources\MatchesResource\Pages;

use App\Filament\Actions\ExportScoresAction;
use App\Filament\Resources\MatchesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMatches extends ListRecords
{
    protected static string $resource = MatchesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Set up real-time table refresh when match updates are broadcast
     */
    protected function getListeners(): array
    {
        return [
            "echo:matches,match.updated" => 'refreshTable',
        ];
    }

    /**
     * Refresh the table when a match is updated
     */
    public function refreshTable(): void
    {
        // Force table refresh
        $this->dispatch('$refresh');
    }
}
