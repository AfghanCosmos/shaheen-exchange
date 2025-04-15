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
use Filament\Forms\Components\Section as FormSection;

use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\MorphToSelect;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    // protected static ?string $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationGroup = 'Finance Management';
    protected static ?string $navigationLabel= 'Customer Wallets';



    /**
     * Form Definition
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            FormSection::make('💼 Wallet Information')
                ->columns(3)
                ->schema([
                    MorphToSelect::make('owner')
                        ->label('👤 Wallet Owner')
                        ->types([
                            MorphToSelect\Type::make(User::class)
                                ->titleAttribute('name')
                                ->label('Customer'),
                        ])
                        ->required()
                        ->hiddenOn(\App\Filament\Resources\StoreResource\RelationManagers\WalletsRelationManager::class)
                        ->columnSpanFull(),

                    TextInput::make('balance')
                        ->label('💰 Balance')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    Select::make('currency_id')
                        ->label('💱 Currency')
                        ->relationship('currency', 'code')
                        ->preload()
                        ->searchable()
                        ->required(),

                    Select::make('status')
                        ->label('📌 Status')
                        ->options([
                            'active' => 'Active',
                            'suspended' => 'Suspended',
                            'closed' => 'Closed',
                        ])
                        ->default('active')
                        ->required(),
                ]),
        ]);
    }


    /**
     * Table Definition
     */
    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Wallet::query()
                    ->where('owner_type', User::class)
            )
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('uuid')
                    ->label('🆔 UUID')
                    ->copyable()
                    ->sortable()
                    ->tooltip('Click to copy'),

                TextColumn::make('owner.name')
                    ->label('👤 Customer')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-user'),

                TextColumn::make('balance')
                    ->label('💰 Balance')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),

                TextColumn::make('currency.code')
                    ->label('💱 Currency')
                    ->sortable()
                    ->icon('heroicon-o-currency-dollar'),

                BadgeColumn::make('status')
                    ->label('📌 Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'suspended',
                        'danger' => 'closed',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('📅 Created At')
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar-days'),

                TextColumn::make('updated_at')
                    ->label('🔄 Updated At')
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->icon('heroicon-o-clock'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('📌 Filter by Status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'closed' => 'Closed',
                    ])
                    ->indicator('Status'),
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


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('💼 Wallet Information')
                ->columns(3)
                ->schema([
                    TextEntry::make('uuid')
                        ->label('🆔 Wallet UUID')
                        ->copyable()
                        ->tooltip('Click to copy UUID')
                        ->icon('heroicon-o-key'),

                    TextEntry::make('owner.name')
                        ->label('👤 Customer')
                        ->icon('heroicon-o-user'),

                    TextEntry::make('currency.code')
                        ->label('💱 Currency')
                        ->icon('heroicon-o-currency-dollar')
                        ->badge()
                        ->formatStateUsing(fn ($state) => strtoupper($state)),

                    TextEntry::make('balance')
                        ->label('💰 Balance')
                        ->icon('heroicon-o-banknotes')
                        ->money(fn ($record) => $record->currency?->code ?? 'USD'),

                    TextEntry::make('status')
                        ->label('📌 Status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'active' => 'success',
                            'suspended' => 'warning',
                            'closed' => 'danger',
                            default => 'gray',
                        }),
                ]),

            Section::make('📅 Metadata')
                ->columns(2)
                ->collapsed()
                ->schema([
                    TextEntry::make('created_at')
                        ->label('📅 Created At')
                        ->icon('heroicon-o-calendar-days')
                        ->dateTime('F j, Y h:i A'),

                    TextEntry::make('updated_at')
                        ->label('🔄 Updated At')
                        ->icon('heroicon-o-clock')
                        ->dateTime('F j, Y h:i A'),
                ]),
        ]);
    }



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
            'view' => Pages\ViewWallet::route('/{record}'),
        ];
    }
}
