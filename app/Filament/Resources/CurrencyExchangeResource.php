<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyExchangeResource\Pages;
use App\Filament\Resources\CurrencyExchangeResource\RelationManagers;
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
            Select::make('store_id')
                ->label('Store')
                ->relationship('store', 'name')
                ->preload()
                ->required(),
            Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->searchable()
                ->default(auth()->user()->id)
                ->preload()
                ->required(),
            Select::make('from_currency_id')
                ->label('From Currency')
                ->relationship('fromCurrency', 'code')
                ->searchable()
                ->preload()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn (Set $set, Get $get) => static::autoFillRate($set, $get)),

            Select::make('to_currency_id')
                ->label('To Currency')
                ->relationship('toCurrency', 'code')
                ->searchable()
                ->preload()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn (Set $set, Get $get) => static::autoFillRate($set, $get)),

            TextInput::make('amount')
                ->label('Amount')
                ->numeric()
                ->reactive()
                ->required()
                ->afterStateUpdated(fn (Set $set, Get $get) => static::calculateReceivedAmount($set, $get)),

            TextInput::make('rate')
                ->label('Exchange Rate')
                ->numeric()
                ->readOnly()
                ->reactive()
                ->required()
                ->afterStateUpdated(fn (Set $set, Get $get) => static::calculateReceivedAmount($set, $get)),

            TextInput::make('received_amount')
                ->label('Received Amount')
                ->numeric()
                ->readOnly()
                ->required(),

            TextInput::make('commission')
                ->label('Commission')
                ->numeric()
                ->helperText('For reference only (not deducted from received amount)')
                ->default(0),

            DatePicker::make('date')
                ->label('Exchange Date')
                ->default(now())
                ->required(),
        ])
        ->columns(3);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // store and user
                TextColumn::make('store.name')
                ->label('Store')
                ->sortable()
                ->searchable(),

                TextColumn::make('user.name')
                ->label('User')
                ->sortable()
                ->searchable(),

                TextColumn::make('fromCurrency.code')
                ->label('From')
                ->sortable()
                ->searchable(),
                TextColumn::make('fromCurrency.code')
                ->label('From')
                ->sortable()
                ->searchable(),

            TextColumn::make('toCurrency.code')
                ->label('To')
                ->sortable()
                ->searchable(),

            TextColumn::make('amount')
                ->label('Amount')
                ->numeric(decimalPlaces: 2, decimalSeparator: '.', thousandsSeparator: ',')
                ->sortable(),

            TextColumn::make('received_amount')
                ->label('Received')
                ->numeric(decimalPlaces: 2, decimalSeparator: '.', thousandsSeparator: ',')
                ->sortable(),

            TextColumn::make('rate')
                ->label('Rate')
                ->numeric(decimalPlaces: 4)
                ->sortable(),

            TextColumn::make('commission')
                ->label('Commission')
                ->numeric(decimalPlaces: 2)
                ->sortable(),

            TextColumn::make('date')
                ->label('Date')
                ->date()
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Created')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('updated_at')
                ->label('Updated')
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
