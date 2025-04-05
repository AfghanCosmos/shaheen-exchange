<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreCommissionRangeResource\Pages;
use App\Filament\Resources\StoreCommissionRangeResource\RelationManagers;
use App\Models\StoreCommissionRange;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StoreResource\RelationManagers\StoreCommissionRangesRelationManager;



class StoreCommissionRangeResource extends Resource
{
    protected static ?string $model = StoreCommissionRange::class;

    protected static ?string $navigationGroup = "Store Management";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('store_id')
                    ->label('Store')
                    ->relationship('store', 'name')
                    ->required()
                    ->columnSpan(2),
                Forms\Components\Select::make('currency_id')
                    ->label('Currency ID')
                    ->relationship('currency', 'name')
                    ->required()
                    ->columnSpan(2),
                Forms\Components\TextInput::make('from')
                    ->label('From Value')
                    ->required()
                    ->maxLength(255)
                    ->hint('Specify the starting value of the range.'),
                Forms\Components\TextInput::make('to')
                    ->label('To Value')
                    ->required()
                    ->maxLength(255)
                    ->hint('Specify the ending value of the range.'),
                Forms\Components\TextInput::make('commission')
                    ->label('Commission')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->helperText('Enter the commission amount in USD.'),
            ])
            ->columns(2);

    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('store.name')
                ->label('Store'),
            Tables\Columns\TextColumn::make('currency.name')
                ->label('Currency'),
            Tables\Columns\TextColumn::make('from')
                ->label('From Value'),
            Tables\Columns\TextColumn::make('to')
                ->label('To Value'),
                Tables\Columns\TextColumn::make('commission')
                 
                    ->label('Commission'),
            ])
            ->filters([
                Tables\Filters\Filter::make('commission')
                    ->query(fn (Builder $query): Builder => $query)
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
            'index' => Pages\ListStoreCommissionRanges::route('/'),
            'create' => Pages\CreateStoreCommissionRange::route('/create'),
            'edit' => Pages\EditStoreCommissionRange::route('/{record}/edit'),
        ];
    }
}
