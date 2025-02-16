<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Add Carbon for date handling

class SalaryStatusBreakdownChart extends ChartWidget
{
    protected static ?string $heading = 'Salaries Paid (This Month)';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '400px';

    // Query salary data and track salary trend over the current month
    protected function getData(): array
    {
        // Get the start of the current month and the current date
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Generate an array of all the dates in the current month
        $daysInMonth = [];
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $daysInMonth[] = $date->toDateString(); // Add each date to the array
        }

        // Query to get the sum of net salary by payment date (grouped by day for trend analysis)
        $salaryData = DB::table('employee_salaries')
            ->select(DB::raw('DATE_FORMAT(payment_date, "%Y-%m-%d") as payment_day'), DB::raw('SUM(net_salary) as total_net_salary'))
            ->whereBetween('payment_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()]) // Filter for the current month
            ->where('status', 'paid') // Check the status should be paid
            ->groupBy(DB::raw('DATE_FORMAT(payment_date, "%Y-%m-%d")')) // Group by day
            ->orderBy('payment_day') // Sort by date
            ->get();

        // Prepare the data for the line chart
        $labels = $daysInMonth;  // Set the labels to all the days in the current month
        $data = array_fill(0, count($daysInMonth), 0);  // Initialize all salary values as 0

        // Loop through the salary data to populate the chart
        foreach ($salaryData as $entry) {
            $index = array_search($entry->payment_day, $daysInMonth);  // Find the index of the day in the array
            if ($index !== false) {
                $data[$index] = $entry->total_net_salary;  // Set the salary value for the corresponding day
            }
        }

        // Return data for the line chart
        return [
            'datasets' => [
                [
                    'label' => 'Net Salary',  // Label for the line chart
                    'data' => $data,  // Net salary totals over time
                    'fill' => false,  // Do not fill below the line
                    'tension' => 0.1,  // Line smoothness (adjust if needed)
                ],
            ],
            'labels' => $labels,  // X-axis labels (all days of the current month)
        ];
    }

    // Optional chart options
    protected static ?array $options = [
        'scales' => [
            'x' => [
                'type' => 'category',  // Use category instead of time since we're working with static dates
                'title' => [
                    'display' => true,
                    'text' => 'Payment Date',
                ],
            ],
            'y' => [
                'title' => [
                    'display' => true,
                    'text' => 'Net Salary',
                ],
            ],
        ],
    ];

    // Set the type of chart (line chart in this case)
    protected function getType(): string
    {
        return 'line';  // Line chart type
    }
}
