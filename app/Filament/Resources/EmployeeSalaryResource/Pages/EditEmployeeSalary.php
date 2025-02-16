<?php

namespace App\Filament\Resources\EmployeeSalaryResource\Pages;

use App\Filament\Resources\EmployeeSalaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeSalary extends EditRecord
{
    protected static string $resource = EmployeeSalaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
{
    $data['basic_salary'] = \App\Models\User::find($data['user_id'])?->salary ?? 0;
    $data['net_salary'] = $data['basic_salary'] + ($data['bonus'] ?? 0) - ($data['deductions'] ?? 0);

    return $data;
}

}
