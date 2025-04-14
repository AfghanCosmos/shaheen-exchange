<?php

namespace App\Filament\Store\Resources;

use App\Filament\Store\Resources\LeaveResource\Pages;
use App\Filament\Store\Resources\UserResource\RelationManagers\LeavesRelationManager;
use App\Models\Leave;
use EightyNine\Approvals\Tables\Actions\ApprovalActions;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\IconPicker;
use Filament\Tables\Actions\Action;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;
    protected static ?string $navigationGroup = "Settings";

    public static function getNavigationGroup(): string
    {
        return __('Human Resources');
    }


    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Section::make('Leave Request Details')
                ->description('Fill in the required details to request a leave')
                ->icon('heroicon-o-document-text')
                ->schema([

                    Grid::make(2)->schema([

                        Select::make('user_id')
                            ->label('Employee Name')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->hiddenOn(LeavesRelationManager::class)
                            ->preload()
                            ->required(),

                        Select::make('leave_type')
                            ->label('Leave Type')
                            ->options([
                                'sick' => '🤒 Sick Leave',
                                'annual' => '🌴 Annual Leave',
                                'casual' => '🏖️ Casual Leave',
                                'unpaid' => '💰 Unpaid Leave',
                            ])
                            ->searchable()
                            ->default('sick')
                            ->required(),

                    ]),

                    Grid::make(2)->schema([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->default(now())
                            ->required(),

                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->default(now()->addDays(1))
                            ->required(),
                    ]),

                    Textarea::make('reason')
                        ->label('Reason for Leave')
                        ->rows(3)
                        ->placeholder('Provide a valid reason for the leave request')
                        ->required(),




                ])
                ->collapsible()
                ->compact(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
        ->defaultSort('created_at', 'desc')->
        columns([
            /** 👤 Employee Name */
            Tables\Columns\TextColumn::make('user.name')
                ->label('👤 Employee')
                ->sortable()
                ->searchable()
                ->badge()
                ->color('info'),

            /** 🏷️ Leave Type */
            Tables\Columns\TextColumn::make('leave_type')
                ->label('🏷️ Leave Type')
                ->sortable()
                ->badge()
                ->color('gray'),

            /** 📅 Leave Start Date */
            Tables\Columns\TextColumn::make('start_date')
                ->label('📅 From')
                ->sortable()
                ->date('M d, Y'),

            /** 📅 Leave End Date */
            Tables\Columns\TextColumn::make('end_date')
                ->label('📅 To')
                ->sortable()
                ->date('M d, Y'),

            /** ✅ Approval Status (custom package column) */
            \EightyNine\Approvals\Tables\Columns\ApprovalStatusColumn::make("approvalStatus.status"),
        ])
        ->actions(
                ApprovalActions::make(
                    // define your action here that will appear once approval is completed
                    [
                        Tables\Actions\EditAction::make(),
                        Tables\Actions\ViewAction::make()
                    ]
                ),
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
