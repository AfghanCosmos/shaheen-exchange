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
}
