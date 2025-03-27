<?php

namespace App\Filament\Resources\StoreResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Filament\Resources\StoreCommissionRangeResource;

class StoreCommissionRangesRelationManager extends RelationManager
{
    protected static string $relationship = 'storeCommissionRanges';

    public function form(Form $form): Form
    {
        return StoreCommissionRangeResource::form($form);
    }

    public function table(Table $table): Table
    {
        return StoreCommissionRangeResource::table($table)
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
