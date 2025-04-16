<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreWalletResource\Pages;
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
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\MorphToSelect;

class StoreWalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationLabel = 'Store Wallet';
    protected static ?string $navigationGroup = 'Store Management';
    protected static ?int $navigationSort = 2;

    /**
     * Form Definition
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormSection::make('Wallet Information')
                    ->schema([

                        Forms\Components\MorphToSelect::make('owner')
                            ->label('🏪 Store Owner') // Emoji in label
                            ->columnSpanFull()
                            ->hiddenOn(WalletsRelationManager::class)
                            ->types([
                                MorphToSelect\Type::make(Store::class)
                                    ->titleAttribute('name'),
                            ]),

                        TextInput::make('balance')
                            ->label('💰 Balance') // Emoji in label
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->placeholder('e.g., 1000.00'),

                        Select::make('currency_id')
                            ->label('💱 Currency') // Emoji in label
                            ->relationship('currency', 'code') // Assuming `code` is the identifier in the `currencies` table
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('status')
                            ->label('📌 Status') // Emoji in label
                            ->options([
                                'active' => '✅ Active',
                                'suspended' => '⏸️ Suspended',
                                'closed' => '❌ Closed',
                            ])
                            ->default('active')
                            ->native(false)
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
            ->query(\App\Models\Wallet::query()->where('owner_type', \App\Models\Store::class))
            ->defaultSort('updated_at', 'desc')
            ->columns([

                // UUID Column
                TextColumn::make('uuid')
                    ->label('🆔 UUID') // Emoji for UUID
                    ->copyable()
                    ->sortable(),

                // Owner Type Column
                TextColumn::make('owner_type')
                    ->label('🏪 Owner Type') // Emoji for Owner Type
                    ->hiddenOn(WalletsRelationManager::class)
                    ->sortable(),

                // Owner Name Column
                TextColumn::make('owner.name')
                    ->label('👤 Owner ID') // Emoji for Owner ID
                    ->hiddenOn(WalletsRelationManager::class)
                    ->sortable(),

                // Balance Column
                TextColumn::make('balance')
                    ->label('💰 Balance') // Emoji for Balance
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 2)), // Format the balance nicely

                // Currency Column
                TextColumn::make('currency.code')
                    ->label('💱 Currency') // Emoji for Currency
                    ->sortable(),

                // Status Column
                BadgeColumn::make('status')
                    ->label('📌 Status') // Emoji for Status
                    ->colors([
                        'success' => 'active',
                        'warning' => 'suspended',
                        'danger' => 'closed',
                    ])
                    ->sortable(),

                // Created At Column
                TextColumn::make('created_at')
                    ->label('📅 Created At') // Emoji for Created At
                    ->dateTime('F j, Y')
                    ->sortable(),

                // Updated At Column
                TextColumn::make('updated_at')
                    ->label('🔄 Updated At') // Emoji for Updated At
                    ->dateTime('F j, Y')
                    ->sortable(),
            ])
            ->filters([
                // Status Filter with Emojis
                SelectFilter::make('status')
                    ->label('📊 Status') // Emoji for Status Filter
                    ->options([
                        'active' => '✅ Active', // Active with a checkmark
                        'suspended' => '⏸️ Suspended', // Suspended with a pause icon
                        'closed' => '❌ Closed', // Closed with a cross icon
                    ]),
            ])
            ->actions([
                // View Action with Emoji
                Tables\Actions\ViewAction::make()
                    ->label('View'), // Emoji for View Action

                // Edit Action with Emoji
                Tables\Actions\EditAction::make()
                    ->label('Edit'), // Emoji for Edit Action

                // Delete Action with Emoji
                Tables\Actions\DeleteAction::make()
                    ->label('Delete'), // Emoji for Delete Action
            ])
            ->bulkActions([
                // Delete Bulk Action with Emoji
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Delete Selected'), // Emoji for Bulk Delete Action
            ]);
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            // Wallet Information Section
            Section::make(__('Wallet Information'))
                ->columns(3)
                ->schema([
                    TextEntry::make('uuid')
                        ->label(__('🆔 Wallet UUID')) // Added emoji for UUID
                        ->copyable(),

                    TextEntry::make('owner_type')
                        ->label(__('🏪 Owner Type')), // Added emoji for Owner Type

                    TextEntry::make('owner.name')
                        ->label(__('👤 Owner Name')), // Added emoji for Owner Name

                    TextEntry::make('currency.code')
                        ->label(__('💱 Currency')), // Added emoji for Currency

                    TextEntry::make('balance')
                        ->label(__('💰 Balance')) // Added emoji for Balance
                        ->money(fn ($record) => $record->currency?->code ?? 'USD'),
                ]),

            // Metadata Section
            Section::make(__('📋 Metadata')) // Added emoji for Metadata
                ->columns(2)
                ->collapsed()
                ->schema([
                    TextEntry::make('created_at')
                        ->label(__('📅 Created At')) // Added emoji for Created At
                        ->dateTime(),

                    TextEntry::make('updated_at')
                        ->label(__('🔄 Updated At')) // Added emoji for Updated At
                        ->dateTime(),
                ]),

            // Latest Hawla Transactions Section
            Section::make(__('💸 Latest Hawla Transactions')) // Added emoji for Hawla Transactions
                ->columns(1)
                ->collapsed()
                ->schema([
                    RepeatableEntry::make('hawlasAsSender')
                        ->label(__('🚚 Sent Hawlas')) // Added emoji for Sent Hawlas
                        ->hidden(fn ($record) => $record->owner_type !== \App\Models\Store::class)
                        ->schema([
                            TextEntry::make('uuid')->label(__('🆔 Hawla Code')), // Added emoji for Hawla Code
                            TextEntry::make('hawlaType.name')->label(__('📜 Type')), // Added emoji for Type
                            TextEntry::make('receiverStore.name')->label(__('🏪 Receiver Store')), // Added emoji for Receiver Store
                            TextEntry::make('givenCurrency.code')->label(__('💱 Currency')), // Added emoji for Currency
                            TextEntry::make('given_amount')
                                ->label(__('💵 Given Amount')) // Added emoji for Given Amount
                                ->money(fn ($record) => $record->givenCurrency?->code ?? 'USD'),
                            TextEntry::make('receiving_amount')
                                ->label(__('💵 Receiving Amount')) // Added emoji for Receiving Amount
                                ->money(fn ($record) => $record->receivingCurrency?->code ?? 'USD'),
                            TextEntry::make('created_at')->label(__('📅 Date'))->dateTime(),
                            TextEntry::make('status')->badge()->label(__('📍 Status')), // Added emoji for Status
                        ])
                        ->columns(4),

                    RepeatableEntry::make('hawlasAsReceiver')
                        ->label(__('📩 Received Hawlas')) // Added emoji for Received Hawlas
                        ->hidden(fn ($record) => $record->owner_type !== \App\Models\Store::class)
                        ->schema([
                            TextEntry::make('uuid')->label(__('🆔 Hawla Code')), // Added emoji for Hawla Code
                            TextEntry::make('hawlaType.name')->label(__('📜 Type')), // Added emoji for Type
                            TextEntry::make('senderStore.name')->label(__('🏪 Sender Store')), // Added emoji for Sender Store
                            TextEntry::make('receivingCurrency.code')->label(__('💱 Currency')), // Added emoji for Currency
                            TextEntry::make('given_amount')
                                ->label(__('💵 Given Amount')) // Added emoji for Given Amount
                                ->money(fn ($record) => $record->givenCurrency?->code ?? 'USD'),
                            TextEntry::make('receiving_amount')
                                ->label(__('💵 Receiving Amount')) // Added emoji for Receiving Amount
                                ->money(fn ($record) => $record->receivingCurrency?->code ?? 'USD'),
                            TextEntry::make('created_at')->label(__('📅 Date'))->dateTime(),
                            TextEntry::make('status')->badge()->label(__('📍 Status')), // Added emoji for Status
                        ])
                        ->columns(4),
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
