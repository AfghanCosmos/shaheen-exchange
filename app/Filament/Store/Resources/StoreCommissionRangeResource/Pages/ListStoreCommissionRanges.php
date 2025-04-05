<?php

namespace App\Filament\Store\Resources\StoreCommissionRangeResource\Pages;

use App\Filament\Store\Resources\StoreCommissionRangeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStoreCommissionRanges extends ListRecords
{
    protected static string $resource = StoreCommissionRangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
