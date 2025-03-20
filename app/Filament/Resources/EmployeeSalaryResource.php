<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeSalaryResource\Pages;
use App\Filament\Widgets\SalaryStatusBreakdownChart;
use App\Filament\Widgets\TotalSalaryPaidByMonthChart;
use App\Models\EmployeeSalary;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeSalaryResource extends Resource
{
    protected static ?string $model = EmployeeSalary::class;


    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'Expenses';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Salary Details'))
                    ->description(__('Manage salary details for employees'))
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('Employee'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->placeholder(__('Select an Employee'))
                            ->reactive()
                            ->preload()
                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                if ($state) {
                                    $basicSalary = \App\Models\User::find($state)?->salary ?? 0;
                                    $set('basic_salary', $basicSalary);
                                    $set('net_salary', $basicSalary + $get('bonus', 0) - $get('deductions', 0));
                                }
                            }),


                        Forms\Components\Select::make('currency_id')
                            ->label(__('Currency'))
                            ->relationship('currency', 'name')
                            ->required()
                            ->native(false)
                            ->default(1)
                            ->placeholder(__('Select a Currency'))
                            ->preload(),

                        Forms\Components\TextInput::make('basic_salary')
                            ->label(__('Basic Salary'))
                            ->numeric()
                            ->default(0.00)
                            ->disabled()
                            ->helperText(__('Fetched automatically from employee details')),

                        Forms\Components\TextInput::make('bonus')
                            ->label(__('Bonus'))
                            ->numeric()
                            ->default(0.00)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                $set('net_salary', $get('basic_salary') + $state - $get('deductions'));
                            }),

                        Forms\Components\TextInput::make('deductions')
                            ->label(__('Deductions'))
                            ->numeric()
                            ->default(0.00)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                $set('net_salary', $get('basic_salary') + $get('bonus') - $state);
                            }),

                        Forms\Components\TextInput::make('net_salary')
                            ->label(__('Net Salary'))
                            ->numeric()
                            ->default(0.00)
                            // ->disabled()
                            ->helperText(__('Calculated as Basic Salary + Bonus - Deductions')),

                        Forms\Components\DatePicker::make('payment_date')
                            ->label(__('Payment Date'))
                            ->default(Carbon::now())
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make(__('Payment Status'))
                    ->description(__('Update the status of the payment'))
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->native(false)
                            ->options([
                                'pending' => __('Pending'),
                                'paid' => __('Paid'),
                            ])
                            ->default('pending')
                            ->required()
                            ->helperText(__('Choose the current status of the payment')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('Employee'))
                    ->searchable()
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('basic_salary')
                    ->label(__('Basic Salary'))
                    ->numeric()
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bonus')
                    ->label(__('Bonus'))
                    ->numeric()
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('deductions')
                    ->label(__('Deductions'))
                    ->numeric()
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('net_salary')
                    ->label(__('Net Salary'))
                    ->numeric()
                    ->toggleable()

                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label(__('Payment Date'))
                    ->date()
                    ->toggleable()

                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->toggleable()
                    ->colors([
                        'pending' => 'primary',
                        'paid' => 'success'
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'pending' => __('Pending'),
                        'paid' => __('Paid'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define any relations if required
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeSalaries::route('/'),
            'create' => Pages\CreateEmployeeSalary::route('/create'),
            'view' => Pages\ViewEmployeeSalary::route('/{record}'),
            'edit' => Pages\EditEmployeeSalary::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            TotalSalaryPaidByMonthChart::class,
            SalaryStatusBreakdownChart::class,
        ];
    }
}
