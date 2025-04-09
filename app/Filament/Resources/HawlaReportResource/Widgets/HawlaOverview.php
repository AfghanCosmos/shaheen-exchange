<?php

namespace App\Filament\Resources\HawlaReportResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Enums\IconPosition;
class HawlaOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Transactions • Sent • In Progress • AFN', number_format(
                \App\Models\Hawla::where('status', 'in_progress')
                    ->whereHas('givenCurrency', fn($q) => $q->where('code', 'AFN'))
                    ->count()
            ))
            ->description('Status: In_Progress, Direction: Sent, Currency: AFN')
            ->descriptionIcon('heroicon-m-chart-bar')
            ->color('info'),

            Stat::make('Total Amount • Sent • In Progress • AFN', number_format(
                \App\Models\Hawla::where('status', 'in_progress')
                    ->whereHas('givenCurrency', fn($q) => $q->where('code', 'AFN'))
                    ->sum('given_amount')
            ))
            ->description('Status: In_Progress, Direction: Sent, Currency: AFN')
            ->descriptionIcon('heroicon-m-chart-bar')
            ->color('info'),

            Stat::make('Total Commission • Sent • In Progress • AFN', number_format(
                \App\Models\Hawla::where('status', 'in_progress')
                    ->whereHas('givenCurrency', fn($q) => $q->where('code', 'AFN'))
                    ->sum('commission')
            ))
            ->description('Status: In_Progress, Direction: Sent, Currency: AFN')
            ->descriptionIcon('heroicon-m-chart-bar')
            ->color('info'),

            Stat::make('Avg Exchange Rate • Sent • In Progress • AFN', number_format(
                \App\Models\Hawla::where('status', 'in_progress')
                    ->whereHas('givenCurrency', fn($q) => $q->where('code', 'AFN'))
                    ->avg('exchange_rate')
            ))
            ->description('Status: In_Progress, Direction: Sent, Currency: AFN')
            ->descriptionIcon('heroicon-m-chart-bar')
            ->color('info'),

        ];
    }
}
