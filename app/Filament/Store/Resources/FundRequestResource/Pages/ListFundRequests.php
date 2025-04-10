<?php

namespace App\Filament\Store\Resources\FundRequestResource\Pages;

use App\Filament\Store\Resources\FundRequestResource;
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
