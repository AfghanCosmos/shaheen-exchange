<?php

namespace App\Filament\Store\Resources\HawlaResource\Pages;

use App\Filament\Store\Resources\HawlaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListHawlas extends ListRecords
{
    protected static string $resource = HawlaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $userStoreId = auth()->user()?->store?->id;

        return [
            'all' => Tab::make('All')
                ->modifyQueryUsing(function (Builder $query) use ($userStoreId) {
                    if (!auth()->user()->hasRole('super_admin')) {
                        $query->where('sender_store_id', $userStoreId);
                    }
                }),

            'completed' => Tab::make('✅ Completed')
                ->modifyQueryUsing(function (Builder $query) use ($userStoreId) {
                    $query->where('status', 'completed');
                    if (!auth()->user()->hasRole('super_admin')) {
                        $query->where('sender_store_id', $userStoreId);
                    }
                }),

            'in_progress' => Tab::make('⏳ In Progress')
                ->modifyQueryUsing(function (Builder $query) use ($userStoreId) {
                    $query->where('status', 'in_progress');
                    if (!auth()->user()->hasRole('super_admin')) {
                        $query->where('sender_store_id', $userStoreId);
                    }
                }),

            'cancelled' => Tab::make('❌ Cancelled')
                ->modifyQueryUsing(function (Builder $query) use ($userStoreId) {
                    $query->where('status', 'cancelled');
                    if (!auth()->user()->hasRole('super_admin')) {
                        $query->where('sender_store_id', $userStoreId);
                    }
                }),
        ];
    }
}
