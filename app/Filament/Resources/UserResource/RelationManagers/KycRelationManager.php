<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\KYC;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Laravel\Prompts\form;

class KycRelationManager extends RelationManager
{
    protected static string $relationship = 'kyc';

    public function form(Form $form): Form
    {
        return KYC::form($form);
    }

    public function table(Table $table): Table
    {
        return KYC::table($table)
        
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
