@php
$record = $getRecord();
$month = Carbon\Carbon::parse($record->month);
$attendances = App\Models\Attendance::where('employee_id', $record->employee_id)
    ->whereYear('date', $month->year)
    ->whereMonth('date', $month->month)
    ->get();

$totalDays = $month->daysInMonth;
$presentDays = $attendances->where('status', 'present')->count();
$halfDays = $attendances->where('status', 'half-day')->count();
$lateDays = $attendances->where('status', 'late')->count();
$absentDays = $totalDays - $presentDays - $halfDays - $lateDays;

$ratio = "{$presentDays}/{$totalDays}";
@endphp

{{ $ratio }}