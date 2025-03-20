<?php

namespace App\Filament\Resources\FunRequestResource\Pages;

use App\Filament\Resources\FunRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFunRequest extends ViewRecord
{
    protected static string $resource = FunRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
