<?php

namespace App\Filament\Store\Resources\HawlaResource\Pages;

use App\Filament\Store\Resources\HawlaResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateHawla extends CreateRecord
{
    protected static string $resource = HawlaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $storeId = auth()->user()?->store?->id;

        if (!$storeId) {
            Notification::make()
                ->title('Creation Error')
                ->body('You do not have an assigned store. Please contact admin.')
                ->danger()
                ->send();

            $this->halt(); // stop creation process
        }

        $data['sender_store_id'] = $storeId;

        return $data;
    }
}
