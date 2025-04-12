<?php

namespace App\Filament\Store\Resources\BankAccountResource\Pages;

use App\Filament\Store\Resources\BankAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBankAccount extends EditRecord
{
    protected static string $resource = BankAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = auth()->user()?->id;

        return $data;
    }
}
