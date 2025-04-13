<?php

namespace App\Filament\Store\Resources;

use App\Filament\Store\Resources\StoreCommissionRangeResource\Pages;
use App\Filament\Store\Resources\StoreCommissionRangeResource\RelationManagers;
use App\Models\StoreCommissionRange;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;




class StoreCommissionRangeResource extends Resource
{
    protected static ?string $model = StoreCommissionRange::class;

    protected static ?string $navigationGroup = "Store Management";
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('💼 Commission Range Details')
                ->description('Define the commission range per store and currency combination.')
                ->icon('heroicon-o-banknotes')
                ->columns(1)
                ->schema([

                    // Store and Currency Selection
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('store_id')
                                ->label('🏬 Store')
                                ->relationship('store', 'name')
                                ->searchable()
                                ->required()
                                ->preload()
                                ->helperText('Select the store to which this commission range applies.'),

                            Forms\Components\Select::make('currency_id')
                                ->label('💱 Currency')
                                ->relationship('currency', 'name')
                                ->searchable()
                                ->required()
                                ->preload()
                                ->helperText('The currency applicable to this range.'),
                        ]),

                    // Range and Commission Input
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('from')
                                ->label('🔢 From Amount')
                                ->numeric()
                                ->prefix('Min:')
                                ->required()
                                ->placeholder('e.g. 100')
                                ->helperText('Minimum amount for this commission range.'),

                            Forms\Components\TextInput::make('to')
                                ->label('🔢 To Amount')
                                ->numeric()
                                ->prefix('Max:')
                                ->required()
                                ->placeholder('e.g. 500')
                                ->helperText('Maximum amount for this commission range.'),

                            Forms\Components\TextInput::make('commission')
                                ->label('💼 Commission (%)')
                                ->numeric()
                                ->suffix('%')
                                ->required()
                                ->placeholder('e.g. 2.5')
                                ->helperText('Percentage to apply within this range.'),
                        ]),
                ]),
            ]);

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
                Tables\Actions\ViewAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Section::make('💼 Commission Range Details')
                ->description('Review the commission configuration for this store and currency.')
                ->icon('heroicon-o-banknotes')
                ->columns(3)
                ->schema([
                    TextEntry::make('store.name')
                        ->label('🏬 Store')
                        ->badge()
                        ->color('success'),

                    TextEntry::make('currency.name')
                        ->label('💱 Currency')
                        ->badge()
                        ->color('info'),

                    TextEntry::make('commission')
                        ->label('💼 Commission')
                        ->suffix('%')
                        ->color(fn ($state) => $state >= 5 ? 'danger' : 'gray')
                        ->badge(),

                    TextEntry::make('from')
                        ->label('🔢 From Amount')
                        ->formatStateUsing(fn ($state) => number_format($state, 2)),

                    TextEntry::make('to')
                        ->label('🔢 To Amount')
                        ->formatStateUsing(fn ($state) => number_format($state, 2)),

                    TextEntry::make('created_at')
                        ->label('🕒 Created At')
                        ->dateTime()
                        ->icon('heroicon-o-calendar'),

                    TextEntry::make('updated_at')
                        ->label('🔄 Last Updated')
                        ->dateTime()
                        ->icon('heroicon-o-arrow-path'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('store', function (Builder $query) {
                $query->where('user_id', Auth::id());
            });
    }
}
