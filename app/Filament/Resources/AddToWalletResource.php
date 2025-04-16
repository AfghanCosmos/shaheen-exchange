<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AddToWalletResource\Pages;
use App\Filament\Resources\AddToWalletResource\RelationManagers;
use App\Models\AddToWallet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AddToWalletResource extends Resource
{
    protected static ?string $model = AddToWallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('uuid')
                    ->label('UUID')
                    ->required()
                    ->maxLength(36),
                Forms\Components\TextInput::make('owner_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('owner_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('currency_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('giver_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('add_by')
                    ->numeric()
                    ->default(null),
                Forms\Components\DateTimePicker::make('approved_at'),
                Forms\Components\TextInput::make('commission_amount')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\Toggle::make('verified_by_store')
                    ->required(),
                Forms\Components\Textarea::make('details')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('owner_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('owner_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('giver_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('add_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('commission_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('verified_by_store')
                    ->boolean(),
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
            'index' => Pages\ListAddToWallets::route('/'),
            'create' => Pages\CreateAddToWallet::route('/create'),
            'view' => Pages\ViewAddToWallet::route('/{record}'),
            'edit' => Pages\EditAddToWallet::route('/{record}/edit'),
        ];
    }
}
