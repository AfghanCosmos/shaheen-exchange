<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreCommission extends Model
{
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the commission type.
     */
    public function commissionType(): BelongsTo
    {
        return $this->belongsTo(CommissionType::class);
    }

    /**
     * Get the currency.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
