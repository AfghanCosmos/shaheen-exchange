<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HawlaResource\Pages;
use App\Filament\Resources\HawlaResource\RelationManagers;
use App\Models\Hawla;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HawlaResource extends Resource
{
    protected static ?string $model = Hawla::class;


    public static function form(Form $form): Form
{
    return $form
        ->schema([

            Forms\Components\Section::make('Sender Details')
                ->description('Information about the sender.')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\Select::make('hawla_type_id')
                                ->label('Hawla Type')
                                ->relationship('hawlaType', 'name')
                                ->native(false)
                                ->preload()
                                ->required(),

                            Forms\Components\TextInput::make('sender_name')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('sender_phone')

                                ->required()
                                ->maxLength(255),
                        ]),
                    Forms\Components\Select::make('sender_store_id')
                        ->relationship('senderStore', 'name', function ($query) {
                            if (! auth()->user()->hasRole('super_admin')) {
                                $query->where('id', auth()->user()?->store?->id);
                            }
                            return $query;
                        })
                        ->label('Sender Store')
                        ->native()
                        ->required(),
                ]),


            Forms\Components\Section::make('Receiver Details')
                ->description('Information about the receiver.')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('receiver_name')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('receiver_father')
                                ->maxLength(255),

                                Forms\Components\TextInput::make('receiver_phone_number')

                                ->maxLength(20),
                        ]),

                    Forms\Components\Textarea::make('receiver_address')
                        ->columnSpanFull(),

                    Forms\Components\Select::make('receiver_store_id')
                        ->relationship('receiverStore', 'name')
                        ->label('Receiver Store')
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),

            Forms\Components\Section::make('Amounts & Currency')
                ->description('Financial values and currency types.')
                ->schema([
                    Forms\Components\Grid::make(4)
                        ->schema([
                            Forms\Components\Select::make('given_amount_currency_id')
                                ->relationship('givenCurrency', 'code')
                                ->label('Given Currency')
                                   ->native()
                                ->required(),

                            Forms\Components\TextInput::make('given_amount')
                                ->required()
                                ->numeric(),

                            Forms\Components\Select::make('receiving_amount_currency_id')
                                ->relationship('receivingCurrency', 'code')
                                ->label('Receiving Currency')
                                ->native()
                                ->preload()
                                ->required(),

                             Forms\Components\TextInput::make('receiving_amount')
                                ->required()
                                ->numeric(),


                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([

                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('exchange_rate')
                                ->numeric(),

                            Forms\Components\TextInput::make('commission')
                                ->numeric(),
                        ]),

                    Forms\Components\Select::make('commission_taken_by')
                    ->options([
                        'sender_store' => 'Sender Store',
                        'receiver_store' => 'Receiver Store',
                    ])->native()
                    ->default('sender_store')
                       ,
                ]),

            Forms\Components\Section::make('Note')
                ->schema([
                    Forms\Components\Textarea::make('note')
                        ->columnSpanFull()
                        ->rows(3),
                ]),



                Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\DateTimePicker::make('date')
                            ->default(now())
                                ->required(),

                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                         'pending' => 'Pending',
                                         'in_progress' => 'In Progress',
                                         'completed' => 'Completed',
                                         'cancelled' => 'Cancelled',
                                    ])
                                    ->native(false)
                                    ->required(),

                            Forms\Components\Select::make('created_by')
                                ->relationship('creator', 'name')
                                ->label('Created By')
                                ->searchable()
                                ->default(auth()->user()->id)
                                ->required()
                                ->visible(fn() => auth()->user()->hasRole('super_admin')),
                        ]),

                        Forms\Components\Section::make()
                        ->col
                        ->columns(1)
                                    ->schema([

                                        Forms\Components\FileUpload::make('receiver_verification_document')
                                            ->label('Receiver Verification Document')
                                            ->maxSize(4024) // Maximum file size in KB
                                            ->directory('receiver_verification_documents')
                                            ->preserveFilenames()
                                    ]),

        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('uuid')
                ->label('UUID')
                ->searchable()
                ->copyable()
                ->copyMessage('UUID copied!')
                ->copyMessageDuration(1500),

            Tables\Columns\TextColumn::make('date')
                ->label('Date')
                ->dateTime()
                ->sortable(),

            Tables\Columns\TextColumn::make('hawlaType.name')
                ->label('Hawla Type')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('sender_name')
                ->label('Sender')
                ->searchable(),

            Tables\Columns\TextColumn::make('receiver_name')
                ->label('Receiver')
                ->searchable(),

            Tables\Columns\TextColumn::make('given_amount')
                ->label('Given')
                ->numeric()
                ->sortable()
                ->suffix(fn ($record) => ' ' . optional($record->givenCurrency)->code),

            Tables\Columns\TextColumn::make('receiving_amount')
                ->label('Receiving')
                ->numeric()
                ->sortable()
                ->suffix(fn ($record) => ' ' . optional($record->receivingCurrency)->code),

            Tables\Columns\TextColumn::make('commission')
                ->numeric()
                ->sortable()
                ->label('Commission'),

            Tables\Columns\BadgeColumn::make('status.name')
                ->label('Status')
                ->sortable()
                ->colors([
                    'primary' => fn ($state) => $state === 'Pending',
                    'success' => fn ($state) => $state === 'Completed',
                    'danger' => fn ($state) => $state === 'Cancelled',
                ])
                ->icons([
                    'heroicon-o-clock' => 'Pending',
                    'heroicon-o-check-circle' => 'Completed',
                    'heroicon-o-x-circle' => 'Cancelled',
                ]),

            Tables\Columns\TextColumn::make('senderStore.name')
                ->label('From Store')
                ->sortable(),

            Tables\Columns\TextColumn::make('receiverStore.name')
                ->label('To Store')
                ->sortable(),

            Tables\Columns\TextColumn::make('creator.name')
                ->label('Created By')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Created')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('updated_at')
                ->label('Updated')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('status')
                ->relationship('status', 'name')
                ->label('Status'),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHawlas::route('/'),
            'create' => Pages\CreateHawla::route('/create'),
            'view' => Pages\ViewHawla::route('/{record}'),
            'edit' => Pages\EditHawla::route('/{record}/edit'),
        ];
    }
}
