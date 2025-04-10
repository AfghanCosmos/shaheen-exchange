<?php

namespace App\Filament\Resources\CurrencyExchangeResource\Pages;

use App\Filament\Resources\CurrencyExchangeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCurrencyExchange extends EditRecord
{
    protected static string $resource = CurrencyExchangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
