<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawalRequestResource\Pages;
use App\Filament\Resources\WithdrawalRequestResource\RelationManagers;
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

class WithdrawalRequestResource extends Resource
{
    protected static ?string $model = WithdrawalRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Finance Management';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('uuid')
                    ->disabled()
                    ->required(),

                Select::make('store_id')
                    ->relationship('store', 'name')
                    ->searchable()
                    ->required(),

                Select::make('offline_transfer_id')
                    ->relationship('offlineTransfer', 'uuid')
                    ->searchable()
                    ->nullable(),

                Select::make('receiver_wallet_id')
                    ->relationship('wallet', 'uuid')
                    ->searchable()
                    ->nullable(),

                TextInput::make('receiver_name')
                    ->required(),

                TextInput::make('receiver_verification_id')
                    ->required(),

                TextInput::make('amount')
                    ->numeric()
                    ->required(),

                Select::make('currency_id')
                    ->relationship('currency', 'code')
                    ->required(),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
                    ->default('pending'),

                TextInput::make('commission_amount')
                    ->numeric()
                    ->default(0),

                DateTimePicker::make('approved_at')
                    ->nullable(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('uuid')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('store.name')
                    ->label('Store')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('receiver_name')
                    ->label('Receiver Name')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'completed' => 'success',
                        'failed' => 'danger',
                    ]),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->sortable(),

                TextColumn::make('commission_amount')
                    ->label('Commission')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('d-M-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn (WithdrawalRequest $record) => $record->update([
                        'status' => 'approved',
                        'approved_at' => now(),
                        'admin_id' => auth()->id()
                    ]))
                    ->visible(fn ($record) => $record->status === 'pending'),

                Action::make('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->action(fn (WithdrawalRequest $record) => $record->update([
                        'status' => 'rejected',
                        'admin_id' => auth()->id()
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
