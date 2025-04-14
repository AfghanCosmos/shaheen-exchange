<?php

namespace App\Filament\Resources\CommissionTypeResource\RelationManagers;

use App\Filament\Resources\StoreCommissionResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class StoreCommissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'storeCommissions';

    public function form(Form $form): Form
    {
        return StoreCommissionResource::form($form);
    }

    public function table(Table $table): Table
    {
        return StoreCommissionResource::table($table)
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

