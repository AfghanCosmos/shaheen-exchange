<?php

namespace App\Filament\Pages;

use App\Filament\Resources\StoreReportResource\Widgets\StoreDetailedSummary;
use App\Filament\Resources\StoreReportResource\Widgets\StoreOverview;
use Filament\Pages\Page;

class StoreReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.store-report';

    protected function getHeaderWidgets(): array
    {
        return [
            StoreOverview::class,
            StoreDetailedSummary::class,

        ];
    }
}
