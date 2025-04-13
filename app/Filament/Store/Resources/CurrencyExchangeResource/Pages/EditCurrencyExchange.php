<?php

namespace App\Filament\Store\Resources\CurrencyExchangeResource\Pages;

use App\Filament\Store\Resources\CurrencyExchangeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['store_id'] = Auth::user()->store->id; // 🔥 this is what you're missing

        return $data;
    }
}
