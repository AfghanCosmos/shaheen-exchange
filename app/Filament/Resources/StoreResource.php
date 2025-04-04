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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
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
