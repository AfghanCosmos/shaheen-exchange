<?php

namespace App\Filament\Resources\WithdrawalWithHawlaResource\Pages;

use App\Filament\Resources\WithdrawalWithHawlaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWithdrawalWithHawla extends ViewRecord
{
    protected static string $resource = WithdrawalWithHawlaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
