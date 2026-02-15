@extends('themes.default.layouts.back.master')

@section('title', __('l.Live Sessions'))

@section('content')
<div class="main-content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ __('l.Live Sessions') }}</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLiveModal">
                        <i class="fas fa-plus"></i> {{ __('l.Add Live Session') }}
                    </button>
                </div>
                @if(session()->has('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                @endif
                @if(session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session()->get('error') }}
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
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <select class="form-select" id="course_filter">
                                <option value="">{{ __('l.All Courses') }}</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="level_filter">
                                <option value="">{{ __('l.All Levels') }}</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="type_filter">
                                <option value="">{{ __('l.All Types') }}</option>
                                <option value="free">{{ __('l.Free') }}</option>
                                <option value="price">{{ __('l.Paid') }}</option>
                                <option value="month">{{ __('l.Monthly') }}</option>
                                <option value="course">{{ __('l.Course') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="date_from_filter" placeholder="{{ __('l.From Date') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="date_to_filter" placeholder="{{ __('l.To Date') }}">
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="livesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('l.Name') }}</th>
                                    <th>{{ __('l.Course') }}</th>
                                    <th>{{ __('l.Level') }}</th>
                                    <th>{{ __('l.Type') }}</th>
                                    <th>{{ __('l.Price') }}</th>
                                    <th>{{ __('l.Start Time') }}</th>
                                    <th>{{ __('l.Duration') }}</th>
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

<!-- Add Live Modal -->
<div class="modal fade" id="addLiveModal" tabindex="-1" aria-labelledby="addLiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('dashboard.admins.lives-store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addLiveModalLabel">{{ __('l.Add Live Session') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('l.Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="course_id" class="form-label">{{ __('l.Course') }} <span class="text-danger">*</span></label>
                                <select class="form-select" id="course_id" name="course_id" required>
                                    <option value="">{{ __('l.Select Course') }}</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">{{ __('l.Type') }} <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="free">{{ __('l.Free') }}</option>
                                    <option value="price">{{ __('l.Paid') }}</option>
                                    {{-- <option value="month">{{ __('l.Monthly') }}</option>
                                    <option value="course">{{ __('l.Course') }}</option> --}}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">{{ __('l.Price') }}</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" disabled>
                                <small class="form-text text-muted">{{ __('l.Required only for paid type') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_at" class="form-label">{{ __('l.Start Time') }}</label>
                                <input type="datetime-local" class="form-control" id="start_at" name="start_at">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="duration" class="form-label">{{ __('l.Duration (minutes)') }}</label>
                                <input type="number" class="form-control" id="duration" name="duration" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">{{ __('l.Image') }}</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="link" class="form-label">{{ __('l.Link') }}</label>
                                <input type="url" class="form-control" id="link" name="link" placeholder="https://...">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('l.Description') }}</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('l.Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('l.Save') }}</button>
                </div>
            </form>
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
            url: '{{ route("dashboard.admins.lives") }}',
            data: function(d) {
                d.course_id = $('#course_filter').val();
                d.level_id = $('#level_filter').val();
                d.type = $('#type_filter').val();
                d.date_from = $('#date_from_filter').val();
                d.date_to = $('#date_to_filter').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'course_name', name: 'course_name'},
            {data: 'level_name', name: 'level_name'},
            {data: 'type', name: 'type'},
            {data: 'price', name: 'price'},
            {data: 'start_at', name: 'start_at'},
            {data: 'duration', name: 'duration'},
            {data: 'status', name: 'status'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[9, 'desc']],
        pageLength: 25,
        language: {
            url: '{{ asset("assets/js/datatables/" . app()->getLocale() . ".json") }}'
        }
    });

    // Apply filters
    $('#course_filter, #level_filter, #type_filter, #date_from_filter, #date_to_filter').on('change keyup', function() {
        table.draw();
    });

    // Handle price field visibility
    $('#type').on('change', function() {
        if ($(this).val() === 'price') {
            $('#price').closest('.mb-3').show();
            $('#price').prop('disabled', false);
        } else {
            $('#price').closest('.mb-3').show();
            $('#price').prop('disabled', true);
            $('#price').val('');
        }
    });

    // Initial state
    $('#type').trigger('change');
});
</script>
@endsection
