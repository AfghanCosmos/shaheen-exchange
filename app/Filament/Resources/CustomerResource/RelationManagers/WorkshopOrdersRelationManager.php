<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Filament\Resources\WorkOrderResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkshopOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'workshopOrders';

    public function form(Form $form): Form
    {
        return WorkOrderResource::form($form);
    }

    public function table(Table $table): Table
    {
        return WorkOrderResource::table($table)

            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
           ;
    }
}
