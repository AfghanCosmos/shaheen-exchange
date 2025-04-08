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

            Action::make('send_whatsapp')
                ->label('Send WhatsApp')
                ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
                ->color('success')
                ->modalHeading('Enter Phone Number')
                ->modalSubheading('The record details will be sent via WhatsApp. You can include an optional custom message.')
                ->form([
                    \Filament\Forms\Components\TextInput::make('phone')
                        ->label('Phone Number')
                        ->tel()
                        ->required(),

                ])
                ->url(fn ($record) =>
                    'https://wa.me/' . preg_replace('/\D/', '', $record->sender_phone) . '?text=' . urlencode(
                        "Hawala Transfer Details:\n" .
                        "Hawala ID: {$record->uuid}\n" .
                        "Sender: {$record->sender_name}\n" .
                        "Receiver: {$record->receiver_name}\n" .
                        "Amount: {$record->receiving_amount} " . ($record->receivingCurrency->name ?? '-') . "\n" .
                        "Receiver Store: " . ($record->receiverStore->name ?? '-') . "\n" .
                        "Address: {$record->receiverStore->address}"
                    )
                )
                ->openUrlInNewTab(),

                Action::make('pay')
                    ->label('Pay')
                    ->icon('heroicon-o-currency-dollar')
                    ->action(fn ($record) => $record->pay())
                    ->requiresConfirmation()
                    ->visible(fn ($record) => is_null($record->paid_at)),
                  Action::make('cancel')
                            ->label('Cancel')
                            ->icon('heroicon-o-x-circle')
                            ->color('danger')
                            ->action(function ($record) {
                                $record->refund();
                            })
                            ->requiresConfirmation()
                            ->visible(fn ($record) => $record->status === 'in_progress'),
            ];
}

}
