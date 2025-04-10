<?php

namespace App\Filament\Store\Resources\WithdrawalRequestResource\Pages;

use App\Filament\Store\Resources\WithdrawalRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWithdrawalRequests extends ListRecords
{
    protected static string $resource = WithdrawalRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
