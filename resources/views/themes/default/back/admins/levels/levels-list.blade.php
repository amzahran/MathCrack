@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.List') @lang('l.Levels')
@endsection

@section('css')
@endsection

@section('content')
    <div class="main-content">

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        @can('show levels')
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    @can('add levels')
                        <button type="button" class="btn btn-primary waves-effect waves-light me-2 mb-2" data-bs-toggle="modal" data-bs-target="#addLevelModal">
                            <i class="fa fa-plus ti-xs me-1"></i>
                            @lang('l.Add New Level')
                        </button>
                    @endcan
                </div>
            </div>

            <div class="card" id="div1" style="padding: 15px;">
                <div class="card-datatable table-responsive">
                    <table class="table" id="levels-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('l.Level Name')</th>
                                <th>@lang('l.Students Count')</th>
                                <th>@lang('l.Courses Count')</th>
                                <th>@lang('l.Created At')</th>
                                <th>@lang('l.Action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        @endcan
    </div>

    <!-- Add Level Modal -->
    @can('add levels')
        <div class="modal fade" id="addLevelModal" tabindex="-1" aria-labelledby="addLevelModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addLevelModalLabel">@lang('l.Add New Level')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('dashboard.admins.levels-store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">@lang('l.Level Name')</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('l.Close')</button>
                            <button type="submit" class="btn btn-primary">@lang('l.Save')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    <!-- Edit Level Modal -->
    @can('edit levels')
        <div class="modal fade" id="editLevelModal" tabindex="-1" aria-labelledby="editLevelModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editLevelModalLabel">@lang('l.Edit Level')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editLevelForm" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">@lang('l.Level Name')</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('l.Close')</button>
                            <button type="submit" class="btn btn-primary">@lang('l.Update')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            var table = $('#levels-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.admins.levels') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {
                        data: 'students_count',
                        name: 'students_count',
                        render: function(data, type, row) {
                            if (data > 0) {
                                return '<a href="' + "{{ route('dashboard.admins.levels-students') }}?level_id=" + row.id + '" class="text-primary fw-bold">' + data + '</a>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'courses_count',
                        name: 'courses_count',
                        render: function(data, type, row) {
                            if (data > 0) {
                                return '<a href="' + "{{ route('dashboard.admins.levels-courses') }}?level_id=" + row.id + '" class="text-primary fw-bold">' + data + '</a>';
                            }
                            return data;
                        }
                    },
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[0, 'desc']],
                language: {
                    url: "{{ asset('assets/themes/default/js/datatables-ar.json') }}"
                }
            });

            // Edit Level
            $(document).on('click', '.edit-level', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

                $('#edit_name').val(name);
                $('#editLevelForm').attr('action', "{{ route('dashboard.admins.levels-update') }}?id=" + id);
                $('#editLevelModal').modal('show');
            });

            // Delete Level
            $(document).on('click', '.delete-level', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

                if (confirm('@lang("l.Are you sure you want to delete") "' + name + '"?')) {
                    window.location.href = "{{ route('dashboard.admins.levels-delete') }}?id=" + id;
                }
            });
        });
    </script>
@endsection
