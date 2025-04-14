<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationGroup = "Settings";
    protected static ?string $navigationLabel = 'Countries';
    protected static ?string $pluralModelLabel = 'Countries';
    protected static ?string $modelLabel = 'Country';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('🌐 Country Details')
                    ->description('Add or update the country name.')
                    ->icon('heroicon-o-flag')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('🏷️ Country Name')
                            ->placeholder('e.g., Afghanistan')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->autofocus()
                            ->prefixIcon('heroicon-o-globe-alt'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('🌐 Country Name')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-flag')
                    ->tooltip('Country name'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('📅 Created')
                    ->dateTime('F j, Y')
                    ->icon('heroicon-o-calendar-days')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('🔄 Updated')
                    ->dateTime('F j, Y')
                    ->icon('heroicon-o-clock')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('✏️ Edit'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('🗑️ Delete Selected'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
