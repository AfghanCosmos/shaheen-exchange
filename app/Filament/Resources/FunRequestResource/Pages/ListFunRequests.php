<?php

namespace App\Filament\Resources\FunRequestResource\Pages;

use App\Filament\Resources\FunRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFunRequests extends ListRecords
{
    protected static string $resource = FunRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
