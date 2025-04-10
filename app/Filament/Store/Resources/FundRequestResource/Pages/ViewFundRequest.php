<?php

namespace App\Filament\Store\Resources\FundRequestResource\Pages;

use App\Filament\Store\Resources\FundRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFundRequest extends ViewRecord
{
    protected static string $resource = FundRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
