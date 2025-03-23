<?php

namespace App\Filament\Widgets;

use App\Models\BankAccount;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class BankAccountOverviewStats extends BaseWidget
{
    /**
     * The heading property should NOT be static.
     */
    protected ?string $heading = 'Bank Account Overview';
    protected static ?int $sort = 1; // 👈 Ensure this appears first


    /**
     * Define the cards that will be displayed in the widget.
     */
    protected function getCards(): array
    {
        return [
            Card::make('Total Accounts', BankAccount::count())
                ->description('All bank accounts')
                ->color('primary'),

            Card::make('Active Accounts', BankAccount::where('status', 'active')->count())
                ->description('Currently active accounts')
                ->color('success'),

            Card::make('Primary Accounts', BankAccount::where('is_primary', true)->count())
                ->description('Primary linked accounts')
                ->color('info'),

            Card::make('Closed Accounts', BankAccount::where('status', 'closed')->count())
                ->description('Closed or inactive accounts')
                ->color('danger'),
        ];
    }
}
