<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;

class StatsDashboardOverview extends BaseWidget
{

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 4,
    ];

    protected function getStats(): array
    {
        $todayAttendance = $this->getTodayAttendance();
        $past7DaysAttendance = $this->getPast7DaysAttendance();
        $weather = $this->getWeatherInfo();
        $currentDateTime = Carbon::now('Asia/Kolkata');
        $githubInfo = $this->getGitHubInfo();
        // $analyticsData = $this->getGoogleAnalyticsData();

        return [
            Card::make('Today\'s Attendance', $todayAttendance['present'] . '/' . $todayAttendance['total'])
                ->description($todayAttendance['percentage'] . '% present')
                ->descriptionIcon('heroicon-m-users')
                ->chart($past7DaysAttendance['chart'])
                ->color('success')
                ->extraAttributes([
                    'class' => 'attendance-stat',
                    'data-tooltip' => $this->getPresentEmployeesInfo(),
                ]),

            Card::make('Weather Update', $weather['temperature'] . '°C')
                ->description($weather['description'] . ' in ' . $weather['city'])  // Modified this line
                ->descriptionIcon($this->getWeatherIcon($weather['description']))
                ->chart([$weather['temp_min'], $weather['temp_max']])
                ->color('info'),

            Card::make('Current Date & Time', $currentDateTime->format('H:i'))
                ->description($currentDateTime->format('F d, Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),

            Card::make('GitHub Activity', $githubInfo['commits_this_month'] . ' commits this month')
                ->description($githubInfo['repos'] . ' repos, ' . $githubInfo['stars'] . ' stars')
                ->descriptionIcon('heroicon-m-code-bracket')
                ->chart([$githubInfo['contribution_days'], Carbon::now()->daysInMonth - $githubInfo['contribution_days']])
                ->color('danger'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }

// attendance count ->
    protected function getTodayAttendance(): array
    {
        $today = Carbon::today();
        $totalEmployees = Employee::count();
        $presentEmployees = Attendance::whereDate('date', $today)
            ->whereIn('status', ['present', 'late'])
            ->count();

        $percentage = $totalEmployees > 0 ? round(($presentEmployees / $totalEmployees) * 100, 2) : 0;

        return [
            'present' => $presentEmployees,
            'total' => $totalEmployees,
            'percentage' => $percentage,
        ];
    }

    protected function getPast7DaysAttendance(): array
    {
        $startDate = Carbon::today()->subDays(6);
        $endDate = Carbon::today();

        $attendanceData = Attendance::select(DB::raw('DATE(date) as date'), DB::raw('COUNT(*) as count'))
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->whereIn('status', ['present', 'late'])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        $chart = [];
        for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $chart[] = $attendanceData[$formattedDate] ?? 0;
        }

        return [
            'chart' => $chart,
        ];
    }

    // get presented employees
    protected function getPresentEmployeesInfo(): string
    {
        $today = Carbon::today();
        $presentEmployees = Attendance::with('employee')
            ->whereDate('date', $today)
            ->whereIn('status', ['present', 'late'])
            ->get();

        $info = "Employees present today:\n";
        foreach ($presentEmployees as $attendance) {
            $employee = $attendance->employee;
            $info .= "{$employee->first_name} {$employee->last_name} - {$attendance->status}\n";
        }

        return htmlspecialchars($info);
    }

    protected function getCurrentCity(): string
    {
        try {
            $ipAddress = request()->ip();
            $response = Http::get("http://ip-api.com/json/{$ipAddress}");
    
            if ($response->successful()) {
                $data = $response->json();
                return $data['city'] ?? 'Waidhan';
            }
        } catch (\Exception $e) {
            // Log the error if needed
            // \Log::error('Error fetching current city: ' . $e->getMessage());
        }
    
        return 'Waidhan'; // Default fallback
    }

    // get github account information
    protected function getGitHubInfo(): array
    {
        $username = 'Prem812';  // Replace with your GitHub username
        $token = env('GITHUB_TOKEN');
    
        $headers = [
            'Authorization' => 'token ' . $token,
            'Accept' => 'application/vnd.github.v3+json',
        ];
    
        try {
            // Fetch user data
            $userResponse = Http::withoutVerifying()->withHeaders($headers)->get("https://api.github.com/user");
            $userData = $userResponse->json();
    
            // Fetch public repositories
            $reposResponse = Http::withoutVerifying()->withHeaders($headers)->get("https://api.github.com/users/{$username}/repos?per_page=100&type=public");
            $repos = $reposResponse->json();
    
            // Calculate total stars and repos
            $totalStars = array_sum(array_column($repos, 'stargazers_count'));
            $totalRepos = count($repos);
    
            // Fetch contribution data (last 30 events)
            $eventsResponse = Http::withoutVerifying()->withHeaders($headers)->get("https://api.github.com/users/{$username}/events/public?per_page=30");
            $events = $eventsResponse->json();
    
            // Calculate commits in the current month
            $currentMonth = now()->format('Y-m');
            $commitsThisMonth = collect($events)
                ->filter(function ($event) use ($currentMonth) {
                    return $event['type'] === 'PushEvent' && 
                           Str::startsWith($event['created_at'], $currentMonth);
                })
                ->sum(function ($event) {
                    return count($event['payload']['commits'] ?? []);
                });
    
            // Calculate contribution days in the current month
            $contributionDays = collect($events)
                ->filter(function ($event) use ($currentMonth) {
                    return Str::startsWith($event['created_at'], $currentMonth);
                })
                ->pluck('created_at')
                ->map(function ($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })
                ->unique()
                ->count();
    
            return [
                'repos' => $totalRepos,
                'stars' => $totalStars,
                'commits_this_month' => $commitsThisMonth,
                'contribution_days' => $contributionDays,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching GitHub info: ' . $e->getMessage());
            return [
                'repos' => 0,
                'stars' => 0,
                'commits_this_month' => 0,
                'contribution_days' => 0,
            ];
        }
    }

    // get weather info
    protected function getWeatherInfo(): array
    {
        $apiKey = env('OPENWEATHERMAP_API_KEY');
        $city = $this->getCurrentCity();
    
        try {
            $response = Http::get("http://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric");
    
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'city' => $city,
                    'temperature' => round($data['main']['temp']),
                    'temp_min' => round($data['main']['temp_min']),
                    'temp_max' => round($data['main']['temp_max']),
                    'description' => $data['weather'][0]['main'],
                ];
            }
        } catch (\Exception $e) {
            // Log the error if needed
            // \Log::error('Error fetching weather info: ' . $e->getMessage());
        }
    
        return [
            'city' => $city, // Use the city we attempted to fetch weather for
            'temperature' => 'N/A',
            'temp_min' => 0,
            'temp_max' => 0,
            'description' => 'Unable to fetch weather',
        ];
    }

    protected function getWeatherIcon(string $description): string
    {
        return match (strtolower($description)) {
            'clear' => 'heroicon-m-sun',
            'clouds' => 'heroicon-m-cloud',
            'rain' => 'heroicon-m-cloud-arrow-down',
            'snow' => 'heroicon-m-cloud-snow',
            default => 'heroicon-m-cloud',
        };
    }

    protected static function viewPath(): string
    {
        return 'filament.widgets.stats-dashboard-overview';
    }
}