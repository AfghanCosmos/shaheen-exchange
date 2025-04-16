<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Filament\Resources\StoreResource\RelationManagers\ReceiverHawlasRelationManager;
use App\Filament\Resources\StoreResource\RelationManagers\SenderHawlasRelationManager;
use App\Filament\Resources\StoreResource\RelationManagers\WalletsRelationManager;
use App\Filament\Resources\StoreResource\RelationManagers\StoreCommissionRangesRelationManager;
use App\Filament\Resources\StoreResource\RelationManagers\StoreCommissionsRelationManager;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;


use App\Models\Hawla;
use App\Models\Store;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Card;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    protected static ?string $navigationGroup = 'Store Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Store Information with Emoji
                Forms\Components\Section::make('🛍️ Store Information')
                    ->schema([
                        Select::make('user_id')
                            ->label('👤 User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Select a user'),

                        TextInput::make('name')
                            ->label('🏪 Store Name')
                            ->required()
                            ->placeholder('Enter store name')
                            ->maxLength(255),

                        Select::make('country_id')
                            ->label('🌍 Country')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->live()
                            ->preload()
                            ->required()
                            ->placeholder('Select a country'),

                        Select::make('province_id')
                            ->label('📍 Province')
                            ->options(function (callable $get) {
                                if ($country = $get('country_id')) {
                                    return \App\Models\Province::where('country_id', $country)
                                        ->pluck('name', 'id')
                                        ->toArray();
                                }
                                return [];
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Select a province'),
                    ])
                    ->columns(2),

                // Contact Information Section with Emojis
                Forms\Components\Section::make('📞 Contact Information')
                    ->schema([
                        TextArea::make('address')
                            ->label('🏠 Address')
                            ->required()
                            ->placeholder('e.g., 123 Main St, City, Province')
                            ->columnSpanFull(),

                        TextInput::make('latitude')
                            ->label('📍 Latitude')
                            ->numeric()
                            ->nullable()
                            ->placeholder('e.g., 34.0522'),

                        TextInput::make('longitude')
                            ->label('📍 Longitude')
                            ->numeric()
                            ->nullable()
                            ->placeholder('e.g., -118.2437'),
                    ])
                    ->columns(2),

                // Store Timings Section with Emojis
                Forms\Components\Section::make('⏰ Store Timings')
                    ->schema([
                        TimePicker::make('open_time')
                            ->label('🕖 Opening Time')
                            ->placeholder('e.g., 08:00 AM'),

                        TimePicker::make('close_time')
                            ->label('🕗 Closing Time')
                            ->placeholder('e.g., 08:00 PM'),

                        Toggle::make('is_closed')
                            ->label('🚫 Is Closed?')
                            ->required(),
                    ])
                    ->columns(2),

                // Status Information Section with Emojis
                Forms\Components\Section::make('🛑 Status Information')
                    ->schema([
                        Select::make('status')
                            ->label('⚙️ Status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'pending' => 'Pending',
                            ])
                            ->native(false)
                            ->required()
                            ->placeholder('Select store status'),
                    ])
                    ->columns(1),

                // Contacts Repeater with Emojis
                Repeater::make('Store Contact')
                    ->relationship('storeContacts')
                    ->columnSpanFull()
                    ->schema([
                        Section::make('📱 Contact Information')
                            ->schema([
                                Select::make('type')
                                    ->label('📡 Contact Type')
                                    ->options([
                                        'phone' => 'Phone',
                                        'email' => 'Email',
                                        'whatsapp' => 'WhatsApp',
                                        'fax' => 'Fax',
                                        'telegram' => 'Telegram',
                                        'skype' => 'Skype',
                                        'messenger' => 'Messenger',
                                        'signal' => 'Signal',
                                        'wechat' => 'WeChat',
                                        'other' => 'Other',
                                    ])
                                    ->default('phone')
                                    ->required(),

                                TextInput::make('contact_value')
                                    ->label('📞 Contact Details')
                                    ->placeholder('e.g., +1 234 567 8901')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                    ]),
            ]);
    }
    /**
     * -------------------------
     *       TABLE SCHEMA
     * -------------------------
     */
    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
        ->columns([
            TextColumn::make('uuid')
                ->label('🔑 UUID')
                ->searchable()
                ->tooltip('Unique identifier for the store')  // Tooltip for extra context
                ->sortable(),

            TextColumn::make('user.name')
                ->label('👤 User')
                ->sortable()
                ->searchable()
                ->tooltip('Name of the user associated with the store')  // Tooltip for user details
                ->wrap(),  // Wrap long names

            TextColumn::make('name')
                ->label('🏪 Store Name')
                ->sortable()
                ->searchable()
                ->tooltip('Name of the store')  // Tooltip for the store name
                ->wrap(),  // Ensures long names wrap properly

            TextColumn::make('province.name')
                ->label('📍 Province')
                ->sortable()
                ->searchable()
                ->tooltip('Province where the store is located'),  // Tooltip for province details

            IconColumn::make('is_closed')
                ->label('🚫 Closed?')
                ->icon('heroicon-o-x-circle')  // Cross icon for closed stores
                ->color('danger')  // Color the icon red for closed stores
                ->boolean()  // Show a checkmark or cross
                ->tooltip('Indicates whether the store is closed or not'),  // Tooltip for closed status

            BadgeColumn::make('status')
                ->label('⚙️ Status')
                ->sortable()
                ->badge()
                ->formatStateUsing(fn ($state) => ucfirst($state))  // Capitalize status (e.g., 'active' -> 'Active')
                ->color(fn ($state) => $state === 'active' ? 'success' : ($state === 'inactive' ? 'danger' : 'warning'))  // Color badges
                ->tooltip('Current status of the store'),  // Tooltip for status

            TextColumn::make('created_at')
                ->label('📅 Created At')
                ->formatStateUsing(fn ($state) => Carbon::parse($state)->diffForHumans())  // Human-readable date format
                ->sortable()
                ->tooltip('Date when the store was created')  // Tooltip for creation date
                ->toggleable(isToggledHiddenByDefault: true),  // Toggle visibility by default

            TextColumn::make('updated_at')
                ->label('🔄 Updated At')
                ->formatStateUsing(fn ($state) => Carbon::parse($state)->diffForHumans())  // Human-readable date format
                ->sortable()
                ->tooltip('Date when the store was last updated')  // Tooltip for last updated date
                ->toggleable(isToggledHiddenByDefault: true),  // Toggle visibility by default
        ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }


public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            InfolistSection::make('Store Details')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Card::make()
                                ->schema([
                                    TextEntry::make('uuid')->label('UUID'),
                                    TextEntry::make('name')->label('Store Name'),
                                    TextEntry::make('status')->label('Status')->badge(),
                                    IconEntry::make('is_closed')
                                        ->label('Closed?')
                                        ->boolean('heroicon-o-check-circle', 'heroicon-o-x-circle')
                                        ->color(fn (bool $state) => $state ? 'success' : 'danger'),
                                ])->columns(4),

                            Card::make()
                                ->schema([
                                    TextEntry::make('user.name')->label('Owner'),
                                    TextEntry::make('country.name')->label('Country'),
                                    TextEntry::make('province.name')->label('Province'),
                                    TextEntry::make('address')->label('Address'),
                                ])->columns(4),
                        ])->columns(4),
                ])->columns(4),

            InfolistSection::make('Geo & Timing Info')
                ->schema([
                    Grid::make(2)->schema([

                                TextEntry::make('latitude')->label('Latitude'),
                                TextEntry::make('longitude')->label('Longitude'),
                                TextEntry::make('open_time')->label('Opening Time'),
                                TextEntry::make('close_time')->label('Closing Time'),

                    ])->columns(4),
                ])->columns(4),

            InfolistSection::make('Timestamps')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('created_at')->label('Created At')->dateTime()->since(),
                        TextEntry::make('updated_at')->label('Updated At')->dateTime()->since(),
                    ]),
                ]),

    //             InfolistSection::make('💱 Hawala Summary')
    // ->schema([

    // InfolistSection::make('💱 Hawala Summary')
    // ->schema([
    //     // 📊 Overall Totals - Dynamic Summary
    //     TextEntry::make('created_at')
    //         ->label('Total Given')
    //         ->formatStateUsing(function ($record) {
    //         $summary = $record->hawlaOverallSummary();
    //         $total = 0;
    //         foreach ($summary as $totals) {
    //             $total += (float) str_replace(',', '', $totals['total_given']);
    //         }
    //         return number_format($total, 2);
    //         })
    //         ->color('info')
    //         ->icon('heroicon-o-arrow-up-right'),

    //     TextEntry::make('created_at')
    //         ->label('Total Received')
    //         ->formatStateUsing(function ($record) {
    //         $summary = $record->hawlaOverallSummary();
    //         $total = 0;
    //         foreach ($summary as $totals) {
    //             $total += (float) str_replace(',', '', $totals['total_received']);
    //         }
    //         return number_format($total, 2);
    //         })
    //         ->color('success')
    //         ->icon('heroicon-o-arrow-down-left'),

    //     TextEntry::make('created_at')
    //         ->label('Net Balance')
    //         ->formatStateUsing(function ($record) {
    //         $summary = $record->hawlaOverallSummary();
    //         $net = 0;
    //         foreach ($summary as $totals) {
    //             $net += (float) str_replace(',', '', $totals['net_balance']);
    //         }
    //         return number_format($net, 2);
    //         })
    //         ->color('gray')
    //         ->icon('heroicon-o-calculator'),

    //     // 🏪 Store-wise breakdown
    //     RepeatableEntry::make('hawala_per_store')
    //         ->label('🧾 Summary with Each Store')
    //         ->schema([
    //             TextEntry::make('store')->label('Store'),
    //             TextEntry::make('currency')->label('Currency'),
    //             TextEntry::make('total_given')->label('Total Given')->color('info'),
    //             TextEntry::make('total_received')->label('Total Received')->color('success'),
    //             TextEntry::make('total_commission')->label('Total Commission')->color('gray'),
    //             TextEntry::make('avg_exchange_rate')->label('Avg. Exchange Rate'),
    //         ])
    //         ->getStateUsing(fn($record) => $record->hawlaPerStoreSummary()),

    // ])->columns(1),

    //     // 💰 Total collected from clients (given amount)
    //     RepeatableEntry::make('created_at')
    //         ->label('💰 Collected from Clients (Currency Wise)')
    //         ->schema([
    //             TextEntry::make('currency')->label('Currency'),
    //             TextEntry::make('amount')->label('Amount')->color('info'),
    //         ])
    //         ->getStateUsing(function ($record) {
    //             return Hawla::with('givenCurrency')
    //                 ->where('sender_store_id', $record->id)
    //                 ->where('status', 'completed')
    //                 ->get()
    //                 ->groupBy('given_amount_currency_id')
    //                 ->map(function ($group) {
    //                     return [
    //                         'currency' => optional($group->first()->givenCurrency)->code ?? '—',
    //                         'amount' => number_format($group->sum('given_amount'), 2),
    //                     ];
    //                 })->values()->toArray();
    //         }),

    //     // 💸 Total paid to other clients (receiving amount)
    //     RepeatableEntry::make('received_by_currency')
    //         ->label('💸 Paid to Other Stores’ Clients (Currency Wise)')
    //         ->schema([
    //             TextEntry::make('currency')->label('Currency'),
    //             TextEntry::make('amount')->label('Amount')->color('success'),
    //         ])
    //         ->getStateUsing(function ($record) {
    //             return Hawla::with('receivingCurrency')
    //                 ->where('receiver_store_id', $record->id)
    //                 ->where('status', 'completed')
    //                 ->get()
    //                 ->groupBy('receiving_amount_currency_id')
    //                 ->map(function ($group) {
    //                     return [
    //                         'currency' => optional($group->first()->receivingCurrency)->code ?? '—',
    //                         'amount' => number_format($group->sum('receiving_amount'), 2),
    //                     ];
    //                 })->values()->toArray();
    //         }),

    //     // 🏪 Given to other stores
    //     RepeatableEntry::make('store_given_summary')
    //         ->label('🏪 Stores I Sent Money To')
    //         ->schema([
    //             TextEntry::make('store')->label('To Store'),
    //             TextEntry::make('currency')->label('Currency'),
    //             TextEntry::make('total_amount')->label('Total Given')->color('info'),
    //             TextEntry::make('total_commission')->label('Commission')->color('gray'),
    //             TextEntry::make('average_exchange_rate')->label('Avg. Exchange Rate')->color('gray'),
    //         ])
    //         ->getStateUsing(function ($record) {
    //             return Hawla::with(['receiverStore', 'givenCurrency'])
    //                 ->where('sender_store_id', $record->id)
    //                 ->where('status', 'completed')
    //                 ->get()
    //                 ->groupBy(fn ($item) => $item->receiver_store_id . '_' . $item->given_amount_currency_id)
    //                 ->map(function ($group) {
    //                     $first = $group->first();
    //                     return [
    //                         'store' => optional($first->receiverStore)->name ?? '—',
    //                         'currency' => optional($first->givenCurrency)->code ?? '—',
    //                         'total_amount' => number_format($group->sum('given_amount'), 2),
    //                         'total_commission' => number_format($group->sum('commission'), 2),
    //                         'average_exchange_rate' => $group->avg('exchange_rate') ? number_format($group->avg('exchange_rate'), 2) : '—',
    //                     ];
    //                 })->values()->toArray();
    //         }),

    //     // 🏪 Received from other stores
    //     RepeatableEntry::make('store_received_summary')
    //         ->label('🏪 Stores That Sent To My Clients')
    //         ->schema([
    //             TextEntry::make('store')->label('From Store'),
    //             TextEntry::make('currency')->label('Currency'),
    //             TextEntry::make('total_amount')->label('Total Received')->color('success'),
    //             TextEntry::make('total_commission')->label('Commission')->color('gray'),
    //             TextEntry::make('average_exchange_rate')->label('Avg. Exchange Rate')->color('gray'),
    //         ])
    //         ->getStateUsing(function ($record) {
    //             return Hawla::with(['senderStore', 'receivingCurrency'])
    //                 ->where('receiver_store_id', $record->id)
    //                 ->where('status', 'completed')
    //                 ->get()
    //                 ->groupBy(fn ($item) => $item->sender_store_id . '_' . $item->receiving_amount_currency_id)
    //                 ->map(function ($group) {
    //                     $first = $group->first();
    //                     return [
    //                         'store' => optional($first->senderStore)->name ?? '—',
    //                         'currency' => optional($first->receivingCurrency)->code ?? '—',
    //                         'total_amount' => number_format($group->sum('receiving_amount'), 2),
    //                         'total_commission' => number_format($group->sum('commission'), 2),
    //                         'average_exchange_rate' => $group->avg('exchange_rate') ? number_format($group->avg('exchange_rate'), 2) : '—',
    //                     ];
    //                 })->values()->toArray();
    //         }),

    // ])
    // ->columns(1),


        ]);

}


    /**
     * -------------------------
     *      RELATIONSHIPS
     * -------------------------
     */
    public static function getRelations(): array
    {
        return [
            ReceiverHawlasRelationManager::class,
            SenderHawlasRelationManager::class,
            WalletsRelationManager::class,
            StoreCommissionsRelationManager::class,
            StoreCommissionRangesRelationManager::class

        ];
    }

    /**
     * -------------------------
     *       RESOURCE PAGES
     * -------------------------
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'view' => Pages\ViewStore::route('/{record}'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }

    /**
     * -------------------------
     *      QUERY MODIFIER
     * -------------------------
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
