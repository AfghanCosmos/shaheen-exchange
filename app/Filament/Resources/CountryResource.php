<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Models\Country;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Tables\Filters\Filter;

/**
 * Country Resource - manages CRUD operations for countries.
 */
class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Geography';

    protected static ?string $label = 'Country';

    protected static ?string $pluralLabel = 'Countries';

    protected static ?int $navigationSort = 10;

    /**
     * Defines the form schema for creating/editing countries.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Country Name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus()
                    ->unique(ignoreRecord: true),
            ]);
    }

    /**
     * Defines the table columns and filters for listing countries.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->label('ID'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Country Name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Updated'),
            ])
            ->defaultSort('name');
    }

    /**
     * Returns the CRUD pages for the resource.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
