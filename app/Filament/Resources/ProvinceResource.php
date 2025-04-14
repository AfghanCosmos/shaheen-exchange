<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProvinceResource\Pages;
use App\Filament\Resources\ProvinceResource\RelationManagers;
use App\Filament\Resources\ProvinceResource\RelationManagers\BranchesRelationManager;
use App\Filament\Resources\ProvinceResource\RelationManagers\StoresRelationManager;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions\Action;

class ProvinceResource extends Resource
{
    protected static ?string $model = Province::class;

    // protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = "Settings";

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Section::make('🌍 Location Details')
                ->description('Please provide the necessary location information.')
                ->icon('heroicon-o-map')
                ->columns(2)
                ->schema([

                    /** 🏷️ Name */
                    TextInput::make('name')
                        ->label('📍 Location Name')
                        ->placeholder('e.g., Kabul, Toronto, Paris')
                        ->prefixIcon('heroicon-o-pencil')
                        ->maxLength(255)
                        ->required(),

                    /** 🌐 Country Select */
                    Select::make('country_id')
                        ->label('🌐 Country')
                        ->relationship('country', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->placeholder('Select a country')
                        ->prefixIcon('heroicon-o-flag')
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('📝 Country Name')
                                ->placeholder('Enter country name')
                                ->required()
                                ->maxLength(255),
                        ])
                        ->createOptionAction(fn (Action $action) => $action
                            ->label('➕ Add Country')
                            ->icon('heroicon-o-plus-circle')
                            ->modalHeading('Create New Country')
                            ->modalWidth('md')
                            ->color('primary')
                        ),
                ]),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
            ->columns([
                /** 🏷️ Name */
                Tables\Columns\TextColumn::make('name')
                    ->label('🏷️ Name')
                    ->searchable()
                    ->icon('heroicon-o-pencil-square')
                    ->tooltip('Location name'),

                /** 🌐 Country */
                Tables\Columns\TextColumn::make('country.name')
                    ->label('🌐 Country')
                    ->searchable()
                    ->icon('heroicon-o-flag')
                    ->tooltip('Country associated with this location'),

                /** 📅 Created At */
                Tables\Columns\TextColumn::make('created_at')
                    ->label('📅 Created At')
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar-days'),

                /** 🔄 Updated At */
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('🔄 Updated At')
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock'),
            ])

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            StoresRelationManager::class,
            BranchesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProvinces::route('/'),
            'create' => Pages\CreateProvince::route('/create'),
            'view' => Pages\ViewProvince::route('/{record}'),
            'edit' => Pages\EditProvince::route('/{record}/edit'),
        ];
    }
}
