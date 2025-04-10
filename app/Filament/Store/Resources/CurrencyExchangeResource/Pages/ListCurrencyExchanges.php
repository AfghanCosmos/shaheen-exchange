<?php

namespace App\Filament\Store\Resources\CurrencyExchangeResource\Pages;

use App\Filament\Store\Resources\CurrencyExchangeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCurrencyExchanges extends ListRecords
{
    protected static string $resource = CurrencyExchangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
