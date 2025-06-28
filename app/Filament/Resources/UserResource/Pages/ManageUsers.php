<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    if (!empty($data['password'])) {
                        $data['password'] = bcrypt($data['password']);
                    }
                    return $data;
                }),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Hash password if provided, otherwise remove it from update data
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        
        $record->update($data);
        
        return $record;
    }
}
