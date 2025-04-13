<?php

namespace App\Filament\Store\Resources\StoreContactResource\Pages;

use App\Filament\Store\Resources\StoreContactResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStoreContact extends CreateRecord
{
    protected static string $resource = StoreContactResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Assign the store of the authenticated user
        $store = auth()->user()->store;

        if ($store) {
            $data['store_id'] = $store->id;
        }

        return $data;
    }
}
