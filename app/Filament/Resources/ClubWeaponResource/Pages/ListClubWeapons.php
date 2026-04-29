<?php

namespace App\Filament\Resources\ClubWeaponResource\Pages;

use App\Filament\Resources\ClubWeaponResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClubWeapons extends ListRecords
{
    protected static string $resource = ClubWeaponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nieuw Clubwapen'),
        ];
    }
}
