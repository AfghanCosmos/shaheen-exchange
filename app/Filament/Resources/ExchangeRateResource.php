<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExchangeRateResource\Pages;
use App\Models\ExchangeRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExchangeRateResource extends Resource
{
    protected static ?string $model = ExchangeRate::class;

    protected static ?string $navigationGroup = 'Exchange Management';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // From Currency Select Field with Icon and Helper Text
                Forms\Components\Section::make('💱 Currency Exchange')
                    ->description('Select the currencies and enter exchange rates.')
                    ->schema([
                        Forms\Components\Select::make('from_currency_id')
                            ->label('From Currency') // Added Currency Icon
                            ->relationship('fromCurrency', 'name')
                            ->preload()
                            ->native(false)
                            ->required()
                            ->reactive() // Make this field reactive for dynamic filtering
                            ->helperText('Select the source currency for the exchange rate.')
                            ->afterStateUpdated(fn (callable $set) => $set('to_currency_id', null)) // Reset 'to_currency_id' when 'from_currency_id' changes
                            ->placeholder('Select the From Currency'),

                        Forms\Components\Select::make('to_currency_id')
                            ->label('To Currency') // Added Currency Icon
                            ->options(fn (callable $get) =>
                                \App\Models\Currency::query()
                                    ->where('id', '!=', $get('from_currency_id')) // Exclude selected 'from_currency_id'
                                    ->pluck('name', 'id')
                            )
                            ->preload()
                            ->native(false)
                            ->required()
                            ->helperText('Select the target currency for the exchange rate.')
                            ->placeholder('Select the To Currency'),
                    ])
                    ->columns(2), // Two columns for currency selection to improve layout

                // Buy Rate Input Field with Helper Text and Icon
                Forms\Components\TextInput::make('buy_rate')
                    ->label('Buy Rate') // Label with currency icon
                    ->helperText('Enter the exchange rate at which you can buy the currency.')
                    ->required()
                    ->numeric()
                    ->placeholder('Enter buy rate')
                    ->prefix('💵') // Adding prefix to show currency // Icon for buy rate (downward arrow)

                    ->maxLength(10) // Limit to 10 digits for a clean UI
                    ->minValue(0.01), // Ensure it's a positive value

                // Sell Rate Input Field with Helper Text and Icon
                Forms\Components\TextInput::make('sell_rate')
                    ->label('Sell Rate') // Label with currency icon
                    ->helperText('Enter the exchange rate at which you can sell the currency.')
                    ->required()
                    ->numeric()
                    ->placeholder('Enter sell rate')
                    ->prefix('💵') // Adding prefix to show currency
                    ->maxLength(10) // Limit to 10 digits for a clean UI
                    ->minValue(0.01), // Ensure it's a positive value

                // Date Picker Field with Icon and Default Date
                Forms\Components\DatePicker::make('date')
                    ->label('📅 Exchange Date') // Added calendar icon
                    ->required()
                    ->columnSpanFull()
                    ->default(now()) // Default to today's date
                    ->displayFormat('Y-m-d') // Display format for the date
                    ->helperText('Select the date for the exchange rates.')
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
            ->columns([
                 // From Currency Column with Icon
                Tables\Columns\TextColumn::make('fromCurrency.name')
                    ->label('🌍 From Currency')  // Emoji for From Currency
                    ->searchable()
                    ->sortable()
                    ->tooltip('The source currency for the exchange rate'),

                // To Currency Column with Icon
                Tables\Columns\TextColumn::make('toCurrency.name')
                    ->label('💱 To Currency')  // Emoji for To Currency
                    ->searchable()
                    ->sortable()
                    ->tooltip('The target currency for the exchange rate'),

                // Buy Rate Column with Icon and Tooltip
                Tables\Columns\TextColumn::make('buy_rate')
                    ->label('💵 Buy Rate')  // Emoji for Buy Rate
                    ->numeric()
                    ->sortable()
                    ->tooltip('The exchange rate for buying the currency'),

                // Sell Rate Column with Icon and Tooltip
                Tables\Columns\TextColumn::make('sell_rate')
                    ->label('💰 Sell Rate')  // Emoji for Sell Rate
                    ->numeric()
                    ->sortable()
                    ->tooltip('The exchange rate for selling the currency'),

                // Date Column with Icon and Tooltip
                Tables\Columns\TextColumn::make('date')
                    ->label('📅 Exchange Date')  // Emoji for Date
                    ->date()
                    ->sortable()
                    ->tooltip('The date when the exchange rate was applied'),

                // Created At Column with Icon and Toggle
                Tables\Columns\TextColumn::make('created_at')
                    ->label('📅 Created At')  // Emoji for Created At
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('The date when the record was created'),

                // Updated At Column with Icon and Toggle
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('🔄 Updated At')  // Emoji for Updated At
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('The date when the record was last updated'),

            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListExchangeRates::route('/'),
            'create' => Pages\CreateExchangeRate::route('/create'),
            'edit' => Pages\EditExchangeRate::route('/{record}/edit'),
        ];
    }
}
