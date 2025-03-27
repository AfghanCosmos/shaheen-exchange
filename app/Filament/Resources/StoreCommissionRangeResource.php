<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreCommissionRangeResource\Pages;
use App\Filament\Resources\StoreCommissionRangeResource\RelationManagers;
use App\Models\StoreCommissionRange;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StoreResource\RelationManagers\StoreCommissionRangesRelationManager;



class StoreCommissionRangeResource extends Resource
{
    protected static ?string $model = StoreCommissionRange::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListStoreCommissionRanges::route('/'),
            'create' => Pages\CreateStoreCommissionRange::route('/create'),
            'edit' => Pages\EditStoreCommissionRange::route('/{record}/edit'),
        ];
    }
}
