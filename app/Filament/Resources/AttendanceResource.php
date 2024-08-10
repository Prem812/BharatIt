<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'employee management';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        $now = Carbon::now();
        $daysInMonth = $now->daysInMonth;
        $totalEmployees = Employee::count();

        $attendances = Attendance::whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->get();

        $presentCount = $attendances->where('status', 'present')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $halfDayCount = $attendances->where('status', 'half-day')->count();
        $absentCount = ($totalEmployees * $daysInMonth) - $presentCount - $lateCount - $halfDayCount;

        return sprintf(
            "%d, %d, %d, %d / %d / %d",
            $presentCount,
            $lateCount,
            $halfDayCount,
            $absentCount,
            $totalEmployees,
            $daysInMonth
        );
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $now = Carbon::now();
        $daysInMonth = $now->daysInMonth;
        $totalEmployees = Employee::count();

        $attendances = Attendance::whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->get();

        $presentCount = $attendances->where('status', 'present')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $halfDayCount = $attendances->where('status', 'half-day')->count();

        $attendancePercentage = ($presentCount + $lateCount + ($halfDayCount / 2)) / ($totalEmployees * $daysInMonth) * 100;

        return $attendancePercentage < 80 ? 'warning' : 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Present, Late, Half-day, Absent / Total Employees / Days in Month';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Select::make('employee_id')
                        ->relationship(name: 'employee', titleAttribute: 'first_name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    Forms\Components\DatePicker::make('date')
                        ->required()
                        ->rules(function (callable $get, $record) {
                            return [
                                Rule::unique('attendances', 'date')
                                    ->where(function ($query) use ($get) {
                                        return $query->where('employee_id', $get('employee_id'));
                                    })
                                    ->when($record, function ($rule, $record) {
                                        return $rule->ignore($record->id);
                                    }),
                            ];
                        }),
                ])->columns(2),
                Forms\Components\Section::make()
                ->schema([
                    TimePicker::make('check_in')
                        ->default('10:00:00')
                        ->reactive()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            $checkInTime = $get('check_in');
                            if ($checkInTime) {
                                $set('status', self::getStatusBasedOnTime($checkInTime));
                            }
                        }),
                    TimePicker::make('check_out')
                        ->default('18:00:00'),
                    Select::make('status')
                        ->default('Present')
                        ->searchable()
                        ->preload()
                        ->options(function (Get $get) {
                            $checkInTime = $get('check_in');
                            if (!$checkInTime) {
                                return [
                                    'present' => 'Present',
                                    'absent' => 'Absent',
                                    'late' => 'Late',
                                    'half-day' => 'Half Day',
                                ];
                            }
                            return self::getStatusOptionsBasedOnTime($checkInTime);
                        })
                        ->required(),
                ])->columns(3),
                Forms\Components\RichEditor::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    protected static function getStatusBasedOnTime($time)
    {
        $checkInTime = Carbon::parse($time);
        $lateTime = Carbon::parse('10:20:00');
        $halfDayTime = Carbon::parse('12:00:00');
        $absentTime = Carbon::parse('14:00:00');

        if ($checkInTime->lt($lateTime)) {
            return 'present';
        } elseif ($checkInTime->lt($halfDayTime)) {
            return 'late';
        } elseif ($checkInTime->lt($absentTime)) {
            return 'half-day';
        } else {
            return 'absent';
        }
    }

    protected static function getStatusOptionsBasedOnTime($time)
    {
        $checkInTime = Carbon::parse($time);
        $lateTime = Carbon::parse('10:20:00');
        $halfDayTime = Carbon::parse('12:00:00');
        $absentTime = Carbon::parse('14:00:00');

        $options = [];

        if ($checkInTime->lt($lateTime)) {
            $options['present'] = 'Present';
        }
        if ($checkInTime->lt($absentTime)) {
            $options['late'] = 'Late';
        }
        if ($checkInTime->lt($absentTime)) {
            $options['half-day'] = 'Half Day';
        }
        $options['absent'] = 'Absent';

        return $options;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.first_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_in')
                    ->time(),
                Tables\Columns\TextColumn::make('check_out')
                    ->time(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'present',
                        'danger' => 'absent',
                        'warning' => 'late',
                        'secondary' => 'half-day',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'view' => Pages\ViewAttendance::route('/{record}'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}