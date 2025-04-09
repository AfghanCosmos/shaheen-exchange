<?php

namespace App\Filament\Resources\HawlaResource\Pages;

use App\Filament\Resources\HawlaResource;
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
        return [
                'all' => Tab::make('All'),
                'completed' => Tab::make('✅ Completed')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),

                'in_progress' => Tab::make('⏳ In Progress')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'in_progress')),

                'cancelled' => Tab::make('❌ Cancelled')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled')),
        ];
    }

}
