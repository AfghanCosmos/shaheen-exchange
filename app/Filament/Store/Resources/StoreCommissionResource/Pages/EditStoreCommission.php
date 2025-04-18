<?php

namespace App\Filament\Store\Resources\StoreCommissionResource\Pages;

use App\Filament\Store\Resources\StoreCommissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStoreCommission extends EditRecord
{
    protected static string $resource = StoreCommissionResource::class;

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
