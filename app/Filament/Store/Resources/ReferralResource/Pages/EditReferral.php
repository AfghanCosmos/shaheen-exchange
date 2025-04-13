<?php

namespace App\Filament\Store\Resources\ReferralResource\Pages;

use App\Filament\Store\Resources\ReferralResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReferral extends EditRecord
{
    protected static string $resource = ReferralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Keep original referrer_id no matter what is submitted
        $data['referrer_id'] = $this->record->referrer_id;

        return $data;
    }
}
