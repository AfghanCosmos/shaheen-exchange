<?php

namespace App\Filament\Store\Resources;

use App\Filament\Store\Resources\HawlaResource\Pages;
use App\Filament\Store\Resources\HawlaResource\RelationManagers;
use App\Models\Hawla;
use Filament\Forms\Components\Select;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class HawlaResource extends Resource
{
    protected static ?string $model = Hawla::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sender Details')
                    ->description('Information about the sender.')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('hawla_type_id')
                                    ->label('Hawla Type')
                                    ->relationship('hawlaType', 'name')
                                    ->native(false)
                                    ->preload()
                                    ->required(),

                                Forms\Components\TextInput::make('sender_name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('sender_phone')

                                    ->required()
                                    ->maxLength(255),
                            ]),
                        // Select::make('sender_store_id')
                        //     ->relationship('senderStore', 'name', function ($query) {
                        //         if (!auth()->user()->hasRole('super_admin')) {
                        //             $query->where('id', auth()->user()?->store?->id);
                        //         }
                        //         return $query;
                        //     })
                        //     ->label('Sender Store')
                        //     ->searchable()
                        //     ->preload()
                        //     ->live() // reactive field
                        //     ->required(),
                    ]),


                Forms\Components\Section::make('Receiver Details')
                    ->description('Information about the receiver.')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('receiver_name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('receiver_father')
                                    ->maxLength(255),

                                    Forms\Components\TextInput::make('receiver_phone_number')

                                    ->maxLength(20),
                            ]),

                        Forms\Components\Textarea::make('receiver_address')
                            ->columnSpanFull(),

                        Select::make('receiver_store_id')
                            ->relationship('receiverStore', 'name', function ($query, $get) {
                                $senderStoreId = $get('sender_store_id');

                                if ($senderStoreId) {
                                    $query->where('id', '!=', $senderStoreId);
                                }

                                return $query;
                            })
                            ->label('Receiver Store')
                            ->searchable()
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
                Forms\Components\Section::make('Amounts & Currency')
                ->description('Financial values and currency types.')
            ->schema([
                Forms\Components\Grid::make(4)->schema([
                // Given Currency
                Forms\Components\Select::make('given_amount_currency_id')
                    ->relationship('givenCurrency', 'code')
                    ->label('Given Currency')
                    ->native()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if (!$get('receiving_amount_currency_id')) {
                            $set('receiving_amount_currency_id', $state);
                        }
                        self::handleExchangeAndCommission($get, $set);
                    }),

                // Given Amount
                Forms\Components\TextInput::make('given_amount')
                    ->required()
                    ->numeric()
                    ->live(debounce: 500)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        self::handleExchangeAndCommission($get, $set);
                    }),

                // Receiving Currency
                Forms\Components\Select::make('receiving_amount_currency_id')
                    ->relationship('receivingCurrency', 'code')
                    ->label('Receiving Currency')
                    ->native()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        self::handleExchangeAndCommission($get, $set);
                    }),

                // Receiving Amount
                Forms\Components\TextInput::make('receiving_amount')
                    ->required()
                    ->numeric()
                    ->readOnly(),
            ]),

            Forms\Components\Grid::make(3)->schema([
                // Commission Type
                Forms\Components\Radio::make('store_commission')
                    ->label('Store Commission')
                    ->options(['range' => 'Range'] + \App\Models\CommissionType::pluck('name', 'id')->toArray())
                    ->default('range')
                    ->inline()
                    ->columnSpan(2)
                    ->live()
                    ->dehydrated(false)
                    ->afterStateUpdated(fn (callable $set, callable $get) => self::handleExchangeAndCommission($get, $set)),

                // Deduct Checkbox
                Forms\Components\Checkbox::make('deduct_commission')
                    ->label('Deduct Commission')
                    ->default(true)
                    ->live()
                    ->dehydrated(false)
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::handleExchangeAndCommission($get, $set)),

                // Exchange Rate & Commission
                Forms\Components\TextInput::make('exchange_rate')
                    ->numeric()
                    ->readOnly(),

                Forms\Components\TextInput::make('commission')
                    ->numeric(),
                Forms\Components\Select::make('commission_taken_by')
                    ->options([
                        'sender_store' => 'Sender Store',
                        'receiver_store' => 'Receiver Store',
                    ])
                    ->default('sender_store')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::handleExchangeAndCommission($get, $set)),
            ]),
            ]),

                Forms\Components\Section::make('Note')
                    ->schema([
                        Forms\Components\Textarea::make('note')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),



                Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\DateTimePicker::make('date')
                            ->default(now())
                                ->required(),

                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                         'in_progress' => 'In Progress',
                                         'completed' => 'Completed',
                                         'cancelled' => 'Cancelled',
                                    ])
                                    ->native(false)
                                    ->default('in_progress')
                                    ->required(),

                            Forms\Components\Select::make('created_by')
                                ->relationship('creator', 'name')
                                ->label('Created By')
                                ->searchable()
                                ->default(auth()->user()->id)
                                ->required()
                                ->visible(fn() => auth()->user()->hasRole('super_admin')),
                        ]),

                        Forms\Components\Section::make()
                        ->columns(1)
                        ->schema([
                            Forms\Components\FileUpload::make('receiver_verification_document')
                                ->label('Receiver Verification Document')
                                ->maxSize(4024) // Maximum file size in KB
                                ->directory('receiver_verification_documents')
                                ->preserveFilenames()
                            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
            return $table
            ->defaultSort('created_at', 'desc')
                ->columns([
                    Tables\Columns\TextColumn::make('uuid')
                        ->label('UUID')
                        ->searchable()
                        ->copyable()
                        ->copyMessage('UUID copied!')
                        ->copyMessageDuration(1500),

                    Tables\Columns\TextColumn::make('date')
                        ->label('Date')
                        ->dateTime()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('hawlaType.name')
                        ->label('Hawla Type')
                        ->sortable()
                        ->searchable(),

                    Tables\Columns\TextColumn::make('sender_name')
                        ->label('Sender')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('receiver_name')
                        ->label('Receiver')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('given_amount')
                        ->label('Given')
                        ->numeric()
                        ->sortable()
                        ->suffix(fn ($record) => ' ' . optional($record->givenCurrency)->code),

                    Tables\Columns\TextColumn::make('receiving_amount')
                        ->label('Receiving')
                        ->numeric()
                        ->sortable()
                        ->suffix(fn ($record) => ' ' . optional($record->receivingCurrency)->code),

                    Tables\Columns\TextColumn::make('commission')
                        ->numeric()
                        ->sortable()
                        ->label('Commission'),


                    Tables\Columns\BadgeColumn::make('status')
                        ->label('Status')
                        ->sortable()
                        ->colors([
                            'primary' => fn ($state) => $state === 'in_progress',
                            'success' => fn ($state) => $state === 'completed',
                            'danger' => fn ($state) => $state === 'cancelled',
                        ])
                        ->icons([
                            'heroicon-o-clock' => 'In Progress',
                            'heroicon-o-check-circle' => 'Completed',
                            'heroicon-o-x-circle' => 'Cancelled',
                        ]),

                    Tables\Columns\TextColumn::make('senderStore.name')
                        ->label('From Store')
                        ->sortable(),

                    Tables\Columns\TextColumn::make('receiverStore.name')
                        ->label('To Store')
                        ->sortable(),

                    Tables\Columns\TextColumn::make('creator.name')
                        ->label('Created By')
                        ->searchable()
                        ->sortable(),

                        Tables\Columns\TextColumn::make('paid_at')
                        ->label('Paid At')
                        ->placeholder('Not Paid')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(),

                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Created')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    Tables\Columns\TextColumn::make('updated_at')
                        ->label('Updated')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ])

                ->actions([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->status === 'in_progress'),
                    Tables\Actions\Action::make('pay')
                        ->label('Pay')
                        ->icon('heroicon-o-currency-dollar')
                        ->action(function ($record) {
                            $record->pay();
                        })
                        ->visible(fn ($record) => is_null($record->paid_at) && $record->status === 'in_progress')
                        ->requiresConfirmation(),
                        Tables\Actions\Action::make('cancel')
                            ->label('Cancel')
                            ->icon('heroicon-o-x-circle')
                            ->color('warning')
                            ->action(function ($record) {
                                $record->refund();
                            })
                            ->requiresConfirmation()
                            ->visible(fn ($record) => $record->status === 'in_progress'),
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
                Section::make('💼 Hawla Summary')
                    ->description('This is the full overview of the hawla transaction.')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('uuid')
                            ->label('Tracking Code')
                            ->copyable()
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('hawlaType.name')->label('Hawla Type')->badge()->color('info'),
                        TextEntry::make('date')->label('Date')->dateTime()->color('gray'),
                    ]),

                Section::make('🧍 Sender')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('sender_name')->label('Name')->icon('heroicon-o-user-circle')->weight('bold'),
                        TextEntry::make('sender_phone')->label('Phone')->icon('heroicon-o-phone'),
                        TextEntry::make('senderStore.name')->label('Store')->icon('heroicon-o-building-storefront')->badge()->color('success'),
                    ]),

                Section::make('👤 Receiver')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('receiver_name')->label('Name')->icon('heroicon-o-user-circle')->weight('bold'),
                        TextEntry::make('receiver_father')->label('Father Name'),
                        TextEntry::make('receiver_phone_number')->label('Phone')->icon('heroicon-o-phone'),
                        TextEntry::make('receiver_address')->label('Address')->columnSpanFull()->markdown(),
                        TextEntry::make('receiverStore.name')->label('Store')->icon('heroicon-o-building-storefront')->badge()->color('warning'),
                    ]),

                Section::make('💸 Money & Currency')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('given_amount')->label('Given Amount')->prefix('💰')->formatStateUsing(fn ($state) => number_format($state, 2)),
                        TextEntry::make('givenCurrency.code')->label('Given Currency')->badge()->color('gray'),
                        TextEntry::make('receiving_amount')->label('Receiving Amount')->prefix('💸')->formatStateUsing(fn ($state) => number_format($state, 2)),
                        TextEntry::make('receivingCurrency.code')->label('Receiving Currency')->badge()->color('success'),
                        TextEntry::make('exchange_rate')->label('Exchange Rate')->prefix('🔁')->formatStateUsing(fn ($state) => $state ? number_format($state, 4) : '-'),
                    ]),

                Section::make('📊 Commission & Logic')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('commission')->label('Commission Amount')->prefix('💼')->formatStateUsing(fn ($state) => number_format($state, 2))->color('gray'),
                        TextEntry::make('commission_taken_by')
                            ->label('Taken By')
                            ->badge()
                            ->color(fn ($state) => $state === 'sender_store' ? 'info' : 'success')
                            ->formatStateUsing(fn ($state) => Str::headline($state)),
                        TextEntry::make('store_commission')
                            ->label('Commission Type')
                            ->badge()
                            ->color('gray')
                            ->formatStateUsing(fn ($state) => $state === 'range' ? 'Range Based' : (\App\Models\CommissionType::find($state)?->name ?? '-')),
                    ]),

                Section::make('📋 Status & Metadata')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->icon(fn ($state) => match ($state) {
                                'in_progress' => 'heroicon-o-arrow-path',
                                'completed' => 'heroicon-o-check-circle',
                                'cancelled' => 'heroicon-o-x-circle',
                            })
                            ->color(fn ($state) => match ($state) {
                                'in_progress' => 'warning',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                            })
                            ->weight('medium'),
                        TextEntry::make('creator.name')->label('Created By')->icon('heroicon-o-user')->color('gray'),
                        TextEntry::make('note')->label('Note')->markdown()->columnSpanFull()->visible(fn ($state) => filled($state)),
                    ]),

                Section::make('📁 Verification Document')
                    ->visible(fn ($record) => filled($record->receiver_verification_document))
                    ->columns(1)
                    ->schema([
                        ImageEntry::make('receiver_verification_document')
                            ->label('Uploaded Document')
                            ->width('100%')
                            ->height('auto')
                            ->disk('public') // customize if needed
                            ->openUrlInNewTab(),
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
            'index' => Pages\ListHawlas::route('/'),
            'create' => Pages\CreateHawla::route('/create'),
            'edit' => Pages\EditHawla::route('/{record}/edit'),
            'view' => Pages\ViewHawla::route('/{record}'),
        ];
    }

    public static function handleExchangeAndCommission(callable $get, callable $set): void
    {
        $givenAmount = (float) $get('given_amount');
        $givenCurrency = $get('given_amount_currency_id');
        $receivingCurrency = $get('receiving_amount_currency_id');
        $commissionType = $get('store_commission');
        $commissionTakenBy = $get('commission_taken_by') ?? 'sender_store';
        $storeId = $commissionTakenBy === 'sender_store' ? $get('sender_store_id') : $get('receiver_store_id');
        $deductCommission = $get('deduct_commission');

        $exchangeRate = null;
        $rate = null;

        // 1. Get exchange rate
        if ($givenCurrency && $receivingCurrency && $givenCurrency !== $receivingCurrency) {
            $exchangeRate = \App\Models\ExchangeRate::where('from_currency_id', $givenCurrency)
                ->where('to_currency_id', $receivingCurrency)
                ->latest('date')
                ->first();

            $rate = $exchangeRate?->sell_rate;
            $set('exchange_rate', $rate);
        } else {
            $rate = null;
            $set('exchange_rate', null);
        }

        // 2. Commission calculation
        $commission = 0;

        if ($givenAmount && $storeId && $givenCurrency) {
            if ($commissionType === 'range') {
                $range = \App\Models\StoreCommissionRange::where('store_id', $storeId)
                    ->where('currency_id', $givenCurrency)
                    ->whereRaw('? BETWEEN CAST(`from` AS DECIMAL(16,2)) AND CAST(`to` AS DECIMAL(16,2))', [$givenAmount])
                    ->first();

                $commission = $range?->commission ?? 0;
            } else {
                $storeCommission = \App\Models\StoreCommission::where('store_id', $storeId)
                    ->where('currency_id', $givenCurrency)
                    ->where('commission_type_id', $commissionType)
                    ->first();

                $commission = $storeCommission
                    ? ($storeCommission->is_fix ? $storeCommission->commission : ($givenAmount * $storeCommission->commission) / 100)
                    : 0;
            }
        }

        $set('commission', $commission);

        // 3. Receiving amount calculation
        if ($rate) {
            $finalGiven = $deductCommission ? max(0, $givenAmount - $commission) : $givenAmount;
            $set('receiving_amount', $finalGiven * $rate);
        } else {
            $finalGiven = $deductCommission ? max(0, $givenAmount - $commission) : $givenAmount;
            $set('receiving_amount', $finalGiven);
        }
    }
}
