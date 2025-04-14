<?php

namespace App\Filament\Store\Resources;

use App\Filament\Store\Resources\BankAccountResource\Pages;
use App\Models\BankAccount;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Icon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;



    protected static ?string $navigationGroup = 'Finance Management';

    /**
     * Form Definition
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('🏦 Bank Account Details')
                ->icon('heroicon-o-banknotes')
                ->description('Please enter the bank account details below. All fields marked with * are required.')
                ->columns(1)
                ->schema([
                    Card::make()
                        ->collapsible()
                        ->columns(2)
                        ->schema([

                            TextInput::make('bank_name')
                                ->label('🏛️ Bank Name')
                                ->prefixIcon('heroicon-o-banknotes')
                                ->maxLength(100)
                                ->placeholder('e.g., Azizi Bank')
                                ->required(),

                            TextInput::make('account_holder_name')
                                ->label('👤 Account Holder Name')
                                ->prefixIcon('heroicon-o-user-circle')
                                ->maxLength(100)
                                ->placeholder('e.g., Mohammad A. Rahimi')
                                ->required(),

                            TextInput::make('account_number')
                                ->label('🔢 Account Number')
                                ->prefixIcon('heroicon-o-hashtag')
                                ->maxLength(50)
                                ->placeholder('e.g., 010101010101')
                                ->required()
                                ->unique('bank_accounts', 'account_number', ignoreRecord: true),

                            TextInput::make('iban')
                                ->label('📘 IBAN')
                                ->prefixIcon('heroicon-o-identification')
                                ->maxLength(34)
                                ->placeholder('e.g., AF56001111223344556677')
                                ->nullable()
                                ->hint('International Bank Account Number'),

                            TextInput::make('swift_code')
                                ->label('🔄 SWIFT Code')
                                ->prefixIcon('heroicon-o-finger-print')
                                ->maxLength(11)
                                ->placeholder('e.g., AFZNKBLKXXX')
                                ->nullable()
                                ->hint('Used for international transfers'),

                            Select::make('currency_id')
                                ->label('💱 Currency')
                                ->prefixIcon('heroicon-o-currency-dollar')
                                ->relationship('currency', 'code')
                                ->searchable()
                                ->required()
                                ->hint('Select account currency'),

                            Toggle::make('is_primary')
                                ->label('⭐ Primary Account')
                                ->default(false)
                                ->inline(false)
                                ->hint('Mark as default account for transactions'),

                            Select::make('status')
                                ->label('📌 Status')
                                ->prefixIcon('heroicon-o-adjustments-horizontal')
                                ->native(false)
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                    'closed' => 'Closed',
                                ])
                                ->default('active')
                                ->required()
                                ->hint('Account operational status'),
                        ]),
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
            /** 👤 User */
            TextColumn::make('user.name')
                ->label('👤 User')
                ->sortable()
                ->searchable()
                ->icon('heroicon-o-user'),

            /** 🏛️ Bank Name */
            TextColumn::make('bank_name')
                ->label('🏛️ Bank Name')
                ->sortable()
                ->searchable()
                ->icon('heroicon-o-banknotes'),

            /** 🔢 Account Number */
            TextColumn::make('account_number')
                ->label('🔢 Account Number')
                ->copyable()
                ->sortable()
                ->searchable()
                ->icon('heroicon-o-hashtag')
                ->tooltip('Click to copy account number'),

            /** 📘 IBAN */
            TextColumn::make('iban')
                ->label('📘 IBAN')
                ->copyable()
                ->sortable()
                ->icon('heroicon-o-identification')
                ->tooltip('International Bank Account Number'),

            /** 💱 Currency */
            TextColumn::make('currency.code')
                ->label('💱 Currency')
                ->sortable()
                ->icon('heroicon-o-currency-dollar'),

            /** 📌 Status */
            BadgeColumn::make('status')
                ->label('📌 Status')
                ->colors([
                    'success' => 'active',
                    'warning' => 'inactive',
                    'danger' => 'closed',
                ])
                ->formatStateUsing(fn ($state) => ucfirst($state)),

            /** 📅 Created At */
            TextColumn::make('created_at')
                ->label('📅 Created At')
                ->dateTime('F j, Y')
                ->sortable(),

            /** 🔄 Updated At */
            TextColumn::make('updated_at')
                ->label('🔄 Updated At')
                ->dateTime('F j, Y')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])

            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'closed' => 'Closed',
                    ])
                    ->label('Status Filter'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            /** 🏦 Bank Account Overview */
            InfolistSection::make('🏦 Bank Account Overview')
                ->description('Details of the selected bank account.')
                ->icon('heroicon-o-banknotes')
                ->columns(2)
                ->schema([
                    TextEntry::make('bank_name')
                        ->label('🏛️ Bank Name')
                        ->icon('heroicon-o-building-library')
                        ->weight('bold'),

                    TextEntry::make('account_holder_name')
                        ->label('👤 Account Holder')
                        ->icon('heroicon-o-user-circle'),

                    TextEntry::make('account_number')
                        ->label('🔢 Account Number')
                        ->copyable()
                        ->tooltip('Click to copy the account number')
                        ->icon('heroicon-o-hashtag'),

                    TextEntry::make('iban')
                        ->label('📘 IBAN')
                        ->copyable()
                        ->tooltip('International Bank Account Number')
                        ->icon('heroicon-o-identification')
                        ->visible(fn ($state) => filled($state)),

                    TextEntry::make('swift_code')
                        ->label('🔄 SWIFT Code')
                        ->icon('heroicon-o-finger-print')
                        ->visible(fn ($state) => filled($state)),

                    TextEntry::make('currency.code')
                        ->label('💱 Currency')
                        ->badge()
                        ->color('gray')
                        ->formatStateUsing(fn ($state) => strtoupper($state)),
                ]),

            /** ⚙️ Account Settings */
            InfolistSection::make('⚙️ Account Settings')
                ->columns(2)
                ->schema([
                    TextEntry::make('status')
                        ->label('📌 Status')
                        ->icon(fn ($state) => match ($state) {
                            'active' => 'heroicon-o-check-circle',
                            'inactive' => 'heroicon-o-pause-circle',
                            'closed' => 'heroicon-o-x-circle',
                        })
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'active' => 'success',
                            'inactive' => 'warning',
                            'closed' => 'danger',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst($state)),

                    TextEntry::make('is_primary')
                        ->label('⭐ Primary Account')
                        ->icon('heroicon-o-star')
                        ->badge()
                        ->color(fn ($state) => $state ? 'primary' : 'gray')
                        ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                ]),

            /** 🕒 Metadata */
            InfolistSection::make('🕒 Metadata')
                ->columns(2)
                ->collapsed()
                ->schema([
                    TextEntry::make('created_at')
                        ->label('📅 Created At')
                        ->icon('heroicon-o-calendar-days')
                        ->dateTime('F j, Y h:i A'),

                    TextEntry::make('updated_at')
                        ->label('🔄 Last Updated')
                        ->icon('heroicon-o-clock')
                        ->dateTime('F j, Y h:i A'),
                ]),
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
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }
}
