<?php

namespace App\Filament\Store\Widgets;

use Filament\Widgets\ChartWidget;

class HawlaByStoreChart extends ChartWidget
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
