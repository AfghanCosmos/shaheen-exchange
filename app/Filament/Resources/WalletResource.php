<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletResource\Pages;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Store;
use App\Filament\Resources\StoreResource\RelationManagers\WalletsRelationManager;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\MorphToSelect;
class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationGroup = 'Finance Management';

    /**
     * Form Definition
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Wallet Information')
                    ->schema([

                       Forms\Components\MorphToSelect::make('owner')
                       ->columnSpanFull()
                       ->hiddenOn(WalletsRelationManager::class)
                        ->types([
                            MorphToSelect\Type::make(User::class)
                                ->titleAttribute('name'),
                            MorphToSelect\Type::make(Store::class)
                                ->titleAttribute('name'),
                         ]),

                        TextInput::make('balance')
                            ->label('Balance')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Select::make('currency_id')
                            ->label('Currency')
                            ->relationship('currency', 'code') // Assuming `code` is the identifier in the `currencies` table
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'suspended' => 'Suspended',
                                'closed' => 'Closed',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(3),
            ]);
    }

    /**
     * Table Definition
     */
    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->copyable()
                    ->sortable(),

                TextColumn::make('owner_type')
                    ->label('Owner Type')
                    ->hiddenOn(WalletsRelationManager::class)

                    ->sortable(),

                TextColumn::make('owner.name')
                    ->label('Owner ID')
                       ->hiddenOn(WalletsRelationManager::class)
                    ->sortable(),

                TextColumn::make('balance')
                    ->label('Balance')
                    ->sortable(),

                TextColumn::make('currency.code')
                    ->label('Currency')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'suspended',
                        'danger' => 'closed',
                    ])
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('F j, Y')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('F j, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'closed' => 'Closed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /**
     * Relations
     */
    public static function getRelations(): array
    {
        return [

        ];
    }

    /**
     * Pages
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWallets::route('/'),
            'create' => Pages\CreateWallet::route('/create'),
            'edit' => Pages\EditWallet::route('/{record}/edit'),
        ];
    }
}
