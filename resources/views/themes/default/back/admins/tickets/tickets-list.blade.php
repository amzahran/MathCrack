@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Support Tickets')
@endsection


@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @can('show tickets')
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

            @can('add tickets')
                <div class="card-action-element mb-2" style="text-align: end;">
                    <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                        data-bs-target="#addTicketModal">
                        <i class="fa fa-plus fa-xs me-1"></i> @lang('l.Add New Ticket')
                    </button>
                </div>
                <!-- Modal -->
                <div class="modal fade" id="addTicketModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content p-3 p-md-5">
                            <div class="modal-header">
                                <h3 class="modal-title text-center">@lang('l.Add New Ticket')</h3>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addTicketForm" class="row g-3" method="post"
                                    action="{{ route('dashboard.admins.tickets-store') }}">
                                    @csrf
                                    <div class="col-12 mb-2">
                                        <label class="form-label" for="modalRoleName">@lang('l.Subject')</label>
                                        <input type="text" id="modalRoleName" name="subject" class="form-control"
                                            placeholder="@lang('l.Enter a Subject')" required />
                                    </div>
                                    <div class="col-12 mb-2">
                                        <label class="form-label" for="id">@lang('l.Support type')</label>
                                        <select id="id" class="select2 form-select" name="support_type" required>
                                            <option value="">@lang('l.Select')</option>
                                            <option value="sales support">@lang('l.Sales support')</option>
                                            <option value="technical support">@lang('l.Technical support')</option>
                                            <option value="Admin">@lang('l.Admin')</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <label class="form-label" for="id">@lang('l.User')</label>
                                        <select id="id" class="select2 form-select" name="user_id" required>
                                            <option value="">@lang('l.Select')</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">
                                                    {{ $user->firstname . ' ' . $user->lastname . ' (' . $user->email . ')' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <label class="form-label" for="Whois">@lang('l.Description')</label>
                                        <textarea id="Whois" name="description" class="form-control" required rows="5"></textarea>
                                    </div>
                                    <div class="col-12 text-center mt-4">
                                        <button type="button" class="btn btn-label-secondary"
                                            data-bs-dismiss="modal">@lang('l.Cancel')</button>
                                        <button type="submit" class="btn btn-secondary">@lang('l.Create')</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

            <div class="nav-align-top mb-4">
                <ul class="nav nav-tabs nav-fill w-100" role="tablist" style="min-height: 50px;">
                    <li class="nav-item">
                        <a class="nav-link {{ !isset($_GET['inactive']) ? 'active' : '' }}"
                            href="{{ route('dashboard.admins.tickets') }}" style="padding: 15px;">
                            <i class="ti ti-ticket me-1"></i> @lang('l.Active Tickets')
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ isset($_GET['inactive']) ? 'active' : '' }}"
                            href="{{ route('dashboard.admins.tickets') }}?inactive=1" style="padding: 15px;">
                            <i class="ti ti-ticket-off me-1"></i> @lang('l.Closed Tickets')
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card" style="padding: 15px;">
                <div class="card-datatable table-responsive">
                    <div class="mb-3">
                        @can('delete tickets')
                            <button id="deleteSelected" class="btn btn-danger d-none">
                                <i class="fa fa-trash ti-xs me-1"></i>@lang('l.Delete Selected')
                            </button>
                            @if (isset($_GET['inactive']))
                                <button class="btn btn-danger delete-all-closed">
                                    <i class="fa fa-trash ti-xs me-1"></i>@lang('l.Delete All Closed Tickets')
                                </button>
                            @endif
                        @endcan
                    </div>
                    <table class="table" id="tickets-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>#</th>
                                <th>@lang('l.Subject')</th>
                                <th>@lang('l.Support Type')</th>
                                <th>@lang('l.User')</th>
                                <th>@lang('l.Status')</th>
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
            // إضافة كائن الترجمة
            const translations = {
                'sales support': '@lang('l.Sales support')',
                'technical support': '@lang('l.Technical support')',
                'admin': '@lang('l.Admin')'
            };

            let table = $('#tickets-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ request()->fullUrl() }}?inactive={{ isset($_GET['inactive']) ? $_GET['inactive'] : '' }}',
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
                        data: 'subject',
                        name: 'subject',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'support_type',
                        name: 'support_type',
                        searchable: true,
                        orderable: true,
                        render: function(data) {
                            return translations[data.toLowerCase()] || data;
                        }
                    },
                    {
                        data: 'user',
                        name: 'user.firstname',
                        searchable: true,
                        orderable: true,
                        render: function(data) {
                            return `<a href="{{ route('dashboard.admins.users-show') }}?id=${data.encrypted_id}"><img src="${data.photo}" alt="User Photo" class="rounded-circle" style="width: 35px; height: 35px;"> ${data.name}</a>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        searchable: true,
                        orderable: true,
                        render: function(data) {
                            let badge = '';
                            let statusText = '';

                            switch (data) {
                                case 'answered':
                                    badge = 'bg-success';
                                    statusText = '@lang('l.Answered')';
                                    break;
                                case 'in_progress':
                                    badge = 'bg-warning';
                                    statusText = '@lang('l.In Progress')';
                                    break;
                                case 'closed':
                                    badge = 'bg-dark';
                                    statusText = '@lang('l.Closed')';
                                    break;
                                default:
                                    badge = 'bg-secondary';
                                    statusText = data;
                            }

                            return `<span class="badge ${badge} ${data === 'in_progress' ? 'blink' : ''}">${statusText}</span>`;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [5, 'desc']
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

            // حذف التذاكر المحددة
            $('#deleteSelected').on('click', function() {
                let selectedIds = $('.row-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length > 0) {
                    Swal.fire({
                        title: '@lang('l.Are you sure?')',
                        text: '@lang('l.Selected tickets will be deleted!')',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '@lang('l.Yes, delete them!')',
                        cancelButtonText: '@lang('l.Cancel')',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href =
                                '{{ route('dashboard.admins.tickets-deleteSelected') }}?ids=' +
                                selectedIds.join(',');
                        }
                    });
                }
            });

            // حذف جميع التذاكر المغلقة
            $('.delete-all-closed').on('click', function() {
                Swal.fire({
                    title: '@lang('l.Are you sure?')',
                    text: '@lang('l.All closed tickets will be deleted!')',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '@lang('l.Yes, delete all!')',
                    cancelButtonText: '@lang('l.Cancel')',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route('dashboard.admins.tickets-deleteAll') }}';
                    }
                });
            });
        });
    </script>
@endsection
