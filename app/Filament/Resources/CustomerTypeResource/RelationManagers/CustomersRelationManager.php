<?php

namespace App\Filament\Resources\CustomerTypeResource\RelationManagers;

use App\Filament\Resources\CustomerResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomersRelationManager extends RelationManager
{
    protected static string $relationship = 'customers';

    public function form(Form $form): Form
    {
        return CustomerResource::form($form);
    }
    public function table(Table $table): Table
    {
        return CustomerResource::table($table);
    }
}
