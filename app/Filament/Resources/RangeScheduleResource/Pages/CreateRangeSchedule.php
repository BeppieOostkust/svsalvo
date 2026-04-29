<?php

namespace App\Filament\Resources\RangeScheduleResource\Pages;

use App\Filament\Resources\RangeScheduleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRangeSchedule extends CreateRecord
{
    protected static string $resource = RangeScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! ($data['is_open'] ?? false)) {
            $data['start_time'] = null;
            $data['end_time'] = null;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
