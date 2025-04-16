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

    protected static ?string $navigationGroup = 'Store Management';
    protected static ?int $navigationSort = 3;

    /**
     * Form Definition
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('📇 Contact Information')
                ->description('Define a communication method for a store.')
                ->icon('heroicon-o-phone')
                ->columns(2)
                ->schema([
                    Select::make('store_id')
                        ->label('🏪 Store')
                        ->relationship('store', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->placeholder('Select store'),

                    Select::make('type')
                        ->label('📞 Contact Type')
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
                        ->native(false)
                        ->required(),

                    TextInput::make('contact_value')
                        ->label('💬 Contact Details')
                        ->placeholder('e.g., +1 234 567 8901 or contact@store.com')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                ]),
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
                    ->label('🏪 Store')
                    ->sortable()
                    ->searchable(),
    
                BadgeColumn::make('type')
                    ->label('📞 Type')
                    ->colors([
                        'primary' => 'phone',
                        'success' => 'email',
                        'info' => 'whatsapp',
                        'warning' => 'telegram',
                        'danger' => 'fax',
                        'gray' => 'other',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->sortable(),
    
                TextColumn::make('contact_value')
                    ->label('💬 Contact Info')
                    ->copyable()
                    ->searchable()
                    ->sortable(),
    
                TextColumn::make('created_at')
                    ->label('📅 Created')
                    ->dateTime('F j, Y')
                    ->sortable(),
    
                TextColumn::make('updated_at')
                    ->label('🔄 Updated')
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Filter by Contact Type')
                    ->options([
                        'phone' => 'Phone',
                        'email' => 'Email',
                        'whatsapp' => 'WhatsApp',
                        'telegram' => 'Telegram',
                        'fax' => 'Fax',
                        'skype' => 'Skype',
                        'messenger' => 'Messenger',
                        'signal' => 'Signal',
                        'wechat' => 'WeChat',
                        'other' => 'Other',
                    ])
                    ->placeholder('All types'),
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
