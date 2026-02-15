@extends('themes.default.layouts.back.student-master')

@section('title', __('l.Live Sessions'))

@section('content')
<div class="main-content">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('l.Live Sessions') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="livesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('l.Name') }}</th>
                                    <th>{{ __('l.Course') }}</th>
                                    <th>{{ __('l.Type') }}</th>
                                    <th>{{ __('l.Price') }}</th>
                                    <th>{{ __('l.Time Status') }}</th>
                                    <th>{{ __('l.Status') }}</th>
                                    <th>{{ __('l.Created At') }}</th>
                                    <th>{{ __('l.Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    var table = $('#livesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("dashboard.users.lives") }}'
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'course_name', name: 'course_name'},
            {data: 'type', name: 'type'},
            {data: 'price', name: 'price'},
            {data: 'time_status', name: 'time_status'},
            {data: 'status', name: 'status'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[7, 'desc']],
        pageLength: 25,
        language: {
            url: '{{ asset("assets/js/datatables/" . app()->getLocale() . ".json") }}'
        }
    });
});
</script>
@endsection
