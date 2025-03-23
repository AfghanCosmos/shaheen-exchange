<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserStats extends ChartWidget
{
    protected static ?string $heading = 'Active Users Statistics';
    protected static ?int $sort = 4; // 👈 Ensure this appears first


    /**
     * Define the chart type - 'bar', 'line', 'pie', etc.
     */
    protected function getType(): string
    {
        return 'bar'; // Example: Bar chart
    }

    /**
     * Fetch and structure the data for the chart.
     */
    protected function getData(): array
    {
        // Count active users created in the last 6 months
        $activeUsersByMonth = User::query()
            ->where('status', 'active')
            ->selectRaw('DATE_FORMAT(created_at, "%M") as month, COUNT(*) as count')
            ->whereBetween('created_at', [now()->subMonths(5), now()])
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return [
            'labels' => array_keys($activeUsersByMonth),
            'datasets' => [
                [
                    'label' => 'Active Users',
                    'data' => array_values($activeUsersByMonth),
                    'backgroundColor' => '#4CAF50', // Green for active users
                ],
            ],
        ];
    }

    /**
     * Optional: Set widget column span
     */
    public static function getColumns(): int
    {
        return 2; // Example layout
    }
}
