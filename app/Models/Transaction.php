<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            $transaction->uuid = Str::uuid();
        });
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }


    public function bank()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }
}
