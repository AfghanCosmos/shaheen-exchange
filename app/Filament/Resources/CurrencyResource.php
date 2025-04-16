<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Filament\Resources\CurrencyResource\RelationManagers;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    // protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Exchange Management';
    protected static ?int $navigationSort = 4;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // Currency Information Section with Icon
                Forms\Components\Section::make('💱 Currency Information')
                    ->description('Enter the details of the currency below')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->placeholder('AF')
                            ->maxLength(3)
                            ->label('🌍 Currency Code')  // Added emoji for Currency Code
                            ->helperText('The 3-letter code for the currency (e.g., USD, EUR, AF).'),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->placeholder('Afghani')
                            ->maxLength(255)
                            ->label('💰 Currency Name')  // Added emoji for Currency Name
                            ->helperText('The full name of the currency (e.g., Afghani, Dollar).'),
                    ])
                    ->columns(1),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
            ->columns([
                 // Currency Code Column with Emoji
                Tables\Columns\TextColumn::make('code')
                    ->label('🌍 Currency Code')  // Added emoji for Currency Code
                    ->sortable()
                    ->searchable()
                    ->tooltip('Currency Code'),

                // Currency Name Column with Emoji
                Tables\Columns\TextColumn::make('name')
                    ->label('💰 Currency Name')  // Added emoji for Currency Name
                    ->sortable()
                    ->searchable()
                    ->tooltip('Currency Name'),

                // Created At Column with Emoji and Tooltip
                Tables\Columns\TextColumn::make('created_at')
                    ->label('📅 Created At')  // Added emoji for Created At
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('Date the currency was created'),

                // Updated At Column with Emoji and Tooltip
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('🔄 Updated At')  // Added emoji for Updated At
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('Date the currency was last updated'),
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
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
