<?php

namespace App\Filament\Store\Pages;

use Filament\Pages\Page;

class ExchangeReport extends Page
{
    protected static ?string $navigationGroup = "Exchange Management";
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.exchange-report';

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\ExchangeReportResource\Widgets\ExchangeOverview::class,
        ];
    }

}
