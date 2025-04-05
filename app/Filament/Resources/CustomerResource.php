<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Resources\CustomerResource\RelationManagers\BanksRelationManager;
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
                Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->placeholder('e.g., John Doe')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->placeholder('e.g., john.doe@example.com')
                            ->unique('users', 'email', ignoreRecord: true),

                        Forms\Components\TextInput::make('phone_number')
                            ->label('Phone Number')
                            // ->tel()
                            ->placeholder('e.g., +1 234 567 8901')
                            ->required()
                            ->unique('users', 'phone_number', ignoreRecord: true),




                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'banned' => 'Banned',
                            ])
                            ->hidden(fn ($record) => $record === null)
                            ->default('active')
                            ->native(false),

                        Forms\Components\FileUpload::make('image')
                            ->label('Profile Image')
                            ->image()
                            ->columnSpanFull()
                            ->imageEditor()
                            ->directory('public/users')
                            ->nullable(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Account Active')
                            ->columnSpanFull()
                            ->default(true),


                            Forms\Components\Fieldset::make('User KYC')
                            ->relationship('kyc')
                                ->schema([
                         Section::make('Information')
                                    ->schema([

                        TextInput::make('govt_id_type')
                            ->label('Government ID Type')
                            ->maxLength(255)
                            ->required()
                            ->placeholder('e.g., Passport, Driver’s License'),

                        TextInput::make('govt_id_number')
                            ->label('Government ID Number')
                            ->maxLength(255)
                            ->required()
                            ->unique('k_y_c_s', 'govt_id_number', ignoreRecord: true)
                            ->placeholder('Enter ID Number'),

                            DatePicker::make('issue_date')
                            ->label('Issue Date')
                            ->required(),

                        DatePicker::make('expire_date')
                            ->label('Expiry Date')
                            ->required()
                            ->after('issue_date'),
                    ])->columns(4),

                Section::make('Document Details')
                    ->schema([
                        Forms\Components\FileUpload::make('govt_id_file')
                            ->label('Government ID File')
                            ->directory('kyc_documents')
                            ->required()
                            ->maxSize(4048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                            ->visibility('public'), // Ensure uploaded files are accessible



                       // Ensures expiry date is after issue date
                    ])->columns(1),

                Section::make('Status & Responses')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->native(false)
                            ->options([
                                'pending' => 'Pending',
                                'verified' => 'Verified',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending')
                            ->required(),

                        Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->placeholder('Provide a reason for rejection (if applicable)')
                            ->columnSpanFull()
                            ->visible(fn ($get) => $get('status') === 'rejected'),

                        Textarea::make('third_party_response')
                            ->label('Third-Party Response')
                            ->placeholder('Details from third-party verification (if applicable)')
                            ->columnSpanFull(),
                    ]),

                                ]),
                    ])
                    ->columns(3),


    Repeater::make('banks')
    ->relationship('banks')
    ->columnSpanFull()
        ->schema([
        Section::make('Bank Account Details')
                    ->schema([


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
                            ->preload()
                            ->required(),


                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'closed' => 'Closed',
                            ])
                            ->default('active')
                            ->required(),


                            Toggle::make('is_primary')
                            ->label('Primary Account')
                            ->inline(false)
                            ->default(false),

                    ])
                    ->columns(3),

    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
        ->modifyQueryUsing(fn(Builder $query) => $query->where('user_type', 'customer')->orderBy('created_at', 'desc'))

            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->label('Profile Image'),

                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone_number')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user_type')
                    ->badge()
                    ->colors([
                        'primary' => 'admin',
                        'success' => 'customer',
                        'info' => 'vendor',
                        'warning' => 'agent',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'banned',
                    ]),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),

                Tables\Columns\TextColumn::make('created_at')
                        ->label('Created At')
                        ->formatStateUsing(fn($state) => Carbon::parse($state)->diffForHumans()) // ✅ Human-readable format
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true), // ✅ Hidden by default for cleaner UI

                Tables\Columns\TextColumn::make('updated_at')
                        ->label('Updated At')
                        ->formatStateUsing(fn($state) => Carbon::parse($state)->diffForHumans()) // ✅ Human-readable format
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
            ]);
    }
}
