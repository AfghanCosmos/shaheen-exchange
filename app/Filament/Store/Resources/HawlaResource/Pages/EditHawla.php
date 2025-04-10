<?php

namespace App\Filament\Store\Resources\HawlaResource\Pages;

use App\Filament\Store\Resources\HawlaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHawla extends EditRecord
{
    protected static string $resource = HawlaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
