<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function country()
    {
        return $this->belongsTo(Country::class);
    }


    public function storeContacts()
    {
        return $this->hasMany(StoreContact::class);
    }

   


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($store) {
            if (empty($store->uuid)) {
                $store->uuid = self::generateUniqueCode();
            }
        });

        static::created(function ($store) {
            self::createWalletForAFN($store->id);
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

    private static function createWalletForAFN($id)
    {
        // Find the currency ID for 'AFN'
        $currency = Currency::where('code', 'AFN')->first();

        if (!$currency) {
            return response()->json(['error' => 'Currency with code AFN not found'], 404);
        }



       Wallet::create([
            // 'uuid' => self::generateUniqueCodeForWallet(), // Generate a unique UUID
            'owner_type' => 'App\Models\Store', // Specify the related model
            'owner_id' => $id, // Assuming the authenticated user
            'balance' => 0.00, // Default balance
            'currency_id' => $currency?->id ?? 1, // Assign the found currency ID
            'status' => 'active',
        ]);

    }


    public function wallets()
    {
        return $this->morphOne(Wallet::class, 'owner');
    }

    public function storeCommissionRanges()
    {
        return $this->hasMany(StoreCommissionRange::class);
    }

    public function storeCommissions()
    {
        return $this->hasMany(StoreCommission::class);
    }

}
