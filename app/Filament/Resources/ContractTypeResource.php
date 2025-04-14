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

    protected static ?string $navigationLabel = 'Contract Types';
    protected static ?string $navigationGroup = "Settings";

    protected static ?string $modelLabel = 'Contract Type';
    protected static ?string $pluralModelLabel = 'Contract Types';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('📝 Contract Type Information')
                ->description('Add or update a contract type.')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Contract Type Name')
                        ->placeholder('e.g., Full-Time, Part-Time, Internship')
                        ->required()
                        ->maxLength(255)
                        ->autofocus()
                        ->prefixIcon('heroicon-o-pencil'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('📄 Contract Type')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-document-text'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('📅 Created At')
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar-days'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('🔄 Updated At')
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('👁️ View'),
                Tables\Actions\EditAction::make()->label('✏️ Edit'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('🗑️ Delete Selected'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
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
}
