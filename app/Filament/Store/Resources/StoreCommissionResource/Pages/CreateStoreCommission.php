<?php

namespace App\Filament\Store\Resources\StoreCommissionResource\Pages;

use App\Filament\Store\Resources\StoreCommissionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStoreCommission extends CreateRecord
{
    protected static string $resource = StoreCommissionResource::class;

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
