<?php

namespace App\Filament\Store\Resources\TransferResource\Pages;

use App\Filament\Store\Resources\TransferResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTransfer extends ViewRecord
{
    protected static string $resource = TransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
