<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\HawlaResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CreatedHawlasRelationManager extends RelationManager
{
    protected static string $relationship = 'createdHawlas';
    protected static ?string $title = 'Created Hawalas';

    public function form(Form $form): Form
    {
        return HawlaResource::form($form);
    }

    public function table(Table $table): Table
    {
        return HawlaResource::table($table)
            ->modifyQueryUsing(fn (Builder $query) => $query->where('created_by', $this->getOwnerRecord()->id))
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        $data['created_by'] = $this->getOwnerRecord()->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
