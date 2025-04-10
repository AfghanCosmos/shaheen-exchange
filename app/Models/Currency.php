<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function storeCommissions(): HasMany
    {
        return $this->hasMany(StoreCommission::class);
    }


    public function givenHawlas() : HasMany {
        return $this->hasMany(Hawla::class, 'given_amount_currency_id');
    }

    public function sentHawlas() : HasMany {
        return $this->hasMany(Hawla::class, 'receiving_amount_currency_id');
    }

    public function fromCurrencyExchanges()
    {
        return $this->hasMany(CurrencyExchange::class, 'from_currency_id');
    }

    public function toCurrencyExchanges()
    {
        return $this->hasMany(CurrencyExchange::class, 'to_currency_id');
    }
}
