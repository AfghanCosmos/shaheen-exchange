<?php

namespace App\Filament\Store\Resources;

use App\Filament\Store\Resources\KycResource\Pages;
use App\Models\KYC;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Storage;

class KycResource extends Resource
{
    protected static ?string $model = KYC::class;

    protected static ?string $navigationGroup = 'Customer Management';

    /**
     * Form Definition - Create & Edit
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Information')
                    ->schema([

                        TextInput::make('govt_id_type')
                            ->label('Government ID Type')
                            ->maxLength(255)
                            ->placeholder('e.g., Passport, Driver’s License'),

                        TextInput::make('govt_id_number')
                            ->label('Government ID Number')
                            ->maxLength(255)
                            ->unique('k_y_c_s', 'govt_id_number', ignoreRecord: true)
                            ->placeholder('Enter ID Number'),
                    ])->columns(2),

                Section::make('Document Details')
                    ->schema([

                        DatePicker::make('issue_date')
                            ->label('Issue Date')
                            ->required(),

                        DatePicker::make('expire_date')
                            ->label('Expiry Date')
                            ->after('issue_date'), // Ensures expiry date is after issue date

                        Forms\Components\FileUpload::make('govt_id_file')
                            ->label('Government ID File')
                            ->directory('kyc_documents')
                            ->preserveFilenames()
                            ->required()
                            ->imageEditor()
                            ->columnspanfull()
                            ->enableDownload()
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                            ->visibility('public'), // Ensure uploaded files are accessible
                    ])->columns(2),

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
            Tables\Columns\TextColumn::make('user.name')
                ->label('👤 User')
                ->sortable()
                ->searchable()
                ->badge()
                ->color('primary')
                ->limit(20)
                ->tooltip(fn ($state) => $state),

            /** 🪪 ID Type */
            Tables\Columns\TextColumn::make('govt_id_type')
                ->label('🪪 ID Type')
                ->sortable()
                ->limit(20)
                ->tooltip(fn ($state) => $state),

            /** 🔢 ID Number */
            Tables\Columns\TextColumn::make('govt_id_number')
                ->label('🔢 ID Number')
                ->searchable()
                ->limit(20)
                ->tooltip(fn ($state) => $state),

            /** 📅 Dates */
            Tables\Columns\TextColumn::make('issue_date')
                ->label('📆 Issue Date')
                ->date()
                ->sortable(),

            Tables\Columns\TextColumn::make('expire_date')
                ->label('⏳ Expiry Date')
                ->date()
                ->sortable(),

            /** 🏷️ Status */
            Tables\Columns\BadgeColumn::make('status')
                ->label('📌 Status')
                ->sortable()
                ->colors([
                    'warning' => 'pending',
                    'success' => 'verified',
                    'danger' => 'rejected',
                ])
                ->formatStateUsing(fn ($state) => ucfirst($state)),

            /** 🕒 Created/Updated */
            Tables\Columns\TextColumn::make('created_at')
                ->label('🕒 Created At')
                ->dateTime('M d, Y H:i')
                ->sortable(),

            Tables\Columns\TextColumn::make('updated_at')
                ->label('🔄 Updated At')
                ->dateTime('M d, Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])

            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ])
                    ->label('Status Filter')
                    ->placeholder('All Statuses'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListKycs::route('/'),
            'create' => Pages\CreateKyc::route('/create'),
            'edit' => Pages\EditKyc::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
