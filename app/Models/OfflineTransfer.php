<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class OfflineTransfer extends Model
{
    /** @use HasFactory<\Database\Factories\OfflineTransferFactory> */
    use HasFactory, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($offlineTransfer) {
            $offlineTransfer->uuid = Str::uuid();
        });
    }

    public function senderWallet()
    {
        return $this->belongsTo(Wallet::class, 'sender_wallet_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
