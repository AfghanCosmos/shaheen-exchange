<?php

namespace App\Filament\Resources\StoreWalletResource\Pages;

use App\Filament\Resources\StoreWalletResource;
use App\Models\Currency;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;

class ListWallets extends ListRecords
{
    protected static string $resource = StoreWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];

        // "All" tab with all wallets
        $tabs['All'] = Tab::make();

        // Dynamically load currencies from wallets and add them as tabs
        $currencies = Currency::whereHas('wallets')->get();  // Get all currencies that are related to wallets

        foreach ($currencies as $currency) {
            $tabs[$currency->name] = Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('currency_id', $currency->id));
        }

        return $tabs;
    }


}
