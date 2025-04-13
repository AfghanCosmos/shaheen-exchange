<?php

namespace App\Filament\Store\Resources\ReferralResource\Pages;

use App\Filament\Store\Resources\ReferralResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReferral extends CreateRecord
{
    protected static string $resource = ReferralResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['referrer_id'] = auth()->id();
        return $data;
    }
}
