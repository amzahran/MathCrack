@extends('themes.default.layouts.back.master')


@section('title')
    {{ __('l.Languages List') }}
@endsection

@section('css')
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @can('show settings')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('l.Languages List') }}</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover languages-table">
                            <thead>
                                <tr class="text-center">
                                    <th>{{ __('l.Code') }}</th>
                                    <th>{{ __('l.Name') }}</th>
                                    <th>{{ __('l.Flag') }}</th>
                                    <th>{{ __('l.Status') }}</th>
                                    <th>{{ __('l.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $defaultLanguage = $languages->firstWhere('code', $settings['default_language'] ?? '');
                                    $activeLanguages = $languages
                                        ->where('is_active', true)
                                        ->where('code', '!=', $settings['default_language'])
                                        ->sortBy('name');
                                    $inactiveLanguages = $languages->where('is_active', false)->sortBy('name');
                                @endphp

                                @if ($defaultLanguage)
                                    <tr class="text-center">
                                        <td><span class="badge bg-label-primary">{{ $defaultLanguage->code }}</span></td>
                                        <td>{{ $defaultLanguage->name }} ({{ $defaultLanguage->native }})</td>
                                        <td>
                                            <i class="fi fi-{{ strtolower($defaultLanguage->flag) }} fs-3"></i>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ __('l.Active') }}</span>
                                            <span class="badge bg-info">{{ __('l.Default') }}</span>
                                        </td>
                                        <td>
                                            @can('edit settings')
                                                <div class="d-inline-flex">
                                                    <a href="{{ route('dashboard.admins.languages-translate') }}?id={{ encrypt($defaultLanguage->id) }}&type=front"
                                                        class="btn btn-sm btn-icon btn-dark me-2"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ __('l.Translate') }}">
                                                        <i class="fas fa-language"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                        </td>
                                    </tr>
                                @endif

                                @foreach ($activeLanguages as $language)
                                    <tr class="text-center">
                                        <td><span class="badge bg-label-primary">{{ $language->code }}</span></td>
                                        <td>{{ $language->name }} ({{ $language->native }})</td>
                                        <td>
                                            <i class="fi fi-{{ strtolower($language->flag) }} fs-3"></i>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ __('l.Active') }}</span>
                                        </td>
                                        <td>
                                            @can('edit settings')
                                                <div class="d-inline-flex">
                                                    <a href="{{ route('dashboard.admins.languages-status') }}?id={{ encrypt($language->id) }}"
                                                        class="btn btn-sm btn-icon btn-warning me-2"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ __('l.Disable') }}">
                                                        <i class="fas fa-ban"></i>
                                                    </a>
                                                    <a href="{{ route('dashboard.admins.languages-translate') }}?id={{ encrypt($language->id) }}&type=front"
                                                        class="btn btn-sm btn-icon btn-dark me-2"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ __('l.Translate') }}">
                                                        <i class="fas fa-language"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-icon btn-danger delete-role"
                                                            data-role-id="{{ encrypt($language->id) }}"
                                                            data-bs-toggle="tooltip"
                                                            title="{{ __('l.Delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach

                                @foreach ($inactiveLanguages as $language)
                                    <tr class="text-center">
                                        <td><span class="badge bg-label-primary">{{ $language->code }}</span></td>
                                        <td>{{ $language->name }} ({{ $language->native }})</td>
                                        <td>
                                            <i class="fi fi-{{ strtolower($language->flag) }} fs-3"></i>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ __('l.Inactive') }}</span>
                                        </td>
                                        <td>
                                            @can('edit settings')
                                                <div class="d-inline-flex">
                                                    <a href="{{ route('dashboard.admins.languages-status') }}?id={{ encrypt($language->id) }}"
                                                        class="btn btn-sm btn-icon btn-success me-2"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ __('l.Activate') }}">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <a href="{{ route('dashboard.admins.languages-translate') }}?id={{ encrypt($language->id) }}&type=front"
                                                        class="btn btn-sm btn-icon btn-dark me-2"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ __('l.Translate') }}">
                                                        <i class="fas fa-language"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-icon btn-danger delete-role"
                                                            data-role-id="{{ encrypt($language->id) }}"
                                                            data-bs-toggle="tooltip"
                                                            title="{{ __('l.Delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
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
            $('.languages-table').DataTable({
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
                    window.location.href = '{{ route("dashboard.admins.languages-delete") }}?id=' + roleId;
                }
            });
        });
    </script>
@endsection
