@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Google Analytics')
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.45.1/apexcharts.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.11.0/css/flag-icons.min.css">
    <style>
        .chart-card {
            min-height: 385px; /* Adjusted height */
        }
        .table-responsive {
            max-height: 350px; /* Add scroll for long tables */
        }
        .nav-pills .nav-link.active,
        .nav-pills .show > .nav-link {
            background-color: var(--bs-primary) !important;
            color: var(--bs-white) !important;
        }
        .card-header .nav-pills {
            margin-bottom: -1rem; /* Adjust alignment */
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-semibold mb-0">@lang('l.Google Analytics')</h4>
            @if ($google != null)
                @can('show visitors_statistics')
                    <div class="btn-group" id="period-selector">
                        <button type="button" class="btn btn-sm btn-label-primary @if(isset($_GET['period']) && $_GET['period'] == 7) active @endif" data-period="7">7 @lang('l.Days')</button>
                        <button type="button" class="btn btn-sm btn-label-primary @if(!isset($_GET['period']) || $_GET['period'] == 30) active @endif" data-period="30">30 @lang('l.Days')</button>
                        <button type="button" class="btn btn-sm btn-label-primary @if(isset($_GET['period']) && $_GET['period'] == 90) active @endif" data-period="90">90 @lang('l.Days')</button>
                        <button type="button" class="btn btn-sm btn-label-primary @if(isset($_GET['period']) && $_GET['period'] == 180) active @endif" data-period="180">180 @lang('l.Days')</button>
                    </div>
                @endcan
            @endif
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p class="mb-0">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($google != null)
            @can('edit settings')
                <div class="alert alert-info d-flex justify-content-between align-items-center mb-4 alert-dismissible fade show" role="alert">
                    <span>@lang('l.Google Analytics is currently enabled. Disabling this feature will stop Google Analytics visitor data')</span>
                    <div>
                        <form id="disable-form" action="{{ route('dashboard.admins.statistics-google-status') }}" method="post" style="display: none;">@csrf</form>
                        <button type="button" onclick="confirmDisable()" class="btn btn-sm btn-danger me-2">@lang('l.Disable Google Analytics')</button>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endcan
            @can('show visitors_statistics')
                <div class="row g-4">
                    <!-- Overview Statistics -->
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="avatar avatar-md mx-auto mb-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-user fs-4"></i></span>
                                </div>
                                <h6 class="mb-1">@lang('l.Total Active Users')</h6>
                                <h4 class="mb-0">{{ $analyticsData['metrics']->first()['activeUsers'] }}</h4>
                                <small class="text-muted">@lang('l.New Users'): {{ $analyticsData['metrics']->first()['newUsers'] }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="avatar avatar-md mx-auto mb-2">
                                    <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-show fs-4"></i></span>
                                </div>
                                <h6 class="mb-1">@lang('l.Total Page Views')</h6>
                                <h4 class="mb-0">{{ $analyticsData['totalVisitors']->sum('screenPageViews') }}</h4>
                                <small class="text-muted">@lang('l.Views Per Session'): {{ number_format($analyticsData['metrics']->first()['screenPageViewsPerSession'], 1) }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="avatar avatar-md mx-auto mb-2">
                                    <span class="avatar-initial rounded-circle bg-label-info"><i class="bx bx-time-five fs-4"></i></span>
                                </div>
                                <h6 class="mb-1">@lang('l.Avg. Session Duration')</h6>
                                <h4 class="mb-0">{{ gmdate("i:s", $analyticsData['metrics']->first()['averageSessionDuration'] ?? 0) }}</h4>
                                <small class="text-muted">@lang('l.Engagement Rate'): {{ number_format($analyticsData['metrics']->first()['engagementRate'] * 100, 1) }}%</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="avatar avatar-md mx-auto mb-2">
                                    <span class="avatar-initial rounded-circle bg-label-warning"><i class="bx bx-line-chart fs-4"></i></span>
                                </div>
                                <h6 class="mb-1">@lang('l.Bounce Rate')</h6>
                                <h4 class="mb-0">{{ number_format(($analyticsData['metrics']->first()['bounceRate'] ?? 0) * 100, 2) }}%</h4>
                                <small class="text-muted">@lang('l.Sessions Per User'): {{ number_format($analyticsData['metrics']->first()['sessionsPerUser'], 1) }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Browsers & Countries -->
                    <div class="col-lg-5">
                        <div class="card text-center h-100">
                            <div class="card-header nav-align-top">
                                <ul class="nav nav-pills card-header-pills" role="tablist">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active" role="tab"
                                            data-bs-toggle="tab" data-bs-target="#navs-pills-browser"
                                            aria-controls="navs-pills-browser" aria-selected="true">
                                            <i class="bx bxl-chrome me-1"></i> @lang('l.Top Browsers')
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                            data-bs-target="#navs-pills-country" aria-controls="navs-pills-country"
                                            aria-selected="false">
                                            <i class="bx bx-globe me-1"></i> @lang('l.Top Countries')
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content p-0">
                                <div class="tab-pane fade show active" id="navs-pills-browser" role="tabpanel">
                                    <div class="table-responsive text-start text-nowrap">
                                        <table class="table table-borderless card-table">
                                            <tbody>
                                                @php
                                                    $totalBrowserViews = $analyticsData['topBrowsers']->sum('screenPageViews');
                                                    $browserIcons = [
                                                        'chrome' => 'chrome.png',
                                                        'safari' => 'safari.png',
                                                        'firefox' => 'firefox.png',
                                                        'edge' => 'edge.png',
                                                        'opera' => 'opera.png',
                                                        'internet explorer' => 'social-label.png',
                                                        'samsung internet' => 'social-label.png',
                                                        'android' => 'android.png',
                                                        'uc browser' => 'uc.png',
                                                    ];
                                                    $colorClasses = ['bg-success', 'bg-primary', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary', 'bg-dark'];
                                                @endphp

                                                @forelse($analyticsData['topBrowsers'] as $index => $browser)
                                                    @php
                                                        $percentage = $totalBrowserViews > 0 ? ($browser['screenPageViews'] / $totalBrowserViews) * 100 : 0;
                                                        $iconFile = strtolower($browser['browser']);
                                                        $icon = $browserIcons[$iconFile] ?? 'social-label.png'; // Fallback icon
                                                        $colorClass = $colorClasses[$index % count($colorClasses)];
                                                    @endphp
                                                    <tr>
                                                        <td style="width: 5%;"><small>{{ $index + 1 }}</small></td>
                                                        <td style="width: 30%;">
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ asset('assets/themes/default/img/icons/brands/' . $icon) }}"
                                                                    alt="{{ $browser['browser'] }}" height="24" class="me-2 rounded-circle" />
                                                                <span class="text-heading text-truncate">{{ $browser['browser'] }}</span>
                                                            </div>
                                                        </td>
                                                        <td style="width: 15%;" class="text-end"><small>{{ $browser['screenPageViews'] }}</small></td>
                                                        <td style="width: 50%;">
                                                            <div class="d-flex justify-content-end align-items-center gap-2">
                                                                 <small class="fw-medium">{{ number_format($percentage, 1) }}%</small>
                                                                <div class="progress w-75" style="height: 6px;">
                                                                    <div class="progress-bar {{ $colorClass }}"
                                                                        role="progressbar" style="width: {{ $percentage }}%"
                                                                        aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                                        aria-valuemax="100"></div>
                            </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                 @empty
                                                    <tr><td colspan="4" class="text-center py-3">@lang('l.No data available')</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                        </div>
                    </div>
                                <div class="tab-pane fade" id="navs-pills-country" role="tabpanel">
                                    <div class="table-responsive text-start text-nowrap">
                                        <table class="table table-borderless card-table">
                                            <tbody>
                                                @php
                                                    $totalCountryViews = $analyticsData['topCountries']->sum('screenPageViews');
                                                    $colorClasses = ['bg-success', 'bg-primary', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary', 'bg-dark'];
                                                @endphp

                                                @forelse($analyticsData['topCountries'] as $index => $country)
                                                    @php
                                                        $percentage = $totalCountryViews > 0 ? ($country['screenPageViews'] / $totalCountryViews) * 100 : 0;
                                                        $colorClass = $colorClasses[$index % count($colorClasses)];
                                                        // تحويل رمز الدولة إلى التنسيق المطلوب لمكتبة الأعلام
                                                        $countryCode = isset($country['countryIsoCode']) && $country['countryIsoCode'] !== '(not set)'
                                                            ? strtolower($country['countryIsoCode'])
                                                            : null;

                                                        // إذا كان الرمز غير موجود، نحاول استخراجه من اسم الدولة
                                                        if (!$countryCode && isset($country['country'])) {
                                                            $countryName = $country['country'];
                                                            // قائمة الدول وأكوادها
                                                            $countryCodes = [
                                                                'United States' => 'us',
                                                                'United Kingdom' => 'gb',
                                                                'Saudi Arabia' => 'sa',
                                                                'United Arab Emirates' => 'ae',
                                                                'Kuwait' => 'kw',
                                                                'Qatar' => 'qa',
                                                                'Bahrain' => 'bh',
                                                                'Oman' => 'om',
                                                                'Egypt' => 'eg',
                                                                'Jordan' => 'jo',
                                                                'Lebanon' => 'lb',
                                                                'Iraq' => 'iq',
                                                                'Syria' => 'sy',
                                                                'Yemen' => 'ye',
                                                                'Palestine' => 'ps',
                                                                'Morocco' => 'ma',
                                                                'Algeria' => 'dz',
                                                                'Tunisia' => 'tn',
                                                                'Libya' => 'ly',
                                                                'Sudan' => 'sd',
                                                                'Türkiye' => 'tr',
                                                                'Iran' => 'ir',
                                                                'Pakistan' => 'pk',
                                                                'India' => 'in',
                                                                'China' => 'cn',
                                                                'Japan' => 'jp',
                                                                'South Korea' => 'kr',
                                                                'Russia' => 'ru',
                                                                'Germany' => 'de',
                                                                'France' => 'fr',
                                                                'Italy' => 'it',
                                                                'Spain' => 'es',
                                                                'Canada' => 'ca',
                                                                'Australia' => 'au',
                                                                'Brazil' => 'br',
                                                                'Mexico' => 'mx',
                                                                'Argentina' => 'ar',
                                                                'South Africa' => 'za',
                                                                'Afghanistan' => 'af',
                                                                'Albania' => 'al',
                                                                'Andorra' => 'ad',
                                                                'Angola' => 'ao',
                                                                'Antigua and Barbuda' => 'ag',
                                                                'Armenia' => 'am',
                                                                'Austria' => 'at',
                                                                'Azerbaijan' => 'az',
                                                                'Bahamas' => 'bs',
                                                                'Bangladesh' => 'bd',
                                                                'Barbados' => 'bb',
                                                                'Belarus' => 'by',
                                                                'Belgium' => 'be',
                                                                'Belize' => 'bz',
                                                                'Benin' => 'bj',
                                                                'Bhutan' => 'bt',
                                                                'Bolivia' => 'bo',
                                                                'Bosnia and Herzegovina' => 'ba',
                                                                'Botswana' => 'bw',
                                                                'Brunei' => 'bn',
                                                                'Bulgaria' => 'bg',
                                                                'Burkina Faso' => 'bf',
                                                                'Burundi' => 'bi',
                                                                'Cambodia' => 'kh',
                                                                'Cameroon' => 'cm',
                                                                'Cape Verde' => 'cv',
                                                                'Central African Republic' => 'cf',
                                                                'Chad' => 'td',
                                                                'Chile' => 'cl',
                                                                'Colombia' => 'co',
                                                                'Comoros' => 'km',
                                                                'Congo' => 'cg',
                                                                'Costa Rica' => 'cr',
                                                                'Croatia' => 'hr',
                                                                'Cuba' => 'cu',
                                                                'Cyprus' => 'cy',
                                                                'Czech Republic' => 'cz',
                                                                'Denmark' => 'dk',
                                                                'Djibouti' => 'dj',
                                                                'Dominica' => 'dm',
                                                                'Dominican Republic' => 'do',
                                                                'East Timor' => 'tl',
                                                                'Ecuador' => 'ec',
                                                                'El Salvador' => 'sv',
                                                                'Equatorial Guinea' => 'gq',
                                                                'Eritrea' => 'er',
                                                                'Estonia' => 'ee',
                                                                'Ethiopia' => 'et',
                                                                'Fiji' => 'fj',
                                                                'Finland' => 'fi',
                                                                'Gabon' => 'ga',
                                                                'Gambia' => 'gm',
                                                                'Georgia' => 'ge',
                                                                'Ghana' => 'gh',
                                                                'Greece' => 'gr',
                                                                'Grenada' => 'gd',
                                                                'Guatemala' => 'gt',
                                                                'Guinea' => 'gn',
                                                                'Guinea-Bissau' => 'gw',
                                                                'Guyana' => 'gy',
                                                                'Haiti' => 'ht',
                                                                'Honduras' => 'hn',
                                                                'Hungary' => 'hu',
                                                                'Iceland' => 'is',
                                                                'Indonesia' => 'id',
                                                                'Ireland' => 'ie',
                                                                'Israel' => 'il',
                                                                'Ivory Coast' => 'ci',
                                                                'Jamaica' => 'jm',
                                                                'Kazakhstan' => 'kz',
                                                                'Kenya' => 'ke',
                                                                'Kiribati' => 'ki',
                                                                'North Korea' => 'kp',
                                                                'Kosovo' => 'xk',
                                                                'Kyrgyzstan' => 'kg',
                                                                'Laos' => 'la',
                                                                'Latvia' => 'lv',
                                                                'Lesotho' => 'ls',
                                                                'Liberia' => 'lr',
                                                                'Liechtenstein' => 'li',
                                                                'Lithuania' => 'lt',
                                                                'Luxembourg' => 'lu',
                                                                'Macedonia' => 'mk',
                                                                'Madagascar' => 'mg',
                                                                'Malawi' => 'mw',
                                                                'Malaysia' => 'my',
                                                                'Maldives' => 'mv',
                                                                'Mali' => 'ml',
                                                                'Malta' => 'mt',
                                                                'Marshall Islands' => 'mh',
                                                                'Mauritania' => 'mr',
                                                                'Mauritius' => 'mu',
                                                                'Micronesia' => 'fm',
                                                                'Moldova' => 'md',
                                                                'Monaco' => 'mc',
                                                                'Mongolia' => 'mn',
                                                                'Montenegro' => 'me',
                                                                'Mozambique' => 'mz',
                                                                'Myanmar' => 'mm',
                                                                'Namibia' => 'na',
                                                                'Nauru' => 'nr',
                                                                'Nepal' => 'np',
                                                                'Netherlands' => 'nl',
                                                                'New Zealand' => 'nz',
                                                                'Nicaragua' => 'ni',
                                                                'Niger' => 'ne',
                                                                'Nigeria' => 'ng',
                                                                'Norway' => 'no',
                                                                'Panama' => 'pa',
                                                                'Papua New Guinea' => 'pg',
                                                                'Paraguay' => 'py',
                                                                'Peru' => 'pe',
                                                                'Philippines' => 'ph',
                                                                'Poland' => 'pl',
                                                                'Portugal' => 'pt',
                                                                'Romania' => 'ro',
                                                                'Rwanda' => 'rw',
                                                                'Saint Kitts and Nevis' => 'kn',
                                                                'Saint Lucia' => 'lc',
                                                                'Saint Vincent and the Grenadines' => 'vc',
                                                                'Samoa' => 'ws',
                                                                'San Marino' => 'sm',
                                                                'Sao Tome and Principe' => 'st',
                                                                'Senegal' => 'sn',
                                                                'Serbia' => 'rs',
                                                                'Seychelles' => 'sc',
                                                                'Sierra Leone' => 'sl',
                                                                'Singapore' => 'sg',
                                                                'Slovakia' => 'sk',
                                                                'Slovenia' => 'si',
                                                                'Solomon Islands' => 'sb',
                                                                'Somalia' => 'so',
                                                                'Sri Lanka' => 'lk',
                                                                'Suriname' => 'sr',
                                                                'Swaziland' => 'sz',
                                                                'Sweden' => 'se',
                                                                'Switzerland' => 'ch',
                                                                'Taiwan' => 'tw',
                                                                'Tajikistan' => 'tj',
                                                                'Tanzania' => 'tz',
                                                                'Thailand' => 'th',
                                                                'Togo' => 'tg',
                                                                'Tonga' => 'to',
                                                                'Trinidad and Tobago' => 'tt',
                                                                'Turkmenistan' => 'tm',
                                                                'Tuvalu' => 'tv',
                                                                'Uganda' => 'ug',
                                                                'Ukraine' => 'ua',
                                                                'Uruguay' => 'uy',
                                                                'Uzbekistan' => 'uz',
                                                                'Vanuatu' => 'vu',
                                                                'Vatican City' => 'va',
                                                                'Venezuela' => 've',
                                                                'Vietnam' => 'vn',
                                                                'Western Sahara' => 'eh',
                                                                'Zambia' => 'zm',
                                                                'Zimbabwe' => 'zw',
                                                            ];

                                                            $countryCode = $countryCodes[$countryName] ?? null;
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td style="width: 5%;"><small>{{ $index + 1 }}</small></td>
                                                        <td style="width: 30%;">
                                                            <div class="d-flex align-items-center">
                                                                @if($countryCode)
                                                                    <span class="fi fi-{{ $countryCode }} me-2"></span>
                                                                @else
                                                                    <i class="bx bx-question-mark rounded-circle fs-5 me-2 bg-label-secondary p-1"></i>
                                                                @endif
                                                                <span class="text-heading text-truncate">{{ $country['country'] }}</span>
                                                            </div>
                                                        </td>
                                                        <td style="width: 15%;" class="text-end"><small>{{ $country['screenPageViews'] }}</small></td>
                                                        <td style="width: 50%;">
                                                           <div class="d-flex justify-content-end align-items-center gap-2">
                                                                 <small class="fw-medium">{{ number_format($percentage, 1) }}%</small>
                                                                <div class="progress w-75" style="height: 6px;">
                                                                    <div class="progress-bar {{ $colorClass }}"
                                                                        role="progressbar" style="width: {{ $percentage }}%"
                                                                        aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                                        aria-valuemax="100"></div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                     <tr><td colspan="4" class="text-center py-3">@lang('l.No data available')</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Device Categories -->
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">@lang('l.Device Categories')</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>@lang('l.Device')</th>
                                                <th>@lang('l.Users')</th>
                                                <th>@lang('l.Percentage')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalDeviceUsers = $analyticsData['deviceCategory']->sum('activeUsers');
                                            @endphp
                                            @foreach($analyticsData['deviceCategory'] as $device)
                                                @php
                                                    $percentage = $totalDeviceUsers > 0 ? ($device['activeUsers'] / $totalDeviceUsers) * 100 : 0;
                                                    $icon = match(strtolower($device['deviceCategory'])) {
                                                        'mobile' => 'bx-mobile-alt',
                                                        'desktop' => 'bx-desktop',
                                                        'tablet' => 'bx-tablet',
                                                        default => 'bx-devices'
                                                    };
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <i class="bx {{ $icon }} me-2"></i>
                                                        {{ $device['deviceCategory'] }}
                                                    </td>
                                                    <td>{{ $device['activeUsers'] }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                                <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                                                            </div>
                                                            <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Engagement -->
                    <div class="col-lg-3">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">@lang('l.User Engagement')</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-primary">
                                                    <i class="bx bx-time"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">@lang('l.Avg. Engagement Time')</h6>
                                                <small class="text-muted">{{ gmdate("i:s", $analyticsData['userEngagement']->first()['userEngagementDuration'] ?? 0) }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-success">
                                                    <i class="bx bx-user-check"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">@lang('l.Engaged Sessions')</h6>
                                                <small class="text-muted">{{ number_format($analyticsData['userEngagement']->first()['engagedSessions'] ?? 0) }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Types Chart -->
                    <div class="col-lg-4">
                        <div class="card chart-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">@lang('l.User Types')</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $totalUsers = $analyticsData['userTypes']->sum('activeUsers');
                                    $newUsers = $analyticsData['userTypes']->where('newVsReturning', 'new')->first()['activeUsers'] ?? 0;
                                    $returningUsers = $analyticsData['userTypes']->where('newVsReturning', 'returning')->first()['activeUsers'] ?? 0;
                                @endphp
                                <div class="d-flex justify-content-center mb-4">
                                    <div id="userTypesChart" style="min-height: 300px;"></div>
                                </div>
                                <div class="d-flex justify-content-around">
                                    <div class="text-center">
                                        <h6 class="mb-1">@lang('l.New Users')</h6>
                                        <h4 class="mb-0">{{ $newUsers }}</h4>
                                        <small class="text-muted">{{ $totalUsers > 0 ? number_format(($newUsers / $totalUsers) * 100, 1) : 0 }}%</small>
                                    </div>
                                    <div class="text-center">
                                        <h6 class="mb-1">@lang('l.Returning Users')</h6>
                                        <h4 class="mb-0">{{ $returningUsers }}</h4>
                                        <small class="text-muted">{{ $totalUsers > 0 ? number_format(($returningUsers / $totalUsers) * 100, 1) : 0 }}%</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Visitors Trend Chart -->
                    <div class="col-lg-8">
                        <div class="card chart-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">@lang('l.Visitors & Page Views Trend')</h5>
                            </div>
                            <div class="card-body">
                                <div id="visitorsChart"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Most Visited Pages -->
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">@lang('l.Most Visited Pages')</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-borderless card-table">
                                        <thead>
                                            <tr class="text-nowrap">
                                                <th>@lang('l.Page Title')</th>
                                                <th>@lang('l.Views')</th>
                                                <th>@lang('l.Percentage')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalViews = $analyticsData['topPages']->sum('screenPageViews');
                                            @endphp
                                            @forelse ($analyticsData['topPages'] as $page)
                                             @php
                                                $percentage = $totalViews > 0 ? ($page['screenPageViews'] / $totalViews) * 100 : 0;
                                            @endphp
                                                <tr>
                                                    <td class="text-truncate">
                                                        <a href="https://{{ $page['fullPageUrl'] }}" target="_blank" title="{{ $page['pageTitle'] }} ({{ $page['fullPageUrl'] }})">
                                                            {{ $page['pageTitle'] !== '(not set)' ? $page['pageTitle'] : $page['fullPageUrl'] }}
                                                        </a>
                                                    </td>
                                                    <td><small>{{ $page['screenPageViews'] }}</small></td>
                                                    <td>
                                                         <div class="d-flex justify-content-end align-items-center gap-2">
                                                            <small class="fw-medium">{{ number_format($percentage, 1) }}%</small>
                                                            <div class="progress w-50" style="height: 6px;">
                                                                <div class="progress-bar bg-primary"
                                                                    role="progressbar" style="width: {{ $percentage }}%"
                                                                    aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                                    aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                             @empty
                                                <tr><td colspan="3" class="text-center py-3">@lang('l.No data available')</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Referrers -->
                     <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">@lang('l.Top Referrers')</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-borderless card-table">
                                        <thead>
                                            <tr class="text-nowrap">
                                                <th>@lang('l.Referrer URL')</th>
                                                <th>@lang('l.Views')</th>
                                                <th>@lang('l.Percentage')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalReferrerViews = $analyticsData['topReferrers']->sum('screenPageViews');
                                            @endphp
                                            @forelse ($analyticsData['topReferrers'] as $referrer)
                                             @php
                                                $percentage = $totalReferrerViews > 0 ? ($referrer['screenPageViews'] / $totalReferrerViews) * 100 : 0;
                                                $url = $referrer['pageReferrer'];
                                                $host = parse_url($url, PHP_URL_HOST) ?? $url; // Display host or full URL if parse fails
                                            @endphp
                                                <tr>
                                                    <td class="text-truncate">
                                                        <a href="{{ $url }}" target="_blank" title="{{ $url }}">
                                                            {{ $host }}
                                                        </a>
                                                    </td>
                                                    <td><small>{{ $referrer['screenPageViews'] }}</small></td>
                                                    <td>
                                                         <div class="d-flex justify-content-end align-items-center gap-2">
                                                            <small class="fw-medium">{{ number_format($percentage, 1) }}%</small>
                                                            <div class="progress w-50" style="height: 6px;">
                                                                <div class="progress-bar bg-info"
                                                                    role="progressbar" style="width: {{ $percentage }}%"
                                                                    aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                                    aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                             @empty
                                                <tr><td colspan="3" class="text-center py-3">@lang('l.No data available')</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endcan
        @else
            @can('edit settings')
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                             <div class="card-header">
                                <h5 class="card-title mb-0">@lang('l.Enable Google Analytics')</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    @lang('l.Google Analytics is currently disabled. Enabling this feature will provide all data of Google Analytics')<br>
                    @lang('l.If you need help getting these credentials, please read our detailed guide at') <a href="{{ config('app.googleAnalyticsGuideUrl') }}" target="_blank" class="alert-link">@lang('l.Here')</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                                <form action="{{ route('dashboard.admins.statistics-google-status') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                                        <label class="form-label" for="google_analytics_id">@lang('l.Google Analytics Property ID')</label>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text"><i class="bx bxs-analyse"></i></span>
                                            <input type="text" class="form-control" id="google_analytics_id" name="google_analytics" required placeholder="G-XXXXXXXXXX">
                        </div>
                    </div>
                    <div class="mb-3">
                                        <label class="form-label" for="google_analytics_file">@lang('l.Google Analytics File')</label>
                                         <input type="file" class="form-control" id="google_analytics_file" name="google_analytics_file" required accept=".json">
                                        <small class="text-muted d-block mt-1">@lang('l.Upload service-account-credentials.json file')</small>
                        </div>
                                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-check me-1"></i>
                            @lang('l.Enable Google Analytics')
                        </button>
                    </div>
                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
        @endif
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.45.1/apexcharts.min.js"></script>
    <script>
        function confirmDisable() {
            Swal.fire({
                title: '@lang("l.Are you sure?")',
                text: '@lang("l.You are about to disable Google Analytics tracking. You can re-enable it later.")',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: config.colors.danger,
                cancelButtonColor: config.colors.secondary,
                confirmButtonText: '@lang("l.Yes, disable it!")',
                cancelButtonText: '@lang("l.Cancel")',
                customClass: {
                    confirmButton: 'btn btn-danger waves-effect waves-light',
                    cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('disable-form').submit();
                }
            });
        }

        @if($google != null)
            document.addEventListener('DOMContentLoaded', function() {
                // Helper function to get theme colors
                const cardColor = config.colors.cardColor;
                const headingColor = config.colors.headingColor;
                const axisColor = config.colors.axisColor;
                const borderColor = config.colors.borderColor;
                const primaryColor = config.colors.primary;
                const secondaryColor = config.colors.secondary;
                const infoColor = config.colors.info;
                const successColor = config.colors.success;

                // Data
                const totalVisitorsData = @json($analyticsData['totalVisitors'] ?? []);
                const userTypesData = @json($analyticsData['userTypes'] ?? []);

                // RTL Check
                const isRTL = document.documentElement.getAttribute('dir') === 'rtl';

                // Visitors Trend Chart
                const visitorsChartEl = document.querySelector('#visitorsChart');
                if (visitorsChartEl) {
                    const visitorsChartConfig = {
                        series: [
                            {
                                name: '@lang("l.Active Users")',
                        type: 'line',
                                data: totalVisitorsData.map(item => [new Date(item.date).getTime(), item.activeUsers])
                            },
                             {
                                name: '@lang("l.Page Views")',
                                type: 'column', // Changed to column for variation
                                data: totalVisitorsData.map(item => [new Date(item.date).getTime(), item.screenPageViews])
                            }
                        ],
                    chart: {
                        height: 350,
                        type: 'line',
                            parentHeightOffset: 0,
                            stacked: false,
                            toolbar: { show: false },
                            zoom: { enabled: false },
                            // dir: isRTL ? 'rtl' : 'ltr' // ApexCharts handles RTL automatically based on body dir
                        },
                        markers: {
                            size: 4,
                            colors: [primaryColor, successColor],
                            strokeColors: 'transparent',
                            strokeWidth: 3,
                            hover: { size: 6 },
                        },
                        colors: [primaryColor, successColor],
                    stroke: {
                        curve: 'smooth',
                            width: [3, 0], // Line width for series 1, 0 for column
                        },
                        dataLabels: { enabled: false },
                        legend: {
                            show: true,
                            position: 'top',
                            horizontalAlign: 'center',
                             labels: { colors: axisColor }, // Use theme color
                            markers: { offsetX: isRTL ? 10 : -10 }
                        },
                        grid: {
                            borderColor: borderColor,
                            padding: { top: -10, bottom: -15, left: 0, right: 0 }
                    },
                    xaxis: {
                        type: 'datetime',
                            axisBorder: { show: false },
                            axisTicks: { color: borderColor },
                            labels: {
                                style: { colors: axisColor, fontSize: '13px' },
                                // formatter: function (val) { // Optional: customize date format
                                //     return new Date(val).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                                // }
                            },
                            tooltip: { enabled: false } // Disable x-axis tooltip
                        },
                        yaxis: [
                            {
                                // Series 1: Active Users
                                title: {
                                    text: '@lang("l.Users")',
                                    style: { color: axisColor, fontSize: '13px' }
                                },
                                labels: {
                                    style: { colors: axisColor, fontSize: '13px' },
                                    formatter: function (val) { return Math.round(val); }
                                },
                                opposite: isRTL
                            },
                            {
                                // Series 2: Page Views
                                opposite: !isRTL,
                                title: {
                                    text: '@lang("l.Views")',
                                    style: { color: axisColor, fontSize: '13px' }
                                },
                        labels: {
                                    style: { colors: axisColor, fontSize: '13px' },
                                     formatter: function (val) { return Math.round(val); }
                                },

                            }
                        ],
                        tooltip: {
                            shared: false,
                            intersect: true,
                            x: {
                                format: 'dd MMM yyyy'
                            }
                        }
                    };
                    new ApexCharts(visitorsChartEl, visitorsChartConfig).render();
                }

                // User Types Chart
                const userTypesChartEl = document.querySelector('#userTypesChart');
                if (userTypesChartEl && userTypesData.length > 0) {
                    const userTypesChartConfig = {
                        series: userTypesData.map(item => item.activeUsers),
                        labels: userTypesData.map(item =>
                            item.newVsReturning === 'new' ? '@lang("l.New Users")' : '@lang("l.Returning Users")'
                        ),
                        chart: {
                            type: 'donut',
                            height: 330 // Adjust height if needed
                        },
                        colors: [primaryColor, secondaryColor],
                        stroke: { width: 5, colors: [cardColor] },
                        dataLabels: {
                            enabled: true,
                            formatter: function (val, opt) {
                                return parseInt(val) + '%';
                            }
                        },
                    legend: {
                            show: true,
                            position: 'bottom',
                            horizontalAlign: 'center',
                            labels: { colors: axisColor, useSeriesColors: false },
                            markers: { offsetX: isRTL ? 10 : -10 },
                            itemMargin: { horizontal: 10 }
                    },
                    plotOptions: {
                            pie: {
                                donut: {
                                    size: '75%',
                                    labels: {
                                        show: true,
                                        value: {
                                            fontSize: '1.5rem',
                                            fontFamily: 'Public Sans',
                                            color: headingColor,
                                            offsetY: -15,
                                            formatter: function (val) {
                                                // Calculate total here if needed, or pass it
                                                // let total = userTypesData.reduce((acc, item) => acc + item.activeUsers, 0);
                                                // return parseInt(total) + ' Users';
                                                return parseInt(val);
                                            }
                                        },
                                        name: {
                                            fontSize: '1rem',
                                            fontFamily: 'Public Sans',
                                            color: axisColor,
                                            offsetY: 10
                                        },
                                        total: {
                                            show: true,
                                            fontSize: '0.9rem',
                                            label: '@lang("l.Total Users")',
                                            color: axisColor,
                                            formatter: function (w) {
                                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    };
                    new ApexCharts(userTypesChartEl, userTypesChartConfig).render();
                } else if (userTypesChartEl) {
                    userTypesChartEl.innerHTML = '<div class="text-center text-muted small py-5">@lang("l.No data available")</div>';
                }

                 // Handle period selector clicks
                document.querySelectorAll('#period-selector button[data-period]').forEach(button => {
                button.addEventListener('click', function() {
                        const period = this.getAttribute('data-period');
                        const currentUrl = new URL(window.location.href);
                        currentUrl.searchParams.set('period', period);
                        window.location.href = currentUrl.toString();
                    });
                });
            });
        @endif
    </script>
@endsection

