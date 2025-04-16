<?php

namespace App\Filament\Resources\AddToWalletResource\Pages;

use App\Filament\Resources\AddToWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAddToWallets extends ListRecords
{
    protected static string $resource = AddToWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
