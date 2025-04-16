<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HawlaResource\Pages;
use App\Filament\Resources\HawlaResource\RelationManagers\ReceiverStoreRelationManager;
use App\Filament\Resources\HawlaResource\RelationManagers\SenderStoreRelationManager;
use App\Models\Hawla;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Support\Str;

class HawlaResource extends Resource
{
    protected static ?string $model = Hawla::class;
    protected static ?string $navigationGroup = "Hawla Management";
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // Sender Details Section with Icon
                Forms\Components\Section::make('📦 Sender Details')
                    ->description('Information about the sender.')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('hawla_type_id')
                                    ->label('🏷️ Hawla Type') // Icon for Hawla Type
                                    ->relationship('hawlaType', 'name')
                                    ->native(false)
                                    ->preload()
                                    ->required()
                                    ->helperText('Select the type of Hawla.'),

                                Forms\Components\TextInput::make('sender_name')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('🧑‍💼 Sender Name') // Icon for Sender Name
                                    ->helperText('Enter the sender\'s name.'),

                                Forms\Components\TextInput::make('sender_phone')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('📞 Sender Phone') // Icon for Sender Phone
                                    ->helperText('Enter the sender\'s phone number.')
                            ]),

                        Forms\Components\Select::make('sender_store_id')
                            ->relationship('senderStore', 'name')
                            ->label('🏪 Sender Store') // Icon for Sender Store
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Select the sender\'s store.')
                            ->live() // reactive field
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::handleExchangeAndCommission($get, $set)),
                    ]),

                // Receiver Details Section with Icon
                Forms\Components\Section::make('👤 Receiver Details')
                    ->description('Information about the receiver.')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('receiver_name')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('👤 Receiver Name') // Icon for Receiver Name
                                    ->helperText('Enter the receiver\'s name.'),

                                Forms\Components\TextInput::make('receiver_father')
                                    ->maxLength(255)
                                    ->label('👨 Father\'s Name') // Icon for Father’s Name
                                    ->helperText('Enter the father\'s name of the receiver.'),

                                Forms\Components\TextInput::make('receiver_phone_number')
                                    ->maxLength(20)
                                    ->label('📞 Receiver Phone') // Icon for Receiver Phone
                                    ->helperText('Enter the receiver\'s phone number.')
                            ]),

                        Forms\Components\Textarea::make('receiver_address')
                            ->columnSpanFull()
                            ->label('🏠 Receiver Address') // Icon for Address
                            ->helperText('Enter the receiver\'s full address.'),

                        Forms\Components\Select::make('receiver_store_id')
                            ->relationship('receiverStore', 'name')
                            ->label('🏪 Receiver Store') // Icon for Receiver Store
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Select the receiver\'s store.')
                            ->live() // reactive field
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::handleExchangeAndCommission($get, $set)),
                    ]),

                // Amounts & Currency Section with Icon
                Forms\Components\Section::make('💵 Amounts & Currency')
                    ->description('Enter the financial details and currency types.')
                    ->schema([
                        Forms\Components\Grid::make(4)->schema([
                            // Given Currency
                            Forms\Components\Select::make('given_amount_currency_id')
                                ->relationship('givenCurrency', 'code')
                                ->label('💰 Given Currency') // Icon for Given Currency
                                ->native()
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::handleExchangeAndCommission($get, $set)),

                            // Given Amount
                            Forms\Components\TextInput::make('given_amount')
                                ->required()
                                ->numeric()
                                ->label('💵 Given Amount') // Icon for Given Amount
                                ->live(debounce: 500)
                                ->helperText('Enter the amount you are giving in the specified currency.')
                                ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::handleExchangeAndCommission($get, $set)),

                            // Receiving Currency
                            Forms\Components\Select::make('receiving_amount_currency_id')
                                ->relationship('receivingCurrency', 'code')
                                ->label('💸 Receiving Currency') // Icon for Receiving Currency
                                ->native()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::handleExchangeAndCommission($get, $set)),

                            // Receiving Amount
                            Forms\Components\TextInput::make('receiving_amount')
                                ->required()
                                ->numeric()
                                ->readOnly()
                                ->label('💸 Receiving Amount') // Icon for Receiving Amount
                                ->helperText('This will be calculated based on the given amount and exchange rate.')
                        ]),

                        Forms\Components\Grid::make(3)->schema([
                            // Commission Type
                            Forms\Components\Radio::make('store_commission')
                                ->label('💼 Store Commission') // Icon for Commission
                                ->options(['range' => 'Range'] + \App\Models\CommissionType::pluck('name', 'id')->toArray())
                                ->default('range')
                                ->inline()
                                ->columnSpan(2)
                                ->live()
                                ->dehydrated(false)
                                ->afterStateUpdated(fn (callable $set, callable $get) => static::handleExchangeAndCommission($get, $set)),

                            // Deduct Checkbox
                            Forms\Components\Checkbox::make('deduct_commission')
                                ->label('💸 Deduct Commission') // Icon for Deduct Commission
                                ->default(true)
                                ->live()
                                ->dehydrated(false)
                                ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::handleExchangeAndCommission($get, $set)),

                            // Exchange Rate & Commission
                            Forms\Components\TextInput::make('exchange_rate')
                                ->numeric()
                                ->readOnly()
                                ->label('🔄 Exchange Rate') // Icon for Exchange Rate
                                ->helperText('Exchange rate based on the selected currencies.'),

                            Forms\Components\TextInput::make('commission')
                                ->numeric()
                                ->label('💼 Commission') // Icon for Commission
                                ->helperText('The commission for the transaction.')
                        ]),
                    ]),

                // Note Section
                Forms\Components\Section::make('📝 Note')
                    ->schema([
                        Forms\Components\Textarea::make('note')
                            ->columnSpanFull()
                            ->rows(3)
                            ->label('📝 Additional Notes') // Icon for Notes
                            ->helperText('Add any additional notes about the transaction.')
                    ]),

                // Status & Created By Section
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\DateTimePicker::make('date')
                            ->default(now())
                            ->required()
                            ->label('📅 Transaction Date') // Icon for Date
                            ->helperText('Date of the transaction.'),

                        Forms\Components\Select::make('status')
                            ->label('🔴 Status') // Icon for Status
                            ->options([
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->native(false)
                            ->default('in_progress')
                            ->required()
                            ->helperText('Current status of the transaction.'),

                        Forms\Components\Select::make('created_by')
                            ->relationship('creator', 'name')
                            ->label('👤 Created By') // Icon for Created By
                            ->searchable()
                            ->default(auth()->user()->id)
                            ->required()
                            ->visible(fn() => auth()->user()->hasRole('super_admin'))
                            ->helperText('Creator of the transaction.')
                    ]),

                // Receiver Verification Document Section
                Forms\Components\Section::make('📁 Receiver Verification Document')
                    ->columns(1)
                    ->schema([
                        Forms\Components\FileUpload::make('receiver_verification_document')
                            ->label('📄 Upload Verification Document') // Icon for File Upload
                            ->maxSize(4024) // Maximum file size in KB
                            ->directory('receiver_verification_documents')
                            ->preserveFilenames()
                            ->helperText('Upload the receiver\'s verification document (optional).')
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([

                // UUID Column with Icon
                Tables\Columns\TextColumn::make('uuid')
                    ->label('🔑 UUID') // Icon for UUID
                    ->searchable()
                    ->copyable()
                    ->copyMessage('UUID copied!')
                    ->copyMessageDuration(1500),

                // Date Column with Icon
                Tables\Columns\TextColumn::make('date')
                    ->label('📅 Date') // Icon for Date
                    ->dateTime()
                    ->sortable(),

                // Hawla Type Column with Icon
                Tables\Columns\TextColumn::make('hawlaType.name')
                    ->label('🏷️ Hawla Type') // Icon for Hawla Type
                    ->sortable()
                    ->searchable(),

                // Sender Column with Icon
                Tables\Columns\TextColumn::make('sender_name')
                    ->label('🧑‍💼 Sender') // Icon for Sender
                    ->searchable(),

                // Receiver Column with Icon
                Tables\Columns\TextColumn::make('receiver_name')
                    ->label('👤 Receiver') // Icon for Receiver
                    ->searchable(),

                // Given Amount with Currency Code
                Tables\Columns\TextColumn::make('given_amount')
                    ->label('💰 Given') // Icon for Given Amount
                    ->numeric()
                    ->sortable()
                    ->suffix(fn ($record) => ' ' . optional($record->givenCurrency)->code),

                // Receiving Amount with Currency Code
                Tables\Columns\TextColumn::make('receiving_amount')
                    ->label('💸 Receiving') // Icon for Receiving Amount
                    ->numeric()
                    ->sortable()
                    ->suffix(fn ($record) => ' ' . optional($record->receivingCurrency)->code),

                // Commission Column with Icon
                Tables\Columns\TextColumn::make('commission')
                    ->numeric()
                    ->sortable()
                    ->label('💼 Commission'), // Icon for Commission

                // Status Column with Badge and Icon
                Tables\Columns\BadgeColumn::make('status')
                    ->label('📊 Status') // Icon for Status
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

                // From Store Column with Icon
                Tables\Columns\TextColumn::make('senderStore.name')
                    ->label('🏪 From Store') // Icon for From Store
                    ->sortable(),

                // To Store Column with Icon
                Tables\Columns\TextColumn::make('receiverStore.name')
                    ->label('🏪 To Store') // Icon for To Store
                    ->sortable(),

                // Created By Column with Icon
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('👤 Created By') // Icon for Created By
                    ->searchable()
                    ->sortable(),

                // Paid At Column with Icon
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('💳 Paid At') // Icon for Paid At
                    ->placeholder('Not Paid')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                // Created At Column with Icon
                Tables\Columns\TextColumn::make('created_at')
                    ->label('📅 Created') // Icon for Created At
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Updated At Column with Icon
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('🔄 Updated') // Icon for Updated At
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            // Actions (View, Edit, Pay, Cancel)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View'), // Icon for View action

                Tables\Actions\EditAction::make()
                    ->label('Edit') // Icon for Edit action
                    ->visible(fn ($record) => $record->status === 'in_progress'),

                Tables\Actions\Action::make('pay')
                    ->label('💵 Pay') // Icon for Pay action
                    ->action(function ($record) {
                        $record->pay();
                    })
                    ->visible(fn ($record) => is_null($record->paid_at) && $record->status === 'in_progress')
                    ->requiresConfirmation(),

                Tables\Actions\Action::make('cancel')
                    ->label('❌ Cancel') // Icon for Cancel action
                    ->color('warning')
                    ->action(function ($record) {
                        $record->refund();
                    })
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'in_progress'),
            ])

            // Bulk Actions (Delete)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('🗑️ Delete'), // Icon for Delete action
                ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            SenderStoreRelationManager::class,
            ReceiverStoreRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHawlas::route('/'),
            'create' => Pages\CreateHawla::route('/create'),
            'view' => Pages\ViewHawla::route('/{record}'),
            'edit' => Pages\EditHawla::route('/{record}/edit'),
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
