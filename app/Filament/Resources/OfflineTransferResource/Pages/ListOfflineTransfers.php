<?php

namespace App\Filament\Resources\OfflineTransferResource\Pages;

use App\Filament\Resources\OfflineTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOfflineTransfers extends ListRecords
{
    protected static string $resource = OfflineTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
