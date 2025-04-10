<?php

namespace App\Filament\Resources\ExchangeReportResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\CurrencyExchange;
use App\Models\Currency;
use Carbon\Carbon;
class ExchangeOverview extends BaseWidget
{
    protected function getStats(): array
    {

return [

    /* 1. Basic Counts */
    Stat::make('Total Exchanges', CurrencyExchange::count())
        ->description('All time exchanges'),

    Stat::make("Today's Exchanges", CurrencyExchange::whereDate('date', today())->count())
        ->description('Exchanges today')

        ->color('success'),

    Stat::make('Monthly Exchanges', CurrencyExchange::whereMonth('date', now()->month)->count())
        ->description('Current month exchanges')
       ,

    Stat::make('Yearly Exchanges', CurrencyExchange::whereYear('date', now()->year)->count())
        ->description('Current year exchanges')
       ,

    Stat::make('Avg Daily Exchanges', function() {
        $days = max(1, Carbon::parse(CurrencyExchange::min('date'))->diffInDays(now()));
        return round(CurrencyExchange::count() / $days, 1);
    })->description('Average exchanges per day'),

    /* 2. By Currency */
    Stat::make('Top Source Currency', function() {
        $top = CurrencyExchange::groupBy('from_currency_id')
            ->selectRaw('count(*) as count, from_currency_id')
            ->orderByDesc('count')
            ->first();
        return "{$top->fromCurrency->code} ({$top->count}x)";
    })->description('Most exchanged from currency'),

    Stat::make('Top Target Currency', function() {
        $top = CurrencyExchange::groupBy('to_currency_id')
            ->selectRaw('count(*) as count, to_currency_id')
            ->orderByDesc('count')
            ->first();
        return "{$top->toCurrency->code} ({$top->count}x)";
    })->description('Most exchanged to currency'),

    Stat::make('Rarest Source Currency', function() {
        $top = CurrencyExchange::groupBy('from_currency_id')
            ->selectRaw('count(*) as count, from_currency_id')
            ->orderBy('count')
            ->first();
        return "{$top->fromCurrency->code} ({$top->count}x)";
    })->description('Least exchanged from currency'),

    Stat::make('Rarest Target Currency', function() {
        $top = CurrencyExchange::groupBy('to_currency_id')
            ->selectRaw('count(*) as count, to_currency_id')
            ->orderBy('count')
            ->first();
        return "{$top->toCurrency->code} ({$top->count}x)";
    })->description('Least exchanged to currency'),

    Stat::make('Unique Source Currencies', CurrencyExchange::distinct('from_currency_id')->count())
        ->description('Different currencies exchanged from'),

    Stat::make('Unique Target Currencies', CurrencyExchange::distinct('to_currency_id')->count())
        ->description('Different currencies exchanged to'),

    Stat::make('Unique Currency Pairs', CurrencyExchange::distinct(['from_currency_id', 'to_currency_id'])->count())
        ->description('Different exchange pairs'),

    Stat::make('Highest Outgoing Volume', function() {
        $top = CurrencyExchange::groupBy('from_currency_id')
            ->selectRaw('sum(amount) as total, from_currency_id')
            ->orderByDesc('total')
            ->first();
        return "{$top->fromCurrency->name} (".number_format($top->total, 2).")";
    })->description('Currency with most amount exchanged from'),

    Stat::make('Highest Incoming Volume', function() {
        $top = CurrencyExchange::groupBy('to_currency_id')
            ->selectRaw('sum(received_amount) as total, to_currency_id')
            ->orderByDesc('total')
            ->first();
        return "{$top->toCurrency->code} (".number_format($top->total, 2).")";
    })->description('Currency with most amount received'),

    /* 3. By Amounts */
    Stat::make('Total Amount Exchanged', function() {
        return number_format(CurrencyExchange::sum('amount'), 2);
    })->description('All time total'),

    Stat::make('Average Exchange Amount', function() {
        return number_format(CurrencyExchange::avg('amount'), 2);
    })->description('Mean exchange size'),

    Stat::make('Largest Single Exchange', function() {
        $exchange = CurrencyExchange::orderByDesc('amount')->first();
        return $exchange ? number_format($exchange->amount, 2)." (ID: {$exchange->id})" : 'N/A';
    })->description('Biggest single transaction'),

    Stat::make('Smallest Single Exchange', function() {
        $exchange = CurrencyExchange::orderBy('amount')->first();
        return $exchange ? number_format($exchange->amount, 2)." (ID: {$exchange->id})" : 'N/A';
    })->description('Smallest single transaction'),

    Stat::make("Today's Volume", function() {
        return number_format(CurrencyExchange::whereDate('date', today())->sum('amount'), 2);
    })->description('Amount exchanged today'),

    Stat::make('Monthly Volume', function() {
        return number_format(CurrencyExchange::whereMonth('date', now()->month)->sum('amount'), 2);
    })->description('Current month volume'),

    Stat::make('Average Exchange Rate', function() {
        return number_format(CurrencyExchange::avg('rate'), 4);
    })->description('Mean rate across all exchanges'),

    /* 4. By Pairs */
    Stat::make('Most Common Pair', function() {
        $pair = CurrencyExchange::groupBy(['from_currency_id', 'to_currency_id'])
            ->selectRaw('count(*) as count, from_currency_id, to_currency_id')
            ->orderByDesc('count')
            ->first();
        return $pair ? "{$pair->fromCurrency->code}→{$pair->toCurrency->code} ({$pair->count}x)" : 'N/A';
    })->description('Most frequent currency pair'),

    Stat::make('Highest Volume Pair', function() {
        $pair = CurrencyExchange::groupBy(['from_currency_id', 'to_currency_id'])
            ->selectRaw('sum(amount) as total, from_currency_id, to_currency_id')
            ->orderByDesc('total')
            ->first();
        return $pair ? "{$pair->fromCurrency->code}→{$pair->toCurrency->code} (".number_format($pair->total, 2).")" : 'N/A';
    })->description('Pair with most amount exchanged'),

    Stat::make('Largest Average Pair', function() {
        $pair = CurrencyExchange::groupBy(['from_currency_id', 'to_currency_id'])
            ->selectRaw('avg(amount) as avg, from_currency_id, to_currency_id')
            ->orderByDesc('avg')
            ->first();
        return $pair ? "{$pair->fromCurrency->code}→{$pair->toCurrency->code} (".number_format($pair->avg, 2).")" : 'N/A';
    })->description('Pair with largest average exchange'),

    Stat::make('Most Profitable Pair', function() {
        $pair = CurrencyExchange::groupBy(['from_currency_id', 'to_currency_id'])
            ->selectRaw('sum(commission) as total, from_currency_id, to_currency_id')
            ->orderByDesc('total')
            ->first();
        return $pair ? "{$pair->fromCurrency->code}→{$pair->toCurrency->code} (".number_format($pair->total, 2).")" : 'N/A';
    })->description('Pair generating most commission'),

    /* 5. Time-Based */
    Stat::make('Busiest Day', function() {
        $day = CurrencyExchange::groupBy('date')
            ->selectRaw('count(*) as count, date')
            ->orderByDesc('count')
            ->first();
        return $day ? "{$day->date} ({$day->count}x)" : 'N/A';
    })->description('Day with most exchanges'),

    Stat::make('Monthly Growth', function() {
        $current = CurrencyExchange::whereMonth('date', now()->month)->count();
        $previous = CurrencyExchange::whereMonth('date', now()->subMonth()->month)->count();
        $change = $previous ? round(($current - $previous) / $previous * 100) : 0;
        return "{$change}%";
    })->description('Month-over-month change')
        ->color('danger'),

];
    }
}
