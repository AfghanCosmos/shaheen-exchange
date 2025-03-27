<?php

namespace App\Filament\Resources\HawlaResource\Pages;

use App\Filament\Resources\HawlaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHawla extends EditRecord
{
    protected static string $resource = HawlaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
