<?php

namespace App\Filament\Store\Resources\WalletResource\Pages;

use App\Filament\Store\Resources\WalletResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWallet extends EditRecord
{
    protected static string $resource = WalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

}
