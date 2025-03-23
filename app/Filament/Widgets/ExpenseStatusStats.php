<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class ExpenseStatusStats extends BaseWidget
{
    protected ?string $heading = 'Expenses Overview';
    protected static ?int $sort = 3; // 👈 Set widget position

    /**
     * Define the cards that will be displayed in the widget.
     */
    protected function getCards(): array
    {
        return [
            Card::make('Total Pending', Expense::where('status', 'pending')->count())
                ->description('Pending expenses awaiting approval')
                ->color('warning'),

            Card::make('Total Approved', Expense::where('status', 'approved')->count())
                ->description('Approved and finalized expenses')
                ->color('success'),

            Card::make('Total Rejected', Expense::where('status', 'rejected')->count())
                ->description('Expenses that were rejected')
                ->color('danger'),

            Card::make('Total Approved Amount', number_format(Expense::where('status', 'approved')->sum('amount'), 2) . ' USD')
                ->description('Total value of approved expenses')
                ->color('info'),
        ];
    }
}
