<?php

namespace App\Filament\Resources\StoreCommissionResource\Pages;

use App\Filament\Resources\StoreCommissionResource;
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
