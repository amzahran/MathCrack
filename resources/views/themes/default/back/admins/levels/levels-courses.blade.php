@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Courses in Level') - {{ $level->name ?? '' }}
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
                    <h4 class="mb-0">@lang('l.Courses in Level'): <span class="text-primary">{{ $level->name ?? '' }}</span></h4>
                </div>
                <div>
                    <a href="{{ route('dashboard.admins.levels') }}" class="btn btn-secondary waves-effect waves-light mb-2">
                        <i class="fa fa-arrow-left ti-xs me-1"></i>
                        @lang('l.Back to Levels')
                    </a>
                </div>
            </div>

            <div class="card" id="div1" style="padding: 15px;">
                <div class="card-datatable table-responsive">
                    <table class="table" id="courses-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('l.Course Name')</th>
                                <th>@lang('l.Image')</th>
                                <th>@lang('l.Price')</th>
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
        $(document).ready(function() {
            var table = $('#courses-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dashboard.admins.levels-courses') }}",
                    data: function(d) {
                        d.level_id = "{{ request('level_id') }}";
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'image', name: 'image', orderable: false, searchable: false},
                    {data: 'price', name: 'price'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[0, 'desc']],
                language: {
                    url: "{{ asset('assets/themes/default/js/datatables-ar.json') }}"
                }
            });
        });
    </script>
@endsection
