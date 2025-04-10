<?php

namespace App\Filament\Store\Resources\HawlaResource\RelationManagers;

use App\Filament\Store\Resources\StoreResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SenderStoreRelationManager extends RelationManager
{
    protected static string $relationship = 'senderStore';

    public function form(Form $form): Form
    {
        return StoreResource::form($form);

    }

    public function table(Table $table): Table
    {
            return StoreResource::table($table)

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
