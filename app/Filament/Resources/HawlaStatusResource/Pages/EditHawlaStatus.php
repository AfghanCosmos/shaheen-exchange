<?php

namespace App\Filament\Resources\HawlaStatusResource\Pages;

use App\Filament\Resources\HawlaStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHawlaStatus extends EditRecord
{
    protected static string $resource = HawlaStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
