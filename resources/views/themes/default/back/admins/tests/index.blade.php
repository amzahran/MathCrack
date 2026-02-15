@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.tests_management')
@endsection

@section('css')
    <style>
        /* تكبير كل عناوين الحقول داخل المودال */
        .modal label.form-label,
        .modal label {
            font-size: 18px !important;
            font-weight: 600;
        }

        /* تكبير عناوين الأقسام داخل المودال */
        .modal h6.text-primary.border-bottom {
            font-size: 22px;
            font-weight: 700;
        }

         /* إخفاء عناصر apexcharts إذا كانت موجودة */
    .apexcharts-canvas,
    #salesOverviewChart,
    #revenueChart,
    #growthChart {
        display: none !important;
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
                    <h4 class="mb-0">@lang('l.tests_management')</h4>
                    <p class="text-muted mb-0">@lang('l.manage_all_tests')</p>
                </div>
                <div>
                    @can('add tests')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTestModal">
                            <i class="fa fa-plus ti-xs me-1"></i>
                            @lang('l.add_new_test')
                        </button>
                    @endcan
                </div>
            </div>

            <!-- فلاتر البحث -->
            <div class="card mb-3">
                <div class="card-body">
                    <form id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="course_filter" class="form-label">@lang('l.Course')</label>
                                <select class="form-select" id="course_filter" name="course_id">
                                    <option value="">@lang('l.all_courses')</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="status_filter" class="form-label">@lang('l.status')</label>
                                <select class="form-select" id="status_filter" name="status">
                                    <option value="">@lang('l.all_statuses')</option>
                                    <option value="1">@lang('l.active')</option>
                                    <option value="0">@lang('l.inactive')</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="price_filter" class="form-label">@lang('l.price_type')</label>
                                <select class="form-select" id="price_filter" name="price_type">
                                    <option value="">@lang('l.all_prices')</option>
                                    <option value="free">@lang('l.free')</option>
                                    <option value="paid">@lang('l.paid')</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary btn-sm" id="clearFilters">
                                    <i class="fa fa-undo me-1"></i>@lang('l.clear_filters')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول الاختبارات -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="testsTable" class="table table-striped table-bordered nowrap w-100">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('l.test_name')</th>
                                <th>@lang('l.Course')</th>
                                <th>@lang('l.price')</th>
                                <th>@lang('l.questions_status')</th>
                                <th>@lang('l.students_count')</th>
                                <th>@lang('l.status')</th>
                                <th>@lang('l.actions')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            @can('add tests')
                <!-- مودال إضافة اختبار جديد -->
                <!-- Add Test Modal -->
<div class="modal fade" id="addTestModal" tabindex="-1" aria-labelledby="addTestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTestModalLabel">@lang('l.add_new_test')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dashboard.admins.tests-store') }}" method="POST" id="addTestForm">
                @csrf
                <div class="modal-body" style="max-height: calc(100vh - 180px); overflow-y: auto;">
                    <div class="row">
                        ...
                        <!-- معلومات أساسية -->
                                        <div class="col-12">
                                            <h6 class="text-primary border-bottom pb-2 mb-3">@lang('l.basic_information')</h6>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">@lang('l.test_name') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="name" name="name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="course_id" class="form-label">@lang('l.Course') <span class="text-danger">*</span></label>
                                                <select class="form-select" id="course_id" name="course_id" required>
                                                    <option value="">@lang('l.select_course')</option>
                                                    @foreach($courses as $course)
                                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="description" class="form-label">@lang('l.test_description')</label>
                                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="price" class="form-label">@lang('l.test_price') (EGP) <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" value="0" required>
                                                <small class="form-text text-muted">@lang('l.put_zero_if_free')</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3 mt-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                                    <label class="form-check-label" for="is_active">
                                                        @lang('l.Active')
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- نظام الدرجات -->
                                        <div class="col-12">
                                            <h6 class="text-primary border-bottom pb-2 mb-3">@lang('l.scoring_system')</h6>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="total_score" class="form-label">@lang('l.total_score') <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="total_score" name="total_score" min="1" max="1000" value="800" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="initial_score" class="form-label">@lang('l.initial_score') <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="initial_score" name="initial_score" min="0" max="800" value="200" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="default_question_score" class="form-label">@lang('l.default_question_score') <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="default_question_score" name="default_question_score" min="1" max="100" value="15" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <strong>@lang('l.Note'):</strong>
                                                @lang('l.total_score_calculation')
                                                <br>
                                                <span id="score-calculation" class="fw-bold"></span>
                                            </div>
                                        </div>

                                        <!-- هيكل الاختبار -->
                                        <div class="col-12">
                                            <h6 class="text-primary border-bottom pb-2 mb-2">@lang('l.test_structure')</h6>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="modules_count" class="form-label">@lang('l.modules_count')</label>
                                            <select class="form-select" id="modules_count" name="modules_count">
                                                <option value="1">1</option>
                                                <option value="2" selected>2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                            <small class="form-text text-muted">
                                                @lang('l.modules_help_text', [], null) ?? 'Select number of modules (1–5). Hidden modules will be treated as 0 questions.'
                                            </small>
                                        </div>

                                        <div class="col-12"></div>

                                        @for($i = 1; $i <= 5; $i++)
                                            <div class="col-md-6 module-block" data-module="{{ $i }}">
                                                <div class="mb-3">
                                                    <label for="part{{ $i }}_questions_count" class="form-label">
                                                        Module {{ $i }} Questions Count
                                                        @if($i === 1) <span class="text-danger">*</span> @endif
                                                    </label>
                                                    <input type="number"
                                                           class="form-control"
                                                           id="part{{ $i }}_questions_count"
                                                           name="part{{ $i }}_questions_count"
                                                           min="{{ $i === 1 ? 1 : 0 }}"
                                                           max="100"
                                                           value="{{ $i <= 2 ? 22 : 0 }}"
                                                           @if($i === 1) required @endif>
                                                </div>
                                            </div>

                                            <div class="col-md-6 module-block" data-module="{{ $i }}">
                                                <div class="mb-3">
                                                    <label for="part{{ $i }}_time_minutes" class="form-label">
                                                        Module {{ $i }} Time (Minutes)
                                                        @if($i === 1) <span class="text-danger">*</span> @endif
                                                    </label>
                                                    <input type="number"
                                                           class="form-control"
                                                           id="part{{ $i }}_time_minutes"
                                                           name="part{{ $i }}_time_minutes"
                                                           min="{{ $i === 1 ? 1 : 0 }}"
                                                           max="300"
                                                           value="{{ $i <= 2 ? 35 : 0 }}"
                                                           @if($i === 1) required @endif>
                                                </div>
                                            </div>
                                        @endfor

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="break_time_minutes" class="form-label">@lang('l.break_time_minutes')</label>
                                                <input type="number" class="form-control" id="break_time_minutes" name="break_time_minutes"
                                                       min="0" max="60" value="15">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="max_attempts" class="form-label">@lang('l.max_attempts') <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="max_attempts" name="max_attempts"
                                                       min="1" max="10" value="1" required>
                                                <small class="form-text text-muted">@lang('l.max_attempts_help')</small>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i>
                                                <strong>@lang('l.Note'):</strong> @lang('l.test_timing_info')
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('l.Cancel')</button>
                                    <button type="submit" class="btn btn-primary">@lang('l.save')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan
        @endcan
    </div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    console.log('Tests Management - Loading...');

    // إعداد DataTable
    var testsTable = $('#testsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('dashboard.admins.tests') }}",
            data: function (d) {
                d.course_id  = $('#course_filter').val();
                d.status     = $('#status_filter').val();
                d.price_type = $('#price_filter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name',        name: 'name' },
            { data: 'course_name', name: 'course.name', defaultContent: '' },
            { data: 'price_formatted',   name: 'price', orderable: false, searchable: false },
            { data: 'questions_status',  name: 'questions_status', orderable: false, searchable: false },
            { data: 'students_count',    name: 'students_count', searchable: false },
            { data: 'status',            name: 'status', orderable: false, searchable: false },
            { data: 'action',            name: 'action', orderable: false, searchable: false },
        ],
        order: [[1, 'asc']],
        language: {
            url: "{{ app()->getLocale() == 'ar' ? asset('back-assets/js/datatable-ar.json') : '' }}"
        }
    });

    // تطبيق الفلاتر
    $('#course_filter, #status_filter, #price_filter').on('change', function() {
        testsTable.draw();
    });

    // مسح الفلاتر
    $('#clearFilters').click(function() {
        $('#filterForm')[0].reset();
        testsTable.draw();
    });

    // تبديل الحالة
    $(document).on('change', '.status-toggle', function() {
        var checkbox = $(this);
        var testId   = checkbox.data('id');
        var isActive = checkbox.is(':checked');

        $.ajax({
            url: "{{ route('dashboard.admins.tests-toggle-status') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: testId
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                    checkbox.prop('checked', !isActive);
                }
            },
            error: function() {
                toastr.error('@lang("l.error_occurred")');
                checkbox.prop('checked', !isActive);
            }
        });
    });

    // ===== حساب الدرجات التلقائي =====
    function getInt(selector) {
        return parseInt($(selector).val()) || 0;
    }

    function updateScoreCalculation() {
        const initialScore  = getInt('#initial_score');
        const questionScore = getInt('#default_question_score');

        const part1Count = getInt('#part1_questions_count');
        const part2Count = getInt('#part2_questions_count');
        const part3Count = getInt('#part3_questions_count');
        const part4Count = getInt('#part4_questions_count');
        const part5Count = getInt('#part5_questions_count');

        const totalQuestions  = part1Count + part2Count + part3Count + part4Count + part5Count;
        const questionsTotal  = totalQuestions * questionScore;
        const calculatedTotal = initialScore + questionsTotal;

        $('#total_score').val(calculatedTotal);

        let message = `Initial Score: ${initialScore} + (${totalQuestions} Questions × ${questionScore}) = ${calculatedTotal}`;
        $('#score-calculation')
            .removeClass('text-danger')
            .addClass('text-success')
            .html(message);
    }

    $('#initial_score, #default_question_score,' +
      '#part1_questions_count, #part2_questions_count, #part3_questions_count, #part4_questions_count, #part5_questions_count'
    ).on('input', updateScoreCalculation);

    updateScoreCalculation();

    // ===== إظهار / إخفاء الموديولات =====
    function toggleModules() {
        var count = parseInt($('#modules_count').val()) || 1;

        $('.module-block').each(function () {
            var mod = parseInt($(this).data('module'));
            if (mod <= count) {
                $(this).show();
            } else {
                $(this).hide();
                $(this).find('input[type="number"]').val(0);
            }
        });

        if (count <= 1) {
            $('#break_time_minutes').val(0);
            $('#break_time_minutes').closest('.col-md-6').hide();
        } else {
            $('#break_time_minutes').closest('.col-md-6').show();
            if (!$('#break_time_minutes').val()) {
                $('#break_time_minutes').val(15);
            }
        }
    }

    $('#modules_count').on('change', toggleModules);
    toggleModules();

    $('#addTestForm').on('submit', function () {
        var count = parseInt($('#modules_count').val()) || 1;
        for (var i = count + 1; i <= 5; i++) {
            $('#part' + i + '_questions_count').val(0);
            $('#part' + i + '_time_minutes').val(0);
        }
    });

    // حذف السجل
    $(document).on('click', '.delete-record', function() {
        var href     = $(this).attr('href');
        var testName = $(this).closest('tr').find('td:eq(1)').text();
        if (confirm('@lang("l.are_you_sure_delete_test") "' + testName + '"?')) {
            window.location.href = href;
        }
        return false;
    });
});
</script>
@endsection