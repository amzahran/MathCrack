@extends('themes.default.layouts.back.master')


@section('title')
    @lang('l.Currencies List')
@endsection

@section('css')
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @can('show settings')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">@lang('l.Currencies List')</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif


                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <strong>@lang('l.Note'):</strong> @lang('l.Currency exchange rates are automatically updated 3 times per day') @lang('l.or')
                        <a href="{{ route('dashboard.admins.currencies-exchange') }}" class="">@lang('l.Update Now')</a>
                        <br>
                        @lang('l.Please note that all currencies are updated based on the euro, even if it is not the default currency, so if you are going to change the rate of one of the currencies manually, please make sure it will be based on euro')
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <div class="alert alert-dark alert-dismissible fade show" role="alert">
                        @lang('l.Please note you can change the default currency in the general settings, and this will affect the currency of the site and all products in the site so please update the prices after changing the default currency')
                        <a href="{{ route('dashboard.admins.settings') }}?tab=general">@lang('l.Go to General Settings')</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover currencies-table">
                            <thead>
                                <tr>
                                    <th>@lang('l.Code')</th>
                                    <th>@lang('l.Name')</th>
                                    <th>@lang('l.Symbol')</th>
                                    <th>@lang('l.Rate')</th>
                                    <th>@lang('l.Status')</th>
                                    <th>@lang('l.Last Updated')</th>
                                    <th>@lang('l.Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $defaultCurrency = $currencies->where('code', $settings['default_currency'])->first();
                                    $activeCurrencies = $currencies
                                        ->where('is_active', true)
                                        ->where('code', '!=', $defaultCurrency->code)
                                        ->sortBy('name');
                                    $inactiveCurrencies = $currencies->where('is_active', false)->sortBy('name');
                                @endphp

                                @if ($defaultCurrency)
                                    <tr class="default-currency">
                                        <td><span class="badge bg-label-primary">{{ strtoupper($defaultCurrency->code) }}</span></td>
                                        <td>{{ $defaultCurrency->name }}</td>
                                        <td>{{ $defaultCurrency->symbol }}</td>
                                        <td data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-dark" data-bs-original-title="{{ $defaultCurrency->rate }} = 1 EUR">{{ number_format($defaultCurrency->rate, 4) }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ __('l.Active') }}</span>
                                            <span class="badge bg-info">{{ __('l.Default') }}</span>
                                            @if ($defaultCurrency->is_manual)
                                                <span class="badge bg-warning">{{ __('l.Manual') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $defaultCurrency->last_updated_at ?? now()->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @can('edit settings')
                                                <a href="{{ route('dashboard.admins.currencies-edit') }}?id={{ encrypt($defaultCurrency->id) }}"
                                                    class="btn btn-sm btn-icon btn-info me-2"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ __('l.Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endif

                                @foreach ($activeCurrencies as $currency)
                                    <tr>
                                        <td><span class="badge bg-label-primary">{{ strtoupper($currency->code) }}</span></td>
                                        <td>{{ $currency->name }}</td>
                                        <td>{{ $currency->symbol }}</td>
                                        <td data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-dark" data-bs-original-title="{{ $currency->rate }} = 1 EUR">{{ number_format($currency->rate, 4) }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ __('l.Active') }}</span>
                                            @if ($currency->is_manual)
                                                <span class="badge bg-warning">{{ __('l.Manual') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $currency->last_updated_at ?? now()->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @can('edit settings')
                                                <a href="{{ route('dashboard.admins.currencies-status') }}?id={{ encrypt($currency->id) }}"
                                                    class="btn btn-sm btn-icon btn-warning me-2"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ __('l.Disable') }}">
                                                    <i class="fas fa-ban"></i>
                                                </a>
                                                <a href="{{ route('dashboard.admins.currencies-edit') }}?id={{ encrypt($currency->id) }}"
                                                    class="btn btn-sm btn-icon btn-info me-2"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ __('l.Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-icon btn-danger delete-role"
                                                        data-role-id="{{ encrypt($currency->id) }}"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ __('l.Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach

                                @foreach ($inactiveCurrencies as $currency)
                                    <tr>
                                        <td><span class="badge bg-label-primary">{{ strtoupper($currency->code) }}</span></td>
                                        <td>{{ $currency->name }}</td>
                                        <td>{{ $currency->symbol }}</td>
                                        <td data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-dark" data-bs-original-title="{{ $currency->rate }} = 1 EUR">{{ number_format($currency->rate, 4) }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ __('l.Inactive') }}</span>
                                            @if ($currency->is_manual)
                                                <span class="badge bg-warning">{{ __('l.Manual') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $currency->last_updated_at ?? now()->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @can('edit settings')
                                                <a href="{{ route('dashboard.admins.currencies-status') }}?id={{ encrypt($currency->id) }}"
                                                    class="btn btn-sm btn-icon btn-success me-2"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ __('l.Activate') }}">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <a href="{{ route('dashboard.admins.currencies-edit') }}?id={{ encrypt($currency->id) }}"
                                                    class="btn btn-sm btn-icon btn-info me-2"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ __('l.Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-icon btn-danger delete-role"
                                                        data-role-id="{{ encrypt($currency->id) }}"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ __('l.Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endsection


@section('js')
    <script>
        $(document).ready(function() {
            $('.currencies-table').DataTable({
                "order": [],
                "pageLength": 25,
                "aaSorting": [],
                "dom": '<"float-end mb-3"f><"float-start mb-3"l>rtip', // تغيير ترتيب عناصر الجدول لنقل البحث للطرف وإضافة مسافة
                language: {
                    buttons: {
                        colvis: '@lang('l.Show/Hide Columns')'
                    },
                    lengthMenu: "@lang('l.Show') _MENU_ @lang('l.Records') @lang('l.Per Page')",
                    search: "@lang('l.Search') :",
                    paginate: {
                        first: "@lang('l.First')",
                        previous: "@lang('l.Previous')",
                        next: "@lang('l.Next')",
                        last: "@lang('l.Last')"
                    },
                    info: "@lang('l.Show') _START_ @lang('l.To') _END_ @lang('l.Of') _TOTAL_ @lang('l.Records')",
                    infoEmpty: "@lang('l.No Records Available')",
                    infoFiltered: "@lang('l.Filtered From') _MAX_ @lang('l.Records')",
                    processing: "<i class='fa fa-spinner fa-spin'></i> @lang('l.Loading...')"
                }
            });
        });

        //  كود حذف اللغة
        $(document).on('click', '.delete-role', function(e) {
            e.preventDefault();
            var roleId = $(this).data('role-id');

            Swal.fire({
                title: '{{ __('l.Are you sure?') }}',
                text: '{{ __('l.You will be delete this forever!') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('l.Yes, delete it!') }}',
                cancelButtonText: '{{ __('l.Cancel') }}',
                customClass: {
                    confirmButton: 'btn btn-danger ms-2 mr-2 ml-2',
                    cancelButton: 'btn btn-dark ms-2 mr-2 ml-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("dashboard.admins.currencies-delete") }}?id=' + roleId;
                }
            });
        });
    </script>
@endsection
