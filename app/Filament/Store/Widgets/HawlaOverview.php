<?php

namespace App\Filament\Widgets;

use App\Models\Hawla;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Auth;

class HawlaOverview extends BaseWidget
{
    protected ?string $heading = 'Hawala Overview (My Store)';
    protected static ?int $sort = 1;

    protected function getCards(): array
    {
        $storeId = Auth::user()->store_id;

        // Get most common given and receiving currency in this store's Hawlas
        $givenCurrencyId = Hawla::where('sender_store_id', $storeId)
            ->select('given_amount_currency_id')
            ->groupBy('given_amount_currency_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(1)
            ->value('given_amount_currency_id');

        $receivingCurrencyId = Hawla::where('receiver_store_id', $storeId)
            ->select('receiving_amount_currency_id')
            ->groupBy('receiving_amount_currency_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(1)
            ->value('receiving_amount_currency_id');

        $givenSymbol = $this->getCurrencySymbol($givenCurrencyId);
        $receivingSymbol = $this->getCurrencySymbol($receivingCurrencyId);

        return [
            Card::make('Total Hawlas', Hawla::where('sender_store_id', $storeId)->orWhere('receiver_store_id', $storeId)->count())
                ->description('All time total')
                ->color('primary'),

            Card::make('Today\'s Hawlas', Hawla::whereDate('date', today())
                ->where(function ($query) use ($storeId) {
                    $query->where('sender_store_id', $storeId)
                          ->orWhere('receiver_store_id', $storeId);
                })->count())
                ->description('Created today')
                ->color('success'),

            Card::make('Pending Hawlas', Hawla::where('status', 1)
                ->where(function ($query) use ($storeId) {
                    $query->where('sender_store_id', $storeId)
                          ->orWhere('receiver_store_id', $storeId);
                })->count())
                ->description('Currently in progress')
                ->color('warning'),

            Card::make('Total Sent Amount', $givenSymbol . number_format(
                Hawla::where('sender_store_id', $storeId)->sum('given_amount'), 2))
                ->description('Sum of given amounts')
                ->color('info'),

            Card::make('Total Received Amount', $receivingSymbol . number_format(
                Hawla::where('receiver_store_id', $storeId)->sum('receiving_amount'), 2))
                ->description('Sum of received amounts')
                ->color('teal'),

            Card::make('Total Commission', $givenSymbol . number_format(
                Hawla::where(function ($query) use ($storeId) {
                    $query->where('sender_store_id', $storeId)
                          ->orWhere('receiver_store_id', $storeId);
                })->sum('commission'), 2))
                ->description('All commissions earned')
                ->color('success'),
        ];
    }

    private function getCurrencySymbol(?int $currencyId): string
    {
        $symbols = [
            1 => '؋', // Afghani
            2 => '$', // USD
            3 => '€', // Euro
            4 => '₹', // INR
            // Extend as needed
        ];

        return $symbols[$currencyId] ?? '$';
    }
}
