<?php

namespace App\Filament\Widgets;

use App\Models\Wallet;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class WalletOverviewStats extends BaseWidget
{
    /**
     * The heading property should NOT be static in Filament v3.
     */
    protected ?string $heading = 'Wallet Overview';
    protected static ?int $sort = 2; // 👈 Ensure this appears first


    /**
     * Define the cards that will be displayed in the widget.
     */
    protected function getCards(): array
    {
        return [
            Card::make('Total Wallets', Wallet::count())
                ->description('All registered wallets')
                ->color('primary'),

            Card::make('Active Wallets', Wallet::where('status', 'active')->count())
                ->description('Currently active wallets')
                ->color('success'),

            Card::make('Suspended Wallets', Wallet::where('status', 'suspended')->count())
                ->description('Temporarily suspended wallets')
                ->color('warning'),

            Card::make('Closed Wallets', Wallet::where('status', 'closed')->count())
                ->description('Closed or inactive wallets')
                ->color('danger'),
        ];
    }
}
