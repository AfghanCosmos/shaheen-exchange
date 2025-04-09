<?php

namespace App\Filament\Resources\StoreReportResource\Widgets;

use App\Models\Store;
use Filament\Widgets\Widget;

class StoreDetailedSummary extends Widget
{
    protected static string $view = 'filament.pages.store-report.blade.php';

    protected function getViewData(): array
    {
        $stores = Store::with(['hawlasGiven.givenCurrency', 'hawlasReceived.receivingCurrency'])->get();

        return [
            'stores' => $stores,
        ];
    }
}
