<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TotalSalaryPaidByMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Total Salary Paid by Month';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '250px';

    // Query the total salary paid by month
    protected function getData(): array
    {
        $salaryData = DB::table('employee_salaries')
            ->join('users', 'employee_salaries.user_id', '=', 'users.id')
            ->selectRaw('users.name as user_name, YEAR(payment_date) as year, MONTH(payment_date) as month, SUM(net_salary) as total_net_salary')
            ->where('employee_salaries.status', 'paid')
            ->whereYear('payment_date', date('Y'))
            ->whereMonth('payment_date', date('m'))
            ->groupBy('user_name', 'year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        // Prepare the data for the bar chart
        $labels = [];
        $data = [];
        $teacherData = [];

        foreach ($salaryData as $entry) {
            // Format the label as "Month Year"
            $label = $entry->month . '/' . $entry->year;
            if (!isset($teacherData[$entry->user_name])) {
                $teacherData[$entry->user_name] = [
                    'label' => $entry->user_name,
                    'data' => [],
                    'backgroundColor' => '#' . substr(md5(rand()), 0, 6), // Random color for each teacher
                ];
            }
            $teacherData[$entry->user_name]['data'][$label] = $entry->total_net_salary;
            if (!in_array($label, $labels)) {
                $labels[] = $label;
            }
        }

        // Fill missing data points with 0
        foreach ($teacherData as &$teacher) {
            foreach ($labels as $label) {
                if (!isset($teacher['data'][$label])) {
                    $teacher['data'][$label] = 0;
                }
            }
            ksort($teacher['data']);
            $teacher['data'] = array_values($teacher['data']);
        }

        return [
            'datasets' => array_values($teacherData),
            'labels' => $labels,
        ];
    }

    // Set the chart type (bar chart in this case)
    protected function getType(): string
    {
        return 'bar'; // Bar chart
    }
}
