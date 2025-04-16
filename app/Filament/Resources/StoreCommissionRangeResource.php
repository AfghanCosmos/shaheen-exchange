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
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('💼 Commission Range Details')
                ->description('Define dynamic commission for a store based on transaction range.')
                ->icon('heroicon-o-chart-bar')
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('store_id')
                        ->label('🏪 Store')
                        ->relationship('store', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('currency_id')
                        ->label('💱 Currency')
                        ->relationship('currency', 'code') // Prefer code over name
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\TextInput::make('commission')
                        ->label('💰 Commission Rate')
                        ->suffix('%')
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                ]),

            Forms\Components\Section::make('📊 Range Limits')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('from')
                        ->label('From Amount')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->placeholder('e.g. 100'),

                    Forms\Components\TextInput::make('to')
                        ->label('To Amount')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->placeholder('e.g. 500'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('from')
            ->columns([
                Tables\Columns\TextColumn::make('store.name')
                    ->label('🏪 Store')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('currency.code')
                    ->label('💱 Currency')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('from')
                    ->label('📉 From')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),

                Tables\Columns\TextColumn::make('to')
                    ->label('📈 To')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),

                Tables\Columns\TextColumn::make('commission')
                    ->label('💰 Commission')
                    ->sortable()
                    ->suffix('%')
                    ->color('success')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) ),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('store_id')
                    ->label('Store')
                    ->relationship('store', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('currency_id')
                    ->label('Currency')
                    ->relationship('currency', 'code')
                    ->searchable()
                    ->preload(),
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
