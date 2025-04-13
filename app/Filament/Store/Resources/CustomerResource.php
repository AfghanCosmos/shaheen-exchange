<?php

namespace App\Filament\Store\Resources;

use App\Filament\Store\Resources\CustomerResource\Pages;
use App\Filament\Store\Resources\CustomerResource\RelationManagers;
use App\Filament\Store\Resources\CustomerResource\RelationManagers\BanksRelationManager;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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
        return $form
            ->schema([

                /** ─────── 👤 USER PROFILE ─────── */
                Section::make('👤 User Information')
                    ->icon('heroicon-o-user-circle')
                    ->description('Basic profile and account status.')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('🧍 Full Name')
                            ->required()
                            ->placeholder('e.g., John Doe'),

                        Forms\Components\TextInput::make('email')
                            ->label('📧 Email Address')
                            ->email()
                            ->unique('users', 'email', ignoreRecord: true)
                            ->placeholder('e.g., john.doe@example.com'),

                        Forms\Components\TextInput::make('phone_number')
                            ->label('📞 Phone Number')
                            ->required()
                            ->unique('users', 'phone_number', ignoreRecord: true)
                            ->placeholder('e.g., +1 234 567 8901'),

                        Forms\Components\Select::make('status')
                            ->label('⚙️ Account Status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'banned' => 'Banned',
                            ])
                            ->default('active')
                            ->native(false)
                            ->hidden(fn ($record) => $record === null),

                        Forms\Components\FileUpload::make('image')
                            ->label('🖼️ Profile Image')
                            ->image()
                            ->imageEditor()
                            ->directory('public/users')
                            ->nullable()
                            ->columnSpanFull(),
                    ]),

                /** ─────── 🔐 KYC FIELDSET ─────── */
                Fieldset::make('🪪 User KYC')
                    ->relationship('kyc')
                    ->columns(1)
                    ->schema([

                        /** ─── ID Information ─── */
                        Section::make('📄 Identification Details')
                            ->icon('heroicon-o-identification')
                            ->columns(4)
                            ->schema([
                                Forms\Components\TextInput::make('govt_id_type')
                                    ->label('📘 ID Type')
                                    ->required()
                                    ->placeholder('e.g., Passport, National ID'),

                                Forms\Components\TextInput::make('govt_id_number')
                                    ->label('🆔 ID Number')
                                    ->required()
                                    ->unique('k_y_c_s', 'govt_id_number', ignoreRecord: true)
                                    ->placeholder('e.g., A1234567'),

                                Forms\Components\DatePicker::make('issue_date')
                                    ->label('📅 Issue Date')
                                    ->required(),

                                Forms\Components\DatePicker::make('expire_date')
                                    ->label('📅 Expiry Date')
                                    ->required()
                                    ->after('issue_date'),
                            ]),

                        /** ─── ID Upload ─── */
                        Section::make('📁 Document Upload')
                            ->columns(1)
                            ->schema([
                                Forms\Components\FileUpload::make('govt_id_file')
                                    ->label('🗂️ Upload Document')
                                    ->directory('kyc_documents')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                                    ->required()
                                    ->maxSize(4048)
                                    ->visibility('public'),
                            ]),

                        /** ─── KYC Status ─── */
                        Section::make('📊 KYC Verification Status')
                            ->icon('heroicon-o-shield-check')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Textarea::make('third_party_response')
                                    ->label('📤 Third-Party Response')
                                    ->placeholder('Optional notes or response from verification service.')
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('status')
                                    ->label('🔍 Status')
                                    ->native(false)
                                    ->options([
                                        'pending' => 'Pending',
                                        'verified' => 'Verified',
                                        'rejected' => 'Rejected',
                                    ])
                                    ->default('pending')
                                    ->required(),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('✅ Account Active')
                                    ->default(true)
                                    ->inline(false)
                                    ->columnSpan(1),

                                Forms\Components\Textarea::make('rejection_reason')
                                    ->label('🚫 Rejection Reason')
                                    ->placeholder('If rejected, explain why.')
                                    ->visible(fn ($get) => $get('status') === 'rejected')
                                    ->columnSpanFull(),

                            ]),
                    ]),

                /** ─────── 💳 BANK ACCOUNTS ─────── */
                Repeater::make('banks')
                    ->label('🏦 Bank Accounts')
                    ->relationship('banks')
                    ->columnSpanFull()
                    ->reorderable()
                    ->schema([
                        Section::make('🏦 Bank Account Details')
                            ->icon('heroicon-o-banknotes')
                            ->columns(3)
                            ->schema([
                                Forms\Components\TextInput::make('bank_name')
                                    ->label('🏛️ Bank Name')
                                    ->required(),

                                Forms\Components\TextInput::make('account_holder_name')
                                    ->label('👤 Account Holder')
                                    ->required(),

                                Forms\Components\TextInput::make('account_number')
                                    ->label('🔢 Account Number')
                                    ->required()
                                    ->unique('bank_accounts', 'account_number', ignoreRecord: true),

                                Forms\Components\TextInput::make('iban')
                                    ->label('📘 IBAN'),

                                Forms\Components\TextInput::make('swift_code')
                                    ->label('🔄 SWIFT Code'),

                                Select::make('currency_id')
                                    ->label('💱 Currency')
                                    ->relationship('currency', 'code')
                                    ->required()
                                    ->searchable()
                                    ->preload(),

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
                                    ->inline(false),
                            ]),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
        ->modifyQueryUsing(fn(Builder $query) => $query->where('user_type', 'customer')->orderBy('created_at', 'desc'))

        ->columns([
            /** 🖼️ Profile Image */
            Tables\Columns\ImageColumn::make('image')
                ->label('🖼️ Profile')
                ->circular()
                ->height(40),

            /** 👤 Name */
            Tables\Columns\TextColumn::make('name')
                ->label('👤 Name')
                ->sortable()
                ->searchable()
                ->weight('medium'),

            /** 📧 Email */
            Tables\Columns\TextColumn::make('email')
                ->label('📧 Email')
                ->copyable()
                ->searchable()
                ->icon('heroicon-o-envelope')
                ->tooltip(fn ($state) => $state),

            /** 📞 Phone */
            Tables\Columns\TextColumn::make('phone_number')
                ->label('📞 Phone')
                ->sortable()
                ->searchable()
                ->icon('heroicon-o-phone'),

            /** 🧩 User Type */
            Tables\Columns\TextColumn::make('user_type')
                ->label('🧩 Type')
                ->badge()
                ->sortable()
                ->color(fn ($state) => match ($state) {
                    'admin' => 'primary',
                    'customer' => 'success',
                    'vendor' => 'info',
                    'agent' => 'warning',
                    default => 'gray',
                }),

            /** 📌 Status */
            Tables\Columns\BadgeColumn::make('status')
                ->label('📌 Status')
                ->sortable()
                ->colors([
                    'success' => 'active',
                    'warning' => 'inactive',
                    'danger' => 'banned',
                ])
                ->formatStateUsing(fn ($state) => ucfirst($state)),

            /** ✅ Active Toggle */
            Tables\Columns\IconColumn::make('is_active')
                ->label('✅ Active')
                ->boolean()
                ->alignCenter(),

            /** 🕒 Created At */
            Tables\Columns\TextColumn::make('created_at')
                ->label('📅 Created')
                ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->diffForHumans())
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            /** 🔄 Updated At */
            Tables\Columns\TextColumn::make('updated_at')
                ->label('🔄 Updated')
                ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->diffForHumans())
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])

            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('user_type')
                    ->label('User Type')
                    ->options([
                        'admin' => 'Admin',
                        'customer' => 'Customer',
                        'vendor' => 'Vendor',
                        'agent' => 'Agent',
                    ]),

                Tables\Filters\SelectFilter::make('status')
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
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfolistSection::make('👤 Customer Profile')
                ->icon('heroicon-o-user-circle')
                ->columns(3)
                ->schema([
                    ImageEntry::make('image')->label('Profile Image')->circular()->columnSpan(1),

                    TextEntry::make('name')->label('Full Name')->columnSpan(2)->weight('medium'),
                    TextEntry::make('email')->label('Email'),
                    TextEntry::make('phone_number')->label('Phone'),
                    TextEntry::make('status')->badge()->color(fn ($state) => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'banned' => 'danger',
                    }),
                    TextEntry::make('is_active')
                        ->label('Active')
                        ->boolean(),
                ]),

            InfolistSection::make('📄 KYC Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('kyc.govt_id_type')->label('ID Type'),
                    TextEntry::make('kyc.govt_id_number')->label('ID Number'),
                    TextEntry::make('kyc.issue_date')->date()->label('Issue Date'),
                    TextEntry::make('kyc.expire_date')->date()->label('Expiry Date'),
                    TextEntry::make('kyc.status')->badge()->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'verified' => 'success',
                        'rejected' => 'danger',
                    }),
                    TextEntry::make('kyc.rejection_reason')->visible(fn ($state) => filled($state)),
                    TextEntry::make('kyc.third_party_response')->visible(fn ($state) => filled($state)),
                    ImageEntry::make('kyc.govt_id_file')
                        ->label('Uploaded Document')
                        ->openUrlInNewTab()
                        ->disk('public')
                        ->visible(fn ($record) => filled($record->kyc?->govt_id_file)),
                ]),

            InfolistSection::make('🏦 Bank Accounts')
                ->schema([
                    RepeatableEntry::make('banks')
                        ->columns(3)
                        ->schema([
                            TextEntry::make('bank_name')->label('Bank'),
                            TextEntry::make('account_holder_name')->label('Account Holder'),
                            TextEntry::make('account_number')->label('Account Number'),
                            TextEntry::make('iban'),
                            TextEntry::make('swift_code'),
                            TextEntry::make('currency.code')->label('Currency'),
                            TextEntry::make('status')->badge()->color(fn ($state) => match ($state) {
                                'active' => 'success',
                                'inactive' => 'warning',
                                'closed' => 'danger',
                            }),
                            TextEntry::make('is_primary')->label('Primary')->boolean(),
                        ])
                        ->emptyLabel('No bank accounts found.'),
                ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
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
            ])
            ->where('user_type', 'customer')
            ->where('id', auth()->id());
    }
}
