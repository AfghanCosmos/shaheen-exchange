<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\LeavesRelationManager;
use App\Models\Leave;
use EightyNine\Approvals\Tables\Actions\ApprovalActions;
use EightyNine\Approvals\Tables\Columns\ApprovalStatusColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;
    protected static ?string $navigationGroup = 'Human Resources';
    protected static ?string $navigationLabel = 'Leave Requests';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('📋 Leave Request Details')
                ->description('Please provide complete information for leave processing.')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\Card::make()->schema([

                        Grid::make(2)->schema([

                            Select::make('user_id')
                                ->label('👤 Employee')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->placeholder('Select employee')
                                ->hiddenOn(LeavesRelationManager::class),

                            Select::make('leave_type')
                                ->label('🗂️ Leave Type')
                                ->options([
                                    'sick' => '🤒 Sick Leave',
                                    'annual' => '🌴 Annual Leave',
                                    'casual' => '🏖️ Casual Leave',
                                    'unpaid' => '💰 Unpaid Leave',
                                ])
                                ->default('sick')
                                ->required()
                                ->native(false),
                        ]),

                        Grid::make(2)->schema([
                            DatePicker::make('start_date')
                                ->label('📅 Start Date')
                                ->default(now())
                                ->required(),

                            DatePicker::make('end_date')
                                ->label('📅 End Date')
                                ->default(now()->addDay())
                                ->required(),
                        ]),

                        Textarea::make('reason')
                            ->label('📝 Reason for Leave')
                            ->rows(4)
                            ->placeholder('Provide a valid reason for the leave request')
                            ->required()
                            ->columnSpanFull(),
                    ])
                ])
                ->columns(1)
                ->collapsible()
                ->compact(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('👤 Employee')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('leave_type')
                    ->label('🗂️ Leave Type')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'sick' => '🤒 Sick',
                        'annual' => '🌴 Annual',
                        'casual' => '🏖️ Casual',
                        'unpaid' => '💰 Unpaid',
                        default => ucfirst($state),
                    }),

                TextColumn::make('start_date')
                    ->label('📅 From')
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('📅 To')
                    ->date()
                    ->sortable(),

                ApprovalStatusColumn::make('approvalStatus.status')
                    ->label('📌 Status'),
            ])
            ->actions(
                ApprovalActions::make([
                    Tables\Actions\ViewAction::make()->label('View'),
                    Tables\Actions\EditAction::make()->label('Edit'),
                ])
            );
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }
}
