<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hawla extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hawala) {

            if (empty($hawala->uuid)) {
                $hawala->uuid = self::generateUniqueCode();
            }
        });

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

    public function status(): BelongsTo
    {
        return $this->belongsTo(HawlaStatus::class, 'status_id');
    }


}
