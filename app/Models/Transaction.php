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

            if (empty($transaction->uuid)) {
                $transaction->uuid = self::generateUniqueCode();
            }
        });

    }

    private static function generateUniqueCode()
    {
        $letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; // Avoids confusing characters
        $numbers = '0123456789'; // Ensures proper numeric flow

        // Code Format: UXX08239
        $code = 'T' . substr(str_shuffle($letters), 0, 2) . random_int(10000, 99999);

        // Ensure Uniqueness
        while (self::where('uuid', $code)->exists()) {
            $code = 'T' . substr(str_shuffle($letters), 0, 2) . random_int(10000, 99999);
        }

        return $code;
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
