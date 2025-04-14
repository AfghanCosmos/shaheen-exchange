<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseCategoryResource\Pages;
use App\Filament\Resources\ExpenseCategoryResource\RelationManagers\ExpensesRelationManager;
use App\Models\ExpenseCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpenseCategoryResource extends Resource
{
    protected static ?string $model = ExpenseCategory::class;

    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Expense Categories';
    protected static ?string $pluralModelLabel = 'Expense Categories';
    protected static ?string $modelLabel = 'Expense Category';

    // ================================
    // 🔹 FORM
    // ================================
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Category Details')
                ->description('Create or update an expense category.')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Category Name')
                        ->placeholder('e.g., Office Supplies, Utilities')
                        ->required()
                        ->maxLength(255)
                        ->autofocus(),
                ]),
        ]);
    }

    // ================================
    // 🔹 TABLE
    // ================================
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('📂 Name')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-tag')
                    ->tooltip('Expense category name'),

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
                Tables\Actions\ViewAction::make()->label('View'),
                Tables\Actions\EditAction::make()->label('Edit'),
                Tables\Actions\DeleteAction::make()->label('Delete'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('Delete Selected'),
            ]);
    }

    // ================================
    // 🔹 RELATIONS
    // ================================
    public static function getRelations(): array
    {
        return [
            ExpensesRelationManager::class, 
        ];
    }

    // ================================
    // 🔹 PAGES
    // ================================
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenseCategories::route('/'),
            'create' => Pages\CreateExpenseCategory::route('/create'),
            'view' => Pages\ViewExpenseCategory::route('/{record}'),
            'edit' => Pages\EditExpenseCategory::route('/{record}/edit'),
        ];
    }
}
