<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommissionTypeResource\Pages;
use App\Filament\Resources\CommissionTypeResource\RelationManagers\StoreCommissionsRelationManager;
use App\Models\CommissionType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CommissionTypeResource extends Resource
{
    protected static ?string $model = CommissionType::class;

    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Commission Types';
    protected static ?string $pluralModelLabel = 'Commission Types';
    protected static ?string $modelLabel = 'Commission Type';

    // ------------------------------
    // 📝 Form
    // ------------------------------
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('💼 Commission Type Details')
                ->description('Provide a unique and descriptive commission type.')
                ->icon('heroicon-o-pencil')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->placeholder('e.g., Sales Bonus, Referral Fee')
                        ->required()
                        ->maxLength(255)
                        ->autofocus()
                        ->prefixIcon('heroicon-o-currency-dollar'),
                ]),
        ]);
    }

    // ------------------------------
    // 📋 Table
    // ------------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('💼 Type Name')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-document-text'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('📅 Created')
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar-days'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('🔄 Updated')
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('View'),
                Tables\Actions\EditAction::make()->label('Edit'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('🗑️ Delete Selected'),
                ]),
            ]);
    }

    // ------------------------------
    // ℹ️ Infolist
    // ------------------------------
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfoSection::make('💼 Commission Type Info')
                ->columns(3)
                ->schema([
                    TextEntry::make('name')
                        ->label('Name')
                        ->icon('heroicon-o-currency-dollar')
                        ->weight('medium'),

                    TextEntry::make('created_at')
                        ->label('Created At')
                        ->icon('heroicon-o-calendar-days')
                        ->dateTime(),

                    TextEntry::make('updated_at')
                        ->label('Last Updated')
                        ->icon('heroicon-o-clock')
                        ->dateTime(),
                ]),
        ]);
    }

    // ------------------------------
    // 🧩 Relations
    // ------------------------------
    public static function getRelations(): array
    {
        return [
            StoreCommissionsRelationManager::class,
        ];
    }

    // ------------------------------
    // 🧭 Pages
    // ------------------------------
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommissionTypes::route('/'),
            'create' => Pages\CreateCommissionType::route('/create'),
            //'view' => Pages\ViewCommissionType::route('/{record}'),
            'edit' => Pages\EditCommissionType::route('/{record}/edit'),
        ];
    }
}
