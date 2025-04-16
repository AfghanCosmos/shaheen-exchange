<?php

namespace App\Filament\Resources\AddToWalletResource\Pages;

use App\Filament\Resources\AddToWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAddToWallet extends ViewRecord
{
    protected static string $resource = AddToWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
