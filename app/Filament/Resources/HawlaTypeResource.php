<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HawlaTypeResource\Pages;
use App\Filament\Resources\HawlaTypeResource\RelationManagers;
use App\Models\HawlaType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HawlaTypeResource extends Resource
{
    protected static ?string $model = HawlaType::class;

    protected static ?string $navigationGroup = 'Hawla Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // Section for Hawla Type Details
                Forms\Components\Section::make('💡 Hawla Type Information')
                    ->description('Enter the details for the Hawla Type.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('🏷️ Hawla Type Name') // Icon for Hawla Type
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255)
                            ->helperText('Provide a descriptive name for the Hawla type.')
                            ->placeholder('e.g., Standard, Premium')
                    ])
                    ->columns(1), // Single column for better layout of the name field

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
        ->columns([

            // Name Column with Emoji
            Tables\Columns\TextColumn::make('name')
                ->label('🏷️ Hawla Type') // Emoji for Hawla Type
                ->searchable()
                ->tooltip('The name of the Hawla type'),

            // Created At Column with Emoji
            Tables\Columns\TextColumn::make('created_at')
                ->label('📅 Created At') // Emoji for Created At
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->tooltip('Date when the Hawla type was created'),

            // Updated At Column with Emoji
            Tables\Columns\TextColumn::make('updated_at')
                ->label('🔄 Updated At') // Emoji for Updated At
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->tooltip('Date when the Hawla type was last updated'),

        ])
        ->filters([

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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHawlaTypes::route('/'),
            'create' => Pages\CreateHawlaType::route('/create'),
            'view' => Pages\ViewHawlaType::route('/{record}'),
            'edit' => Pages\EditHawlaType::route('/{record}/edit'),
        ];
    }
}
