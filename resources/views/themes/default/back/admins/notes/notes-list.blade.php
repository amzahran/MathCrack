@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Notes')
@endsection


@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card-action-element mb-2" style="text-align: end;">
            <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                data-bs-target="#addTicketModal">
                <i class="fa fa-plus fa-xs me-1"></i> @lang('l.Add New Note')
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Modal -->
        <div class="modal fade" id="addTicketModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-header">
                        <h3 class="modal-title text-center">@lang('l.Add New Note')</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addTicketForm" class="row g-3" method="post"
                            action="{{ route('dashboard.admins.notes-store') }}">
                            @csrf
                            <div class="col-12 mb-2">
                                <label class="form-label" for="note">@lang('l.Note')</label>
                                <textarea type="text" id="note" name="note" class="form-control" placeholder="@lang('l.Enter a note')"
                                    rows="5" required></textarea>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label" for="date">@lang('l.Date')</label>
                                <input type="date" id="date" name="date" class="form-control"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required />
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label" for="is_still_active">@lang('l.Keep it always') <i
                                        class="fa fa-info-circle" data-bs-toggle="tooltip"
                                        title="@lang('l.When selected, the notification will remain visible until you delete it. If not selected, the notification will only appear on its specified date and disappear afterwards.')"></i></label>
                                <input type="hidden" name="is_still_active" value="0" />
                                <input type="checkbox" id="is_still_active" name="is_still_active" class="form-check-input"
                                    value="1" />
                            </div>
                            <div class="col-12 text-center mt-4">
                                <button type="button" class="btn btn-label-secondary"
                                    data-bs-dismiss="modal">@lang('l.Cancel')</button>
                                <button type="submit" class="btn btn-primary">@lang('l.Create')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="padding: 15px;">
            <div class="card-datatable table-responsive">
                <div class="mb-3">
                    <button id="deleteSelected" class="btn btn-danger d-none">
                        <i class="fa fa-trash ti-xs me-1"></i>@lang('l.Delete Selected')
                    </button>
                </div>
                <table class="table" id="notes-table">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="select-all" class="form-check-input">
                            </th>
                            <th>#</th>
                            <th>@lang('l.Note')</th>
                            <th>@lang('l.Date')</th>
                            <th>@lang('l.Action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            let table = $('#notes-table').DataTable({
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
                        data: 'note',
                        name: 'note',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'date',
                        name: 'date',
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
                    [3, 'desc']
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
                        text: '@lang('l.Selected notes will be deleted!')',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '@lang('l.Yes, delete them!')',
                        cancelButtonText: '@lang('l.Cancel')',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href =
                                '{{ route('dashboard.admins.notes-deleteSelected') }}?ids=' +
                                selectedIds.join(',');
                        }
                    });
                }
            });

            // إضافة معالج حدث الحذف للأزرار التي يتم إنشاؤها ديناميكياً
            $(document).on('click', '.delete-note', function() {
                let noteId = $(this).data('id');

                Swal.fire({
                    title: '@lang('l.Are you sure?')',
                    text: '@lang('l.You will delete this note forever!')',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '@lang('l.Yes, delete it!')',
                    cancelButtonText: '@lang('l.Cancel')',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route('dashboard.admins.notes-delete') }}?id=' +
                            noteId;
                    }
                });
            });
        });
    </script>
@endsection
