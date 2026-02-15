@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Contact Us')
@endsection


@section('content')
    <div class="main-content">
        @can('show contact_us')
            {{-- @if ($settings['recaptcha'] != 1)
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    @lang('l.Please enable reCAPTCHA settings to prevent spam messages from bots from')
                    <a href="{{ route('dashboard.admins.settings') }}?tab=security">
                        @lang('l.here')
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif --}}

            <div class="card" style="padding: 15px;">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                <div class="card-datatable table-responsive">
                    <div class="mb-3">
                        @can('delete contact_us')
                            <button id="deleteSelected" class="btn btn-danger d-none">
                                <i class="fa fa-trash ti-xs me-1"></i>@lang('l.Delete Selected')
                            </button>
                        @endcan
                    </div>
                    <table class="table" id="contacts-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>#</th>
                                <th>@lang('l.Name')</th>
                                <th>@lang('l.Email')</th>
                                <th>@lang('l.Phone')</th>
                                <th>@lang('l.Subject')</th>
                                <th>@lang('l.Message')</th>
                                <th>@lang('l.Status')</th>
                                <th>@lang('l.Created At')</th>
                                <th>@lang('l.Action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        @endcan
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            let table = $('#contacts-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ request()->fullUrl() }}',
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<input type="checkbox" class="form-check-input row-checkbox" value="${data.id}">`;
                        }
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'id',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'email',
                        name: 'email',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'subject',
                        name: 'subject',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'details',
                        name: 'details',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'status',
                        name: 'status',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [8, 'desc']
                ],
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "@lang('l.All')"]
                ],
                dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rtip',
                language: {
                    search: "@lang('l.Search'):",
                    lengthMenu: "@lang('l.Show') _MENU_ @lang('l.entries')",
                    paginate: {
                        next: '@lang('l.Next')',
                        previous: '@lang('l.Previous')'
                    },
                    info: "@lang('l.Showing') _START_ @lang('l.to') _END_ @lang('l.of') _TOTAL_ @lang('l.entries')",
                    infoEmpty: "@lang('l.Showing') 0 @lang('l.To') 0 @lang('l.Of') 0 @lang('l.entries')",
                    infoFiltered: "@lang('l.Showing') 1 @lang('l.Of') 1 @lang('l.entries')",
                    zeroRecords: "@lang('l.No matching records found')",
                    loadingRecords: "@lang('l.Loading...')",
                    processing: "@lang('l.Processing...')",
                    emptyTable: "@lang('l.No data available in table')",
                }
            });

            // حدث تحديد/إلغاء تحديد الكل
            $('#select-all').on('change', function() {
                $('.row-checkbox').prop('checked', $(this).prop('checked'));
                updateDeleteButton();
            });

            // تحديث حالة زر الحذف عند تغيير أي صندوق تحديد
            $(document).on('change', '.row-checkbox', function() {
                updateDeleteButton();
                let allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
                $('#select-all').prop('checked', allChecked);
            });

            function updateDeleteButton() {
                let checkedCount = $('.row-checkbox:checked').length;
                if (checkedCount > 0) {
                    $('#deleteSelected').removeClass('d-none');
                } else {
                    $('#deleteSelected').addClass('d-none');
                }
            }

            // حذف الملاحظات المحددة
            $('#deleteSelected').on('click', function() {
                let selectedIds = $('.row-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length > 0) {
                    Swal.fire({
                        title: '@lang('l.Are you sure?')',
                        text: '@lang('l.Selected Contacts will be deleted!')',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '@lang('l.Yes, delete them!')',
                        cancelButtonText: '@lang('l.Cancel')',
                        reverseButtons: true,
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn btn-danger me-3',
                            cancelButton: 'btn btn-secondary'
                        }
                    }).then(function(result) {
                        if (result && (result.isConfirmed || result.value === true)) {
                            window.location.assign(
                                '{{ route('dashboard.admins.contacts-deleteSelected') }}?ids=' + selectedIds.join(',')
                            );
                        }
                    });
                }
            });

            // إضافة معالج حدث الحذف للأزرار التي يتم إنشاؤها ديناميكياً
            $(document).on('click', '.delete-contact', function() {
                let contactId = $(this).data('id');

                Swal.fire({
                    title: '@lang('l.Are you sure?')',
                    text: '@lang('l.You will be delete this forever!')',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '@lang('l.Yes, delete it!')',
                    cancelButtonText: '@lang('l.Cancel')',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-danger me-3',
                        cancelButton: 'btn btn-secondary'
                    }
                }).then(function(result) {
                    if (result && (result.isConfirmed || result.value === true)) {
                        window.location.assign(
                            '{{ route('dashboard.admins.contacts-delete') }}?id=' + contactId
                        );
                    }
                });
            });
        });
    </script>
@endsection
