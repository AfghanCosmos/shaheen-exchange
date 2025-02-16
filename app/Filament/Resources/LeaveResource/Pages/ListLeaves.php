<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use App\Filament\Resources\LeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeaves extends ListRecords
{
    protected static string $resource = LeaveResource::class;

    use  \EightyNine\Approvals\Traits\HasApprovalHeaderActions;


    /**
     * Get the completion action.
     *
     * @return Filament\Actions\Action
     * @throws Exception
     */


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
