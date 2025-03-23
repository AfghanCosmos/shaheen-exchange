<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-pointing-out';
    protected static ?string $navigationGroup = 'Expenses';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->label(__('Category'))
                    ->relationship('category', 'name') // Assuming a relationship with `ExpenseCategory`
                    ->searchable()
                    ->required()
                    ->preload()
                    ->placeholder(__('Select a category')),


                    Forms\Components\Select::make('currency_id')
                    ->label(__('Currency'))
                    ->relationship('currency', 'name')
                    ->required()
                    ->native(false)
                    ->default(1)
                    ->placeholder(__('Select a Currency'))
                    ->preload(),

                Forms\Components\TextInput::make('amount')
                    ->label(__('Amount'))
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->placeholder(__('Enter amount')),


                Forms\Components\DatePicker::make('date')
                    ->label(__('Date'))
                    ->default(Carbon::now())
                    ->required(),



                Forms\Components\RichEditor::make('description')
                ->label(__('Description'))
                ->placeholder(__('Enter description'))
                ->columnSpanFull(),

                Forms\Components\FileUpload::make('invoice')
                    ->label(__('Invoice'))
                    ->directory('uploads/expenses') // Directory for storing invoices
                    ->placeholder(__('Upload invoice'))
                    ->columnSpanFull()
                    ->acceptedFileTypes(['application/pdf', 'image/*']),

                Forms\Components\Select::make('status')
                    ->label(__('Status'))
                    ->native(false)
                    ->options([
                        'pending' => __('Pending'),
                        'approved' => __('Approved'),
                        'rejected' => __('Rejected'),
                    ])
                    ->default('pending')
                    ->required(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice')
                    ->label(__('Invoice'))
                    ->url(fn ($record) => $record->invoice) // Assuming 'invoice' stores file URL or path
                    ->default(__('No Invoice Uploaded')),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),

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
                Tables\Filters\SelectFilter::make('category_id')
                    ->label(__('Category'))
                    ->native(false)
                    ->relationship('category', 'name'),
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
            // Add relation managers here if necessary
        ];
    }

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
