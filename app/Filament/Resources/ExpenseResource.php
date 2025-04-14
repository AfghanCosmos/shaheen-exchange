<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Expenses';

    // ================================
    // 🔹 FORM
    // ================================
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Expense Details')
                ->icon('heroicon-o-banknotes')
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('category_id')
                        ->label('📂 Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->required()
                        ->preload()
                        ->placeholder('Select a category')
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->label('Category Name')
                                ->placeholder('Enter category name')
                                ->required()
                                ->maxLength(255),
                        ]),


                    Forms\Components\Select::make('currency_id')
                        ->label('💱 Currency')
                        ->relationship('currency', 'name')
                        ->required()
                        ->native(false)
                        ->default(1)
                        ->preload()
                        ->placeholder('Select a currency'),

                    Forms\Components\TextInput::make('amount')
                        ->label('💸 Amount')
                        ->required()
                        ->numeric()
                        ->prefix('؋') // Afghani symbol
                        ->placeholder('Enter amount'),

                    Forms\Components\DatePicker::make('date')
                        ->label('📅 Date')
                        ->default(Carbon::now())
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->label('📌 Status')
                        ->native(false)
                        ->options([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ])
                        ->default('pending')
                        ->required(),
                ]),

            Forms\Components\Section::make('More Info')
                ->columns(1)
                ->schema([
                    Forms\Components\RichEditor::make('description')
                        ->label('📝 Description')
                        ->placeholder('Enter description'),

                    Forms\Components\FileUpload::make('invoice')
                        ->label('🧾 Invoice')
                        ->directory('uploads/expenses')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->placeholder('Upload invoice'),
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
                TextColumn::make('category.name')
                    ->label('📂 Category')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('💰 Amount')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('date')
                    ->label('📅 Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('📝 Description')
                    ->limit(25)
                    ->html()
                    ->tooltip(fn ($record) => strip_tags($record->description)),

                TextColumn::make('invoice')
                    ->label('🧾 Invoice')
                    ->url(fn ($record) => $record->invoice ? asset('storage/' . $record->invoice) : null)
                    ->default('No Invoice Uploaded')
                    ->openUrlInNewTab(),

                TextColumn::make('status')
                    ->label('📌 Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('🕒 Created At')
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('🔄 Updated At')
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('📂 Category')
                    ->relationship('category', 'name')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('View'),
                Tables\Actions\EditAction::make()->label('Edit'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('🗑️ Delete Selected'),
            ]);
    }

    // ================================
    // 🔹 RELATIONS
    // ================================
    public static function getRelations(): array
    {
        return [];
    }

    // ================================
    // 🔹 PAGES
    // ================================
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'view' => Pages\ViewExpense::route('/{record}'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
