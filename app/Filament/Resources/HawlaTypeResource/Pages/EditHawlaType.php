<?php

namespace App\Filament\Resources\HawlaTypeResource\Pages;

use App\Filament\Resources\HawlaTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHawlaType extends EditRecord
{
    protected static string $resource = HawlaTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
