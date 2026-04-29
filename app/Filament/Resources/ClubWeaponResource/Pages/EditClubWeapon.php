<?php

namespace App\Filament\Resources\ClubWeaponResource\Pages;

use App\Filament\Resources\ClubWeaponResource;
use Filament\Resources\Pages\EditRecord;

class EditClubWeapon extends EditRecord
{
    protected static string $resource = ClubWeaponResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
