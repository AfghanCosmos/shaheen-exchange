<?php

namespace App\Filament\Store\Resources;

use App\Filament\Store\Resources\TransactionResource\Pages;
use App\Filament\Store\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use App\Models\BankAccount;
use App\Models\Wallet;
use Filament\Forms\Get;
use Illuminate\Support\Collection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationGroup = 'Transaction';
    protected static ?string $navigationLabel = 'Deposit';


    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Select::make('wallet_id')
                        ->label('Wallet')
                        ->required()
                        ->relationship('wallet', 'uuid')
                        ->searchable()
                      ->live()
                        ->placeholder('Select Wallet'),
                        Forms\Components\Select::make('currency_id')
                            ->label('Currency')
                            ->disabled(


                            )
                            ->relationship('currency', 'id')
                            ->default(fn (Get $get) => optional(Wallet::find($get('wallet_id')))->currency_id)
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->code}")
                            ->dehydrated(true),
                ])
                ->columns(2),

            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\TextInput::make('amount')
                        ->label('Amount')
                        ->required()
                        ->numeric()
                        ->placeholder('Enter Amount'),

                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->required()
                        ->options([
                            'pending' => 'Pending',
                            'completed' => 'Completed',
                            'failed' => 'Failed',
                        ])
                        ->placeholder('Select Status'),

                    Forms\Components\TextInput::make('payment_gateway')
                        ->label('Payment Gateway')
                        ->maxLength(255)
                        ->nullable()
                        ->placeholder('Enter Payment Gateway'),

                    Forms\Components\TextInput::make('reference_id')
                        ->label('Reference ID')
                        ->maxLength(255)
                        ->nullable()
                        ->placeholder('Enter Reference ID'),
                ])
                ->columns(2),

            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Select::make('source')
                        ->label('Transaction Source')
                        ->required()
                        ->options([
                            'manual' => 'Manual',
                            'card' => 'Card',
                            'bank' => 'Bank',
                            'crypto' => 'Crypto',
                            'referral' => 'Referral',
                        ])
                        ->placeholder('Select Source'),

                    Forms\Components\Select::make('referral_id')
                        ->label('Referral')
                        ->relationship('referral', 'id')
                        ->nullable()
                        ->searchable()
                        ->placeholder('Select Referral'),

                    Forms\Components\Select::make('bank_account_id')
                        ->label('Bank Account')
                        // ->relationship('bankAccount', 'account_number')
                        ->nullable()
                        ->searchable()
                        ->placeholder('Select Bank Account')
                        ->options(fn (Get $get): Collection => BankAccount::query()
                            ->where('user_id', Wallet::find($get('wallet_id'))?->owner_id) // Filter bank accounts by wallet's owner_id
                            ->pluck('account_number', 'bank_name')),
                ])
                ->columns(3),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('wallet.uuid')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('payment_gateway')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reference_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('source'),
                Tables\Columns\TextColumn::make('referral.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bank.bank_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
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
