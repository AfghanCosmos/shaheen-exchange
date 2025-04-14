<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferralResource\Pages;
use App\Filament\Resources\ReferralResource\RelationManagers;
use App\Models\Referral;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReferralResource extends Resource
{
    protected static ?string $model = Referral::class;
    protected static ?string $navigationGroup = "Customer Management";


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Referral Information')
                    ->schema([
                Forms\Components\Select::make('referrer_id')
                    ->label('Referrer')
                    ->relationship('referrer', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('referred_user_id')
                    ->label('Referred User')
                    ->relationship('referredUser', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('currency_id')
                    ->label('Currency')
                    ->relationship('currency', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('reward_amount')
                    ->label('Reward Amount')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Pending',
                        'credited' => 'Credited',
                        'failed'   => 'Failed',
                    ])
                    ->default('pending')
                    ->required(),
                Forms\Components\DateTimePicker::make('credited_at')
                    ->label('Credited At')
                    ->displayFormat('Y-m-d H:i')
                    ->nullable(),
    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('referrer.name')

                    ->sortable(),
                Tables\Columns\TextColumn::make('referredUser.name')

                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.name')

                    ->sortable(),
                Tables\Columns\TextColumn::make('reward_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                ->badge(),
                Tables\Columns\TextColumn::make('credited_at')
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
            'index' => Pages\ListReferrals::route('/'),
            'create' => Pages\CreateReferral::route('/create'),
            'view' => Pages\ViewReferral::route('/{record}'),
            'edit' => Pages\EditReferral::route('/{record}/edit'),
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
