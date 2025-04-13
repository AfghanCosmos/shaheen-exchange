<?php

namespace App\Filament\Store\Resources;

use App\Filament\Store\Resources\ExchangeRateResource\Pages;
use App\Models\ExchangeRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

use Filament\Tables\Table;

class ExchangeRateResource extends Resource
{
    protected static ?string $model = ExchangeRate::class;

    protected static ?string $navigationGroup = 'Exchange Management';
    protected static ?int $navigationSort = 3;



    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
        ->columns([
            /** 🔁 Currency Names */
            Tables\Columns\TextColumn::make('fromCurrency.name')
                ->label('💸 From Currency')
                ->searchable()
                ->sortable()
                ->badge()
                ->color('gray'),

            Tables\Columns\TextColumn::make('toCurrency.name')
                ->label('💵 To Currency')
                ->searchable()
                ->sortable()
                ->badge()
                ->color('info'),

            /** 📈 Rates */
            Tables\Columns\TextColumn::make('buy_rate')
                ->label('🟢 Buy Rate')
                ->numeric(decimalPlaces: 4)
                ->sortable()
                ->color('success'),

            Tables\Columns\TextColumn::make('sell_rate')
                ->label('🔴 Sell Rate')
                ->numeric(decimalPlaces: 4)
                ->sortable()
                ->color('danger'),

            /** 🗓️ Dates */
            Tables\Columns\TextColumn::make('date')
                ->label('📅 Rate Date')
                ->date()
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('🕒 Created')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('updated_at')
                ->label('🔄 Updated')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])


           ;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExchangeRates::route('/'),

        ];
    }


}
