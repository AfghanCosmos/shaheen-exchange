<?php

namespace App\Filament\Resources\CommissionTypeResource\Pages;

use App\Filament\Resources\CommissionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommissionType extends EditRecord
{
    protected static string $resource = CommissionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
