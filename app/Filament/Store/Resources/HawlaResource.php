<?php

namespace App\Filament\Store\Resources;

use App\Filament\Store\Resources\HawlaResource\Pages;
use App\Filament\Store\Resources\HawlaResource\RelationManagers;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;
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

    protected static ?string $navigationGroup = 'Hawla Management';
    public static function form(Form $form): Form
{
    return $form
        ->schema([
            /** 🧍 Sender Section */
            Forms\Components\Section::make('🧍 Sender Details')
                ->description('Information about the sender.')
                ->icon('heroicon-o-user-circle')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Select::make('hawla_type_id')
                            ->label('Hawla Type')
                            ->relationship('hawlaType', 'name')
                            ->native(false)
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('sender_name')
                            ->label('Sender Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('sender_phone')
                            ->label('Sender Phone')
                            ->required()
                            ->maxLength(255),
                    ]),
                ]),

            /** 👤 Receiver Section */
            Forms\Components\Section::make('👤 Receiver Details')
                ->description('Information about the receiver.')
                ->icon('heroicon-o-user-group')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('receiver_name')
                            ->label('Receiver Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('receiver_father')
                            ->label('Father Name')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('receiver_phone_number')
                            ->label('Phone Number')
                            ->maxLength(20),
                    ]),
                    Forms\Components\Textarea::make('receiver_address')
                        ->label('Address')
                        ->columnSpanFull(),

                    Forms\Components\Select::make('receiver_store_id')
                        ->label('Receiver Store')
                        ->relationship('receiverStore', 'name', function ($query, $get) {
                            $senderStoreId = $get('sender_store_id');
                            return $senderStoreId ? $query->where('id', '!=', $senderStoreId) : $query;
                        })
                        ->searchable()
                        ->native(false)
                        ->preload()
                        ->required(),
                ]),

            /** 💱 Amounts & Currency Section */
            Forms\Components\Section::make('💱 Amounts & Currency')
                ->description('Financial values and currency types.')
                ->icon('heroicon-o-currency-dollar')
                ->schema([
                    Forms\Components\Grid::make(4)->schema([
                        Forms\Components\Select::make('given_amount_currency_id')
                            ->label('Given Currency')
                            ->relationship('givenCurrency', 'code')
                            ->native()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::syncCurrencies($state, $set, $get)),

                        Forms\Components\TextInput::make('given_amount')
                            ->label('Given Amount')
                            ->numeric()
                            ->required()
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::handleExchangeAndCommission($get, $set)),

                        Forms\Components\Select::make('receiving_amount_currency_id')
                            ->label('Receiving Currency')
                            ->relationship('receivingCurrency', 'code')
                            ->native()
                            ->required()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::handleExchangeAndCommission($get, $set)),

                        Forms\Components\TextInput::make('receiving_amount')
                            ->label('Receiving Amount')
                            ->numeric()
                            ->readOnly()
                            ->required(),
                    ]),

                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Radio::make('store_commission')
                            ->label('Store Commission Type')
                            ->options(['range' => 'Range'] + \App\Models\CommissionType::pluck('name', 'id')->toArray())
                            ->default('range')
                            ->inline()
                            ->live()
                            ->dehydrated(false)
                            ->columnSpan(2)
                            ->afterStateUpdated(fn (callable $set, callable $get) => static::handleExchangeAndCommission($get, $set)),

                        Forms\Components\Checkbox::make('deduct_commission')
                            ->label('Deduct Commission')
                            ->default(true)
                            ->live()
                            ->dehydrated(false)
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::handleExchangeAndCommission($get, $set)),

                        Forms\Components\TextInput::make('exchange_rate')
                            ->label('Exchange Rate')
                            ->readOnly()
                            ->numeric(),

                        Forms\Components\TextInput::make('commission')
                            ->label('Commission')
                            ->numeric(),

                        Forms\Components\Select::make('commission_taken_by')
                            ->label('Commission Taken By')
                            ->options([
                                'sender_store' => 'Sender Store',
                                'receiver_store' => 'Receiver Store',
                            ])
                            ->default('sender_store')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::handleExchangeAndCommission($get, $set)),
                    ]),
                ]),

            /** 📝 Note Section */
            Forms\Components\Section::make('📝 Note')
                ->icon('heroicon-o-pencil-square')
                ->schema([
                    Forms\Components\Textarea::make('note')
                        ->label('Internal Note')
                        ->columnSpanFull()
                        ->rows(3),
                ]),

            /** 📆 Meta Section */
            Forms\Components\Section::make('📆 Metadata')
                ->columns(3)
                ->icon('heroicon-o-calendar-days')
                ->schema([
                    Forms\Components\DateTimePicker::make('date')
                        ->label('Transaction Date')
                        ->default(now())
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->label('Transaction Status')
                        ->options([
                            'in_progress' => 'In Progress',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->native(false)
                        ->default('in_progress')
                        ->required(),

                    Forms\Components\Select::make('created_by')
                        ->label('Created By')
                        ->relationship('creator', 'name')
                        ->searchable()
                        ->default(auth()->id())
                        ->required()
                        ->visible(fn () => auth()->user()->hasRole('super_admin')),
                ]),

            /** 📎 Attachment */
            Forms\Components\Section::make('📎 Verification Document')
                ->icon('heroicon-o-paper-clip')
                ->columns(1)
                ->schema([
                    Forms\Components\FileUpload::make('receiver_verification_document')
                        ->label('Receiver Verification Document')
                        ->maxSize(4024)
                        ->directory('receiver_verification_documents')
                        ->preserveFilenames(),
                ]),
        ]);
}


    public static function table(Table $table): Table
    {
            return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                /** 🆔 UUID */
                Tables\Columns\TextColumn::make('uuid')
                    ->label('🆔 UUID')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('UUID copied!')
                    ->copyMessageDuration(1500)
                    ->tooltip('Click to copy transaction ID'),

                /** 📅 Transaction Date */
                Tables\Columns\TextColumn::make('date')
                    ->label('📅 Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),

                /** 🧾 Hawla Type */
                Tables\Columns\TextColumn::make('hawlaType.name')
                    ->label('📄 Type')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                /** 👤 Sender */
                Tables\Columns\TextColumn::make('sender_name')
                    ->label('👤 Sender')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                /** 👤 Receiver */
                Tables\Columns\TextColumn::make('receiver_name')
                    ->label('👤 Receiver')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                /** 💸 Given Amount */
                Tables\Columns\TextColumn::make('given_amount')
                    ->label('💸 Given')
                    ->numeric(decimalPlaces: 2, decimalSeparator: '.', thousandsSeparator: ',')
                    ->sortable()
                    ->suffix(fn ($record) => optional($record->givenCurrency)->code),

                /** 💵 Receiving Amount */
                Tables\Columns\TextColumn::make('receiving_amount')
                    ->label('💵 Receiving')
                    ->numeric(decimalPlaces: 2, decimalSeparator: '.', thousandsSeparator: ',')
                    ->sortable()
                    ->suffix(fn ($record) => optional($record->receivingCurrency)->code)
                    ->color('success'),

                /** 💼 Commission */
                Tables\Columns\TextColumn::make('commission')
                    ->label('💼 Commission')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->color(fn ($state) => $state > 10 ? 'danger' : ($state > 5 ? 'warning' : 'gray')),

                /** 📊 Status */
                Tables\Columns\BadgeColumn::make('status')
                    ->label('📊 Status')
                    ->sortable()
                    ->colors([
                        'primary' => fn ($state) => $state === 'in_progress',
                        'success' => fn ($state) => $state === 'completed',
                        'danger' => fn ($state) => $state === 'cancelled',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'in_progress',
                        'heroicon-o-check-circle' => 'completed',
                        'heroicon-o-x-circle' => 'cancelled',
                    ])
                    ->formatStateUsing(fn ($state) => str_replace('_', ' ', ucfirst($state))),

                /** 🏬 From Store */
                Tables\Columns\TextColumn::make('senderStore.name')
                    ->label('🏬 From Store')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                /** 🏪 To Store */
                Tables\Columns\TextColumn::make('receiverStore.name')
                    ->label('🏪 To Store')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                /** 👤 Creator */
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('👨‍💼 Created By')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                /** 💰 Paid At */
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('💰 Paid At')
                    ->placeholder('Not Paid')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(),

                /** 🕒 Created Timestamp */
                Tables\Columns\TextColumn::make('created_at')
                    ->label('🕒 Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                /** 🔄 Updated Timestamp */
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('🔄 Updated')
                    ->dateTime('M d, Y H:i')
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

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();
        $storeId = $user?->store?->id;

        if (!$storeId) {
            return parent::getEloquentQuery()->whereRaw('0 = 1');
        }

        return parent::getEloquentQuery()
            ->where(function (Builder $query) use ($storeId) {
                $query->where('sender_store_id', $storeId)
                    ->orWhere('receiver_store_id', $storeId);
            });
    }
}
