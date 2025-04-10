<?php

namespace App\Filament\Store\Resources\ExpenseCategoryResource\Pages;

use App\Filament\Store\Resources\ExpenseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExpenseCategory extends CreateRecord
{
    protected static string $resource = ExpenseCategoryResource::class;
}
