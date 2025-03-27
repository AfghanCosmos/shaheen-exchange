<?php

namespace App\Filament\Resources\HawlaStatusResource\Pages;

use App\Filament\Resources\HawlaStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHawlaStatus extends ViewRecord
{
    protected static string $resource = HawlaStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
