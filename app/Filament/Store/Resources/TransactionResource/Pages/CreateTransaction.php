<?php

namespace App\Filament\Store\Resources\TransactionResource\Pages;

use App\Filament\Store\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;
}
