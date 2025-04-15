<?php

namespace App\Filament\Resources\BankAccountResource\Pages;

use App\Filament\Resources\BankAccountResource;
use App\Models\Currency;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListBankAccounts extends ListRecords
{
    protected static string $resource = BankAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];

        // All bank accounts
        $tabs['All'] = Tab::make('All');

        // One tab per currency
        foreach (Currency::all() as $currency) {
            $tabs[$currency->name] = Tab::make($currency->name)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('currency_id', $currency->id));
        }

        return $tabs;
    }
}


