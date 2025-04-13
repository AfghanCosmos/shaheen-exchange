<?php

namespace App\Filament\Store\Resources;

use App\Filament\Resources\CurrencyResource;
use App\Filament\Store\Resources\StoreCommissionResource\Pages;
use App\Models\StoreCommission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StoreResource\RelationManagers\StoreCommissionsRelationManager;
use Illuminate\Support\Facades\Auth;

class StoreCommissionResource extends Resource
{
    protected static ?string $model = StoreCommission::class;
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = "Store Management";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Store Commission Details')
                    ->schema([

                        Forms\Components\Select::make('commission_type_id')
                            ->label('Commission Type')
                            ->relationship('commissionType', 'name')
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->required()
                            ->createOptionForm(fn(Form $form) => StoreCommissionResource::form($form))
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
                            ,

                            Forms\Components\Toggle::make('is_fix')
                                ->label('Is Fix')
                                ->default(false),


                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            /** 🏬 Store Name with Tooltip */
            Tables\Columns\TextColumn::make('store.name')
                ->label('🏬 Store')
                ->searchable()
                ->sortable()
                ->tooltip(fn ($record) => $record->store?->name)
                ->badge()
                ->color('gray'),

            /** 🏷️ Commission Type */
            Tables\Columns\BadgeColumn::make('commissionType.name')
                ->label('📊 Type')
                ->searchable()
                ->sortable()
                ->colors([
                    'info' => 'Percentage',
                    'success' => 'Fixed',
                ])
                ->formatStateUsing(fn ($state) => ucfirst($state)),

            /** 💱 Currency Code */
            Tables\Columns\BadgeColumn::make('currency.code')
                ->label('💱 Currency')
                ->searchable()
                ->sortable()
                ->colors([
                    'primary' => 'USD',
                    'warning' => 'EUR',
                    'danger' => 'AFN',
                    'gray' => null, // fallback
                ]),

            /** ✅ Is Fixed? */
            Tables\Columns\BooleanColumn::make('is_fix')
                ->label('🔒 Is Fixed')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->alignCenter(),

            /** 💰 Commission Value */
            Tables\Columns\TextColumn::make('commission')
                ->label('💰 Commission')
                ->sortable()
                ->formatStateUsing(fn ($state) => number_format($state, 2))
                ->suffix(fn ($record) => $record->commissionType?->name === 'Percentage' ? '%' : null)
                ->color('success'),

            /** 🕒 Created At */
            Tables\Columns\TextColumn::make('created_at')
                ->label('📅 Created At')
                ->dateTime('d M Y, H:i')
                ->sortable()
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('store', function (Builder $query) {
                $query->where('user_id', Auth::id());
            });
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
