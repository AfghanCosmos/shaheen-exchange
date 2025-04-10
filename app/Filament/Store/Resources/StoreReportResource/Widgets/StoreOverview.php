<?php

namespace App\Filament\Resources\StoreReportResource\Widgets;

use App\Models\Store;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StoreOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = [];

        // Get all active stores (you can filter further if needed)
        $stores = Store::with(['hawlasGiven.givenCurrency', 'hawlasReceived.receivingCurrency'])->get();

        foreach ($stores as $store) {
            $summary = $store->hawlaOverallSummary();

            // Loop through each currency's summary for this store
            foreach ($summary as $currency => $totals) {
                $stats[] = Stat::make("{$store->name} - {$currency} Net", $totals['net_balance'])
                    ->description("Given: {$totals['total_given']} | Received: {$totals['total_received']}")
                    ->descriptionIcon('heroicon-o-banknotes')
                    ->color('gray');
            }
        }

        return $stats;
    }
}
