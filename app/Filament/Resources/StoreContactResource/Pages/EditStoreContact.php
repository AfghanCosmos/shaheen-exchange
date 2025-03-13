<?php

namespace App\Filament\Resources\StoreContactResource\Pages;

use App\Filament\Resources\StoreContactResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStoreContact extends EditRecord
{
    protected static string $resource = StoreContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
