<?php

namespace App\Filament\Resources\OrganizationInfoResource\Pages;

use App\Filament\Resources\OrganizationInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrganizationInfo extends EditRecord
{
    protected static string $resource = OrganizationInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Verwijderen'),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
