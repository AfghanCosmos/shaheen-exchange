<?php

namespace App\Filament\Store\Pages;

use App\Filament\Store\Resources\StoreReportResource\Widgets\StoreOverview;
use Filament\Pages\Page;

class StoreReport extends Page
{
    protected static ?string $navigationGroup = 'Store Management';

    protected static string $view = 'filament.pages.store-report';
    protected static ?int $navigationSort = 3;

    protected function getHeaderWidgets(): array
    {
        return [
            StoreOverview::class,

        ];
    }
}
