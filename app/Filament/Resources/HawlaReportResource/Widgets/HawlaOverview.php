<?php

namespace App\Filament\Resources\HawlaReportResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\{Hawla, Currency, Store, User, HawlaType};
class HawlaOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [

        Stat::make('Total Hawlas', Hawla::count())
    ->description('All historical transactions')
    ->color('primary'),

// [2] Active Transactions
Stat::make('Active Hawlas', Hawla::where('status', 'in_progress')->count())
    ->description('Currently being processed')
    ->color('warning'),

// [3] Completed Today
Stat::make('Completed Today', Hawla::where('status', 'completed')
    ->whereDate('paid_at', today())->count())
    ->color('success'),

// [4] Cancellation Rate
Stat::make('Cancellation Rate', round(Hawla::where('status', 'cancelled')->count() / max(1, Hawla::count()) * 100, 1))
    ->description('% of cancelled transactions')
    ->color('red'),

// [5] Average Transaction Value
Stat::make('Avg Given Amount', number_format(Hawla::avg('given_amount'), 2))
    ->color('blue'),

// [6] Largest Transaction
Stat::make('Largest Hawla', number_format(Hawla::max('given_amount'), 2))
    ->color('purple'),

// [7] Most Used Currency
Stat::make('Popular Currency', function () {
    $currency = Currency::withCount('givenHawlas')->orderByDesc('given_hawlas_count')->first();
    return $currency ? "{$currency->code} ({$currency->given_hawlas_count})" : 'N/A';
})->color('indigo'),


// [8] Top Currency Pair
Stat::make('Common Currency Pair', function () {
    $pair = Hawla::selectRaw('CONCAT(gc.code, "→", rc.code) as pair, COUNT(*) as count')
           ->join('currencies as gc', 'given_amount_currency_id', '=', 'gc.id')
           ->join('currencies as rc', 'receiving_amount_currency_id', '=', 'rc.id')
           ->groupBy('pair')
           ->orderByDesc('count')
           ->first();
    return $pair ? $pair->pair : 'N/A';
})->color('fuchsia'),

// [9] Total Commission
Stat::make('Total Commission', number_format(Hawla::sum('commission'), 2))
    ->color('green'),

// [10] Commission Split
Stat::make('Commission By', function () {
    $sender = Hawla::where('commission_taken_by', 'sender_store')->count();
    $receiver = Hawla::where('commission_taken_by', 'receiver_store')->count();
    return "Sender: $sender | Receiver: $receiver";
})->color('cyan'),

// [11] Top Sender Store
Stat::make('Busiest Sender', function () {
    $store = Store::withCount('sentHawlas')->orderByDesc('sent_hawlas_count')->first();
    return $store ? "{$store->name} ({$store->sent_hawlas_count})" : 'N/A';
})->color('violet'),

// [12] Top Receiver Store
Stat::make('Busiest Receiver', function () {
    $store = Store::withCount('receivedHawlas')->orderByDesc('received_hawlas_count')->first();
    return $store ? "{$store->name} ({$store->received_hawlas_count})" : 'N/A';
})->color('pink'),

// [13] Popular Hawla Type
Stat::make('Common Type', function () {
    $type = HawlaType::withCount('hawlas')->orderByDesc('hawlas_count')->first();
    return $type ? $type->name : 'N/A';
})->color('amber'),
// [14] Top Employee
Stat::make('Active Employee', function () {
    $user = User::withCount('createdHawlas')->orderByDesc('created_hawlas_count')->first();
    return $user ? $user->name : 'N/A';
})->color('sky'),

// [15] Verification Rate
Stat::make('ID Verification %', round(Hawla::whereNotNull('receiver_verification_document')->count() / max(1, Hawla::count()) * 100, 1))
    ->color('lime'),

// [16] Processing Time
Stat::make('Avg Process Time', function () {
    $avg = Hawla::whereNotNull('paid_at')
          ->average(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, paid_at)'));
    return $avg ? round($avg)." mins" : 'N/A';
})->color('orange'),

// [17] Today's Volume
Stat::make("Today's Count", Hawla::whereDate('date', today())->count())
    ->color('emerald'),
// [18] Monthly Trend
Stat::make('Monthly Change', function () {
    $current = Hawla::whereMonth('date', now()->month)->count();
    $previous = Hawla::whereMonth('date', now()->subMonth()->month)->count();
    return $previous ? round(($current - $previous) / $previous * 100, 1)."%" : 'N/A';
})->color('red'),

// [19] Exchange Rate
Stat::make('Avg Exchange Rate', number_format(Hawla::avg('exchange_rate'), 4))
    ->color('blue'),

// [20] Notes Frequency
Stat::make('Notes Added', Hawla::whereNotNull('note')->count())
    ->color('yellow'),
// [21] Peak Hour
Stat::make('Busiest Hour', function () {
    $hour = Hawla::selectRaw('HOUR(created_at) as hour')->groupBy('hour')->orderByRaw('COUNT(*) DESC')->first();
    return $hour ? $hour->hour.":00" : 'N/A';
})->color('purple'),

// [22] Weekday Analysis
Stat::make('Busiest Day', function () {
    $day = Hawla::selectRaw('DAYNAME(created_at) as day')->groupBy('day')->orderByRaw('COUNT(*) DESC')->first();
    return $day ? $day->day : 'N/A';
})->color('rose'),

// [21] Peak Hour
Stat::make('Busiest Hour', function () {
    $hour = Hawla::selectRaw('HOUR(created_at) as hour')->groupBy('hour')->orderByRaw('COUNT(*) DESC')->first();
    return $hour ? $hour->hour.":00" : 'N/A';
})->color('purple'),

// [22] Weekday Analysis
Stat::make('Busiest Day', function () {
    $day = Hawla::selectRaw('DAYNAME(created_at) as day')->groupBy('day')->orderByRaw('COUNT(*) DESC')->first();
    return $day ? $day->day : 'N/A';
})->color('rose'),

// [23] Commission Rate
Stat::make('Avg Commission %', round(Hawla::avg(DB::raw('(commission/given_amount)*100')), 2))
    ->color('teal'),

// [24] Active Stores
Stat::make('Participating Stores', Store::has('sentHawlas')->orHas('receivedHawlas')->count())
    ->color('indigo'),

// [25] Recent Activity
Stat::make('New This Week', Hawla::where('created_at', '>=', now()->subWeek())->count())
    ->color('sky'),

// [26] High-Value Count
Stat::make('Large Transactions', Hawla::where('given_amount', '>', 10000)->count())
    ->color('gold'),

// [27] Currency Diversity
Stat::make('Currencies Used', Currency::has('givenHawlas')->count())
    ->color('orange'),

// [28] Completion Rate
Stat::make('Success Rate', round(Hawla::where('status', 'completed')->count() / max(1, Hawla::count()) * 100, 1))
    ->color('green'),

// [29] Sender Phone Coverage
Stat::make('Sender Contacts', Hawla::whereNotNull('sender_phone')->count())
    ->color('blue'),

// [30] Receiver Address Coverage
Stat::make('Receiver Addresses', Hawla::whereNotNull('receiver_address')->count())
    ->color('green'),


// [1] Total Given Amount (All Currencies)
Stat::make('Total Given Amount', function () {
    return Currency::withSum('givenHawlas', 'given_amount')->get()
        ->map(fn($c) => "{$c->code}: " . number_format($c->given_hawlas_sum_given_amount, 2))
        ->join(' | ');
})->description('Breakdown by currency')->color('blue'),

// [2] Total Received Amount (All Currencies)
Stat::make('Total Received Amount', function () {
    return Currency::withSum('sentHawlas', 'receiving_amount')->get()
        ->map(fn($c) => "{$c->code}: " . number_format($c->sent_hawlas_sum_receiving_amount, 2))
        ->join(' | ');
})->description('Breakdown by currency')->color('green'),

// [3] AFN Transaction Volume
Stat::make('AFN Volume', function () {
    $afn = Currency::where('code', 'AFN')->first();
    return $afn ? number_format($afn->givenHawlas()->sum('given_amount'), 2) : '0.00';
})->description('Total in Afghanis')->color('black'),

// [4] USD Transaction Volume
Stat::make('USD Volume', function () {
    $usd = Currency::where('code', 'USD')->first();
    return $usd ? number_format($usd->givenHawlas()->sum('given_amount'), 2) : '0.00';
})->description('Total in US Dollars')->color('green'),

// [5] Top 3 Currencies by Volume
Stat::make('Currency Leaders', function () {
    return Currency::withSum('givenHawlas', 'given_amount')
        ->orderByDesc('given_hawlas_sum_given_amount')
        ->take(3)
        ->get()
        ->map(fn($c) => "{$c->code}: " . number_format($c->given_hawlas_sum_given_amount, 2))
        ->join(' | ');
})->color('purple'),

// [6] AFN to USD Conversions
Stat::make('AFN→USD Conversions', function () {
    $afn = Currency::where('code', 'AFN')->first();
    $usd = Currency::where('code', 'USD')->first();
    if (!$afn || !$usd) return '0';

    return Hawla::where('given_amount_currency_id', $afn->id)
        ->where('receiving_amount_currency_id', $usd->id)
        ->count();
})->description('Number of exchanges')->color('gold'),

// [7] USD to AFN Conversions
Stat::make('USD→AFN Conversions', function () {
    $afn = Currency::where('code', 'AFN')->first();
    $usd = Currency::where('code', 'USD')->first();
    if (!$afn || !$usd) return '0';

    return Hawla::where('given_amount_currency_id', $usd->id)
        ->where('receiving_amount_currency_id', $afn->id)
        ->count();
})->description('Number of exchanges')->color('orange'),

// [8] Average AFN Transaction
Stat::make('Avg AFN Hawla', function () {
    $afn = Currency::where('code', 'AFN')->first();
    return $afn ? number_format($afn->givenHawlas()->avg('given_amount'), 2) : '0.00';
})->color('black'),

// [9] Average USD Transaction
Stat::make('Avg USD Hawla', function () {
    $usd = Currency::where('code', 'USD')->first();
    return $usd ? number_format($usd->givenHawlas()->avg('given_amount'), 2) : '0.00';
})->color('green'),

// [10] Largest AFN Transaction
Stat::make('Largest AFN Hawla', function () {
    $afn = Currency::where('code', 'AFN')->first();
    return $afn ? number_format($afn->givenHawlas()->max('given_amount'), 2) : '0.00';
})->color('black'),

// [11] Largest USD Transaction
Stat::make('Largest USD Hawla', function () {
    $usd = Currency::where('code', 'USD')->first();
    return $usd ? number_format($usd->givenHawlas()->max('given_amount'), 2) : '0.00';
})->color('green'),

// [12] AFN Commission Earned
Stat::make('AFN Commission', function () {
    $afn = Currency::where('code', 'AFN')->first();
    return $afn ? number_format(
        $afn->givenHawlas()->sum('commission'),
        2
    ) : '0.00';
})->color('black'),

// [13] USD Commission Earned
Stat::make('USD Commission', function () {
    $usd = Currency::where('code', 'USD')->first();
    return $usd ? number_format(
        $usd->givenHawlas()->sum('commission'),
        2
    ) : '0.00';
})->color('green'),

// [14] Today's AFN Volume
Stat::make("Today's AFN", function () {
    $afn = Currency::where('code', 'AFN')->first();
    return $afn ? number_format(
        $afn->givenHawlas()
            ->whereDate('date', today())
            ->sum('given_amount'),
        2
    ) : '0.00';
})->color('black'),

// [15] Today's USD Volume
Stat::make("Today's USD", function () {
    $usd = Currency::where('code', 'USD')->first();
    return $usd ? number_format(
        $usd->givenHawlas()
            ->whereDate('date', today())
            ->sum('given_amount'),
        2
    ) : '0.00';
})->color('green'),

// [16] Monthly AFN Trend
Stat::make('Monthly AFN', function () {
    $afn = Currency::where('code', 'AFN')->first();
    $current = $afn ? $afn->givenHawlas()->whereMonth('date', now()->month)->sum('given_amount') : 0;
    $previous = $afn ? $afn->givenHawlas()->whereMonth('date', now()->subMonth()->month)->sum('given_amount') : 0;
    $change = $previous ? round(($current - $previous)/$previous*100, 1) : 100;
    return number_format($current, 2) . " (" . ($change >= 0 ? "+" : "") . "$change%";
})->color('red'),

// [17] Monthly USD Trend
Stat::make('Monthly USD', function () {
    $usd = Currency::where('code', 'USD')->first();
    $current = $usd ? $usd->givenHawlas()->whereMonth('date', now()->month)->sum('given_amount') : 0;
    $previous = $usd ? $usd->givenHawlas()->whereMonth('date', now()->subMonth()->month)->sum('given_amount') : 0;
    $change = $previous ? round(($current - $previous)/$previous*100, 1) : 100;
    return number_format($current, 2) . " (" . ($change >= 0 ? "+" : "") . "$change%";
})->color('red'),

// [18] AFN Exchange Efficiency
Stat::make('AFN Exchange Rate Avg', function () {
    $afn = Currency::where('code', 'AFN')->first();
    $usd = Currency::where('code', 'USD')->first();
    if (!$afn || !$usd) return 'N/A';

    return number_format(
        Hawla::where('given_amount_currency_id', $afn->id)
            ->where('receiving_amount_currency_id', $usd->id)
            ->avg('exchange_rate'),
        4
    );
})->color('blue'),

// [19] USD Exchange Efficiency
Stat::make('USD Exchange Rate Avg', function () {
    $afn = Currency::where('code', 'AFN')->first();
    $usd = Currency::where('code', 'USD')->first();
    if (!$afn || !$usd) return 'N/A';

    return number_format(
        Hawla::where('given_amount_currency_id', $usd->id)
            ->where('receiving_amount_currency_id', $afn->id)
            ->avg('exchange_rate'),
        4
    );
})->color('blue'),

// [20] Currency Distribution
Stat::make('Currency Distribution', function () {
    return Currency::withCount('givenHawlas')
        ->orderByDesc('given_hawlas_count')
        ->get()
        ->map(fn($c) => "{$c->code}: {$c->given_hawlas_count}")
        ->join(' | ');
})->description('By transaction count')->color('purple'),
        ];
    }
}
