<?php

namespace App\Filament\Store\Resources\StoreWalletResource\Pages;

use App\Filament\Store\Resources\StoreWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWallets extends ListRecords
{
    protected static string $resource = StoreWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
