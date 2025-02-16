<?php

namespace App\Filament\Resources\EmployeeSalaryResource\Pages;

use App\Filament\Resources\EmployeeSalaryResource;
use App\Filament\Widgets\TotalSalaryPaidByMonthChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeSalaries extends ListRecords
{
    protected static string $resource = EmployeeSalaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array {
        return [
            TotalSalaryPaidByMonthChart::class,
            \App\Filament\Widgets\SalaryStatusBreakdownChart::class,
        ];
    }
}
