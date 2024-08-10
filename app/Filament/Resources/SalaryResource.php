<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalaryResource\Pages;
use App\Filament\Resources\SalaryResource\RelationManagers;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Salary;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class SalaryResource extends Resource
{
    protected static ?string $model = Salary::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-rupee';

    protected static ?string $navigationGroup = 'employee management';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        $totalPayable = static::getModel()::sum('payable_amount');
        return number_format($totalPayable, 2);
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $totalPayable = static::getModel()::sum('payable_amount');
        return $totalPayable > 50000 ? 'warning' : 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total Payable Amount';
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'first_name')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateSalaryDetails($get, $set)),
                Forms\Components\DatePicker::make('month')
                    ->required()
                    ->default(now()->startOfMonth())
                    ->format('Y-m')
                    ->displayFormat('F Y')
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $date = Carbon::parse($get('month'))->startOfMonth();
                        $set('month', $date->toDateString());
                        self::updateSalaryDetails($get, $set);
                    })
                    ->rules([
                        function (Get $get, $record) {
                            return Rule::unique('salaries', 'month')
                                ->where('employee_id', $get('employee_id'))
                                ->ignore($record);
                        },
                    ])
                    ->validationMessages([
                        'unique' => 'A salary record for this employee already exists for the selected month.',
                    ]),
                Forms\Components\TextInput::make('base_salary')
                    ->required()
                    ->numeric()
                    ->default(10000)
                    ->reactive()
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateSalaryDetails($get, $set)),
                Forms\Components\TextInput::make('upi_id')
                    ->required(),
                Forms\Components\Repeater::make('advance_payments')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('reason')
                            ->required(),
                    ])
                    ->columns(2)
                    ->reactive()
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateSalaryDetails($get, $set)),
                Forms\Components\TextInput::make('attendance_ratio')
                    ->disabled(),
                Forms\Components\TextInput::make('paid_amount')
                    ->disabled(),
                Forms\Components\TextInput::make('payable_amount')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name'),
                TextColumn::make('month')
                    ->date(),
                TextColumn::make('base_salary')
                    ->money('inr'),
                TextColumn::make('upi_id'),
                ViewColumn::make('attendance_ratio')
                    ->view('tables.columns.attendance-ratio'),
                TextColumn::make('paid_amount')
                    ->money('inr'),
                ViewColumn::make('payable_amount')
                    ->view('tables.columns.payable-amount-link')
                    ->alignRight(),
            ])
            ->filters([
                //
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

    protected static function updateSalaryDetails(Get $get, Set $set): void
    {
        $employeeId = $get('employee_id');
        $month = $get('month');
        $baseSalary = $get('base_salary') ?? 10000;

        if (!$employeeId || !$month) return;

        $employee = Employee::find($employeeId);
        if (!$employee) return;

        $monthDate = Carbon::parse($month);
        $totalDays = $monthDate->daysInMonth;
        
        $attendances = Attendance::where('employee_id', $employeeId)
            ->whereYear('date', $monthDate->year)
            ->whereMonth('date', $monthDate->month)
            ->get();

        $presentDays = $attendances->where('status', 'present')->count();
        $halfDays = $attendances->where('status', 'half-day')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $absentDays = $totalDays - $presentDays - $halfDays - $lateDays;

        $perDayAmount = $baseSalary / 25;

        $payingAmount = ($perDayAmount * $presentDays) +
                        (($perDayAmount / 2) * $halfDays) +
                        (($perDayAmount - 50) * $lateDays);

        $advancePayments = collect($get('advance_payments') ?? []);
        $paidAmount = $advancePayments->sum('amount');

        $payableAmount = $payingAmount - $paidAmount;

        $attendanceRatio = "{$presentDays}/{$totalDays}";

        $set('attendance_ratio', $attendanceRatio);
        $set('paid_amount', $paidAmount);
        $set('payable_amount', max(0, $payableAmount));
    }

    protected static function formatPayableAmountLink($record): string
    {
        $amount = number_format($record->payable_amount, 2, '.', '');
        $upiId = $record->upi_id;
        $phonePeUrl = "phonepe://pay?pa={$upiId}&pn=Salary&am={$amount}&cu=INR";
        $webUrl = "https://phon.pe/ru_PayeeVPA={$upiId}&am={$amount}";
        
        return "<a href='{$phonePeUrl}' target='_blank' onclick='handlePaymentClick(event, \"{$webUrl}\")'>₹ {$amount}</a>";
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
            'index' => Pages\ListSalaries::route('/'),
            'create' => Pages\CreateSalary::route('/create'),
            'view' => Pages\ViewSalary::route('/{record}'),
            'edit' => Pages\EditSalary::route('/{record}/edit'),
        ];
    }
}
