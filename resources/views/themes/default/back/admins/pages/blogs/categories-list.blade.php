@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Blog Categories')
@endsection


@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @can('show blog_category')
            @can('add blog_category')
                <div class="card-action-element mb-2" style="text-align: end;">
                    <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                        data-bs-target="#addTicketModal">
                        <i class="fa fa-plus fa-xs me-1"></i> @lang('l.Add New Category')
                    </button>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="addTicketModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content p-3 p-md-5">
                            <div class="modal-header">
                                <h3 class="modal-title text-center">@lang('l.Add New Category')</h3>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addTicketForm" class="row g-3" method="post"
                                    action="{{ route('dashboard.admins.blogs.categories-store') }}">
                                    @csrf
                                    <div class="col-12 mb-2">
                                        <label class="form-label" for="meta_keywords">@lang('l.Meta Keywords')</label>
                                        <input type="text" id="meta_keywords" name="meta_keywords" class="form-control form-control-tags" />
                                    </div>
                                    <div class="col-12 mb-2">
                                        <label class="form-label" for="meta_description">@lang('l.Meta Description')</label>
                                        <textarea id="meta_description" name="meta_description" class="form-control"
                                            placeholder="@lang('l.Enter a meta description')" rows="3"></textarea>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <label class="form-label" for="name">@lang('l.Name')<i
                                                class="fi fi-{{ strtolower($defaultLanguage->flag) }} fs-8 me-2 ms-2"></i></label>
                                        <input type="text" id="name" name="name" class="form-control"
                                            placeholder="@lang('l.Enter a name')" required />
                                    </div>
                                    <div class="col-12 mb-2">
                                        <div class="form-check form-switch mb-2">
                                            <input type="hidden" name="auto_translate" value="0">
                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="auto_translate" value="1" />
                                            <label class="form-check-label" for="flexSwitchCheckDefault">@lang('l.Auto Translate')</label>
                                        </div>
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
            @endcan

            <div class="card" style="padding: 15px;">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card-datatable table-responsive">
                    <div class="mb-3">
                        @can('delete blog_category')
                            <button id="deleteSelected" class="btn btn-danger d-none">
                                <i class="fa fa-trash ti-xs me-1"></i>@lang('l.Delete Selected')
                            </button>
                        @endcan
                    </div>
                    <table class="table" id="categories-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>#</th>
                                <th>@lang('l.Name')</th>
                                <th>@lang('l.Slug')</th>
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
            let table = $('#categories-table').DataTable({
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
                        data: 'slug',
                        name: 'slug',
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
                    [1, 'desc']
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
                        text: '@lang('l.Selected categories will be deleted!')',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '@lang('l.Yes, delete them!')',
                        cancelButtonText: '@lang('l.Cancel')',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href =
                                '{{ route('dashboard.admins.blogs.categories-deleteSelected') }}?ids=' +
                                selectedIds.join(',');
                        }
                    });
                }
            });

            // إضافة معالج حدث الحذف للأزرار التي يتم إنشاؤها ديناميكياً
            $(document).on('click', '.delete-category', function() {
                let categoryId = $(this).data('id');

                Swal.fire({
                    title: '@lang('l.Are you sure?')',
                    text: '@lang('l.You will be delete this forever!')',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '@lang('l.Yes, delete it!')',
                    cancelButtonText: '@lang('l.Cancel')',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href =
                            '{{ route('dashboard.admins.blogs.categories-delete') }}?id=' +
                            categoryId;
                    }
                });
            });
        });
    </script>
@endsection
