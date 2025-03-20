<?php

namespace App\Filament\Resources\OfflineTransferResource\Pages;

use App\Filament\Resources\OfflineTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOfflineTransfer extends EditRecord
{
    protected static string $resource = OfflineTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
