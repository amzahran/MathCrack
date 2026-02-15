@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Visitors Statistics')
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/jquery-jvectormap@2.0.5/jquery-jvectormap.css" rel="stylesheet">
    <style>
        .jvectormap-container {
            width: 100%;
            height: 100%;
            border-radius: 8px;
        }

        #deviceTypesList .badge {
            font-size: 0.9rem;
            margin-right: 8px;
            margin-bottom: 8px;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-semibold mb-4">@lang('l.Visitors Statistics')</h4>

        @if ($accept == 1)
            @can('edit settings')
                <div class="alert alert-info d-flex justify-content-between align-items-center">
                    @lang('l.Visitor tracking is currently enabled. Disabling this feature will stop tracking visitor data')
                    <form id="disable-form" action="{{ route('dashboard.admins.statistics-visitors-status') }}" method="post" style="display: none;">
                        @csrf
                    </form>
                    <button type="button" onclick="confirmDisable()" class="btn btn-primary mt-2 btn-sm">@lang('l.Disable Visitor Tracking')</button>
                </div>
            @endcan
            @can('show visitors_statistics')
                <form method="GET" action="{{ route('dashboard.admins.statistics-visitors') }}">
                    <div class="input-group mb-3">
                        <select class="form-select" name="month">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}"
                                    {{ $m == request()->input('month', now()->month) ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-secondary" type="submit">@lang('l.Show Statistics')</button>
                    </div>
                </form>

                <!-- الإحصائيات العامة -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-chart-line text-primary me-2" style="font-size: 24px;"></i>
                                    <h5 class="mb-0">@lang('l.Total Visits This Month')</h5>
                                </div>
                                <h2>{{ $statistics['totalVisits'] }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user-plus text-success me-2" style="font-size: 24px;"></i>
                                    <h5 class="mb-0">@lang('l.New Visits This Month')</h5>
                                </div>
                                <h2>{{ $statistics['newVisits'] }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-users text-info me-2" style="font-size: 24px;"></i>
                                    <h5 class="mb-0">@lang('l.Registered Visitors This Month')</h5>
                                </div>
                                <h2>{{ number_format($statistics['registeredPercentage'], 2) }}%</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user-secret text-warning me-2" style="font-size: 24px;"></i>
                                    <h5 class="mb-0">@lang('l.Unregistered Visitors This Month')</h5>
                                </div>
                                <h2>{{ number_format($statistics['unregisteredPercentage'], 2) }}%</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- المخططات -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5>@lang('l.Device Types Distribution This Month')</h5>
                                <div id="deviceTypesChart" style="height: 350px;"></div>
                                <div class="mt-3">
                                    <div id="deviceTypesList" class="d-flex flex-wrap gap-2">
                                        @foreach ($statistics['deviceTypes'] as $device => $count)
                                            <div class="badge bg-primary p-2">
                                                {{ $device }}: {{ $count }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5>@lang('l.Today Visits')</h5>
                                <h2 class="mb-0">{{ $statistics['todayVisits'] }}</h2>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h5>@lang('l.Browsers This Month')</h5>
                                <div id="browsersChart"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الصفحات الأكثر زيارة -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5>@lang('l.Top Pages This Month')</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>@lang('l.Page')</th>
                                                <th>@lang('l.Visits')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($statistics['topPages'] as $page)
                                                <tr>
                                                    <td><a href="{{ $page->url }}" target="_blank">{{ $page->url }}</a></td>
                                                    <td>{{ $page->count }}</td>
                                                </tr>
                                            @endforeach
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
                <div class="alert alert-warning">
                    @lang('l.Visitor tracking is currently disabled. Enabling this feature will provide comprehensive visitor analytics but please be aware that it requires significant server resources to track and store all visitor data')
                    <form action="{{ route('dashboard.admins.statistics-visitors-status') }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-primary mt-2 btn-sm">@lang('l.Enable Visitor Tracking')</button>
                    </form>
                </div>
            @endcan
        @endif
    </div>
@endsection

@section('js')
    @if ($accept == 1)
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-jvectormap@2.0.5/jquery-jvectormap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-jvectormap@2.0.5/jquery-jvectormap-world-mill.js"></script>

        <script>
            function confirmDisable() {
                Swal.fire({
                    title: '@lang('l.Are you sure?')',
                    text: '@lang('l.You are about to disable visitor tracking. This action cannot be undone while disabled.')',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '@lang('l.Yes, disable it!')',
                    cancelButtonText: '@lang('l.Cancel')'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('disable-form').submit();
                    }
                });
            }

            // مخطط المتصفحات
            var browserOptions = {
                series: {!! json_encode($statistics['browsers']->pluck('count')) !!},
                labels: {!! json_encode($statistics['browsers']->pluck('browser')) !!},
                chart: {
                    type: 'donut',
                    height: 300
                },
                legend: {
                    position: 'bottom'
                }
            };
            new ApexCharts(document.querySelector("#browsersChart"), browserOptions).render();

            // مخطط توزيع الأجهزة
            var deviceOptions = {
                series: [{
                    name: '@lang('l.Number of Visits')',
                    data: {!! json_encode(array_values($statistics['deviceTypes']->toArray())) !!}
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true,
                    }
                },
                dataLabels: {
                    enabled: true
                },
                xaxis: {
                    categories: {!! json_encode(array_keys($statistics['deviceTypes']->toArray())) !!},
                },
                colors: ['#696cff']
            };

            new ApexCharts(document.querySelector("#deviceTypesChart"), deviceOptions).render();
        </script>
    @endif
@endsection
