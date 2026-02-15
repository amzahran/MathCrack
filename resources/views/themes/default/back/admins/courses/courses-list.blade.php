@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.List') @lang('l.Courses')
@endsection

@section('css')
    <style>
        .filters-section {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .filters-section h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .filters-section .form-select,
        .filters-section .form-control {
            border-radius: 6px;
            border: 1px solid #ced4da;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .filters-section .form-select:focus,
        .filters-section .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .btn-filter {
            border-radius: 6px;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .btn-filter i {
            margin-right: 6px;
        }
    </style>
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

        @can('show courses')
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    @can('add courses')
                        <button type="button" class="btn btn-primary waves-effect waves-light me-2 mb-2" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                            <i class="fa fa-plus ti-xs me-1"></i>
                            @lang('l.Add New Course')
                        </button>
                    @endcan
                </div>
            </div>

            <div class="card" id="div1" style="padding: 15px;">
                <!-- Filters Section -->
                <div class="filters-section">
                    <h6 class="mb-3">
                        <i class="fas fa-filter me-2"></i>@lang('l.filters')
                    </h6>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label small text-muted">@lang('l.Level')</label>
                            <select class="form-select" id="filter_level">
                                <option value="">@lang('l.all_levels')</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-md-3 mb-2">
                            <label class="form-label small text-muted">@lang('l.price_range')</label>
                            <select class="form-select" id="filter_price">
                                <option value="">@lang('l.all_prices')</option>
                                <option value="free">@lang('l.Free')</option>
                                <option value="paid">@lang('l.paid_courses')</option>
                                <option value="low">@lang('l.low_price') (1-100)</option>
                                <option value="medium">@lang('l.medium_price') (101-500)</option>
                                <option value="high">@lang('l.high_price') (+500)</option>
                            </select>
                        </div> --}}
                        <div class="col-md-4 mb-2">
                            <label class="form-label small text-muted">@lang('l.from_date')</label>
                            <input type="date" class="form-control" id="filter_date_from">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label small text-muted">@lang('l.to_date')</label>
                            <input type="date" class="form-control" id="filter_date_to">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary btn-sm btn-filter d-none" id="apply_filters">
                                <i class="fas fa-filter"></i>@lang('l.apply_filters')
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-filter" id="clear_filters">
                                <i class="fas fa-times"></i>@lang('l.clear_filters')
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="table" id="courses-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('l.Course Name')</th>
                                <th>@lang('l.Image')</th>
                                <th>@lang('l.Price')</th>
                                <th>@lang('l.Access Duration')</th>
                                <th>@lang('l.Level')</th>
                                <th>@lang('l.Created At')</th>
                                <th>@lang('l.Action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        @endcan
    </div>

    <!-- Add Course Modal -->
    @can('add courses')
        <div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCourseModalLabel">@lang('l.Add New Course')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('dashboard.admins.courses-store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">@lang('l.Course Name')</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="level_id" class="form-label">@lang('l.Level')</label>
                                <select class="form-select" id="level_id" name="level_id" required>
                                    <option value="">@lang('l.Select Level')</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">@lang('l.Price')</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0">
                            </div>

                            <div class="mb-3">
                                <label for="tests_price" class="form-label">@lang('l.Tests Price')</label>
                                <input type="number" class="form-control" id="tests_price" name="tests_price" step="0.01" min="0">
                            </div>

                            <div class="mb-3">
                                <label for="access_duration_days" class="form-label">@lang('l.Access Duration') (@lang('l.Days')) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="access_duration_days" name="access_duration_days" min="0" max="3650" value="90" placeholder="90" required>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">@lang('l.Image')</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
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


@endsection

@section('js')
    <script>
        $(document).ready(function() {
            var table = $('#courses-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dashboard.admins.courses') }}",
                    data: function (d) {
                        d.level_id = $('#filter_level').val();
                        d.price_range = $('#filter_price').val();
                        d.date_from = $('#filter_date_from').val();
                        d.date_to = $('#filter_date_to').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'image', name: 'image', orderable: false, searchable: false},
                    {data: 'price', name: 'price'},
                    {data: 'access_duration', name: 'access_duration', orderable: false, searchable: false},
                    {data: 'level_name', name: 'level_name'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[0, 'desc']],
                language: {
                    url: "{{ asset('assets/back/js/datatables-ar.json') }}"
                }
            });

            // Apply filters
            $('#apply_filters').click(function() {
                updateFiltersStatus();
                table.draw();
            });

            // Clear filters
            $('#clear_filters').click(function() {
                $('#filter_level').val('');
                $('#filter_price').val('');
                $('#filter_date_from').val('');
                $('#filter_date_to').val('');
                updateFiltersStatus();
                table.draw();
            });

            // Update filters status
            function updateFiltersStatus() {
                var hasActiveFilters = $('#filter_level').val() || $('#filter_price').val() ||
                                     $('#filter_date_from').val() || $('#filter_date_to').val();

                if (hasActiveFilters) {
                    $('#apply_filters').removeClass('btn-primary').addClass('btn-success');
                    $('#apply_filters i').removeClass('fa-filter').addClass('fa-check');
                } else {
                    $('#apply_filters').removeClass('btn-success').addClass('btn-primary');
                    $('#apply_filters i').removeClass('fa-check').addClass('fa-filter');
                }
            }

            // Auto apply filters on change
            $('#filter_level, #filter_price, #filter_date_from, #filter_date_to').change(function() {
                updateFiltersStatus();
                table.draw();
            });

            // Initialize filters status
            updateFiltersStatus();

            // Delete Course
            $(document).on('click', '.delete-course', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

                if (confirm('@lang("l.Are you sure you want to delete") "' + name + '"?')) {
                    window.location.href = "{{ route('dashboard.admins.courses-delete') }}?id=" + id;
                }
            });

            // Access Duration Helper
            $('#access_duration_days').on('input', function() {
                var days = parseInt($(this).val());
                var helpText = '';

                if (!days || days === 0) {
                    helpText = '@lang("l.Leave empty or 0 for unlimited access")';
                } else if (days === 30) {
                    helpText = '@lang("l.1 Month")';
                } else if (days === 90) {
                    helpText = '@lang("l.3 Months")';
                } else if (days === 180) {
                    helpText = '@lang("l.6 Months")';
                } else if (days === 365) {
                    helpText = '@lang("l.1 Year")';
                } else if (days > 365) {
                    var years = Math.floor(days / 365);
                    var remainingDays = days % 365;
                    helpText = years + ' @lang("l.Years")';
                    if (remainingDays > 0) {
                        helpText += ' + ' + remainingDays + ' @lang("l.Days")';
                    }
                } else {
                    helpText = days + ' @lang("l.Days")';
                }

                $(this).next('.form-text').text(helpText);
            });
        });
    </script>
@endsection
