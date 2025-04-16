<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyExchangeResource\Pages;
use App\Filament\Resources\CurrencyExchangeResource\RelationManagers;
use App\Filament\Resources\CurrencyExchangeResource\RelationManagers\StoreRelationManager;
use App\Filament\Resources\CurrencyExchangeResource\RelationManagers\UserRelationManager;
use App\Models\CurrencyExchange;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use App\Models\ExchangeRate;
use Filament\Forms\Get;
use Filament\Forms\Set;
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

                // Currency Exchange Information Section
                Forms\Components\Section::make('💱 Currency Exchange Information')
                    ->description('Select the currencies and enter the exchange rates to calculate the received amount.')
                    ->schema([
                        Forms\Components\Select::make('store_id')
                            ->label('🏪 Store') // Emoji for Store
                            ->relationship('store', 'name')
                            ->preload()
                            ->native(false)
                            ->required()
                            ->helperText('Select the store for the transaction.'),

                        Forms\Components\Select::make('user_id')
                            ->label('👤 User') // Emoji for User
                            ->relationship('user', 'name')
                            ->searchable()
                            ->default(auth()->user()->id)
                            ->preload()
                            ->required()
                            ->helperText('Select the user involved in the exchange.'),

                        Forms\Components\Select::make('from_currency_id')
                            ->label('🌍 From Currency') // Emoji for From Currency
                            ->relationship('fromCurrency', 'code')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->helperText('Select the currency to exchange from.')
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::autoFillRate($set, $get)),

                        Forms\Components\Select::make('to_currency_id')
                            ->label('💱 To Currency') // Emoji for To Currency
                            ->relationship('toCurrency', 'code')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->helperText('Select the currency to exchange to.')
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::autoFillRate($set, $get)),

                    ])
                    ->columns(2), // Two columns for better organization

                // Amount and Rate Section
                Forms\Components\Section::make('💵 Exchange Rates and Amount')
                    ->description('Enter the amount and rate to calculate the received amount.')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('💵 Amount') // Emoji for Amount
                            ->numeric()
                            ->reactive()
                            ->required()
                            ->helperText('Enter the amount to exchange.')
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::calculateReceivedAmount($set, $get)),

                        Forms\Components\TextInput::make('rate')
                            ->label('🔄 Exchange Rate') // Emoji for Rate
                            ->numeric()
                            ->readOnly()
                            ->reactive()
                            ->required()
                            ->helperText('The exchange rate between the two currencies.')
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::calculateReceivedAmount($set, $get)),

                        Forms\Components\TextInput::make('received_amount')
                            ->label('💰 Received Amount') // Emoji for Received Amount
                            ->numeric()
                            ->readOnly()
                            ->required()
                            ->helperText('Calculated based on the exchange rate and amount.'),

                        Forms\Components\TextInput::make('commission')
                            ->label('💸 Commission') // Emoji for Commission
                            ->numeric()
                            ->helperText('For reference only (not deducted from received amount)')
                            ->default(0),
                    ])
                    ->columns(2), // Two columns for better organization

                // Date and Additional Information Section
                Forms\Components\Section::make('📅 Exchange Date')
                    ->description('Enter the exchange date for the transaction.')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->label('📅 Exchange Date') // Emoji for Date Picker
                            ->default(now())
                            ->required()
                            ->helperText('Select the date for the exchange rate.'),

                    ])
                    ->columns(1), // Single column for a clean layout

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
        ->columns([

            // Store Column with Emoji
                Tables\Columns\TextColumn::make('store.name')
                    ->label('🏪 Store') // Emoji for Store
                    ->sortable()
                    ->searchable()
                    ->tooltip('The store where the transaction took place'),

                // User Column with Emoji
                Tables\Columns\TextColumn::make('user.name')
                    ->label('👤 User') // Emoji for User
                    ->sortable()
                    ->searchable()
                    ->tooltip('The user involved in the exchange'),

                // From Currency Column with Emoji
                Tables\Columns\TextColumn::make('fromCurrency.code')
                    ->label('🌍 From Currency') // Emoji for From Currency
                    ->sortable()
                    ->searchable()
                    ->tooltip('The currency being exchanged from'),

                // Duplicate From Currency Column Removed (Already covered above)
                // TextColumn::make('fromCurrency.code')
                //     ->label('From')
                //     ->sortable()
                //     ->searchable(),

                // To Currency Column with Emoji
                Tables\Columns\TextColumn::make('toCurrency.code')
                    ->label('💱 To Currency') // Emoji for To Currency
                    ->sortable()
                    ->searchable()
                    ->tooltip('The currency being exchanged to'),

                // Amount Column with Emoji and Numeric Formatting
                Tables\Columns\TextColumn::make('amount')
                    ->label('💵 Amount') // Emoji for Amount
                    ->sortable()
                    ->numeric(decimalPlaces: 2, decimalSeparator: '.', thousandsSeparator: ',')
                    ->tooltip('The amount of currency being exchanged'),

                // Received Amount Column with Emoji and Numeric Formatting
                Tables\Columns\TextColumn::make('received_amount')
                    ->label('💰 Received') // Emoji for Received Amount
                    ->sortable()
                    ->numeric(decimalPlaces: 2, decimalSeparator: '.', thousandsSeparator: ',')
                    ->tooltip('The amount received after exchange'),

                // Exchange Rate Column with Emoji and Numeric Formatting
                Tables\Columns\TextColumn::make('rate')
                    ->label('🔄 Rate') // Emoji for Rate
                    ->sortable()
                    ->numeric(decimalPlaces: 4)
                    ->tooltip('The exchange rate between the two currencies'),

                // Commission Column with Emoji and Numeric Formatting
                Tables\Columns\TextColumn::make('commission')
                    ->label('💸 Commission') // Emoji for Commission
                    ->sortable()
                    ->numeric(decimalPlaces: 2)
                    ->tooltip('The commission charged for the exchange'),

                // Date Column with Emoji and Tooltip
                Tables\Columns\TextColumn::make('date')
                    ->label('📅 Date') // Emoji for Date
                    ->date()
                    ->sortable()
                    ->tooltip('The date when the exchange rate was applied'),

                // Created At Column with Emoji and Tooltip
                Tables\Columns\TextColumn::make('created_at')
                    ->label('📅 Created') // Emoji for Created At
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('The date when the exchange record was created'),

                // Updated At Column with Emoji and Tooltip
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('🔄 Updated') // Emoji for Updated At
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('The date when the exchange record was last updated'),

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
            StoreRelationManager::class,
            UserRelationManager::class,
        ];
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
}
