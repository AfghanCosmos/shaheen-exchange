<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;
    protected static ?string $navigationGroup = "Settings";

    // ================================
    // 🔹 FORM
    // ================================
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('🏢 Branch Details')
                ->icon('heroicon-o-building-office')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Branch Name')
                        ->placeholder('Enter branch name')
                        ->required()
                        ->maxLength(255)
                        ->autofocus()
                        ->prefixIcon('heroicon-o-building-library'),

                    Forms\Components\Select::make('province_id')
                        ->label('Province')
                        ->relationship('province', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->prefixIcon('heroicon-o-map-pin')
                        ->placeholder('Select province')
                        ->createOptionForm([
                            Forms\Components\Select::make('country_id') // ✅ Add this
                                ->label('Country')
                                ->relationship('country', 'name') // assumes province belongsTo country
                                ->required()
                                ->searchable()
                                ->preload()
                                ->placeholder('Select a country'),

                            Forms\Components\TextInput::make('name')
                                ->label('Province Name')
                                ->placeholder('Enter new province name')
                                ->required()
                                ->maxLength(255),
                        ]),


                    Forms\Components\Select::make('manager_id')
                        ->label('Manager')
                        ->relationship('manager', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->prefixIcon('heroicon-o-user-circle'),

                    Forms\Components\DatePicker::make('start_at')
                        ->label('Start Date')
                        ->default(Carbon::now())
                        ->required()
                        ->prefixIcon('heroicon-o-calendar'),

                    Forms\Components\Textarea::make('full_address')
                        ->label('Full Address')
                        ->placeholder('Enter branch full address')
                        ->required()
                        ->columnSpanFull()
                        ->maxLength(255),
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
                    ->label('🏢 Branch Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('province.name')
                    ->label('📍 Province')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('manager.name')
                    ->label('👤 Manager')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('full_address')
                    ->label('📌 Full Address')
                    ->limit(20) // limits display to 15 characters
                    ->tooltip(fn ($record) => $record->full_address) // shows full value on hover
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_at')
                    ->label('📅 Start Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('🕒 Created At')
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('🔄 Updated At')
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('View'),
                    Tables\Actions\EditAction::make()->label('Edit'),
                    Tables\Actions\DeleteAction::make()->label('Delete'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    // ================================
    // 🔹 INFOLIST (View Page)
    // ================================
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfoSection::make('🏢 Branch Overview')
                ->columns(2)
                ->schema([
                    TextEntry::make('name')->label('Branch Name')->icon('heroicon-o-building-library'),

                    TextEntry::make('province.name')->label('Province')->icon('heroicon-o-map-pin'),

                    TextEntry::make('manager.name')->label('Manager')->icon('heroicon-o-user-circle'),

                    TextEntry::make('start_at')->label('Start Date')->date()->icon('heroicon-o-calendar'),

                    TextEntry::make('created_at')->label('Created At')->dateTime()->icon('heroicon-o-calendar-days'),

                    TextEntry::make('updated_at')->label('Updated At')->dateTime()->icon('heroicon-o-clock'),

                    TextEntry::make('full_address')
                        ->label('Full Address')
                        ->columnSpanFull()
                        ->icon('heroicon-o-map'),
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
