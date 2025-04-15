<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KycResource\Pages;
use App\Models\KYC;

use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfolistSection;

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
        return $form->schema([
            /** 👤 User Information */
            Section::make('👤 User Information')
                ->description('Select the user and enter their ID details.')
                ->columns(3)
                ->schema([
                    Select::make('user_id')
                        ->label('🙋 User')
                        ->relationship('user', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),

                    TextInput::make('govt_id_type')
                        ->label('📘 ID Type')
                        ->placeholder('e.g., Passport, Driver’s License')
                        ->maxLength(255)
                        ->required(),

                    TextInput::make('govt_id_number')
                        ->label('🆔 ID Number')
                        ->placeholder('Enter ID Number')
                        ->maxLength(255)
                        ->required()
                        ->unique('k_y_c_s', 'govt_id_number', ignoreRecord: true),
                ]),

            /** 📁 Document Details */
            Section::make('📁 Document Details')
                ->description('Upload the ID document and enter its issue/expiry dates.')
                ->columns(2)
                ->schema([
                    DatePicker::make('issue_date')
                        ->label('📅 Issue Date')
                        ->required(),

                    DatePicker::make('expire_date')
                        ->label('📅 Expiry Date')
                        ->after('issue_date')
                        ->required(),

                    FileUpload::make('govt_id_file')
                        ->label('🗂️ Government ID File')
                        ->directory('kyc_documents')
                        ->preserveFilenames()
                        ->imageEditor()
                        ->enableDownload()
                        ->maxSize(2048)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                        ->visibility('public')
                        ->required()
                        ->columnSpanFull(),
                ]),

            /** 📊 Status & Response */
            Section::make('📊 Status & Review')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->label('🔍 KYC Status')
                        ->options([
                            'pending' => 'Pending',
                            'verified' => 'Verified',
                            'rejected' => 'Rejected',
                        ])
                        ->default('pending')
                        ->native(false)
                        ->required(),

                    Textarea::make('rejection_reason')
                        ->label('🚫 Rejection Reason')
                        ->placeholder('Reason for rejection (required if status is Rejected)')
                        ->visible(fn ($get) => $get('status') === 'rejected')
                        ->columnSpanFull(),

                    Textarea::make('third_party_response')
                        ->label('📤 Third-Party Response')
                        ->placeholder('Optional notes from third-party verification tools')
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
                TextColumn::make('user.name')
                    ->label('👤 User')
                    ->searchable()
                    ->sortable()
                    ->limit(20),

                TextColumn::make('govt_id_type')
                    ->label('📘 ID Type')
                    ->sortable()
                    ->limit(20),

                TextColumn::make('govt_id_number')
                    ->label('🆔 ID Number')
                    ->searchable()
                    ->limit(20),

                IconColumn::make('govt_id_file')
                    ->label('📄 Document')
                    ->icon('heroicon-o-document-arrow-down')
                    ->tooltip('View/Download ID Document')
                    ->url(fn ($record) => \Storage::url($record->govt_id_file), true),

                TextColumn::make('issue_date')
                    ->label('📅 Issued')
                    ->date()
                    ->sortable(),

                TextColumn::make('expire_date')
                    ->label('📅 Expires')
                    ->date()
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('📌 Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'verified',
                        'danger' => 'rejected',
                    ])
                    ->sortable()
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                TextColumn::make('created_at')
                    ->label('📅 Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('🔄 Updated')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('📌 Filter by Status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ])
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfolistSection::make('👤 User & ID Info')
                ->columns(3)
                ->schema([
                    TextEntry::make('user.name')->label('User'),
                    TextEntry::make('govt_id_type')->label('ID Type'),
                    TextEntry::make('govt_id_number')->label('ID Number'),
                    TextEntry::make('issue_date')->label('Issued On')->date(),
                    TextEntry::make('expire_date')->label('Expires On')->date(),
                    TextEntry::make('status')->label('Status')->badge()->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'verified' => 'success',
                        'rejected' => 'danger',
                    }),
                ]),

            InfolistSection::make('📄 Document')
                ->schema([
                    TextEntry::make('govt_id_file')
                        ->label('Download Document')
                        ->url(fn ($record) => \Storage::url($record->govt_id_file), true)
                        ->icon('heroicon-o-document-arrow-down'),
                ]),

            InfolistSection::make('📤 Third-Party & Notes')
                ->collapsed()
                ->schema([
                    TextEntry::make('third_party_response')->visible(fn ($state) => filled($state)),
                    TextEntry::make('rejection_reason')->visible(fn ($state) => filled($state)),
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
            'index' => Pages\ListKycs::route('/'),
            'create' => Pages\CreateKyc::route('/create'),
            'edit' => Pages\EditKyc::route('/{record}/edit'),
        ];
    }
}
