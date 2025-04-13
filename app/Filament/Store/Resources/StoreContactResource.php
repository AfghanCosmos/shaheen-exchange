<?php

namespace App\Filament\Store\Resources;

use App\Filament\Store\Resources\StoreContactResource\Pages;
use App\Models\StoreContact;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;

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
        return $form
            ->schema([
                Section::make('Contact Information')
                    ->schema([
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
            /** 🏬 Store */
            TextColumn::make('store.name')
                ->label('🏬 Store')
                ->sortable()
                ->searchable()
                ->badge()
                ->color('gray'),

            /** 📡 Contact Type */
            BadgeColumn::make('type')
                ->label('📲 Contact Type')
                ->sortable()
                ->colors([
                    'primary' => 'phone',
                    'success' => 'email',
                    'info' => 'whatsapp',
                    'warning' => 'telegram',
                    'danger' => 'fax',
                    'gray' => 'other',
                ])
                ->formatStateUsing(fn ($state) => ucfirst($state)),

            /** 📞 Contact Details */
            TextColumn::make('contact_value')
                ->label('📞 Contact Info')
                ->copyable()
                ->copyMessage('Copied!')
                ->copyMessageDuration(1500)
                ->searchable()
                ->sortable()
                ->icon(fn ($record) => match ($record->type) {
                    'phone' => 'heroicon-o-phone',
                    'email' => 'heroicon-o-envelope',
                    'whatsapp' => 'heroicon-o-chat-bubble-left-right',
                    'telegram' => 'heroicon-o-paper-airplane',
                    'fax' => 'heroicon-o-printer',
                    default => 'heroicon-o-question-mark-circle',
                }),

            /** 🕒 Created Date */
            TextColumn::make('created_at')
                ->label('🕒 Created')
                ->dateTime('M d, Y')
                ->sortable(),

            /** 🔄 Updated Date */
            TextColumn::make('updated_at')
                ->label('🔄 Updated')
                ->dateTime('M d, Y')
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('store', function (Builder $query) {
                $query->where('user_id', Auth::id());
            });
    }
}
