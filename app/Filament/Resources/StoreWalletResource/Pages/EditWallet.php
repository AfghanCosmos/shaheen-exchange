<?php

namespace App\Filament\Resources\StoreWalletResource\Pages;

use App\Filament\Resources\StoreWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWallet extends EditRecord
{
    protected static string $resource = StoreWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
