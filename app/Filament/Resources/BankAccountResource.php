<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankAccountResource\Pages;
use App\Models\BankAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;
    protected static ?string $navigationGroup = 'Finance Management';
    protected static ?string $navigationLabel = 'Bank Accounts';

    /**
     * Form
     */
    public static function form(Form $form): Form
{
    return $form->schema([
        // Section 1: Owner & Bank Info
        Section::make('🏦 Bank Information')
            ->description('Select the user and provide the basic bank details.')
            ->icon('heroicon-o-user-circle')
            ->schema([
                Grid::make(3)->schema([
                    Select::make('user_id')
                        ->label('👤 User')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->placeholder('Select user'),

                    TextInput::make('bank_name')
                        ->label('🏛️ Bank Name')
                        ->prefixIcon('heroicon-o-building-library')
                        ->required()
                        ->placeholder('e.g., AIB'),

                    TextInput::make('account_holder_name')
                        ->label('👤 Account Holder Name')
                        ->prefixIcon('heroicon-o-user')
                        ->required()
                        ->placeholder('e.g., Mohammad Ali'),
                ]),
            ]),

        // Section 2: Account Identifiers
        Section::make('🔢 Account Identifiers')
            ->description('Add account number, IBAN and SWIFT code (if any).')
            ->icon('heroicon-o-identification')
            ->schema([
                Grid::make(3)->schema([
                    TextInput::make('account_number')
                        ->label('🔢 Account Number')
                        ->prefixIcon('heroicon-o-hashtag')
                        ->required()
                        ->unique('bank_accounts', 'account_number', ignoreRecord: true)
                        ->placeholder('e.g., 0001122334455'),

                    TextInput::make('iban')
                        ->label('📘 IBAN')
                        ->prefixIcon('heroicon-o-credit-card')
                        ->placeholder('e.g., AF12345678901234567890'),

                    TextInput::make('swift_code')
                        ->label('🔄 SWIFT Code')
                        ->prefixIcon('heroicon-o-code-bracket-square')
                        ->placeholder('e.g., AIBKAFKA'),
                ]),
            ]),

        // Section 3: Currency & Status
        Section::make('⚙️ Account Settings')
            ->description('Set account currency, status, and primary flag.')
            ->icon('heroicon-o-cog-6-tooth')
            ->schema([
                Grid::make(3)->schema([
                    Select::make('currency_id')
                        ->label('💱 Currency')
                        ->relationship('currency', 'code')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->placeholder('Select currency'),

                    Select::make('status')
                        ->label('📌 Status')
                        ->native(false)
                        ->options([
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                            'closed' => 'Closed',
                        ])
                        ->default('active')
                        ->required(),

                    Toggle::make('is_primary')
                        ->label('⭐ Primary Account')
                        ->inline(false)
                        ->default(false),
                ]),
            ]),
    ]);
}


    /**
     * Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('👤 User')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('bank_name')
                    ->label('🏛️ Bank Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('account_number')
                    ->label('🔢 Account Number')
                    ->copyable()
                    ->tooltip('Click to copy account number')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('iban')
                    ->label('📘 IBAN')
                    ->copyable()
                    ->tooltip('Click to copy IBAN')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('currency.code')
                    ->label('💱 Currency')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('📌 Status')
                    ->sortable()
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'closed',
                    ]),

                TextColumn::make('created_at')
                    ->label('🕒 Created')
                    ->dateTime('F j, Y')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('🔄 Updated')
                    ->dateTime('F j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('📌 Status Filter')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'closed' => 'Closed',
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
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }
}
