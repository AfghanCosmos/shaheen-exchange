<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class WithdrawalRequest extends Model
{
    /** @use HasFactory<\Database\Factories\WithdrawalRequestFactory> */
    use HasFactory, SoftDeletes;



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


    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function withdrawBy()
    {
        return $this->belongsTo(User::class, 'withdraw_by')
        ->where('user_type','!=' ,'customer');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }

}
