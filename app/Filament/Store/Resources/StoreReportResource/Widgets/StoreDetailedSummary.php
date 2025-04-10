<?php

namespace App\Filament\StoreDetailedSummary\Resources\StoreReportResource\Widgets;

use Filament\Widgets\ChartWidget;

class StoreDetailedSummary extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
