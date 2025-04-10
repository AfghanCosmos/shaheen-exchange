<?php

namespace App\Filament\Store\Resources\WithdrawalRequestResource\Pages;

use App\Filament\Store\Resources\WithdrawalRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWithdrawalRequest extends EditRecord
{
    protected static string $resource = WithdrawalRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
