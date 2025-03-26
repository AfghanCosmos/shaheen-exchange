<?php

namespace App\Filament\Resources\StoreCommissionRangeResource\Pages;

use App\Filament\Resources\StoreCommissionRangeResource;
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
