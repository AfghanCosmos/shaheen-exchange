<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferralResource\Pages;
use App\Filament\Resources\ReferralResource\RelationManagers;
use App\Models\Referral;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReferralResource extends Resource
{
    protected static ?string $model = Referral::class;
    protected static ?string $navigationGroup = "Customer Management";


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('🎁 Referral Details')
                ->icon('heroicon-o-user-plus')
                ->description('Link between referrer and referred user')
                ->columns(3)
                ->schema([

                    Forms\Components\Select::make('referrer_id')
                        ->label('🙋 Referrer')
                        ->relationship('referrer', 'name')
                        ->searchable()
                        ->required(),

                    Forms\Components\Select::make('referred_user_id')
                        ->label('👤 Referred User')
                        ->relationship('referredUser', 'name')
                        ->searchable()
                        ->required(),

                    Forms\Components\Select::make('currency_id')
                        ->label('💱 Currency')
                        ->relationship('currency', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\TextInput::make('reward_amount')
                        ->label('💰 Reward Amount')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->placeholder('e.g., 10.00'),

                    Forms\Components\Select::make('status')
                        ->label('📌 Status')
                        ->options([
                            'pending'  => 'Pending',
                            'credited' => 'Credited',
                            'failed'   => 'Failed',
                        ])
                        ->default('pending')
                        ->native(false)
                        ->required(),

                    Forms\Components\DateTimePicker::make('credited_at')
                        ->label('✅ Credited At')
                        ->displayFormat('Y-m-d H:i')
                        ->placeholder('Optional')
                        ->nullable(),
                ]),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('referrer.name')
                    ->label('🙋 Referrer')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('referredUser.name')
                    ->label('👤 Referred User')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('currency.code')
                    ->label('💱 Currency')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('reward_amount')
                    ->label('💰 Reward')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('📌 Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'credited',
                        'danger' => 'failed',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('credited_at')
                    ->label('✅ Credited At')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('📅 Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('🔄 Updated')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('🗑️ Deleted At')
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
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('🎁 Referral Summary')
                ->columns(3)
                ->schema([
                    TextEntry::make('referrer.name')->label('🙋 Referrer'),
                    TextEntry::make('referredUser.name')->label('👤 Referred User'),
                    TextEntry::make('currency.code')->label('💱 Currency')->badge()->color('gray'),
                    TextEntry::make('reward_amount')->label('💰 Reward')->money(fn ($record) => $record->currency?->code ?? 'USD'),
                    TextEntry::make('status')->label('📌 Status')->badge()->color(fn ($state) => match($state) {
                        'pending' => 'warning',
                        'credited' => 'success',
                        'failed' => 'danger',
                    }),
                    TextEntry::make('credited_at')->label('✅ Credited At')->dateTime('Y-m-d H:i'),
                    TextEntry::make('created_at')->label('📅 Created At')->dateTime(),
                    TextEntry::make('updated_at')->label('🔄 Updated At')->dateTime(),
                ]),
        ]);
    }


    public static function getRelations(): array
    {
        return [
            
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
