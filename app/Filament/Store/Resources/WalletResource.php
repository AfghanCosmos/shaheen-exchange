<?php

namespace App\Filament\Store\Resources;

use App\Filament\Store\Resources\WalletResource\Pages;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Store;
use App\Filament\Store\Resources\StoreResource\RelationManagers\WalletsRelationManager;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section as FormSection;

use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\MorphToSelect;
use Illuminate\Support\Facades\Auth;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    // protected static ?string $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationGroup = 'Finance Management';
    protected static ?string $navigationLabel = 'Customer Wallets';



     /**
     * Form Definition
     */
     public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormSection::make('Wallet Information')
                    ->schema([
                        Select::make('owner_id')
                        ->label('👤 User')
                        ->options(function () {
                            $storeId = Auth::user()?->store->id;

                            return \App\Models\User::where('store_id', $storeId)
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpanFull()
                        ->placeholder('Select user'),

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

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
        ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('user', fn ($q) => $q->where('store_id', auth()->user()?->store?->id)))

            ->columns([
                /** 🆔 UUID */
                TextColumn::make('uuid')
                    ->label('🆔 UUID')
                    ->copyable()
                    ->icon('heroicon-o-key')
                    ->tooltip('Click to copy UUID')
                    ->sortable(),

                /** 🧩 Owner Type */
                TextColumn::make('owner_type')
                    ->label('👥 Owner Type')
                    ->sortable()
                    ->icon('heroicon-o-user-group')
                    ->hiddenOn(WalletsRelationManager::class),

                /** 🔗 Owner Name */
                TextColumn::make('owner.name')
                    ->label('👤 Owner')
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->hiddenOn(WalletsRelationManager::class),

                /** 💰 Balance */
                TextColumn::make('balance')
                    ->label('💰 Balance')
                    ->sortable()
                    ->icon('heroicon-o-banknotes')
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),

                /** 💱 Currency */
                TextColumn::make('currency.code')
                    ->label('💱 Currency')
                    ->sortable()
                    ->icon('heroicon-o-currency-dollar')
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

                /** 📌 Status */
                BadgeColumn::make('status')
                    ->label('📌 Status')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->colors([
                        'success' => 'active',
                        'warning' => 'suspended',
                        'danger' => 'closed',
                    ]),

                /** 📅 Created At */
                TextColumn::make('created_at')
                    ->label('📅 Created')
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar-days'),

                /** 🔄 Updated At */
                TextColumn::make('updated_at')
                    ->label('🔄 Updated')
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
            ]);
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            /** 🏦 Wallet Information */
            Section::make('💼 Wallet Information')
                ->columns(3)
                ->schema([
                    TextEntry::make('uuid')
                        ->label('🆔 Wallet UUID')
                        ->copyable()
                        ->tooltip('Click to copy UUID')
                        ->icon('heroicon-o-key'),

                    TextEntry::make('owner.name')
                        ->label('👤 Owner Name')
                        ->icon('heroicon-o-user'),

                    TextEntry::make('currency.code')
                        ->label('💱 Currency')
                        ->icon('heroicon-o-currency-dollar')
                        ->badge()
                        ->color('gray')
                        ->formatStateUsing(fn ($state) => strtoupper($state)),

                    TextEntry::make('balance')
                        ->label('💰 Balance')
                        ->icon('heroicon-o-banknotes')
                        ->money(fn ($record) => $record->currency?->code ?? 'USD'),
                ]),

            /** 🕒 Metadata */
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

            /** 🔄 Latest Hawla Transactions */
            Section::make('🔄 Latest Hawla Transactions')
                ->columns(1)
                ->collapsed()
                ->schema([
                    RepeatableEntry::make('hawlasAsSender')
                        ->label('📤 Sent Hawlas')
                        ->hidden(fn ($record) => $record->owner_type !== \App\Models\Store::class)
                        ->columns(3)
                        ->schema([
                            TextEntry::make('uuid')->label('🧾 Hawla Code')->copyable(),
                            TextEntry::make('hawlaType.name')->label('🔖 Type')->badge()->color('info'),
                            TextEntry::make('receiverStore.name')->label('🏪 Receiver Store'),
                            TextEntry::make('givenCurrency.code')->label('💱 Currency')->badge()->color('gray'),
                            TextEntry::make('created_at')->label('📅 Date')->dateTime('F j, Y'),
                        ]),

                    RepeatableEntry::make('hawlasAsReceiver')
                        ->label('📥 Received Hawlas')
                        ->hidden(fn ($record) => $record->owner_type !== \App\Models\Store::class)
                        ->columns(3)
                        ->schema([
                            TextEntry::make('uuid')->label('🧾 Hawla Code')->copyable(),
                            TextEntry::make('hawlaType.name')->label('🔖 Type')->badge()->color('info'),
                            TextEntry::make('senderStore.name')->label('🏪 Sender Store'),
                            TextEntry::make('receivingCurrency.code')->label('💱 Currency')->badge()->color('gray'),
                            TextEntry::make('created_at')->label('📅 Date')->dateTime('F j, Y'),
                        ]),
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
            'view' => Pages\ViewWallet::route('/{record}'),
        ];
    }


}
