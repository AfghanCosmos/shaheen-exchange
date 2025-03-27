<?php

namespace App\Filament\Resources\HawlaTypeResource\Pages;

use App\Filament\Resources\HawlaTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHawlaTypes extends ListRecords
{
    protected static string $resource = HawlaTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
