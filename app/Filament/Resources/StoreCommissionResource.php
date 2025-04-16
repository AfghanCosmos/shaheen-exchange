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
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = "Store Management";

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('💼 Store Commission Setup')
                ->icon('heroicon-o-banknotes')
                ->description('Configure a fixed or percentage-based commission for a store.')
                ->columns(3)
                ->schema([

                    Forms\Components\Select::make('store_id')
                        ->label('🏪 Store')
                        ->relationship('store', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->native(false)
                        ->hiddenOn(StoreCommissionsRelationManager::class)
                        ->columnSpanFull()
                        ->placeholder('Select a store...')
                        ->createOptionForm(fn (Form $form) => StoreResource::form($form)),

                    Forms\Components\Select::make('commission_type_id')
                        ->label('📊 Commission Type')
                        ->relationship('commissionType', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required()
                        ->placeholder('Select a type...')
                        ->createOptionForm(fn (Form $form) => CommissionTypeResource::form($form)),

                    Forms\Components\Select::make('currency_id')
                        ->label('💱 Currency')
                        ->relationship('currency', 'code')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required()
                        ->placeholder('Select a currency...')
                        ->createOptionForm(fn (Form $form) => CurrencyResource::form($form)),

                    Forms\Components\TextInput::make('commission')
                        ->label('💰 Commission Value')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->placeholder('e.g., 5 or 12.5'),

                    Forms\Components\Toggle::make('is_fix')
                        ->label('📌 Fixed Amount')
                        ->inline(false)
                        ->default(false)
                        ->helperText('Enable if the commission is a fixed value instead of a percentage.'),
                ]),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('store.name')
                    ->label('🏪 Store')
                    ->tooltip(fn ($record) => $record->store?->name)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('commissionType.name')
                    ->label('📊 Type')
                    ->sortable()
                    ->colors([
                        'info' => 'Percentage',
                        'success' => 'Fixed',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\BadgeColumn::make('currency.code')
                    ->label('💱 Currency')
                    ->sortable()
                    ->colors([
                        'primary' => 'USD',
                        'warning' => 'EUR',
                        'danger' => 'AFN',
                        'gray' => null,
                    ])
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

                Tables\Columns\BooleanColumn::make('is_fix')
                    ->label('📌 Fixed?')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('commission')
                    ->label('💰 Commission')
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) =>
                        number_format($state, 2) . ($record->is_fix ? '' : ' %')
                    ),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('📅 Created')
                    ->sortable()
                    ->dateTime('d M Y, H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
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
