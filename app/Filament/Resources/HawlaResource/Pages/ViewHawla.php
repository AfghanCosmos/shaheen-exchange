<?php

namespace App\Filament\Resources\HawlaResource\Pages;

use App\Filament\Resources\HawlaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHawla extends ViewRecord
{
    protected static string $resource = HawlaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
