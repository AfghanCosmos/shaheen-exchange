<?php

namespace App\Filament\Store\Resources;

use App\Filament\Store\Resources\ReferralResource\Pages;
use App\Filament\Store\Resources\ReferralResource\RelationManagers;
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
                Forms\Components\Select::make('referred_user_id')
                    ->relationship(
                        name: 'referredUser',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('id', '!=', auth()->id())
                    )
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('reward_amount')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'credited' => 'Credited',
                        'failed' => 'Failed',
                    ])
                    ->default('pending')
                    ->required(),
                Forms\Components\DateTimePicker::make('credited_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
        ->columns([
            /** 🙋 Referrer */
            Tables\Columns\TextColumn::make('referrer.name')
                ->label('🙋 Referrer')
                ->sortable()
                ->searchable()
                ->badge()
                ->color('primary'),

            /** 🧑 Referred User */
            Tables\Columns\TextColumn::make('referredUser.name')
                ->label('👤 Referred User')
                ->sortable()
                ->searchable()
                ->badge()
                ->color('info'),

            /** 💰 Reward Amount */
            Tables\Columns\TextColumn::make('reward_amount')
                ->label('💰 Reward')
                ->numeric(decimalPlaces: 2, decimalSeparator: '.', thousandsSeparator: ',')
                ->sortable()
                ->color('success'),

            /** 📌 Status */
            Tables\Columns\TextColumn::make('status')
                ->label('📌 Status')
                ->badge()
                ->sortable()
                ->color(fn ($state) => match ($state) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'declined' => 'danger',
                    default => 'gray',
                }),

            /** 📆 Credited Date */
            Tables\Columns\TextColumn::make('credited_at')
                ->label('📅 Credited At')
                ->dateTime('M d, Y H:i')
                ->sortable(),

            /** 🕒 Created */
            Tables\Columns\TextColumn::make('created_at')
                ->label('🕒 Created')
                ->dateTime('M d, Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            /** 🔄 Updated */
            Tables\Columns\TextColumn::make('updated_at')
                ->label('🔄 Updated')
                ->dateTime('M d, Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            /** 🗑️ Soft Deleted */
            Tables\Columns\TextColumn::make('deleted_at')
                ->label('🗑️ Deleted')
                ->dateTime('M d, Y H:i')
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
            ])
            ->where('referrer_id', auth()->id()); // ✅ Only show records where the current user is the referrer
    }
}
