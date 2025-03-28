<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreCommissionResource\Pages;
use App\Models\StoreCommission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StoreResource\RelationManagers\StoreCommissionsRelationManager;

class StoreCommissionResource extends Resource
{
    protected static ?string $model = StoreCommission::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Store Commission Details')
                    ->schema([
                        Forms\Components\Select::make('store_id')
                            ->label('Store')
                            ->relationship('store', 'name')
                            ->preload()
                            ->searchable()
                            ->native(false)
                       ->hiddenOn(StoreCommissionsRelationManager::class)
                            ->columnSpanFull()
                            ->required()
                            ->createOptionForm(fn(Form $form) => StoreResource::form($form))
                            ->placeholder('Select a store...'),

                        Forms\Components\Select::make('commission_type_id')
                            ->label('Commission Type')
                            ->relationship('commissionType', 'name')
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->required()
                            ->createOptionForm(fn(Form $form) => CommissionTypeResource::form($form))
                            ->placeholder('Select a type...'),

                        Forms\Components\Select::make('currency_id')
                            ->label('Currency')
                            ->relationship('currency', 'code')
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->required()
                            ->createOptionForm(fn(Form $form) => CurrencyResource::form($form))
                            ->placeholder('Select a currency...'),

                        Forms\Components\TextInput::make('commission')
                            ->label('Commission Rate')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->placeholder('Enter commission (e.g. 5, 10.5)')
                            ->suffix('%'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('store.name')
                    ->label('Store')
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn ($record) => $record->store?->name),

                Tables\Columns\BadgeColumn::make('commissionType.name')
                    ->label('Type')
                    ->searchable()
                    ->sortable()
                    ->colors([
                        'info' => 'Percentage',
                        'success' => 'Fixed',
                    ]),

                Tables\Columns\BadgeColumn::make('currency.code')
                    ->label('Currency')
                    ->searchable()
                    ->sortable()
                    ->colors([
                        'primary' => 'USD',
                        'warning' => 'EUR',
                        'danger' => 'AFN',
                    ]),

                Tables\Columns\TextColumn::make('commission')
                    ->label('Commission')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . '%'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->sortable()
                    ->dateTime('d M Y, H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStoreCommissions::route('/'),
            'create' => Pages\CreateStoreCommission::route('/create'),
            'edit' => Pages\EditStoreCommission::route('/{record}/edit'),
        ];
    }
}
