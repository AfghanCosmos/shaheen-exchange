<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HawlaChart extends ChartWidget
{
    protected static ?string $heading = 'Hawlas Created (Monthly)';
    protected int | string | array $columnSpan = 6;
    protected function getData(): array
    {
        $storeId = Auth::user()->store_id; // or Auth::user()->store->id

        $results = DB::table('hawlas')
            ->selectRaw('MONTH(date) as month, COUNT(*) as total')
            ->whereYear('date', now()->year)
            ->where(function ($query) use ($storeId) {
                $query->where('sender_store_id', $storeId)
                      ->orWhere('receiver_store_id', $storeId);
            })
            ->groupByRaw('MONTH(date)')
            ->orderBy('month')
            ->get();

        $labels = collect(range(1, 12))->map(fn ($m) => now()->setMonth($m)->format('F'))->toArray();
        $data = collect(range(1, 12))->map(fn ($m) => $results->firstWhere('month', $m)->total ?? 0)->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Hawlas Created',
                    'data' => $data,
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

}

