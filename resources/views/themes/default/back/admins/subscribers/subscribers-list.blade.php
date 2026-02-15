@extends('themes.default.layouts.back.master')

@section('title')
    {{ __('l.Subscribers') }}
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">{{ __('l.Subscribers') }} /</span> {{ __('l.List') }}</h4>

        @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session()->get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('l.Subscribers') }}</h5>
                @can('add newsletters_subscribers')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubscriberModal">
                    <i class="fa fa-plus me-2"></i>{{ __('l.Add New') }}
                </button>
                @endcan
            </div>

            <div class="card-body">
                <div class="card-datatable table-responsive">
                    <div class="mb-3">
                        @can('delete newsletters_subscribers')
                        <button type="button" class="btn btn-danger d-none" id="delete-all-btn">
                            <i class="fa fa-trash me-2"></i>{{ __('l.Delete Selected') }}
                        </button>
                        @endcan
                    </div>
                    <table class="table table-hover" id="subscribers-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>#</th>
                                <th>{{ __('l.Email') }}</th>
                                <th>{{ __('l.Status') }}</th>
                                <th>{{ __('l.Date') }}</th>
                                <th>{{ __('l.Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Subscriber Modal -->
        <div class="modal fade" id="addSubscriberModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('l.Add New Subscriber') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('dashboard.admins.subscribers-store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="email" class="form-label">{{ __('l.Email') }} <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="{{ __('l.Enter Email') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('l.Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('l.Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
<script>
    $(function() {
        let table = $('#subscribers-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('dashboard.admins.subscribers') }}",
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    exportable: false,
                    render: function(data, type, row) {
                        return `<input type="checkbox" class="form-check-input row-checkbox" value="${row.id || ''}">`;
                    }
                },
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'email',
                    name: 'email',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'is_active',
                    name: 'is_active',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[4, 'desc']],
            dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"lB><"d-flex align-items-center"f>>rtip',
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "@lang('l.All')"]
            ],
            pageLength: 10,
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="fa fa-download me-1"></i> @lang("l.Export")',
                    className: 'btn btn-primary dropdown-toggle mx-3',
                    buttons: [
                        {
                            text: '<i class="fa fa-file-excel me-1"></i> @lang("l.Excel")',
                            className: 'dropdown-item',
                            extend: 'excel',
                            exportOptions: {
                                columns: ':visible:not(:first-child):not(:last-child)'
                            }
                        },
                        {
                            text: '<i class="fa fa-file-csv me-1"></i> @lang("l.CSV")',
                            className: 'dropdown-item',
                            extend: 'csv',
                            exportOptions: {
                                columns: ':visible:not(:first-child):not(:last-child)'
                            }
                        },
                        {
                            text: '<i class="fa fa-file-pdf me-1"></i> @lang("l.PDF")',
                            className: 'dropdown-item',
                            extend: 'pdf',
                            exportOptions: {
                                columns: ':visible:not(:first-child):not(:last-child)'
                            }
                        },
                        {
                            text: '<i class="fa fa-print me-1"></i> @lang("l.Print")',
                            className: 'dropdown-item',
                            extend: 'print',
                            exportOptions: {
                                columns: ':visible:not(:first-child):not(:last-child)'
                            }
                        }
                    ]
                },
                {
                    extend: 'colvis',
                    text: '<i class="fa fa-eye me-1"></i> @lang("l.Columns")',
                    className: 'btn btn-secondary'
                }
            ],
            language: {
                // url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/{{ app()->getLocale() }}.json',
                buttons: {
                    colvis: '@lang("l.Show/Hide Columns")'
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
            },
            drawCallback: function(settings) {
                $('.dataTables_processing').hide();
            },
            preDrawCallback: function(settings) {
                $('.dataTables_processing').show();
            }
        });

        // Delete Subscriber
        $(document).on('click', '.delete-subscriber', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: "{{ __('l.Are you sure?') }}",
                text: "{{ __('l.You will not be able to recover this item!') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "{{ __('l.Yes, delete it!') }}",
                cancelButtonText: "{{ __('l.Cancel') }}",
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('dashboard.admins.subscribers-delete') }}?id=" + id;
                }
            });
        });

        // تحديد/إلغاء تحديد الكل
        $('#select-all').on('change', function() {
            $('.row-checkbox').prop('checked', $(this).prop('checked'));
            updateDeleteButton();
        });

        // تحديث حالة زر الحذف عند تغيير أي صندوق تحديد
        $(document).on('change', '.row-checkbox', function() {
            updateDeleteButton();

            // تحديث حالة "تحديد الكل" إذا تم تحديد/إلغاء تحديد كل الصناديق
            let allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
            $('#select-all').prop('checked', allChecked);
        });

        // تحديث ظهور زر الحذف
        function updateDeleteButton() {
            let checkedCount = $('.row-checkbox:checked').length;
            if (checkedCount > 0) {
                $('#delete-all-btn').removeClass('d-none');
            } else {
                $('#delete-all-btn').addClass('d-none');
            }
        }

        // Delete selected subscribers
        $('#delete-all-btn').click(function() {
            let selectedIds = [];

            $('.row-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length > 0) {
                Swal.fire({
                    title: "{{ __('l.Are you sure?') }}",
                    text: "{{ __('l.You will not be able to recover these items!') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "{{ __('l.Yes, delete them!') }}",
                    cancelButtonText: "{{ __('l.Cancel') }}",
                    customClass: {
                        confirmButton: 'btn btn-danger me-3',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('dashboard.admins.subscribers-deleteSelected') }}?ids=" + selectedIds.join(',');
                    }
                });
            }
        });
    });
</script>

@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: '@lang("l.Done")',
                text: '{{ session("success") }}',
                confirmButtonText: '@lang("l.OK")'
            });
        });
    </script>
@endif
@endsection