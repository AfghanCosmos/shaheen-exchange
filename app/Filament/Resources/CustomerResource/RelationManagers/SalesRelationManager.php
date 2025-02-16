<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Filament\Resources\SaleResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesRelationManager extends RelationManager
{
    protected static string $relationship = 'sales';

    public function form(Form $form): Form
    {
        return SaleResource::form($form);
    }

    public function table(Table $table): Table
    {
        return SaleResource::table($table)
        ->headerActions([
            Tables\Actions\CreateAction::make(),
        ]);

    }
}
