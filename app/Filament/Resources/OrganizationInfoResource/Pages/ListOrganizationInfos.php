<?php

namespace App\Filament\Resources\OrganizationInfoResource\Pages;

use App\Filament\Resources\OrganizationInfoResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListOrganizationInfos extends ListRecords
{
    protected static string $resource = OrganizationInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nieuwe Informatie Toevoegen'),
        ];
    }
}
