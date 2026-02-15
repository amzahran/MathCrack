@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Lectures')
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
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @can('show lectures')
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0">@lang('l.Lectures')</h4>
                </div>
                <div>
                    <button type="button" class="btn btn-primary waves-effect waves-light mb-2" data-bs-toggle="modal" data-bs-target="#addLectureModal">
                        <i class="fa fa-plus ti-xs me-1"></i>
                        @lang('l.Add Lecture')
                    </button>
                </div>
            </div>

            <div class="card" id="div1" style="padding: 15px;">
                <!-- Filters Section -->
                <div class="filters-section">
                    <h6 class="mb-3">
                        <i class="fas fa-filter me-2"></i>@lang('l.filters')
                    </h6>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="form-label small text-muted">@lang('l.Level')</label>
                            <select class="form-select" id="filter_level">
                                <option value="">@lang('l.all_levels')</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label small text-muted">@lang('l.Course')</label>
                            <select class="form-select" id="filter_course">
                                <option value="">@lang('l.all_courses')</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" data-level="{{ $course->level_id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label small text-muted">@lang('l.Lecture Type')</label>
                            <select class="form-select" id="filter_type">
                                <option value="">@lang('l.all_types')</option>
                                <option value="free">@lang('l.Free')</option>
                                <option value="price">@lang('l.Paid')</option>
                                <option value="month">@lang('l.Monthly')</option>
                                <option value="course">@lang('l.Course')</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label small text-muted">@lang('l.from_date')</label>
                            <input type="date" class="form-control" id="filter_date_from">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label small text-muted">@lang('l.to_date')</label>
                            <input type="date" class="form-control" id="filter_date_to">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary btn-sm btn-filter d-none" id="apply_filters">
                                <i class="fas fa-filter me-1"></i>@lang('l.apply_filters')
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-filter" id="clear_filters">
                                <i class="fas fa-times me-1"></i>@lang('l.clear_filters')
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="lecturesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('l.Lecture Name')</th>
                                    <th>@lang('l.Course')</th>
                                    <th>@lang('l.Level')</th>
                                    <th>@lang('l.Lecture Type')</th>
                                    <th>@lang('l.Price')</th>
                                    <th>@lang('l.Assignments Count')</th>
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

    <!-- Add Lecture Modal -->
    <div class="modal fade" id="addLectureModal" tabindex="-1" aria-labelledby="addLectureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLectureModalLabel">@lang('l.Add Lecture')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('dashboard.admins.lectures-store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">@lang('l.Lecture Name')</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('name')" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">@lang('l.Course')</label>
                                    <select class="form-select" id="course_id" name="course_id" required>
                                        <option value="">@lang('l.Select Course')</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('course_id')" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">@lang('l.Lecture Type')</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="free">@lang('l.Free')</option>
                                        <option value="price">@lang('l.Paid')</option>
                                        <option value="month">@lang('l.Monthly')</option>
                                        <option value="course">@lang('l.Course')</option>
                                    </select>
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('type')" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">@lang('l.Price')</label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" disabled>
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('price')" />
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">@lang('l.Description')</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            <x-input-error class="mt-2 error-message" :messages="$errors->get('description')" />
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="video_url" class="form-label">@lang('l.Video URL')</label>
                                    <input type="url" class="form-control" id="video_url" name="video_url">
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('video_url')" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image" class="form-label">@lang('l.Image')</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('image')" />
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="files" class="form-label">@lang('l.Lecture Files')</label>
                            <input type="file" class="form-control" id="files" name="files" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip,.rar">
                            <x-input-error class="mt-2 error-message" :messages="$errors->get('files')" />
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
            var table = $('#lecturesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dashboard.admins.lectures') }}",
                    data: function (d) {
                        d.level_id = $('#filter_level').val();
                        d.course_id = $('#filter_course').val();
                        d.type = $('#filter_type').val();
                        d.date_from = $('#filter_date_from').val();
                        d.date_to = $('#filter_date_to').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'course_name', name: 'course_name'},
                    {data: 'level_name', name: 'level_name'},
                    {data: 'type', name: 'type'},
                    {data: 'price', name: 'price'},
                    {data: 'assignments_count', name: 'assignments_count'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[7, 'desc']],
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
                $('#filter_course').val('').find('option').show();
                $('#filter_type').val('');
                $('#filter_date_from').val('');
                $('#filter_date_to').val('');
                updateFiltersStatus();
                table.draw();
            });

            // Update filters status
            function updateFiltersStatus() {
                var hasActiveFilters = $('#filter_level').val() || $('#filter_course').val() ||
                                     $('#filter_type').val() || $('#filter_date_from').val() ||
                                     $('#filter_date_to').val();

                if (hasActiveFilters) {
                    $('#apply_filters').removeClass('btn-primary').addClass('btn-success');
                    $('#apply_filters i').removeClass('fa-filter').addClass('fa-check');
                } else {
                    $('#apply_filters').removeClass('btn-success').addClass('btn-primary');
                    $('#apply_filters i').removeClass('fa-check').addClass('fa-filter');
                }
            }

            // Filter courses based on selected level
            $('#filter_level').change(function() {
                var selectedLevel = $(this).val();
                var courseSelect = $('#filter_course');

                courseSelect.val('');

                if (selectedLevel === '') {
                    courseSelect.find('option').show();
                } else {
                    courseSelect.find('option').each(function() {
                        var courseLevel = $(this).data('level');
                        if ($(this).val() === '' || courseLevel == selectedLevel) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
            });

            // Auto apply filters on change
            $('#filter_level, #filter_course, #filter_type, #filter_date_from, #filter_date_to').change(function() {
                updateFiltersStatus();
                table.draw();
            });

            // Initialize filters status
            updateFiltersStatus();

            // Delete confirmation
            $(document).on('click', '.delete-record', function() {
                var id = $(this).attr('href').split('id=')[1];
                var name = $(this).closest('tr').find('td:eq(1)').text();
                if (confirm('@lang("l.Are you sure you want to delete") "' + name + '"?')) {
                    window.location.href = $(this).attr('href');
                }
                return false;
            });

            // Handle lecture type change
            $('#type').change(function() {
                var selectedType = $(this).val();
                var priceField = $('#price');
                var priceFieldContainer = priceField.closest('.mb-3');

                // Reset price field
                priceField.val('');

                if (selectedType === 'free' || selectedType === 'course' || selectedType === 'month') {
                    // Free, Course, and Monthly types - disable price field
                    priceField.prop('disabled', true);
                    priceField.attr('placeholder', '');

                    // Add explanation for monthly type
                    if (selectedType === 'month') {
                        if (!priceFieldContainer.find('.monthly-note').length) {
                            priceFieldContainer.append('<small class="text-info monthly-note d-block mt-1"><i class="fas fa-info-circle me-1"></i>المحاضرات الشهرية تتطلب اشتراك شهري للكورس (بدون سعر منفصل)</small>');
                        }
                    } else {
                        priceFieldContainer.find('.monthly-note').remove();
                    }
                } else if (selectedType === 'price') {
                    // Paid type - enable price field for amount
                    priceField.prop('disabled', false);
                    priceField.attr('type', 'number');
                    priceField.attr('min', '0');
                    priceField.attr('step', '0.01');
                    priceField.attr('max', '');
                    priceField.attr('placeholder', '@lang("l.Enter amount")');
                    priceFieldContainer.find('.monthly-note').remove();
                }
            });

            // Initialize on page load
            $('#type').trigger('change');
        });
    </script>
@endsection
