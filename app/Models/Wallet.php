<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    /** @use HasFactory<\Database\Factories\WalletFactory> */
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($wallet) {
            if (empty($wallet->uuid)) {
                $wallet->uuid = self::generateUniqueCode();
            }
        });
    }

    public function hawlasAsSender()
    {
        if ($this->owner_type !== Store::class) {
            return $this->hasMany(Hawla::class, 'sender_store_id', 'owner_id');
        }

        return $this->owner->hasMany(Hawla::class, 'sender_store_id');
    }

    public function hawlasAsReceiver()
    {
        if ($this->owner_type !== Store::class) {
            return $this->hasMany(Hawla::class, 'receiver_store_id', 'owner_id');
        }

        return $this->owner->hasMany(Hawla::class, 'receiver_store_id');
    }

    private static function generateUniqueCode()
    {
        $letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; // Avoids confusing characters
        $numbers = '0123456789'; // Ensures proper numeric flow

        // Code Format: UXX08239
        $code = 'W' . substr(str_shuffle($letters), 0, 2) . random_int(10000, 99999);

        // Ensure Uniqueness
        while (self::where('uuid', $code)->exists()) {
            $code = 'W' . substr(str_shuffle($letters), 0, 2) . random_int(10000, 99999);
        }

        return $code;
    }


    public function owner()
    {
        return $this->morphTo();
    }



    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id')->where('owner_type', User::class);
    }


    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
