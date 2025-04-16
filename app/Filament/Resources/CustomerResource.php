<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Resources\CustomerResource\RelationManagers\BanksRelationManager;
use App\Filament\Resources\CustomerResource\RelationManagers\StoreRelatedToRelationManager;
use App\Filament\Resources\CustomerResource\RelationManagers\WalletsRelationManager;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getLabel(): string
    {
        return "Customer";
    }

    public static function getPluralModelLabel(): string
    {
        return "Customers";
    }

    public static function getNavigationLabel(): string
    {
        return "Customers";
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Customer Management';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('user_type', 'customer')
            ->count();
    }



    // protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form->schema([
            /** 👤 USER INFO */
            Section::make('👤 User Information')
                ->description('Personal and contact information.')
                ->icon('heroicon-o-user-circle')
                ->columns(3)
                ->schema([
                    Select::make('store_id')
                        ->label('🏪 Store')
                        ->relationship('storeRelatedTo', 'name')
                        ->searchable(['name'])
                        ->columnSpanFull()
                        ->default(auth()->user()->store->id)
                        ->visible(fn () => auth()->user()?->hasRole('super_admin')),

                    TextInput::make('name')
                        ->label('🧍 Full Name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('📧 Email')
                        ->email()
                        ->unique('users', 'email', ignoreRecord: true)
                        ->placeholder('john@example.com'),

                    TextInput::make('phone_number')
                        ->label('📞 Phone')
                        ->required()
                        ->unique('users', 'phone_number', ignoreRecord: true),

                    Select::make('status')
                        ->label('📌 Account Status')
                        ->options([
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                            'banned' => 'Banned',
                        ])
                        ->default('active')
                        ->native(false)
                        ->hidden(fn ($record) => $record === null),

                    FileUpload::make('image')
                        ->label('🖼️ Profile Image')
                        ->image()
                        ->imageEditor()
                        ->directory('public/users')
                        ->nullable()
                        ->columnSpanFull(),

                    Toggle::make('is_active')
                        ->label('✅ Account Active')
                        ->columnSpanFull()
                        ->default(true),
                ]),

            /** 🪪 USER KYC */
            Fieldset::make('🪪 KYC Verification')
                ->relationship('kyc')
                ->schema([
                    Section::make('📄 Identification')
                        ->columns(4)
                        ->schema([
                            TextInput::make('govt_id_type')
                                ->label('📘 ID Type')
                                ->required()
                                ->placeholder('e.g., Passport'),

                            TextInput::make('govt_id_number')
                                ->label('🆔 ID Number')
                                ->required()
                                ->unique('k_y_c_s', 'govt_id_number', ignoreRecord: true),

                            DatePicker::make('issue_date')
                                ->label('📅 Issue Date')
                                ->required(),

                            DatePicker::make('expire_date')
                                ->label('📅 Expiry Date')
                                ->required()
                                ->after('issue_date'),
                        ]),

                    Section::make('📁 ID File Upload')
                        ->columns(1)
                        ->schema([
                            FileUpload::make('govt_id_file')
                                ->label('🗂️ Upload Document')
                                ->directory('kyc_documents')
                                ->disk('public')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                                ->required()
                                ->visibility('public'),
                        ]),

                    Section::make('📊 Verification Result')
                        ->columns(2)
                        ->schema([
                            Select::make('status')
                                ->label('🔍 KYC Status')
                                ->native(false)
                                ->options([
                                    'pending' => 'Pending',
                                    'verified' => 'Verified',
                                    'rejected' => 'Rejected',
                                ])
                                ->default('pending')
                                ->required(),

                            Textarea::make('third_party_response')
                                ->label('📤 Third Party Response')
                                ->placeholder('Optional integration result')
                                ->columnSpanFull(),

                            Textarea::make('rejection_reason')
                                ->label('🚫 Rejection Reason')
                                ->placeholder('Explain rejection if applicable')
                                ->columnSpanFull()
                                ->visible(fn ($get) => $get('status') === 'rejected'),
                        ]),
                ]),

            /** 🏦 BANK ACCOUNTS */
            Repeater::make('banks')
                ->label('🏦 Bank Accounts')
                ->relationship('banks')
                ->reorderable()
                ->columnSpanFull()
                ->schema([
                    Section::make('🏦 Bank Account')
                        ->columns(3)
                        ->schema([
                            TextInput::make('bank_name')
                                ->label('🏛️ Bank Name')
                                ->required(),

                            TextInput::make('account_holder_name')
                                ->label('👤 Account Holder')
                                ->required(),

                            TextInput::make('account_number')
                                ->label('🔢 Account Number')
                                ->required()
                                ->unique('bank_accounts', 'account_number', ignoreRecord: true),

                            TextInput::make('iban')->label('📘 IBAN'),
                            TextInput::make('swift_code')->label('🔁 SWIFT Code'),

                            Select::make('currency_id')
                                ->label('💱 Currency')
                                ->relationship('currency', 'code')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('status')
                                ->label('📌 Status')
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                    'closed' => 'Closed',
                                ])
                                ->default('active'),

                            Toggle::make('is_primary')
                                ->label('⭐ Primary Account')
                                ->default(false)
                                ->inline(false),
                        ]),
                ]),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('user_type', 'customer')
            )
            ->columns([
                TextColumn::make('uuid')
                    ->label('🆔 ID')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\ImageColumn::make('image')
                    ->label('🖼️')
                    ->circular()
                    ->height(40),

                TextColumn::make('storeRelatedTo.name')
                    ->label('🏪 Store')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('name')
                    ->label('👤 Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('📧 Email')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('phone_number')
                    ->label('📞 Phone')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user_type')
                    ->label('🧩 Type')
                    ->badge()
                    ->colors([
                        'primary' => 'admin',
                        'success' => 'customer',
                        'info' => 'vendor',
                        'warning' => 'agent',
                    ]),

                BadgeColumn::make('status')
                    ->label('📌 Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'banned',
                    ]),

                IconColumn::make('is_active')
                    ->label('✅ Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('📅 Created')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->diffForHumans())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('🔄 Updated')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->diffForHumans())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('user_type')
                    ->label('User Type')
                    ->options([
                        'admin' => 'Admin',
                        'customer' => 'Customer',
                        'vendor' => 'Vendor',
                        'agent' => 'Agent',
                    ]),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'banned' => 'Banned',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            WalletsRelationManager::class,
            StoreRelatedToRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
