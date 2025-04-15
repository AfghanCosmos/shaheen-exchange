<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\BanksRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\CreatedHawlasRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\KycRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\StoreRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\WalletsRelationManager;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = "Human Resources";

    public static function form(Form $form): Form
    {
        return $form->schema([
            // SECTION: USER INFO
            Forms\Components\Section::make('👤 User Information')
                ->description('Basic details of the user account')
                ->icon('heroicon-o-user-circle')
                ->schema([
                    Forms\Components\Card::make()->schema([
                        Forms\Components\Grid::make(4)->schema([

                            // Store (Visible only to Super Admin)
                            Forms\Components\Select::make('store_id')
                                ->label('🏢 Store')
                                ->relationship('storeRelatedTo', 'name')
                                ->searchable()
                                ->preload()
                                ->default(auth()->user()?->store?->id)
                                ->visible(fn ($record) => auth()->user()?->hasRole('super_admin'))
                                ->placeholder('Select store'),

                            Forms\Components\TextInput::make('name')
                                ->label('🧑 Full Name')
                                ->prefixIcon('heroicon-o-user')
                                ->required()
                                ->placeholder('e.g., John Doe')
                                ->maxLength(255),

                            Forms\Components\TextInput::make('email')
                                ->label('📧 Email')
                                ->email()
                                ->prefixIcon('heroicon-o-envelope')
                                ->required()
                                ->unique('users', 'email', ignoreRecord: true)
                                ->placeholder('e.g., john.doe@example.com'),

                            Forms\Components\TextInput::make('phone_number')
                                ->label('📞 Phone Number')
                                ->tel()
                                ->prefixIcon('heroicon-o-phone')
                                ->required()
                                ->unique('users', 'phone_number', ignoreRecord: true)
                                ->placeholder('+1 234 567 8901'),

                            Forms\Components\TextInput::make('password')
                                ->label('🔒 Password')
                                ->password()
                                ->prefixIcon('heroicon-o-lock-closed')
                                ->required(fn ($record) => $record === null)
                                ->visible(fn ($record) => $record === null)
                                ->placeholder('••••••••')
                                ->maxLength(255)
                                ->helperText('Required only for new user creation'),
                        ]),
                    ]),
                ]),

            // SECTION: ACCOUNT SETTINGS
            Forms\Components\Section::make('⚙️ Account Settings')
                ->description('Role, status and activity flags')
                ->icon('heroicon-o-cog')
                ->schema([
                    Forms\Components\Card::make()->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\Select::make('user_type')
                                ->label('🧩 User Type')
                                ->prefixIcon('heroicon-o-identification')
                                ->native(false)
                                ->options([
                                    'admin' => 'Admin',
                                    'customer' => 'Customer',
                                    'vendor' => 'Vendor',
                                    'agent' => 'Agent',
                                ])
                                ->default('customer')
                                ->placeholder('Select user type'),

                            Forms\Components\Select::make('status')
                                ->label('📌 Status')
                                ->prefixIcon('heroicon-o-adjustments-horizontal')
                                ->native(false)
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                    'banned' => 'Banned',
                                ])
                                ->default('active')
                                ->placeholder('Select status'),

                            Forms\Components\Toggle::make('is_active')
                                ->label('✅ Account Active')
                                ->inline(false)
                                ->columnSpan(1)
                                ->default(true),
                        ]),
                    ]),
                ]),

            // SECTION: PROFILE IMAGE
            Forms\Components\Section::make('🖼️ Profile Image')
                ->icon('heroicon-o-photo')
                ->schema([
                    Forms\Components\Card::make()->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Upload Image')
                            ->image()
                            ->imageEditor()
                            ->directory('public/users')
                            ->nullable(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Account Active')
                            ->columnSpanFull()
                            ->default(true),

                        Forms\Components\CheckboxList::make('roles')
                            ->relationship('roles', 'name')
                            ->columnSpanFull()
                            ->searchable(),
                    ]) ->columns(3)
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('uuid')
                    ->label('🆔 User ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\ImageColumn::make('image')
                    ->label('🖼️ Profile')
                    ->circular()
                    ->height(40)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('storeRelatedTo.name')
                    ->label('🏢 Store')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('👤 Name')
                    ->sortable()
                    ->searchable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('email')
                    ->label('📧 Email')
                    ->copyable()
                    ->searchable()
                    ->tooltip(fn ($state) => $state),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label('📞 Phone')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user_type')
                    ->label('🔐 Role')
                    ->badge()
                    ->tooltip(fn ($state) => ucfirst($state))
                    ->colors([
                        'primary' => 'admin',
                        'success' => 'customer',
                        'info' => 'vendor',
                        'warning' => 'agent',
                    ])
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('📌 Status')
                    ->sortable()
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'banned',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('✅ Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('🕒 Created')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->diffForHumans())
                    ->tooltip(fn ($state) => Carbon::parse($state)->toDayDateTimeString())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('🔄 Updated')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->diffForHumans())
                    ->tooltip(fn ($state) => Carbon::parse($state)->toDayDateTimeString())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\SelectFilter::make('user_type')
                    ->label('🔐 User Type')
                    ->options([
                        'admin' => 'Admin',
                        'customer' => 'Customer',
                        'vendor' => 'Vendor',
                        'agent' => 'Agent',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('📌 Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'banned' => 'Banned',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('View'),
                    Tables\Actions\EditAction::make()->label('Edit'),
                    Tables\Actions\DeleteAction::make()->label('Delete'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()->label('Restore Selected'),
                    Tables\Actions\ForceDeleteBulkAction::make()->label('Delete Selected'),
                ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            KycRelationManager::class,
            WalletsRelationManager::class,
            StoreRelationManager::class,
            BanksRelationManager::class,
            CreatedHawlasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
