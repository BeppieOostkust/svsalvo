<?php

namespace App\Filament\Resources\BoardMemberResource\Pages;

use App\Filament\Resources\BoardMemberResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListBoardMembers extends ListRecords
{
    protected static string $resource = BoardMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nieuw Bestuurslid'),
        ];
    }
}
