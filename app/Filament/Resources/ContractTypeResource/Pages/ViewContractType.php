<?php

namespace App\Filament\Resources\ContractTypeResource\Pages;

use App\Filament\Resources\ContractTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContractType extends ViewRecord
{
    protected static string $resource = ContractTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
