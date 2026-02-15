<?php

namespace App\Http\Controllers\Web\Back\Admins\Statistics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Shetabit\Visitor\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

class VisitiorsController extends Controller
{
    public function visitors(Request $request)
    {
        if (!Gate::allows('show visitors_statistics')) {
            return view('themes/default/back.permission-denied');
        }

        $accept = Setting::where('option', 'allow_cookies')->first()->value ?? 0;
        if ($accept == 0) {
            return view('themes/default/back.admins.statistics.visitors.visitors-statistics', compact('accept'));
        }

        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $totalVisits = Visit::whereBetween('created_at', [$startDate, $endDate])->count();

        $statistics = [
            'totalVisits' => $totalVisits,
            'todayVisits' => Visit::whereDate('created_at', Carbon::today())->count(),
            'newVisits' => Visit::whereBetween('created_at', [$startDate, $endDate])
                                ->distinct('ip')
                                ->count('ip'),
            'registeredPercentage' => $totalVisits > 0 ?
                (Visit::whereBetween('created_at', [$startDate, $endDate])
                    ->whereNotNull('visitor_id')
                    ->count() * 100 / $totalVisits) : 0,
            'unregisteredPercentage' => $totalVisits > 0 ?
                (Visit::whereBetween('created_at', [$startDate, $endDate])
                    ->whereNull('visitor_id')
                    ->count() * 100 / $totalVisits) : 0,
            'deviceTypes' => Visit::selectRaw('platform, COUNT(*) as count')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('platform')
                ->groupBy('platform')
                ->get()
                ->map(function($item) {
                    $device = strtolower($item->platform);
                    if ($device == 'windows' || $device == 'windowsphone' || $device == 'windowsphoneos') {
                        $device = 'Desktop';
                    } elseif ($device == 'mac' || $device == 'macos') {
                        $device = 'Desktop';
                    } elseif ($device == 'ios' || $device == 'iphone' || $device == 'ipad') {
                        $device = 'Mobile';
                    } elseif ($device == 'androidos' || $device == 'android' || $device == 'androidtablet') {
                        $device = 'Mobile';
                    } else{
                        $device = 'Other';
                    }
                    return [
                        'name' => $device,
                        'count' => $item->count
                    ];
                })
                ->groupBy('name')
                ->map(function($group) {
                    return $group->sum('count');
                }),
            'browsers' => Visit::selectRaw('browser, COUNT(*) as count')
                               ->whereBetween('created_at', [$startDate, $endDate])
                               ->groupBy('browser')
                               ->get(),
            'topPages' => Visit::selectRaw('url, COUNT(*) as count')
                               ->whereBetween('created_at', [$startDate, $endDate])
                               ->groupBy('url')
                               ->orderByDesc('count')
                               ->limit(10)
                               ->get(),
        ];
        $accept = 1;

        return view('themes/default/back.admins.statistics.visitors.visitors-statistics', compact('statistics', 'accept'));
    }

    public function visitorsStatus(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $accept = Setting::where('option', 'allow_cookies')->first()->value ?? 0;
        if ($accept == 1) {
            Visit::truncate();
        }
        $accept = !$accept;
        Setting::where('option', 'allow_cookies')->update(['value' => $accept]);

        // مسح الكاش وإعادة تحميله
        Cache::forget('app_cached_data');

        return redirect()->back()->with('success', __('l.Visitor tracking status updated successfully'));
    }

    public function google(Request $request)
    {
        if (!Gate::allows('show google_analytics')) {
            return view('themes/default/back.permission-denied');
        }

        $google = Setting::where('option', 'google_analytics')->first()->value ?? null;

        if ($google) {
            $days = $request->input('period', 30);
            $period = Period::days($days);

            $analyticsData = [
                'visitors' => Analytics::fetchVisitorsAndPageViews($period),
                'topBrowsers' => Analytics::fetchTopBrowsers($period),
                'topPages' => Analytics::fetchMostVisitedPages($period),
                'totalVisitors' => Analytics::fetchTotalVisitorsAndPageViews($period),
                'userTypes' => Analytics::fetchUserTypes($period),
                'topCountries' => Analytics::fetchTopCountries($period),
                'topReferrers' => Analytics::fetchTopReferrers($period),
                'metrics' => Analytics::get($period, [
                    'averageSessionDuration',
                    'bounceRate',
                    'engagedSessions',
                    'engagementRate',
                    'sessionsPerUser',
                    'totalUsers',
                    'newUsers',
                    'activeUsers',
                    'screenPageViewsPerSession'
                ]),
                'deviceCategory' => Analytics::get($period, ['activeUsers'], ['deviceCategory']),
                'userEngagement' => Analytics::get($period, ['userEngagementDuration', 'engagedSessions'])
            ];

            return view('themes/default/back.admins.statistics.visitors.google-analytics', compact('analyticsData', 'google'));
        }

        return view('themes/default/back.admins.statistics.visitors.google-analytics', compact('google'));
    }

    public function googleStatus(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $google = Setting::where('option', 'google_analytics')->first()->value ?? null;
        if ($google == null) {
            $request->validate([
                'google_analytics' => 'required|string|max:255',
                'google_analytics_file' => 'required|file|mimes:json',
            ]);

            $google = $request->input('google_analytics');
            // upload the file
            $file = $request->file('google_analytics_file');
            $file->move(public_path('files/analytics'), 'service-account-credentials.json');
            // update the env file
            update_env(['ANALYTICS_PROPERTY_ID' => $google]);
        } else {
            $google = null;
            // delete the file
            unlink(public_path('files/analytics/service-account-credentials.json'));
        }

        Setting::where('option', 'google_analytics')->update(['value' => $google]);

        // مسح الكاش وإعادة تحميله
        Cache::forget('app_cached_data');

        return redirect()->back()->with('success', __('l.Google Analytics status updated successfully'));
    }
}