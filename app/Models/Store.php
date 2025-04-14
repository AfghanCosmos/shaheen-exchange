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

    public function sentHawlas()
    {
        return $this->hasMany(\App\Models\Hawla::class, 'sender_store_id');
    }

    public function receivedHawlas()
    {
        return $this->hasMany(\App\Models\Hawla::class, 'receiver_store_id');
    }



public function hawlaOverallSummary(): array
{
    // Load hawlas with their related currency information
    $givenHawlas = $this->hawlasGiven()->with('givenCurrency')->get();
    $receivedHawlas = $this->hawlasReceived()->with('receivingCurrency')->get();

    $result = [];

    // Group given hawlas by currency code and sum given amounts
    $groupedGiven = $givenHawlas->groupBy(function ($item) {
        return optional($item->givenCurrency)->code ?? 'Unknown';
    });

    foreach ($groupedGiven as $currency => $hawlas) {
        if (!isset($result[$currency])) {
            $result[$currency] = [
                'total_given' => 0,
                'total_received' => 0,
                'net_balance' => 0,
            ];
        }
        $result[$currency]['total_given'] = $hawlas->sum('given_amount');
    }

    // Group received hawlas by currency code and sum receiving amounts
    $groupedReceived = $receivedHawlas->groupBy(function ($item) {
        return optional($item->receivingCurrency)->code ?? 'Unknown';
    });

    foreach ($groupedReceived as $currency => $hawlas) {
        if (!isset($result[$currency])) {
            $result[$currency] = [
                'total_given' => 0,
                'total_received' => 0,
                'net_balance' => 0,
            ];
        }
        $result[$currency]['total_received'] = $hawlas->sum('receiving_amount');
    }

    // Calculate net balance and format values
    foreach ($result as $currency => &$totals) {
        $net = $totals['total_given'] - $totals['total_received'];
        $totals['net_balance'] = number_format($net, 2);
        $totals['total_given'] = number_format($totals['total_given'], 2);
        $totals['total_received'] = number_format($totals['total_received'], 2);
    }

    return $result;
}

public function hawlaPerStoreSummary(): array
{
    $results = [];

    $given = Hawla::with(['receiverStore', 'givenCurrency'])
        ->where('sender_store_id', $this->id)
        ->where('status', 'completed')
        ->get();

    $received = Hawla::with(['senderStore', 'receivingCurrency'])
        ->where('receiver_store_id', $this->id)
        ->where('status', 'completed')
        ->get();

    // Combine both sent & received grouped by store + currency
    $grouped = collect();

    foreach ($given as $hawla) {
        $key = 'given_' . $hawla->receiver_store_id . '_' . $hawla->given_amount_currency_id;
        $grouped->push([
            'type' => 'given',
            'store_id' => $hawla->receiver_store_id,
            'store_name' => optional($hawla->receiverStore)->name,
            'currency' => optional($hawla->givenCurrency)->code,
            'amount' => $hawla->given_amount,
            'commission' => $hawla->commission,
            'exchange_rate' => $hawla->exchange_rate,
            'key' => $key,
        ]);
    }

    foreach ($received as $hawla) {
        $key = 'received_' . $hawla->sender_store_id . '_' . $hawla->receiving_amount_currency_id;
        $grouped->push([
            'type' => 'received',
            'store_id' => $hawla->sender_store_id,
            'store_name' => optional($hawla->senderStore)->name,
            'currency' => optional($hawla->receivingCurrency)->code,
            'amount' => $hawla->receiving_amount,
            'commission' => $hawla->commission,
            'exchange_rate' => $hawla->exchange_rate,
            'key' => $key,
        ]);
    }

    // Final grouping
    return $grouped->groupBy('key')->map(function ($transactions) {
        $first = $transactions->first();
        $type = $transactions->pluck('type');
        return [
            'store' => $first['store_name'] ?? '—',
            'currency' => $first['currency'] ?? '—',
            'total_given' => $type->contains('given') ? number_format($transactions->where('type', 'given')->sum('amount'), 2) : null,
            'total_received' => $type->contains('received') ? number_format($transactions->where('type', 'received')->sum('amount'), 2) : null,
            'total_commission' => number_format($transactions->sum('commission'), 2),
            'avg_exchange_rate' => $transactions->avg('exchange_rate') ? number_format($transactions->avg('exchange_rate'), 2) : '—',
        ];
    })->values()->toArray();
}

public function hawlasGiven()
{
    return $this->hasMany(\App\Models\Hawla::class, 'sender_store_id')
        ->where('status', 'completed');
}

public function hawlasReceived()
{
    return $this->hasMany(\App\Models\Hawla::class, 'receiver_store_id')
        ->where('status', 'completed');
}
}

