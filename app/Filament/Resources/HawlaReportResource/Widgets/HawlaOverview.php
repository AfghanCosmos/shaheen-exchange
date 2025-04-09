<?php

namespace App\Filament\Resources\HawlaReportResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\Carbon;
use App\Models\Hawla;

class HawlaOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
     // ✅ Completed • Today
     Stat::make('Total Transactions • Completed • Today', number_format(
        Hawla::where('status', 'completed')->whereDate('date', Carbon::today())->count()
    ))->color('success'),

    Stat::make('Total Given Amount • Completed • Today', number_format(
        Hawla::where('status', 'completed')->whereDate('date', Carbon::today())->sum('given_amount')
    ))->color('info'),

    Stat::make('Total Receiving Amount • Completed • Today' . Carbon::today(), number_format(
        Hawla::where('status', 'completed')->whereDate('date', Carbon::today())->sum('receiving_amount')
    ))->color('info'),

    Stat::make('Total Commission • Completed • Today', number_format(
        Hawla::where('status', 'completed')->whereDate('date', Carbon::today())->sum('commission')
    ))->color('warning'),

    // ✅ Completed • This Week
    Stat::make('Total Transactions • Completed • This Week', number_format(
        Hawla::where('status', 'completed')
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count()
    ))->color('success'),
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
