<?php

namespace App\Filament\Resources\TransferGenerateResource\Pages;

use App\Filament\Resources\TransferGenerateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransferGenerates extends ListRecords
{
    protected static string $resource = TransferGenerateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
