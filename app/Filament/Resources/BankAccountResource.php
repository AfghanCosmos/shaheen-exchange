<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankAccountResource\Pages;
use App\Models\BankAccount;
use App\Models\User;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
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

    /**
     * Form Definition
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Bank Account Details')
                    ->schema([
                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),

                        TextInput::make('bank_name')
                            ->label('Bank Name')
                            ->maxLength(100)
                            ->required(),

                        TextInput::make('account_holder_name')
                            ->label('Account Holder Name')
                            ->maxLength(100)
                            ->required(),

                        TextInput::make('account_number')
                            ->label('Account Number')
                            ->maxLength(50)
                            ->required()
                            ->unique('bank_accounts', 'account_number', ignoreRecord: true),

                        TextInput::make('iban')
                            ->label('IBAN')
                            ->maxLength(34)
                            ->nullable(),

                        TextInput::make('swift_code')
                            ->label('SWIFT Code')
                            ->maxLength(11)
                            ->nullable(),

                        Select::make('currency_id')
                            ->label('Currency')
                            ->relationship('currency', 'code')
                            ->searchable()
                            ->required(),

                        Toggle::make('is_primary')
                            ->label('Primary Account')
                            ->default(false),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'closed' => 'Closed',
                            ])
                            ->default('active')
                            ->required(),
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
                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('bank_name')
                    ->label('Bank Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('account_number')
                    ->label('Account Number')
                    ->copyable()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('iban')
                    ->label('IBAN')
                    ->copyable()
                    ->sortable(),

                TextColumn::make('currency.code')
                    ->label('Currency')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'closed',
                    ]),

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
