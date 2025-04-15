<?php

namespace App\Filament\Store\Resources\WalletResource\Pages;

use App\Filament\Store\Resources\WalletResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\Wallet;
class CreateWallet extends CreateRecord
{
    protected static string $resource = WalletResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
{
    $user = auth()->user();
    $storeId = $user?->store?->id;

    if (!$storeId) {
        Notification::make()
            ->title('Creation Error')
            ->body('You do not have an assigned store. Please contact admin.')
            ->danger()
            ->send();

        $this->halt();
    }

    // Ensure the user does not already have a wallet with the same currency
    $existingWallet = Wallet::where('owner_type', 'App\Models\User')
        ->where('owner_id', $data['owner_id']) // use user_id from form
        ->where('currency_id', $data['currency_id']) // check for same currency
        ->exists();

    if ($existingWallet) {
        Notification::make()
            ->title('Wallet Exists')
            ->body('This user already has a wallet in this currency.')
            ->danger()
            ->send();

        $this->halt();
    }

    $data['owner_type'] = 'App\Models\User';
    $data['owner_id'] = $data['owner_id'];

    return $data;
}

}
