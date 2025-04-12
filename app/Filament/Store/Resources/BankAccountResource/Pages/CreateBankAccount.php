<?php

namespace App\Filament\Store\Resources\BankAccountResource\Pages;

use App\Filament\Store\Resources\BankAccountResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBankAccount extends CreateRecord
{
    protected static string $resource = BankAccountResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $userId = auth()->user()?->id;

        if (!$userId) {
            Notification::make()
                ->title('Creation Error')
                ->body('No authenticated user found.')
                ->danger()
                ->send();

            $this->halt(); // Prevent creation
        }

        $data['user_id'] = $userId;

        return $data;
    }
}
