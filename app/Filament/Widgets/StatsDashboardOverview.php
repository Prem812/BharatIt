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
use Google_Client;
use Google_Service_AdSense;

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
        $analyticsData = $this->getGoogleAnalyticsData();
        $adSenseData = $this->getGoogleAdSenseData();

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

            // Card::make('Linkedin Followers/Connections', '149/168')
            //     ->description('Last updated: ' . date('Y-m-d'))
            //     ->descriptionIcon('heroicon-m-arrow-trending-up')
            //     ->chart([7, 2, 10, 3, 15, 4, 17])
            //     ->color('success'),

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

            Card::make('Website Visitors (Last 7 Days)', $analyticsData['totalActiveUsers'])
                ->description($analyticsData['totalPageViews'] . ' page views, ' . $analyticsData['totalSessions'] . ' sessions')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->chart($analyticsData['dailyActiveUsers'])
                ->color('success'),

            Card::make('AdSense Earnings (Last 30 Days)', '$' . number_format($adSenseData['totalEarnings'], 2))
                ->description('Daily earnings trend')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart($adSenseData['dailyEarnings'])
                ->color('success'),
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


    // get google analytics data
    protected function getGoogleAnalyticsData(): array
    {
        try {
            $client = new BetaAnalyticsDataClient([
                'credentials' => storage_path('app/analytics/service-account-credentials.json'),
            ]);
    
            $property_id = 'YOUR_GA4_PROPERTY_ID';
    
            $response = $client->runReport([
                'property' => 'properties/' . $property_id,
                'dateRanges' => [
                    new DateRange([
                        'start_date' => '7daysAgo',
                        'end_date' => 'today',
                    ]),
                ],
                'dimensions' => [
                    new Dimension(['name' => 'date']),
                ],
                'metrics' => [
                    new Metric(['name' => 'activeUsers']),
                    new Metric(['name' => 'screenPageViews']),
                    new Metric(['name' => 'sessions']),
                ],
            ]);
    
            $dailyData = [];
            $totals = [
                'activeUsers' => 0,
                'pageViews' => 0,
                'sessions' => 0,
            ];
    
            foreach ($response->getRows() as $row) {
                $date = $row->getDimensionValues()[0]->getValue();
                $activeUsers = (int) $row->getMetricValues()[0]->getValue();
                $pageViews = (int) $row->getMetricValues()[1]->getValue();
                $sessions = (int) $row->getMetricValues()[2]->getValue();
    
                $dailyData[] = $activeUsers;
    
                $totals['activeUsers'] += $activeUsers;
                $totals['pageViews'] += $pageViews;
                $totals['sessions'] += $sessions;
            }
    
            return [
                'dailyActiveUsers' => $dailyData,
                'totalActiveUsers' => $totals['activeUsers'],
                'totalPageViews' => $totals['pageViews'],
                'totalSessions' => $totals['sessions'],
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching Google Analytics data: ' . $e->getMessage());
            return [
                'dailyActiveUsers' => [],
                'totalActiveUsers' => 0,
                'totalPageViews' => 0,
                'totalSessions' => 0,
            ];
        }
    }

    // get google adsense data
    protected function getGoogleAdSenseData(): array
    {
        try {
            $client = new Google_Client();
            $client->setAuthConfig(storage_path('app/adsense/client_secrets.json'));
            $client->addScope(Google_Service_AdSense::ADSENSE_READONLY);

            // Load previously authorized token from a file, if it exists.
            // The file token.json stores the user's access and refresh tokens.
            $tokenPath = storage_path('app/adsense/token.json');
            if (file_exists($tokenPath)) {
                $accessToken = json_decode(file_get_contents($tokenPath), true);
                $client->setAccessToken($accessToken);
            }

            // If there is no previous token or it's expired.
            if ($client->isAccessTokenExpired()) {
                // Refresh the token if possible, else fetch a new one.
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                } else {
                    // Request authorization from the user.
                    $authUrl = $client->createAuthUrl();
                    printf("Open the following link in your browser:\n%s\n", $authUrl);
                    print 'Enter verification code: ';
                    $authCode = trim(fgets(STDIN));

                    // Exchange authorization code for an access token.
                    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                    $client->setAccessToken($accessToken);

                    // Save the token to a file.
                    if (!file_exists(dirname($tokenPath))) {
                        mkdir(dirname($tokenPath), 0700, true);
                    }
                    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
                }
            }

            $service = new Google_Service_AdSense($client);

            // Calculate date range (last 30 days)
            $endDate = date('Y-m-d');
            $startDate = date('Y-m-d', strtotime('-30 days'));

            $optParams = [
                'dateRange' => 'CUSTOM',
                'startDate.year' => substr($startDate, 0, 4),
                'startDate.month' => substr($startDate, 5, 2),
                'startDate.day' => substr($startDate, 8, 2),
                'endDate.year' => substr($endDate, 0, 4),
                'endDate.month' => substr($endDate, 5, 2),
                'endDate.day' => substr($endDate, 8, 2),
            ];

            $report = $service->accounts_reports->generate('accounts/pub-XXXXXXXXXXXXXXXX', $optParams);

            $totalEarnings = 0;
            $dailyEarnings = [];

            foreach ($report->getRows() as $row) {
                $earnings = $row[1]; // Assuming earnings are in the second column
                $totalEarnings += $earnings;
                $dailyEarnings[] = $earnings;
            }

            return [
                'totalEarnings' => $totalEarnings,
                'dailyEarnings' => $dailyEarnings,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching Google AdSense data: ' . $e->getMessage());
            return [
                'totalEarnings' => 0,
                'dailyEarnings' => [],
            ];
        }
    }

    // get github account information
    protected function getGitHubInfo(): array
    {
        $username = 'Prem812';  // Replace with your GitHub username
        $token = '';  // Replace with your new fine-grained token
    
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
        $apiKey = '038e52e58da2766af1f820a6e3a42150';
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