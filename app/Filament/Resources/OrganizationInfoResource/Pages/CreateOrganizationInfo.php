<?php

namespace App\Filament\Resources\OrganizationInfoResource\Pages;

use App\Filament\Resources\OrganizationInfoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrganizationInfo extends CreateRecord
{
    protected static string $resource = OrganizationInfoResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
