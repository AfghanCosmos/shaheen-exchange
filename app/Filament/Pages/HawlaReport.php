<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Resources\HawlaReportResource\Widgets\HawlaOverview;

class HawlaReport extends Page
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.hawla-report';

    protected static ?string $navigationGroup = "Hawla Management";

    protected static ?int $navigationSort = 2;

    protected function getHeaderWidgets(): array
    {
        return [
            HawlaOverview::class,

        ];
    }

}
