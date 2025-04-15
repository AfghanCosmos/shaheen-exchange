<?php

namespace App\Filament\Resources\WalletResource\Pages;

use App\Filament\Resources\WalletResource;
use App\Models\Currency;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;

use Filament\Resources\Components\Tab;

use Filament\Resources\Pages\ListRecords;

class ListWallets extends ListRecords
{
    protected static string $resource = WalletResource::class;

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

        // Dynamically load currencies
        foreach (Currency::all() as $currency) {
            $tabs[$currency->name] = Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('currency_id', $currency->id));
        }

        return $tabs;
    }
}
