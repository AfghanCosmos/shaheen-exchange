<?php

namespace App\Filament\Store\Resources\TransferGenerateResource\Pages;

use App\Filament\Store\Resources\TransferGenerateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTransferGenerate extends ViewRecord
{
    protected static string $resource = TransferGenerateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
