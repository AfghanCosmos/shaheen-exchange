<?php

namespace App\Filament\Resources\FundRequestResource\Pages;

use App\Filament\Resources\FundRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFundRequests extends ListRecords
{
    protected static string $resource = FundRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
