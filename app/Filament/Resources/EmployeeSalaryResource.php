<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeSalaryResource\Pages;
use App\Filament\Widgets\SalaryStatusBreakdownChart;
use App\Filament\Widgets\TotalSalaryPaidByMonthChart;
use App\Models\EmployeeSalary;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeSalaryResource extends Resource
{
    protected static ?string $model = EmployeeSalary::class;

    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Employee Salaries';
    protected static ?string $pluralModelLabel = 'Employee Salaries';

    // ================================
    // 🔹 FORM
    // ================================
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('💼 Salary Details')
                ->description('Manage salary information for employees.')
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('👤 Employee')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->required()
                        ->preload()
                        ->placeholder('Select an employee')
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state, $get) {
                            if ($state) {
                                $basicSalary = \App\Models\User::find($state)?->salary ?? 0;
                                $set('basic_salary', $basicSalary);
                                $set('net_salary', $basicSalary + $get('bonus', 0) - $get('deductions', 0));
                            }
                        }),

                    Forms\Components\Select::make('currency_id')
                        ->label('💱 Currency')
                        ->relationship('currency', 'name')
                        ->required()
                        ->native(false)
                        ->default(1)
                        ->preload()
                        ->placeholder('Select currency'),

                    Forms\Components\TextInput::make('basic_salary')
                        ->label('💵 Basic Salary')
                        ->numeric()
                        ->default(0.00)
                        ->disabled()
                        ->helperText('Auto-fetched from employee profile'),

                    Forms\Components\TextInput::make('bonus')
                        ->label('🎁 Bonus')
                        ->numeric()
                        ->default(0.00)
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set, $state, $get) =>
                            $set('net_salary', $get('basic_salary') + $state - $get('deductions'))
                        ),

                    Forms\Components\TextInput::make('deductions')
                        ->label('🧾 Deductions')
                        ->numeric()
                        ->default(0.00)
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set, $state, $get) =>
                            $set('net_salary', $get('basic_salary') + $get('bonus') - $state)
                        ),

                    Forms\Components\TextInput::make('net_salary')
                        ->label('💰 Net Salary')
                        ->numeric()
                        ->default(0.00)
                        ->helperText('Basic Salary + Bonus - Deductions'),
                ]),

            Forms\Components\Section::make('📌 Payment Status')
                ->description('Track payment status for this entry.')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->native(false)
                        ->options([
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                        ])
                        ->default('pending')
                        ->required()
                        ->helperText('Mark this salary as paid or pending.'),

                    Forms\Components\DatePicker::make('payment_date')
                        ->label('📅 Payment Date')
                        ->default(Carbon::now())
                        ->required(),
                ]),
        ]);
    }

    // ================================
    // 🔹 TABLE
    // ================================
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('👤 Employee')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('basic_salary')
                    ->label('💵 Basic Salary')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('bonus')
                    ->label('🎁 Bonus')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('deductions')
                    ->label('🧾 Deductions')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('net_salary')
                    ->label('💰 Net Salary')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('📅 Payment Date')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('📌 Status')
                    ->badge()
                    ->colors([
                        'pending' => 'warning',
                        'paid' => 'success',
                    ])
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('🕒 Created At')
                    ->dateTime('F j, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('🔄 Updated At')
                    ->dateTime('F j, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('View'),
                Tables\Actions\EditAction::make()->label('Edit'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('Delete Selected'),
            ]);
    }

    // ================================
    // 🔹 RELATIONS
    // ================================
    public static function getRelations(): array
    {
        return [];
    }

    // ================================
    // 🔹 PAGES
    // ================================
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeSalaries::route('/'),
            'create' => Pages\CreateEmployeeSalary::route('/create'),
            'view' => Pages\ViewEmployeeSalary::route('/{record}'),
            'edit' => Pages\EditEmployeeSalary::route('/{record}/edit'),
        ];
    }

    // ================================
    // 🔹 WIDGETS
    // ================================
    public static function getWidgets(): array
    {
        return [
            TotalSalaryPaidByMonthChart::class,
            SalaryStatusBreakdownChart::class,
        ];
    }
}
