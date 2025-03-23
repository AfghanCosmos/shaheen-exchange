<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BranchResource extends Resource
{
    // Model Definition
    protected static ?string $model = Branch::class;

    // Navigation Settings
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = "Settings";


    // ================================
    // 🔹 FORM DEFINITION
    // ================================
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Branch Name')
                    ->placeholder('Enter branch name')
                    ->required()
                    ->maxLength(255),

                Select::make('province_id')
                    ->label('Province')
                    ->relationship('province', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Province Name')
                            ->placeholder('Enter new province name')
                            ->required()
                            ->maxLength(255),
                    ]),

                Select::make('manager_id')
                    ->label('Manager')
                    ->relationship('manager', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Manager Name')
                            ->placeholder('Enter manager name')
                            ->required()
                            ->maxLength(255),
                    ]),

                DatePicker::make('start_at')
                    ->label('Start Date')
                    ->default(Carbon::now())
                    ->required(),

                Textarea::make('full_address')
                    ->label('Full Address')
                    ->placeholder('Enter branch full address')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
            ]);
    }

    // ================================
    // 🔹 TABLE DEFINITION
    // ================================
    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Branch Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('province.name')
                    ->label('Province')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('manager.name')
                    ->label('Manager')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('full_address')
                    ->label('Full Address')
                    ->searchable(),

                TextColumn::make('start_at')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'view' => Pages\ViewBranch::route('/{record}'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
