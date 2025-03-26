<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommissionTypeResource\Pages;
use App\Filament\Resources\CommissionTypeResource\RelationManagers;
use App\Models\CommissionType;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommissionTypeResource extends Resource
{
    protected static ?string $model = CommissionType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Commission Type Name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('name')->sortable()->searchable(),
            TextColumn::make('created_at')->dateTime()->label('Created'),
            TextColumn::make('updated_at')->dateTime()->label('Updated'),
        ])
        ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommissionTypes::route('/'),
            'create' => Pages\CreateCommissionType::route('/create'),
            'edit' => Pages\EditCommissionType::route('/{record}/edit'),
        ];
    }
}
