<?php

namespace App\Filament\Resources\WithdrawalWithHawlaResource\Pages;

use App\Filament\Resources\WithdrawalWithHawlaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWithdrawalWithHawla extends EditRecord
{
    protected static string $resource = WithdrawalWithHawlaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
