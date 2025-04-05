<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractTypeResource\Pages;
use App\Models\ContractType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContractTypeResource extends Resource
{
    protected static ?string $model = ContractType::class;
    protected static ?string $navigationLabel = 'Contract Type';
    protected static ?string $label = 'Contract Type';
    protected static ?string $pluralLabel = 'Contract Types';

    protected static ?string $navigationGroup = 'Settings';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Define filters here if needed
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define related managers here if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContractTypes::route('/'),
            'create' => Pages\CreateContractType::route('/create'),
            'view' => Pages\ViewContractType::route('/{record}'),
            'edit' => Pages\EditContractType::route('/{record}/edit'),
        ];
    }




    public static function getLabel(): ?string
    {
        return __('Contract Type');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Contract Types');
    }
}
