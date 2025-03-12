<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WithdrawalRequest extends Model
{
    /** @use HasFactory<\Database\Factories\WithdrawalRequestFactory> */
    use HasFactory;



    protected static function boot()
    {
        parent::boot();

        static::creating(function ($withdrawal) {
            $withdrawal->uuid = Str::uuid();
        });
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function offlineTransfer()
    {
        return $this->belongsTo(OfflineTransfer::class);
    }

    public function receiverWallet()
    {
        return $this->belongsTo(Wallet::class, 'receiver_wallet_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
