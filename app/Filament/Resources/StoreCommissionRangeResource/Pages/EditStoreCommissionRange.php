<?php

namespace App\Filament\Resources\StoreCommissionRangeResource\Pages;

use App\Filament\Resources\StoreCommissionRangeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStoreCommissionRange extends EditRecord
{
    protected static string $resource = StoreCommissionRangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
