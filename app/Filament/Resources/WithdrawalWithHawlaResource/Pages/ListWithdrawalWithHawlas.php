<?php

namespace App\Filament\Resources\WithdrawalWithHawlaResource\Pages;

use App\Filament\Resources\WithdrawalWithHawlaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWithdrawalWithHawlas extends ListRecords
{
    protected static string $resource = WithdrawalWithHawlaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
