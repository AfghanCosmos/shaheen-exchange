<?php

namespace App\Filament\Store\Resources\HawlaResource\Pages;

use App\Filament\Store\Resources\HawlaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHawlas extends ListRecords
{
    protected static string $resource = HawlaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
