<?php

namespace App\Filament\Resources\AddToWalletResource\Pages;

use App\Filament\Resources\AddToWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAddToWallet extends EditRecord
{
    protected static string $resource = AddToWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
