<?php

namespace App\Filament\Resources\HawlaStatusResource\Pages;

use App\Filament\Resources\HawlaStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHawlaStatuses extends ListRecords
{
    protected static string $resource = HawlaStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
