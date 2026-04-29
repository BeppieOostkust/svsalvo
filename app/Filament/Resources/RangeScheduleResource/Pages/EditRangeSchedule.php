<?php

namespace App\Filament\Resources\RangeScheduleResource\Pages;

use App\Filament\Resources\RangeScheduleResource;
use Filament\Resources\Pages\EditRecord;

class EditRangeSchedule extends EditRecord
{
    protected static string $resource = RangeScheduleResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
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
