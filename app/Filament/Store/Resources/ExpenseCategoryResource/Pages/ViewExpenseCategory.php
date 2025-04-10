<?php

namespace App\Filament\Store\Resources\ExpenseCategoryResource\Pages;

use App\Filament\Store\Resources\ExpenseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewExpenseCategory extends ViewRecord
{
    protected static string $resource = ExpenseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
