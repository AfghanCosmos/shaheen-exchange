<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawalWithHawlaResource\Pages;
use App\Filament\Resources\WithdrawalWithHawlaResource\RelationManagers;
use App\Models\WithdrawalWithHawla;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WithdrawalWithHawlaResource extends Resource
{
    protected static ?string $model = WithdrawalWithHawla::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('uuid')
                    ->label('UUID')
                    ->required()
                    ->maxLength(36),
                Forms\Components\TextInput::make('hawla_type_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('customer_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('receiver_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('receiver_father')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('sender_store_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('given_amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('wallet_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('receiving_amount_currency_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('receiving_amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('exchange_rate')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('commission')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('commission_taken_by')
                    ->required(),
                Forms\Components\TextInput::make('receiver_phone_number')
                    ->tel()
                    ->maxLength(20)
                    ->default(null),
                Forms\Components\Textarea::make('receiver_address')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('receiver_store_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('note')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('created_by')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('receiver_verification_document')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\DateTimePicker::make('paid_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hawla_type_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('receiver_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('receiver_father')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sender_store_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('given_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('wallet_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('receiving_amount_currency_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('receiving_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('exchange_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('commission')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('commission_taken_by'),
                Tables\Columns\TextColumn::make('receiver_phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('receiver_store_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('receiver_verification_document')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime()
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListWithdrawalWithHawlas::route('/'),
            'create' => Pages\CreateWithdrawalWithHawla::route('/create'),
            'view' => Pages\ViewWithdrawalWithHawla::route('/{record}'),
            'edit' => Pages\EditWithdrawalWithHawla::route('/{record}/edit'),
        ];
    }
}
