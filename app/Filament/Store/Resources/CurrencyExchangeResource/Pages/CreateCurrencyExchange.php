<?php

namespace App\Filament\Store\Resources\CurrencyExchangeResource\Pages;

use App\Filament\Store\Resources\CurrencyExchangeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCurrencyExchange extends CreateRecord
{
    protected static string $resource = CurrencyExchangeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['store_id'] = auth()->user()->store->id; // or custom logic if needed
        return $data;
    }
}
