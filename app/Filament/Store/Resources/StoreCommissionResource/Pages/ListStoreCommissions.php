<?php

namespace App\Filament\Store\Resources\StoreCommissionResource\Pages;

use App\Filament\Store\Resources\StoreCommissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStoreCommissions extends ListRecords
{
    protected static string $resource = StoreCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
