<?php

namespace App\Filament\Store\Resources;

use App\Filament\Store\Resources\WithdrawalRequestResource\Pages;
use App\Filament\Store\Resources\WithdrawalRequestResource\RelationManagers;
use App\Models\WithdrawalRequest;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
class WithdrawalRequestResource extends Resource
{
    protected static ?string $model = WithdrawalRequest::class;

    // protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Finance Management';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->columns(3)
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
                            ->required(fn (callable $get, ?\App\Models\WithdrawalRequest $record) => is_null($record))
                            ->dehydrated(false)
                            ->reactive()
                            ->columnSpanFull()
                            ->placeholder('Select user'),

                        Select::make('wallet_id')
                            ->label('Wallet')
                            ->options(function (callable $get) {
                                $ownerId = $get('owner_id');
                                if (!$ownerId) {
                                    return [];
                                }
                                return \App\Models\Wallet::where('owner_type', 'App\Models\User')
                                    ->where('owner_id', $ownerId)
                                    ->get()
                                    ->mapWithKeys(function ($wallet) {
                                        $formattedAmount = number_format($wallet->amount, 2);
                                        $symbol = $wallet->currency->code ?? 'Af';
                                        return [
                                            $wallet->id => $wallet->uuid . ' (' . $symbol . ' ' . $formattedAmount . ')'
                                        ];
                                    })
                                    ->toArray();
                            })
                            ->getOptionLabelUsing(function ($value) {
                                $wallet = \App\Models\Wallet::find($value);
                                if ($wallet) {
                                    $formattedAmount = number_format($wallet->amount, 2);
                                    $symbol = $wallet->currency->code ?? 'Af';
                                    return $wallet->uuid . ' (' . $symbol . ' ' . $formattedAmount . ')';
                                }
                                return $value;
                            })
                            ->reactive()
                            ->searchable()
                            ->nullable(),


                TextInput::make('receiver_name')
                    ->required(),

                TextInput::make('amount')
                    ->numeric()
                    ->required(),

                Select::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'approved'  => 'Approved',
                        'rejected'  => 'Rejected',
                        'completed' => 'Completed',
                        'failed'    => 'Failed',
                    ])
                    ->default('pending'),

                DateTimePicker::make('approved_at')
                    ->nullable(),

                TextInput::make('commission_amount')
                    ->numeric()
                    ->default(0),

                Select::make('withdraw_by')
                    ->relationship(
                        'withdrawBy',
                        'name',
                        fn ($query) => $query->whereHas('store', fn($q) => $q->where('id', auth()->user()?->store->id))
                    )
                    ->label('Withdraw By')
                    ->searchable()
                ->columnSpanFull()
                    ->preload()
                    ->nullable(),



                Forms\Components\RichEditor::make('details')
                ->columnSpanFull()
                    ->nullable(),

                    Forms\Components\FileUpload::make('receiver_verification_id')
                        ->label('Receiver Verification ID')
                        ->directory('receiver_verification_ids')
                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                        ->columnSpanFull()
                        ->maxSize(5024) ,

                        Forms\Components\Toggle::make('verified_by_store')
                        ->default(false),

            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('withdrawBy', fn ($q) => $q->where('store_id', auth()->user()?->store?->id)))
            ->columns([
                TextColumn::make('uuid')
                    ->sortable()
                    ->toggleable()

                    ->searchable(),

                TextColumn::make('wallet_info')
                    ->label('Wallet')
                    ->toggleable()

                    ->getStateUsing(function ($record) {
                        if (! $record->wallet) {
                            return '';
                        }
                        $uuid = $record->wallet->uuid;
                        $currency = $record->wallet->currency->code ?? 'Af';
                        $formattedAmount = number_format($record->wallet->amount, 2);
                        return "{$uuid} ({$currency} {$formattedAmount})";
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('receiver_name')
                    ->label('Receiver Name')
                    ->toggleable()

                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->colors([
                        'pending'   => 'warning',
                        'approved'  => 'success',
                        'rejected'  => 'danger',
                        'completed' => 'success',
                        'failed'    => 'danger',
                    ]),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('commission_amount')
                    ->label('Commission')
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('withdrawBy.name')
                    ->label('Withdraw By')
                    ->sortable()
                    ->toggleable()

                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime('d-M-Y H:i')
                    ->toggleable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'approved'  => 'Approved',
                        'rejected'  => 'Rejected',
                        'completed' => 'Completed',
                        'failed'    => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn (WithdrawalRequest $record) => $record->update([
                        'status'      => 'approved',
                        'approved_at' => now(),
                        'withdraw_by' => auth()->id()
                    ]))
                    ->visible(fn ($record) => $record->status === 'pending'),

                Action::make('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->action(fn (WithdrawalRequest $record) => $record->update([
                        'status'      => 'rejected',
                        'withdraw_by' => auth()->id()
                    ]))
                    ->visible(fn ($record) => $record->status === 'pending'),
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
            'index' => Pages\ListWithdrawalRequests::route('/'),
            'create' => Pages\CreateWithdrawalRequest::route('/create'),
            'view' => Pages\ViewWithdrawalRequest::route('/{record}'),
            'edit' => Pages\EditWithdrawalRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
