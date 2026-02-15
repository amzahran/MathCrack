@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Assignments') - {{ $lecture->name }}
@endsection

@section('css')
@endsection

@section('content')
    <div class="main-content">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        @can('show lectures')
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0">@lang('l.Assignments') - <span class="text-primary">{{ $lecture->name }}</span></h4>
                    <p class="text-muted mb-0">{{ $lecture->course->name ?? '' }}</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary waves-effect waves-light mb-2" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
                        <i class="fa fa-plus ti-xs me-1"></i>
                        @lang('l.Add Assignment')
                    </button>
                    <a href="{{ route('dashboard.admins.lectures') }}" class="btn btn-secondary waves-effect waves-light mb-2">
                        <i class="fa fa-arrow-left ti-xs me-1"></i>
                        @lang('l.Back to Lectures')
                    </a>
                </div>
            </div>

            <div class="card" id="div1" style="padding: 15px;">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="assignmentsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('l.Assignment Title')</th>
                                    <th>@lang('l.Time Limit')</th>
                                    <th>@lang('l.Questions Count')</th>
                                    <th>@lang('l.Status')</th>
                                    <th>@lang('l.Created At')</th>
                                    <th>@lang('l.Action')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    <!-- Add Assignment Modal -->
    <div class="modal fade" id="addAssignmentModal" tabindex="-1" aria-labelledby="addAssignmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAssignmentModalLabel">@lang('l.Add Assignment')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('dashboard.admins.lectures-assignments-store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="lecture_id" value="{{ encrypt($lecture->id) }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">@lang('l.Assignment Title')</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('title')" />
                                </div>
                            </div>

                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">@lang('l.Assignment Description')</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            <x-input-error class="mt-2 error-message" :messages="$errors->get('description')" />
                        </div>

                        <div class="mb-3">
                            <label for="time_limit" class="form-label">@lang('l.Time Limit (minutes)')</label>
                            <input type="number" class="form-control" id="time_limit" name="time_limit" min="1" placeholder="@lang('l.No Limit')">
                            <small class="form-text text-muted">@lang('l.Leave empty for no time limit')</small>
                            <x-input-error class="mt-2 error-message" :messages="$errors->get('time_limit')" />
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <input type="hidden" name="show_answers" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="show_answers" name="show_answers" value="1" checked>
                                    <label class="form-check-label" for="show_answers">
                                        @lang('l.Show Answers')
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input type="hidden" name="is_active" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">
                                        @lang('l.Active')
                                    </label>
                                </div>
                            </div>
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
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#assignmentsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.admins.lectures-assignments') }}?id={{ encrypt($lecture->id) }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'title', name: 'title'},
                    {data: 'time_limit', name: 'time_limit'},
                    {data: 'questions_count', name: 'questions_count'},
                    {data: 'status', name: 'status'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[5, 'desc']],
                language: {
                    url: "{{ asset('assets/back/js/datatables-ar.json') }}"
                }
            });

            // Delete confirmation
            $(document).on('click', '.delete-record', function() {
                var id = $(this).attr('href').split('id=')[1];
                var name = $(this).closest('tr').find('td:eq(1)').text();
                if (confirm('@lang("l.Are you sure you want to delete") "' + name + '"?')) {
                    window.location.href = $(this).attr('href');
                }
                return false;
            });
        });
    </script>
@endsection
