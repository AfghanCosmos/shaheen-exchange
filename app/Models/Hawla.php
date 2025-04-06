<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hawla extends Model
{
    use SoftDeletes, HasFactory;



    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hawala) {
            if (empty($hawala->uuid)) {
                $hawala->uuid =  self::generateUniqueCode();
            }
        });

        static::created(function ($hawala) {
            self::adjustWalletsOnCreate($hawala);
        });

        static::saving(function ($hawala) {
            $original = $hawala->getOriginal();
            self::adjustWalletsOnUpdate($hawala, $original);
        });

        static::deleting(function ($hawala) {
            self::adjustWalletsOnDelete($hawala);
        });
    }

    // On Create
    protected static function adjustWalletsOnCreate($hawala)
    {
        $storeId = $hawala->getStoreToCredit();
        $currencyId = $hawala->given_amount_currency_id;
        $amount = $hawala->given_amount;
        $commission = $hawala->commission ?? 0;

        $total = $amount + $commission;

        $wallet = self::getWallet($storeId, $currencyId);
        $wallet->increment('balance', $total);
    }

    // On Update
    protected static function adjustWalletsOnUpdate($hawala, $original)
    {
        // Remove old values
        $oldCommissionTakenBy = $original['commission_taken_by'] ?? $hawala->commission_taken_by;
        $oldStoreId = $oldCommissionTakenBy === 'sender_store' ? ($original['sender_store_id'] ?? $hawala->sender_store_id) : ($original['receiver_store_id'] ?? $hawala->receiver_store_id);
        $oldCurrencyId = $original['given_amount_currency_id'] ?? $hawala->given_amount_currency_id;
        $oldAmount = $original['given_amount'] ?? 0;
        $oldCommission = $original['commission'] ?? 0;

        $oldWallet = self::getWallet($oldStoreId, $oldCurrencyId);
        $oldWallet->decrement('balance', $oldAmount + $oldCommission);


        // Add new values
        $newStoreId = $hawala->getStoreToCredit();
        $newCurrencyId = $hawala->given_amount_currency_id;
        $newAmount = $hawala->given_amount ?? 0;
        $newCommission = $hawala->commission ?? 0;

        $newWallet = self::getWallet($newStoreId, $newCurrencyId);
        $newWallet->increment('balance', $newAmount + $newCommission);
    }

    // On Delete
    protected static function adjustWalletsOnDelete($hawala)
    {
        $storeId = $hawala->getStoreToCredit();
        $currencyId = $hawala->given_amount_currency_id;
        $amount = $hawala->given_amount ?? 0;
        $commission = $hawala->commission ?? 0;

        $wallet = self::getWallet($storeId, $currencyId);
        $wallet->decrement('balance', $amount + $commission);
    }

    // Get correct wallet
    protected static function getWallet($storeId, $currencyId)
    {
        return \App\Models\Wallet::firstOrCreate([
            'owner_type' => \App\Models\Store::class,
            'owner_id' => $storeId,
            'currency_id' => $currencyId,
        ], [
            'balance' => 0,
        ]);
    }

    // Which store should receive the commission?
    public function getStoreToCredit()
    {
        return $this->commission_taken_by === 'sender_store'
            ? $this->sender_store_id
            : $this->receiver_store_id;
    }


    private static function generateUniqueCode()
    {
        $letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; // Avoids confusing characters
        $numbers = '0123456789'; // Ensures proper numeric flow

        // Code Format: UXX08239
        $code = 'HN' . substr(str_shuffle($letters), 0, 2) . random_int(10000, 99999);

        // Ensure Uniqueness
        while (self::where('uuid', $code)->exists()) {
            $code = 'HN' . substr(str_shuffle($letters), 0, 2) . random_int(10000, 99999);
        }

        return $code;
    }

    // Relationships
    public function hawlaType(): BelongsTo
    {
        return $this->belongsTo(HawlaType::class);
    }

    public function senderStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'sender_store_id');
    }

    public function receiverStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'receiver_store_id');
    }

    public function givenCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'given_amount_currency_id');
    }

    public function receivingCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'receiving_amount_currency_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }




}
