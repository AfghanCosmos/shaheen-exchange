<?php

namespace App\Filament\Resources\CustomerTypeResource\Pages;

use App\Filament\Resources\CustomerTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomerType extends ViewRecord
{
    protected static string $resource = CustomerTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
