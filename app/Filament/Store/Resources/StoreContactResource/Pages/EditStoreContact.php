<?php

namespace App\Filament\Store\Resources\StoreContactResource\Pages;

use App\Filament\Store\Resources\StoreContactResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStoreContact extends EditRecord
{
    protected static string $resource = StoreContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Prevent store_id from being changed
        $data['store_id'] = $this->record->store_id;

        return $data;
    }
}
