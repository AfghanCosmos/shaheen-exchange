<?php

namespace App\Filament\Resources\CurrencyExchangeResource\Pages;

use App\Filament\Resources\CurrencyExchangeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCurrencyExchange extends ViewRecord
{
    protected static string $resource = CurrencyExchangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
