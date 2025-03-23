<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreContactResource\Pages;
use App\Models\StoreContact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class StoreContactResource extends Resource
{
    protected static ?string $model = StoreContact::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'Store Management';

    /**
     * Form Definition
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Contact Information')
                    ->schema([
                        Select::make('store_id')
                            ->label('Store')
                            ->relationship('store', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('type')
                            ->label('Contact Type')
                            ->options([
                                'phone' => 'Phone',
                                'email' => 'Email',
                                'whatsapp' => 'WhatsApp',
                                'fax' => 'Fax',
                                'telegram' => 'Telegram',
                                'skype' => 'Skype',
                                'messenger' => 'Messenger',
                                'signal' => 'Signal',
                                'wechat' => 'WeChat',
                                'other' => 'Other',
                            ])
                            ->default('phone')
                            ->required(),

                        TextInput::make('contact_value')
                            ->label('Contact Details')
                            ->placeholder('e.g., +1 234 567 8901')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Table Definition
     */
    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('store.name')
                    ->label('Store')
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'phone',
                        'success' => 'email',
                        'info' => 'whatsapp',
                        'warning' => 'telegram',
                        'danger' => 'fax',
                        'gray' => 'other',
                    ])
                    ->label('Contact Type')
                    ->sortable(),

                TextColumn::make('contact_value')
                    ->label('Contact Details')
                    ->copyable()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('F j, Y')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Contact Type')
                    ->options([
                        'phone' => 'Phone',
                        'email' => 'Email',
                        'whatsapp' => 'WhatsApp',
                        'telegram' => 'Telegram',
                        'fax' => 'Fax',
                        'other' => 'Other',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /**
     * Relations
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Pages
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStoreContacts::route('/'),
            'create' => Pages\CreateStoreContact::route('/create'),
            'edit' => Pages\EditStoreContact::route('/{record}/edit'),
        ];
    }
}
