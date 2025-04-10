<?php

namespace App\Filament\Store\Resources;

use App\Filament\Store\Resources\ExchangeRateResource\Pages;
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
                Forms\Components\Select::make('store_id')
                    ->label('Store')
                    ->relationship('store', 'name')
                    ->preload()
                    ->native(false)
                    ->required(),

                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->preload()
                    ->native(false)
                    ->required(),
                Forms\Components\Select::make('from_currency_id')
                    ->label('From Currency')
                    ->relationship('fromCurrency', 'name')
                    ->preload()
                    ->native(false)
                    ->required()
                    ->reactive() // Make this field reactive for dynamic filtering
                    ->afterStateUpdated(fn (callable $set) => $set('to_currency_id', null)), // Reset 'to_currency_id' when 'from_currency_id' changes

                Forms\Components\Select::make('to_currency_id')
                    ->label('To Currency')
                    ->options(fn (callable $get) =>
                        \App\Models\Currency::query()
                            ->where('id', '!=', $get('from_currency_id')) // Exclude selected 'from_currency_id'
                            ->pluck('name', 'id')
                    )
                    ->preload()
                    ->native(false)
                    ->required(),

                Forms\Components\TextInput::make('buy_rate')
                    ->label('Buy Rate')
                    ->required()
                    ->numeric()
                    ->placeholder('Enter buy rate'),

                Forms\Components\TextInput::make('sell_rate')
                    ->label('Sell Rate')
                    ->required()
                    ->numeric()
                    ->placeholder('Enter sell rate'),
                Forms\Components\DatePicker::make('date')
                    ->label('Exchange Date')
                    ->required()
                    ->default(now())
                    ->displayFormat('Y-m-d'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('fromCurrency.name')
                    ->label('From Currency')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('toCurrency.name')
                    ->label('To Currency')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buy_rate')
                    ->numeric()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('sell_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
