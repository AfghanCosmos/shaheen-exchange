<?php

namespace App\Filament\Store\Resources\HawlaReportResource\Widgets;

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

    $query = $this->getScopedQuery();
    return [
        // [1] Total Hawlas
        Stat::make('Total Hawlas', $query->count())
            ->description('All historical transactions')
            ->color('primary'),

        // [2] Active Transactions
        Stat::make('Active Hawlas', $query->clone()
            ->where('status', 'in_progress')->count())
            ->description('Currently being processed')
            ->color('warning'),

        // [3] Completed Today
        Stat::make('Completed Today', $query->clone()
            ->where('status', 'completed')
            ->whereDate('paid_at', today())->count())
            ->color('success'),

        // [4] Cancellation Rate
        Stat::make('Cancellation Rate', function () use ($query) {
            $cancelled = $query->clone()->where('status', 'cancelled')->count();
            $total = $query->count();
            return round($cancelled / max(1, $total) * 100, 1) . '%';
        })
            ->description('% of cancelled transactions')
            ->color('red'),

        // [5] Average Transaction Value
        Stat::make('Avg Given Amount', number_format($query->clone()->avg('given_amount'), 2))
            ->color('blue'),

        // [6] Largest Transaction
        Stat::make('Largest Hawla', number_format($query->clone()->max('given_amount'), 2))
            ->color('purple'),

        // [7] Most Used Currency
        Stat::make('Popular Currency', function () use ($query) {
            $hawlaIds = $query->pluck('id');
            $currency = Currency::withCount([
                'givenHawlas as scoped_count' => fn ($q) => $q->whereIn('id', $hawlaIds)
            ])->orderByDesc('scoped_count')->first();

            return $currency ? "{$currency->code} ({$currency->scoped_count})" : 'N/A';
        })->color('indigo'),

        // [8] Top Currency Pair
        Stat::make('Common Currency Pair', function () use ($query) {
            $pair = $query->clone()
                ->selectRaw('CONCAT(gc.code, "→", rc.code) as pair, COUNT(*) as count')
                ->join('currencies as gc', 'given_amount_currency_id', '=', 'gc.id')
                ->join('currencies as rc', 'receiving_amount_currency_id', '=', 'rc.id')
                ->groupBy('pair')
                ->orderByDesc('count')
                ->first();

            return $pair ? $pair->pair : 'N/A';
        })->color('fuchsia'),

        // [9] Total Commission
        Stat::make('Total Commission', number_format($query->clone()->sum('commission'), 2))
            ->color('green'),

        // [10] Commission Split
        Stat::make('Commission By', function () use ($query) {
            $sender = $query->clone()->where('commission_taken_by', 'sender_store')->count();
            $receiver = $query->clone()->where('commission_taken_by', 'receiver_store')->count();
            return "Sender: $sender | Receiver: $receiver";
        })->color('cyan'),

            // [11] Top Sender Store
           // [11] Busiest Sender Store (only from store's perspective)
        Stat::make('Busiest Sender', function () use ($query) {
            $topSenderId = $query->clone()
                ->select('sender_store_id', DB::raw('COUNT(*) as count'))
                ->groupBy('sender_store_id')
                ->orderByDesc('count')
                ->first();

            $store = Store::find($topSenderId?->sender_store_id);

            return $store ? "{$store->name} ({$topSenderId->count})" : 'N/A';
        })->color('violet'),

        // [12] Busiest Receiver Store
        Stat::make('Busiest Receiver', function () use ($query) {
            $topReceiverId = $query->clone()
                ->select('receiver_store_id', DB::raw('COUNT(*) as count'))
                ->groupBy('receiver_store_id')
                ->orderByDesc('count')
                ->first();

            $store = Store::find($topReceiverId?->receiver_store_id);

            return $store ? "{$store->name} ({$topReceiverId->count})" : 'N/A';
        })->color('pink'),

        // [13] Most Common Hawla Type
        Stat::make('Common Type', function () use ($query) {
            $topTypeId = $query->clone()
                ->select('hawla_type_id', DB::raw('COUNT(*) as count'))
                ->groupBy('hawla_type_id')
                ->orderByDesc('count')
                ->first();

            $type = \App\Models\HawlaType::find($topTypeId?->hawla_type_id);

            return $type ? "{$type->name}" : 'N/A';
        })->color('amber'),

        // [14] Most Active Employee (based on created Hawlas for this store)
        Stat::make('Active Employee', function () use ($query) {
            $topUserId = $query->clone()
                ->select('created_by', DB::raw('COUNT(*) as count'))
                ->groupBy('created_by')
                ->orderByDesc('count')
                ->first();

            $user = \App\Models\User::find($topUserId?->created_by);

            return $user ? "{$user->name} ({$topUserId->count})" : 'N/A';
        })->color('sky'),

        // [15] Verification Rate
        Stat::make('ID Verification %', function () use ($query) {
            $withDoc = $query->clone()->whereNotNull('receiver_verification_document')->count();
            $total = $query->count();
            return round($withDoc / max(1, $total) * 100, 1) . '%';
        })->color('lime'),

        // [16] Avg. Processing Time
        Stat::make('Avg Process Time', function () use ($query) {
            $avg = $query->clone()
                ->whereNotNull('paid_at')
                ->average(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, paid_at)'));

            return $avg ? round($avg) . " mins" : 'N/A';
        })->color('orange'),

        // [17] Today’s Volume (created today)
        Stat::make("Today's Count", $query->clone()
            ->whereDate('date', today())->count())
            ->color('emerald'),

        // [18] Monthly Change
        Stat::make('Monthly Change', function () use ($query) {
            $current = $query->clone()->whereMonth('date', now()->month)->count();
            $previous = $query->clone()->whereMonth('date', now()->subMonth()->month)->count();

            return $previous ? round(($current - $previous) / $previous * 100, 1) . "%" : 'N/A';
        })->color('red'),

        // [19] Avg Exchange Rate
        Stat::make('Avg Exchange Rate', number_format($query->clone()->avg('exchange_rate'), 4))
            ->color('blue'),

        // [20] Notes Filled
        Stat::make('Notes Added', $query->clone()->whereNotNull('note')->count())
            ->color('yellow'),
             // [21] Peak Hour (based on created_at)
    Stat::make('Busiest Hour', function () use ($query) {
        $hour = $query->clone()
            ->selectRaw('HOUR(created_at) as hour')
            ->groupBy('hour')
            ->orderByRaw('COUNT(*) DESC')
            ->first();

        return $hour ? str_pad($hour->hour, 2, '0', STR_PAD_LEFT) . ":00" : 'N/A';
    })->color('purple'),

    // [22] Busiest Day of the Week
    Stat::make('Busiest Day', function () use ($query) {
        $day = $query->clone()
            ->selectRaw('DAYNAME(created_at) as day')
            ->groupBy('day')
            ->orderByRaw('COUNT(*) DESC')
            ->first();

        return $day ? $day->day : 'N/A';
    })->color('rose'),

    // [23] Average Commission Rate (percentage)
    Stat::make('Avg Commission %', function () use ($query) {
        return round($query->clone()
            ->where('given_amount', '>', 0)
            ->avg(DB::raw('(commission / given_amount) * 100')), 2) . '%';
    })->color('teal'),

    // [24] Participating Stores (as sender or receiver in this store's hawlas)
    Stat::make('Participating Stores', function () use ($query) {
        $storeIds = $query->clone()
            ->selectRaw('sender_store_id')
            ->union(
                $query->clone()->selectRaw('receiver_store_id')
            )
            ->pluck('sender_store_id')
            ->merge($query->clone()->pluck('receiver_store_id'))
            ->unique()
            ->count();

        return $storeIds;
    })->color('indigo'),

    // [25] New This Week
    Stat::make('New This Week', $query->clone()
        ->where('created_at', '>=', now()->subWeek())->count())
        ->color('sky'),

    // [26] High-Value Transactions (> 10,000)
    Stat::make('Large Transactions', $query->clone()
        ->where('given_amount', '>', 10000)->count())
        ->color('gold'),

    // [27] Currency Diversity (how many unique currencies used)
    Stat::make('Currencies Used', $query->clone()
        ->distinct('given_amount_currency_id')
        ->count('given_amount_currency_id'))
        ->color('orange'),

    // [28] Completion Rate
    Stat::make('Success Rate', function () use ($query) {
        $completed = $query->clone()->where('status', 'completed')->count();
        $total = $query->count();
        return round($completed / max(1, $total) * 100, 1) . '%';
    })->color('green'),

    // [29] Sender Phone Coverage
    Stat::make('Sender Contacts', $query->clone()
        ->whereNotNull('sender_phone')->count())
        ->color('blue'),

    // [30] Receiver Address Coverage
    Stat::make('Receiver Addresses', $query->clone()
        ->whereNotNull('receiver_address')->count())
        ->color('green'),

// [1] Total Given Amount (by currency)
Stat::make('Total Given Amount', function () use ($query) {
    $hawlaIds = $query->pluck('id');

    return Currency::get()->map(function ($currency) use ($hawlaIds) {
        $amount = $currency->givenHawlas()->whereIn('id', $hawlaIds)->sum('given_amount');
        return $amount > 0 ? "{$currency->code}: " . number_format($amount, 2) : null;
    })->filter()->join(' | ');
})->description('Breakdown by currency')->color('blue'),

// [2] Total Received Amount (by currency)
Stat::make('Total Received Amount', function () use ($query) {
    $hawlaIds = $query->pluck('id');

    return Currency::get()->map(function ($currency) use ($hawlaIds) {
        $amount = $currency->sentHawlas()->whereIn('id', $hawlaIds)->sum('receiving_amount');
        return $amount > 0 ? "{$currency->code}: " . number_format($amount, 2) : null;
    })->filter()->join(' | ');
})->description('Breakdown by currency')->color('green'),

// [3] AFN Volume
Stat::make('AFN Volume', function () use ($query) {
    $afn = Currency::where('code', 'AFN')->first();
    if (!$afn) return '0.00';

    return number_format(
        $query->clone()->where('given_amount_currency_id', $afn->id)->sum('given_amount'),
        2
    );
})->description('Total in Afghanis')->color('black'),

// [4] USD Volume
Stat::make('USD Volume', function () use ($query) {
    $usd = Currency::where('code', 'USD')->first();
    if (!$usd) return '0.00';

    return number_format(
        $query->clone()->where('given_amount_currency_id', $usd->id)->sum('given_amount'),
        2
    );
})->description('Total in US Dollars')->color('green'),

// [5] Top 3 Currencies by Volume
Stat::make('Currency Leaders', function () use ($query) {
    $hawlaIds = $query->pluck('id');

    return Currency::get()->map(function ($currency) use ($hawlaIds) {
        $sum = $currency->givenHawlas()->whereIn('id', $hawlaIds)->sum('given_amount');
        return ['code' => $currency->code, 'sum' => $sum];
    })
    ->filter(fn ($item) => $item['sum'] > 0)
    ->sortByDesc('sum')
    ->take(3)
    ->map(fn ($c) => "{$c['code']}: " . number_format($c['sum'], 2))
    ->join(' | ');
})->color('purple'),

// [6] AFN → USD Conversions
Stat::make('AFN→USD Conversions', function () use ($query) {
    $afn = Currency::where('code', 'AFN')->first();
    $usd = Currency::where('code', 'USD')->first();
    if (!$afn || !$usd) return '0';

    return $query->clone()
        ->where('given_amount_currency_id', $afn->id)
        ->where('receiving_amount_currency_id', $usd->id)
        ->count();
})->description('Number of exchanges')->color('gold'),

// [7] USD → AFN Conversions
Stat::make('USD→AFN Conversions', function () use ($query) {
    $afn = Currency::where('code', 'AFN')->first();
    $usd = Currency::where('code', 'USD')->first();
    if (!$afn || !$usd) return '0';

    return $query->clone()
        ->where('given_amount_currency_id', $usd->id)
        ->where('receiving_amount_currency_id', $afn->id)
        ->count();
})->description('Number of exchanges')->color('orange'),

// [8] Average AFN Transaction
Stat::make('Avg AFN Hawla', function () use ($query) {
    $afn = Currency::where('code', 'AFN')->first();
    if (!$afn) return '0.00';

    return number_format(
        $query->clone()
            ->where('given_amount_currency_id', $afn->id)
            ->avg('given_amount'),
        2
    );
})->color('black'),

// [9] Average USD Transaction
Stat::make('Avg USD Hawla', function () use ($query) {
    $usd = Currency::where('code', 'USD')->first();
    if (!$usd) return '0.00';

    return number_format(
        $query->clone()
            ->where('given_amount_currency_id', $usd->id)
            ->avg('given_amount'),
        2
    );
})->color('green'),

// [10] Largest AFN Transaction
Stat::make('Largest AFN Hawla', function () use ($query) {
    $afn = Currency::where('code', 'AFN')->first();
    if (!$afn) return '0.00';

    return number_format(
        $query->clone()
            ->where('given_amount_currency_id', $afn->id)
            ->max('given_amount'),
        2
    );
})->color('black'),

             // [11] Largest USD Transaction (Scoped)
    Stat::make('Largest USD Hawla', function () use ($query) {
        $usd = Currency::where('code', 'USD')->first();
        return $usd
            ? number_format(
                $query->clone()->where('given_amount_currency_id', $usd->id)->max('given_amount'),
                2
            )
            : '0.00';
    })->color('green'),

    // [12] AFN Commission Earned
    Stat::make('AFN Commission', function () use ($query) {
        $afn = Currency::where('code', 'AFN')->first();
        return $afn
            ? number_format(
                $query->clone()->where('given_amount_currency_id', $afn->id)->sum('commission'),
                2
            )
            : '0.00';
    })->color('black'),

    // [13] USD Commission Earned
    Stat::make('USD Commission', function () use ($query) {
        $usd = Currency::where('code', 'USD')->first();
        return $usd
            ? number_format(
                $query->clone()->where('given_amount_currency_id', $usd->id)->sum('commission'),
                2
            )
            : '0.00';
    })->color('green'),

    // [14] Today's AFN Volume
    Stat::make("Today's AFN", function () use ($query) {
        $afn = Currency::where('code', 'AFN')->first();
        return $afn
            ? number_format(
                $query->clone()
                    ->where('given_amount_currency_id', $afn->id)
                    ->whereDate('date', today())
                    ->sum('given_amount'),
                2
            )
            : '0.00';
    })->color('black'),

    // [15] Today's USD Volume
    Stat::make("Today's USD", function () use ($query) {
        $usd = Currency::where('code', 'USD')->first();
        return $usd
            ? number_format(
                $query->clone()
                    ->where('given_amount_currency_id', $usd->id)
                    ->whereDate('date', today())
                    ->sum('given_amount'),
                2
            )
            : '0.00';
    })->color('green'),

    // [16] Monthly AFN Trend
    Stat::make('Monthly AFN', function () use ($query) {
        $afn = Currency::where('code', 'AFN')->first();

        $current = $afn
            ? $query->clone()
                ->where('given_amount_currency_id', $afn->id)
                ->whereMonth('date', now()->month)
                ->sum('given_amount')
            : 0;

        $previous = $afn
            ? $query->clone()
                ->where('given_amount_currency_id', $afn->id)
                ->whereMonth('date', now()->subMonth()->month)
                ->sum('given_amount')
            : 0;

        $change = $previous ? round(($current - $previous) / $previous * 100, 1) : 100;

        return number_format($current, 2) . " (" . ($change >= 0 ? '+' : '') . $change . "%)";
    })->color('red'),

    // [17] Monthly USD Trend
    Stat::make('Monthly USD', function () use ($query) {
        $usd = Currency::where('code', 'USD')->first();

        $current = $usd
            ? $query->clone()
                ->where('given_amount_currency_id', $usd->id)
                ->whereMonth('date', now()->month)
                ->sum('given_amount')
            : 0;

        $previous = $usd
            ? $query->clone()
                ->where('given_amount_currency_id', $usd->id)
                ->whereMonth('date', now()->subMonth()->month)
                ->sum('given_amount')
            : 0;

        $change = $previous ? round(($current - $previous) / $previous * 100, 1) : 100;

        return number_format($current, 2) . " (" . ($change >= 0 ? '+' : '') . $change . "%)";
    })->color('red'),

    // [18] AFN Exchange Efficiency (AFN → USD)
    Stat::make('AFN Exchange Rate Avg', function () use ($query) {
        $afn = Currency::where('code', 'AFN')->first();
        $usd = Currency::where('code', 'USD')->first();

        if (!$afn || !$usd) return 'N/A';

        return number_format(
            $query->clone()
                ->where('given_amount_currency_id', $afn->id)
                ->where('receiving_amount_currency_id', $usd->id)
                ->avg('exchange_rate'),
            4
        );
    })->color('blue'),

    // [19] USD Exchange Efficiency (USD → AFN)
    Stat::make('USD Exchange Rate Avg', function () use ($query) {
        $afn = Currency::where('code', 'AFN')->first();
        $usd = Currency::where('code', 'USD')->first();

        if (!$afn || !$usd) return 'N/A';

        return number_format(
            $query->clone()
                ->where('given_amount_currency_id', $usd->id)
                ->where('receiving_amount_currency_id', $afn->id)
                ->avg('exchange_rate'),
            4
        );
    })->color('blue'),

    // [20] Currency Distribution
    Stat::make('Currency Distribution', function () use ($query) {
        $hawlaIds = $query->pluck('id');

        return Currency::get()
            ->map(function ($currency) use ($hawlaIds) {
                $count = $currency->givenHawlas()->whereIn('id', $hawlaIds)->count();
                return $count > 0 ? "{$currency->code}: {$count}" : null;
            })
            ->filter()
            ->join(' | ');
    })->description('By transaction count')->color('purple'),
        ];
    }


    protected function getScopedQuery()
    {
        $store = auth()->user()?->store;

        if (! $store) {
            return Hawla::query()->whereRaw('0 = 1'); // No data if store is not found
        }

        return Hawla::query()->where(function ($query) use ($store) {
            $query->where('sender_store_id', $store->id)
                ->orWhere('receiver_store_id', $store->id);
        });
    }
}
