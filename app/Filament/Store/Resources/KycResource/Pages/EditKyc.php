<?php

namespace App\Filament\Store\Resources\KycResource\Pages;

use App\Filament\Store\Resources\KycResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKyc extends EditRecord
{
    protected static string $resource = KycResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Prevent user_id tampering
        $data['user_id'] = $this->record->user_id;

        return $data;
    }
}
