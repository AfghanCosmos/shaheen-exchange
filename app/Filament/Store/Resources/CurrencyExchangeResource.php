<?php

namespace App\Filament\Store\Resources;

use App\Filament\Store\Resources\CurrencyExchangeResource\Pages;
use App\Filament\Store\Resources\CurrencyExchangeResource\RelationManagers;
use App\Models\CurrencyExchange;
use Filament\Forms;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\BadgeEntry;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use App\Models\ExchangeRate;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Auth;

class CurrencyExchangeResource extends Resource
{
    protected static ?string $model = CurrencyExchange::class;


    protected static ?string $navigationGroup = "Exchange Management";
    protected static ?int $navigationSort = 1;


    protected static function autoFillRate(Set $set, Get $get): void
{
    $from = $get('from_currency_id');
    $to = $get('to_currency_id');

    if ($from && $to && $from !== $to) {
        $rate = ExchangeRate::where('from_currency_id', $from)
            ->where('to_currency_id', $to)
            ->latest('date')
            ->first();

        if ($rate) {
            $set('rate', $rate->sell_rate); // You can change to buy_rate if needed
        } else {
            $set('rate', 0);
        }
    }

    self::calculateReceivedAmount($set, $get);
}

protected static function calculateReceivedAmount(Set $set, Get $get): void
{
    $amount = (float) $get('amount');
    $rate = (float) $get('rate');

    if ($amount > 0 && $rate > 0) {
        $received = $amount * $rate;
        $set('received_amount', round($received, 2));
    } else {
        $set('received_amount', 0);
    }
}


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                /** ─────── 💱 Exchange Info ─────── */
                Forms\Components\Section::make('💱 Currency Exchange Details')
                    ->icon('heroicon-o-banknotes')
                    ->description('Define the currency exchange details including rate and amount.')
                    ->columns(3)
                    ->schema([

                        Select::make('from_currency_id')
                            ->label('From Currency')
                            ->relationship('fromCurrency', 'code')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->placeholder('Select currency')
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::autoFillRate($set, $get)),

                        Select::make('to_currency_id')
                            ->label('To Currency')
                            ->relationship('toCurrency', 'code')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->placeholder('Select currency')
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::autoFillRate($set, $get)),

                        DatePicker::make('date')
                            ->label('📅 Exchange Date')
                            ->default(now())
                            ->required()
                            ->columnSpan(1),
                    ]),

                /** ─────── 🔢 Amount & Rate ─────── */
                Forms\Components\Section::make('🔢 Amount & Calculations')
                    ->icon('heroicon-o-calculator')
                    ->description('Enter the amount to exchange, and we’ll auto-calculate the result.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('amount')
                            ->label('💸 Amount')
                            ->numeric()
                            ->reactive()
                            ->required()
                            ->placeholder('e.g., 100')
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::calculateReceivedAmount($set, $get)),

                        TextInput::make('rate')
                            ->label('📈 Exchange Rate')
                            ->numeric()
                            ->readOnly()
                            ->reactive()
                            ->required()
                            ->suffixIcon('heroicon-o-currency-dollar')
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::calculateReceivedAmount($set, $get)),

                        TextInput::make('received_amount')
                            ->label('✅ Received Amount')
                            ->numeric()
                            ->readOnly()
                            ->required()
                            ->suffixIcon('heroicon-o-banknotes'),
                    ]),

                /** ─────── 💼 Optional Commission ─────── */
                Forms\Components\Section::make('💼 Commission')
                    ->icon('heroicon-o-receipt-percent')
                    ->description('Optional reference commission. Not deducted from received amount.')
                    ->columns(1)
                    ->schema([
                        TextInput::make('commission')
                            ->label('Commission')
                            ->numeric()
                            ->default(0)
                            ->placeholder('e.g., 2.5')
                            ->helperText('This value is for reference only.'),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            /** 🏬 Store & User */
            TextColumn::make('store.name')
                ->label('🏬 Store')
                ->sortable()
                ->searchable()
                ->badge()
                ->color('gray'),

            TextColumn::make('user.name')
                ->label('👤 User')
                ->sortable()
                ->searchable()
                ->badge()
                ->color('info'),

            /** 💱 Currencies */
            TextColumn::make('fromCurrency.code')
                ->label('💸 From')
                ->sortable()
                ->searchable()
                ->badge()
                ->color('danger'),

            TextColumn::make('toCurrency.code')
                ->label('💵 To')
                ->sortable()
                ->searchable()
                ->badge()
                ->color('success'),

            /** 🔢 Financial Details */
            TextColumn::make('amount')
                ->label('💰 Amount')
                ->numeric(decimalPlaces: 2, decimalSeparator: '.', thousandsSeparator: ',')
                ->sortable(),

            TextColumn::make('received_amount')
                ->label('✅ Received')
                ->numeric(decimalPlaces: 2, decimalSeparator: '.', thousandsSeparator: ',')
                ->sortable()
                ->color('success'),

            TextColumn::make('rate')
                ->label('📈 Rate')
                ->numeric(decimalPlaces: 4)
                ->sortable(),

            TextColumn::make('commission')
                ->label('💼 Commission')
                ->numeric(decimalPlaces: 2)
                ->sortable()
                ->color(fn ($state) => $state > 0 ? 'warning' : 'gray'),

            /** 📅 Dates */
            TextColumn::make('date')
                ->label('📅 Date')
                ->date()
                ->sortable(),

            TextColumn::make('created_at')
                ->label('🕒 Created')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('updated_at')
                ->label('🔄 Updated')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('💱 Currency Exchange Overview')
                    ->description('Summary of the selected exchange operation.')
                    ->icon('heroicon-o-banknotes')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('store.name')
                            ->label('🏬 Store')
                            ->badge()
                            ->color('success'),

                        TextEntry::make('user.name')
                            ->label('👤 User')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('date')
                            ->label('📅 Exchange Date')
                            ->date()
                            ->icon('heroicon-o-calendar-days'),
                    ]),

                Section::make('🔁 Currency Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('fromCurrency.code')
                            ->label('From Currency')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('toCurrency.code')
                            ->label('To Currency')
                            ->badge()
                            ->color('info'),
                    ]),

                Section::make('💵 Amount & Calculations')
                    ->icon('heroicon-o-calculator')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('amount')
                            ->label('💸 Amount')
                            ->suffix('FROM')
                            ->formatStateUsing(fn ($state) => number_format($state, 2)),

                        TextEntry::make('rate')
                            ->label('📈 Exchange Rate')
                            ->formatStateUsing(fn ($state) => number_format($state, 4)),

                        TextEntry::make('received_amount')
                            ->label('✅ Received Amount')
                            ->suffix('TO')
                            ->formatStateUsing(fn ($state) => number_format($state, 2)),
                    ]),

                Section::make('💼 Additional Info')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('commission')
                            ->label('Commission')
                            ->formatStateUsing(fn ($state) => number_format($state, 2))
                            ->suffix('%')
                            ->visible(fn ($state) => $state > 0),

                        TextEntry::make('created_at')
                            ->label('📥 Created At')
                            ->dateTime()
                            ->icon('heroicon-o-clock'),

                        TextEntry::make('updated_at')
                            ->label('📤 Last Updated')
                            ->dateTime()
                            ->icon('heroicon-o-arrow-path'),
                    ]),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencyExchanges::route('/'),
            'create' => Pages\CreateCurrencyExchange::route('/create'),
            'view' => Pages\ViewCurrencyExchange::route('/{record}'),
            'edit' => Pages\EditCurrencyExchange::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

}
