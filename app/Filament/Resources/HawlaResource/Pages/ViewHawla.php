<?php

namespace App\Filament\Resources\HawlaResource\Pages;

use App\Filament\Resources\HawlaResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewHawla extends ViewRecord
{
    protected static string $resource = HawlaResource::class;

    public function getHeaderActions(): array
{
    return [
        Action::make('print')
            ->label('Print Receipt')
            ->icon('heroicon-o-printer')
            ->color('primary')
            ->url(fn () => route('hawla.print', ['hawla' => $this->record]))
            ->openUrlInNewTab(), // optional
    ];
}

}
