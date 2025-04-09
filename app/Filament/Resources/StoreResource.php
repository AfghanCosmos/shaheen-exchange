<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Filament\Resources\StoreResource\RelationManagers\WalletsRelationManager;
use App\Filament\Resources\StoreResource\RelationManagers\StoreCommissionRangesRelationManager;
use App\Filament\Resources\StoreResource\RelationManagers\StoreCommissionsRelationManager;



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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    protected static ?string $navigationGroup = 'Store Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Store Information
                Forms\Components\Section::make('Store Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                    ->createOptionForm(fn(Form $form) => UserResource::form($form))

                            ->placeholder('Select a user'),

                        Forms\Components\TextInput::make('name')
                            ->label('Store Name')
                            ->required()
                            ->placeholder('Enter store name')
                            ->maxLength(255),
                            Forms\Components\Select::make('country_id')
                                ->label('Country')
                                ->relationship('country', 'name')
                                ->searchable()
                                ->live()
                                ->preload()
                                ->required()
                                ->placeholder('Select a country'),
                            Forms\Components\Select::make('province_id')
                                ->label('Province')
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

                // Contact Information
                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->required()
                            ->placeholder('e.g., 123 Main St, City, Province')
                            ->columnSpanFull(),



                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->nullable()
                            ->placeholder('e.g., 34.0522'),

                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->nullable()
                            ->placeholder('e.g., -118.2437'),
                    ])
                    ->columns(2),

                // Store Timings
                Forms\Components\Section::make('Store Timings')
                    ->schema([
                        Forms\Components\TimePicker::make('open_time')
                            ->label('Opening Time')
                            ->placeholder('e.g., 08:00 AM'),

                        Forms\Components\TimePicker::make('close_time')
                            ->label('Closing Time')
                            ->placeholder('e.g., 08:00 PM'),

                        Forms\Components\Toggle::make('is_closed')
                            ->label('Is Closed?')
                            ->required(),
                    ])
                    ->columns(2),

                // Status Information
                Forms\Components\Section::make('Status Information')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
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

                    Repeater::make('Store Contact')
                        ->relationship('storeContacts')
                        ->columnSpanFull()
                        ->schema([
                            Section::make('Contact Information')
                                ->schema([
                                    Select::make('type')
                                        ->label('Contact Type')
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
                                        ->label('Contact Details')
                                        ->placeholder('e.g., +1 234 567 8901')
                                        ->required()
                                        ->maxLength(255),
                                    ])->columns(2)
                                ])
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
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Store Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('province.name')
                    ->label('Province')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_closed')
                    ->label('Closed?')
                    ->boolean(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->diffForHumans())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->diffForHumans())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

            InfolistSection::make('💱 Hawla Transactions Summary')
                ->schema([
                    Grid::make(4)->schema([
                        TextEntry::make('created_at')
                            ->label('Total Given Amount (Completed)')
                            ->icon('heroicon-o-arrow-up-right')
                            ->color('info')
                            ->formatStateUsing(fn ($record) => number_format(
                                \App\Models\Hawla::where('sender_store_id', $record->id)
                                    ->where('status', 'completed')
                                    ->sum('given_amount'),
                                2
                            )),

                        TextEntry::make('created_at')
                            ->label('Total Received Amount (Completed)')
                            ->icon('heroicon-o-arrow-down-left')
                            ->color('success')
                            ->formatStateUsing(fn ($record) => number_format(
                                \App\Models\Hawla::where('receiver_store_id', $record->id)
                                    ->where('status', 'completed')
                                    ->sum('receiving_amount'),
                                2
                            )),

                        TextEntry::make('created_at')
                            ->label('Remaining Balance')
                            ->icon('heroicon-o-calculator')
                            ->color('gray')
                            ->formatStateUsing(function ($record) {
                                $given = \App\Models\Hawla::where('sender_store_id', $record->id)
                                    ->where('status', 'completed')
                                    ->sum('given_amount');

                                $received = \App\Models\Hawla::where('receiver_store_id', $record->id)
                                    ->where('status', 'completed')
                                    ->sum('receiving_amount');

                                return number_format($given - $received, 2);
                            }),

                        TextEntry::make('created_at')
                            ->label('Given To Stores')
                            ->icon('heroicon-o-arrow-right-circle')
                            ->color('info')
                            ->formatStateUsing(function ($record) {
                                return \App\Models\Hawla::where('sender_store_id', $record->id)
                                    ->where('status', 'completed')
                                    ->with('receiverStore')
                                    ->get()
                                    ->groupBy('receiver_store_id')
                                    ->map(function ($group) {
                                        $store = $group->first()->receiverStore;
                                        $amount = number_format($group->sum('given_amount'), 2);
                                        return optional($store)->name . " ({$amount})";
                                    })
                                    ->implode(', ') ?: '—';
                            }),
                    ]),
                ])->columns(1)
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
