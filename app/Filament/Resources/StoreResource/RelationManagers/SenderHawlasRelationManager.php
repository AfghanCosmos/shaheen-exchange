<?php

namespace App\Filament\Resources\StoreResource\RelationManagers;

use App\Filament\Resources\HawlaResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SenderHawlasRelationManager extends RelationManager
{
    protected static string $relationship = 'senderHawlas';

    public function form(Form $form): Form
    {
        return HawlaResource::form($form); // Delegate to HawlaResource

    }

    public function table(Table $table): Table
    {
        return HawlaResource::table($table) // Reuse table schema

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
