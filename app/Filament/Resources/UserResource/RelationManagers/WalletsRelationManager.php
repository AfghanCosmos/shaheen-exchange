<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\WalletResource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class WalletsRelationManager extends RelationManager
{
    protected static string $relationship = 'wallets';
    protected static ?string $title = 'User Wallets';

    public function form(Form $form): Form
    {
        return WalletResource::form($form);
    }

    public function table(Table $table): Table
    {
        return WalletResource::table($table)
            ->filters([
            //
            ])
            ->headerActions([
            Tables\Actions\CreateAction::make(),
            ])
            ->actions([
            Tables\Actions\EditAction::make(),
            ])

            ->modifyQueryUsing(function (Builder $query) {
            return $query->where('owner_id', $this->ownerRecord->id);
            });
        }
}
