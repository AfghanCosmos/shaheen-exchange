<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    /** @use HasFactory<\Database\Factories\StoreFactory> */
    use HasFactory, SoftDeletes;


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($store) {
            if (empty($store->uuid)) {
                $store->uuid = self::generateUniqueCode();
            }
        });
    }

    private static function generateUniqueCode()
    {
        $letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; // Avoids confusing characters
        $numbers = '0123456789'; // Ensures proper numeric flow

        // Code Format: UXX08239
        $code = 'S' . substr(str_shuffle($letters), 0, 2) . random_int(10000, 99999);

        // Ensure Uniqueness
        while (self::where('uuid', $code)->exists()) {
            $code = 'S' . substr(str_shuffle($letters), 0, 2) . random_int(10000, 99999);
        }

        return $code;
    }
}
