<?php

namespace App\Filament\Store\Resources\KycResource\Pages;

use App\Filament\Store\Resources\KycResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKyc extends CreateRecord
{
    protected static string $resource = KycResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id(); // Force the current user
        return $data;
    }
}
