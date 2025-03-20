<?php

namespace App\Filament\Resources\TransferGenerateResource\Pages;

use App\Filament\Resources\TransferGenerateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransferGenerate extends EditRecord
{
    protected static string $resource = TransferGenerateResource::class;

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
