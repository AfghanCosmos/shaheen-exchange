<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Filament\Notifications\Notification;
class Hawla extends Model
{
    use SoftDeletes, HasFactory;



    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hawala) {
            if (empty($hawala->uuid)) {
                $hawala->uuid = self::generateUniqueCode();
            }
        });

        static::created(function ($hawala) {
            self::adjustWalletOnCreate($hawala);
        });

        static::updating(function ($hawala) {
            $original = $hawala->getOriginal();
            self::adjustWalletOnUpdate($hawala, $original);
        });

        static::deleting(function ($hawala) {
            self::adjustWalletOnDelete($hawala);
        });
    }

    public function refund()
    {
        try {
            // Only allow refund when the transaction is not paid and is still pending
            if ($this->paid_at) {
                \Filament\Notifications\Notification::make()
                    ->title('Refund Error')
                    ->body('This transaction has been paid and cannot be refunded.')
                    ->danger()
                    ->send();
                return;
            }

            if ($this->status !== 'in_progress') {
                \Filament\Notifications\Notification::make()
                    ->title('Refund Error')
                    ->body('Only pending transactions can be refunded.')
                    ->danger()
                    ->send();
                return;
            }

            // Reverse the credit applied to the sender's wallet during creation.
            $creditAmount = $this->given_amount;
            if ($this->commission_taken_by === 'sender_store') {
                $creditAmount += ($this->commission ?? 0);
            }
            $senderWallet = self::getWallet($this->sender_store_id, $this->given_amount_currency_id);

            // Check if balance is enough for refund
            if ($senderWallet->balance < $creditAmount) {
                \Filament\Notifications\Notification::make()
                    ->title('Refund Error')
                    ->body('Insufficient balance to process the refund.')
                    ->danger()
                    ->send();
                return;
            }

            $senderWallet->decrement('balance', $creditAmount);

            $this->status = 'cancelled';
            $this->save();

            \Filament\Notifications\Notification::make()
                ->title('Refund Successful')
                ->body('Transaction has been successfully refunded.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Refund Error')
                ->body('Refund failed: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function pay()
    {
        if ($this->paid_at) return;

        try {
            $wallet = self::getWallet($this->receiver_store_id, $this->receiving_amount_currency_id);

            // Use the receiving amount and include commission if it's taken by the receiver store.
            $deductAmount = $this->receiving_amount;
            if ($this->commission_taken_by === 'receiver_store') {
                $deductAmount -= ($this->commission ?? 0);
            }

            if ($wallet->balance < $deductAmount) {
                \Filament\Notifications\Notification::make()
                    ->title('Insufficient Balance')
                    ->body('The receiver store does not have enough balance to complete this transaction.')
                    ->danger()
                    ->send();
                return;
            }

            $wallet->decrement('balance', $deductAmount);

            $this->paid_at = now();
            $this->status = 'completed';
            $this->save();

            \Filament\Notifications\Notification::make()
                ->title('Transaction Successful')
                ->body('Hawala marked as paid and balance updated successfully.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('Something went wrong: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected static function adjustWalletOnCreate($hawala)
    {
        $wallet = self::getWallet($hawala->sender_store_id, $hawala->given_amount_currency_id);

        $amount = $hawala->given_amount;
        if ($hawala->commission_taken_by === 'sender_store') {
            $amount += ($hawala->commission ?? 0);
        }

        $wallet->increment('balance', $amount);
    }

    protected static function adjustWalletOnUpdate($hawala, $original)
    {
        $oldWallet = self::getWallet($original['sender_store_id'], $original['given_amount_currency_id']);
        $oldAmount = $original['given_amount'];
        if ($original['commission_taken_by'] === 'sender_store') {
            $oldAmount += ($original['commission'] ?? 0);
        }
        $oldWallet->decrement('balance', $oldAmount);

        // Apply new logic
        self::adjustWalletOnCreate($hawala);
    }

    protected static function adjustWalletOnDelete($hawala)
    {
        $wallet = self::getWallet($hawala->sender_store_id, $hawala->given_amount_currency_id);

        $amount = $hawala->given_amount;
        if ($hawala->commission_taken_by === 'sender_store') {
            $amount += ($hawala->commission ?? 0);
        }

        $wallet->decrement('balance', $amount);
    }

    protected static function getWallet($storeId, $currencyId)
    {
        return \App\Models\Wallet::firstOrCreate([
            'owner_type' => \App\Models\Store::class,
            'owner_id' => $storeId,
            'currency_id' => $currencyId,
        ], ['balance' => 0]);
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
