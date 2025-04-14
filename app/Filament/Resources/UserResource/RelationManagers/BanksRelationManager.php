<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\BankAccountResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BanksRelationManager extends RelationManager
{
    protected static string $relationship = 'banks';
    protected static ?string $title = 'User Bank Accounts';

    public function form(Form $form): Form
    {
        return BankAccountResource::form($form);
    }

    public function table(Table $table): Table
    {
        return BankAccountResource::table($table)
            ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', $this->getOwnerRecord()->id))
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        $data['user_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
