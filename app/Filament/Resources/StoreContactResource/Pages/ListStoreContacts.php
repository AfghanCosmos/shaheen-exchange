<?php

namespace App\Filament\Resources\StoreContactResource\Pages;

use App\Filament\Resources\StoreContactResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStoreContacts extends ListRecords
{
    protected static string $resource = StoreContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
