@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.tests_management')
@endsection

@section('css')
    <style>
        .module-row {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: #f8fafc;
        }
        .module-header {
            background: #4f46e5;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .score-calculation {
            font-size: 14px;
            padding: 10px;
            border-radius: 6px;
            margin-top: 5px;
        }
        .score-correct {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .score-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }
        .is-invalid {
            border-color: #dc3545 !important;
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
                    <div>{{ $error }}</div>
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
                    <button type="button" class="btn btn-primary waves-effect waves-light mb-2" data-bs-toggle="modal" data-bs-target="#addTestModal">
                        <i class="fa fa-plus ti-xs me-1"></i>
                        @lang('l.add_new_test')
                    </button>
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
                            <div class="col-md-3">
                                <label for="status_filter" class="form-label">@lang('l.Status')</label>
                                <select class="form-select" id="status_filter" name="status">
                                    <option value="">@lang('l.all_statuses')</option>
                                    <option value="1">@lang('l.Active')</option>
                                    <option value="0">@lang('l.Inactive')</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="price_filter" class="form-label">@lang('l.price_type')</label>
                                <select class="form-select" id="price_filter" name="price_type">
                                    <option value="">@lang('l.all_prices')</option>
                                    <option value="free">@lang('l.free')</option>
                                    <option value="paid">@lang('l.Paid')</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-secondary" id="clearFilters">
                                    @lang('l.clear_filters')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card" id="div1" style="padding: 15px;">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="testsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('l.test_name')</th>
                                    <th>@lang('l.Course')</th>
                                    <th>@lang('l.Price')</th>
                                    <th>@lang('l.questions_status')</th>
                                    <th>@lang('l.students_count')</th>
                                    <th>@lang('l.Status')</th>
                                    <th>@lang('l.Action')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    <!-- Add Test Modal -->
    <div class="modal fade" id="addTestModal" tabindex="-1" aria-labelledby="addTestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTestModalLabel">@lang('l.add_new_test')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('dashboard.admins.tests-store') }}" method="POST" id="addTestForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- معلومات أساسية -->
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">@lang('l.basic_information')</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">@lang('l.test_name') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">@lang('l.course') <span class="text-danger">*</span></label>
                                    <select class="form-select" id="course_id" name="course_id" required>
                                        <option value="">@lang('l.select_course')</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">@lang('l.test_description')</label>
                                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modules_count" class="form-label">Number of Modules <span class="text-danger">*</span></label>
                                    <select class="form-select" id="modules_count" name="modules_count" required>
                                        <option value="1" {{ old('modules_count') == 1 ? 'selected' : '' }}>1 Module</option>
                                        <option value="2" {{ old('modules_count', 2) == 2 ? 'selected' : '' }}>2 Modules</option>
                                        <option value="3" {{ old('modules_count') == 3 ? 'selected' : '' }}>3 Modules</option>
                                        <option value="4" {{ old('modules_count') == 4 ? 'selected' : '' }}>4 Modules</option>
                                        <option value="5" {{ old('modules_count') == 5 ? 'selected' : '' }}>5 Modules</option>
                                    </select>
                                    @error('modules_count')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">@lang('l.test_price') (@lang('l.currency')) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" value="{{ old('price', 0) }}" required>
                                    <small class="form-text text-muted">@lang('l.put_zero_if_free')</small>
                                    @error('price')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 mt-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            @lang('l.active')
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
                                    <label for="initial_score" class="form-label">@lang('l.initial_score') <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="initial_score" name="initial_score" min="0" max="500" value="{{ old('initial_score', 200) }}" required>
                                    @error('initial_score')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="default_question_score" class="form-label">@lang('l.default_question_score') <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="default_question_score" name="default_question_score" min="1" max="100" value="{{ old('default_question_score', 15) }}" required>
                                    @error('default_question_score')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="total_score" class="form-label">@lang('l.total_score') <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="total_score" name="total_score" min="1" max="1000" value="{{ old('total_score', 500) }}" readonly required>
                                    <small class="form-text text-muted">@lang('l.calculated_automatically')</small>
                                    @error('total_score')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <strong>@lang('l.note'):</strong> @lang('l.total_score_calculation_auto')
                                    <br>
                                    <div id="score-calculation" class="score-calculation"></div>
                                </div>
                            </div>

                            <!-- هيكل الاختبار -->
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">@lang('l.test_structure')</h6>
                            </div>
                            
                            <div id="modules-container">
                                <!-- سيتم إضافة الـ Modules هنا ديناميكياً -->
                            </div>

                            <!-- نلف البريك في ديف عشان نتحكم في ظهوره -->
                            <div class="col-md-6" id="break_block">
                                <div class="mb-3">
                                    <label for="break_time_minutes" class="form-label">@lang('l.break_time_minutes')</label>
                                    <input type="number" class="form-control" id="break_time_minutes" name="break_time_minutes" min="0" max="60" value="{{ old('break_time_minutes', 15) }}">
                                    <small class="form-text text-muted">@lang('l.break_time_help')</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_attempts" class="form-label">@lang('l.max_attempts') <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="max_attempts" name="max_attempts" min="1" max="10" value="{{ old('max_attempts', 1) }}" required>
                                    <small class="form-text text-muted">@lang('l.max_attempts_help')</small>
                                    @error('max_attempts')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>@lang('l.note'):</strong> @lang('l.test_timing_info')
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('l.Close')</button>
                        <button type="submit" class="btn btn-primary">@lang('l.save_test')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // إعداد DataTable
        var testsTable = $('#testsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('dashboard.admins.tests') }}",
                data: function (d) {
                    d.course_id = $('#course_filter').val();
                    d.status = $('#status_filter').val();
                    d.price_type = $('#price_filter').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'course_name', name: 'course.name'},
                {data: 'price_formatted', name: 'price'},
                {data: 'questions_status', name: 'questions_status', orderable: false, searchable: false},
                {data: 'students_count', name: 'students_count', orderable: false, searchable: false},
                {data: 'status', name: 'is_active', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[1, 'desc']],
            language: {
                url: "{{ asset('assets/back/js/datatables-ar.json') }}"
            }
        });

        // تطبيق الفلاتر
        $('#course_filter, #status_filter, #price_filter').change(function() {
            testsTable.draw();
        });

        // مسح الفلاتر
        $('#clearFilters').click(function() {
            $('#filterForm')[0].reset();
            testsTable.draw();
        });

        // تبديل الحالة
        $(document).on('change', '.status-toggle', function() {
            var testId = $(this).data('id');
            var isActive = $(this).is(':checked');

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
                        // إعادة تعيين الحالة
                        $(this).prop('checked', !isActive);
                    }
                },
                error: function() {
                    toastr.error('@lang("l.error_occurred")');
                    // إعادة تعيين الحالة
                    $(this).prop('checked', !isActive);
                }
            });
        });

        // دالة إظهار/إخفاء البريك حسب عدد الموديولز
        function toggleBreakVisibility() {
            const modulesCount = parseInt($('#modules_count').val()) || 1;
            const $breakBlock = $('#break_block');
            const $breakInput = $('#break_time_minutes');

            if (modulesCount === 1) {
                // hide break and force 0
                if ($breakBlock.length) $breakBlock.hide();
                if ($breakInput.length) $breakInput.val(0);
            } else {
                // show break; give default if empty/zero
                if ($breakBlock.length) $breakBlock.show();
                if ($breakInput.length && (!$breakInput.val() || $breakInput.val() === '0')) {
                    $breakInput.val(15);
                }
            }
        }

        // إدارة الـ Modules ديناميكياً (كما في الكود الأصلي مع ترجمة النصوص فقط)
        function updateModules() {
            const modulesCount = parseInt($('#modules_count').val());
            const container = $('#modules-container');
            container.empty();

            for (let i = 1; i <= modulesCount; i++) {
                const moduleHtml = `
                    <div class="row module-row">
                        <div class="col-12">
                            <div class="module-header">
                                <h6 class="mb-0">Module ${i}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="module${i}_questions_count" class="form-label">
                                    Module ${i} Questions Count <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control module-questions"
                                       id="module${i}_questions_count"
                                       name="modules[${i}][questions_count]"
                                       min="1" max="100"
                                       value="${i <= 2 ? 20 : 10}"
                                       required>
                                <div class="invalid-feedback">Please enter questions count.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="module${i}_time_minutes" class="form-label">
                                    Module ${i} Time (Minutes) <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control module-time"
                                       id="module${i}_time_minutes"
                                       name="modules[${i}][time_minutes]"
                                       min="1" max="300"
                                       value="${i <= 2 ? 30 : 15}"
                                       required>
                                <div class="invalid-feedback">Please enter time in minutes.</div>
                            </div>
                        </div>
                    </div>
                `;
                container.append(moduleHtml);
            }
            
            // تحديث حساب الدرجات بعد إضافة الـ Modules
            updateScoreCalculation();
            toggleBreakVisibility();
        }

        // تحديث الـ Modules عند تغيير العدد
        $('#modules_count').change(updateModules);

        // حساب الدرجات التلقائي (نفس المنطق السابق مع نص إنجليزي)
        function updateScoreCalculation() {
            const initialScore = parseInt($('#initial_score').val()) || 0;
            const questionScore = parseInt($('#default_question_score').val()) || 0;
            
            let totalQuestions = 0;
            $('.module-questions').each(function() {
                totalQuestions += parseInt($(this).val()) || 0;
            });

            const calculatedTotal = initialScore + (totalQuestions * questionScore);

            // تحديث حقل الدرجة الكلية
            $('#total_score').val(calculatedTotal);

            let message = `Initial score: ${initialScore} + `;
            message += `(${totalQuestions} questions × ${questionScore} points) = ${calculatedTotal} total points`;

            $('#score-calculation').text(message);
            
            if (initialScore >= calculatedTotal) {
                $('#score-calculation').removeClass('score-correct').addClass('score-warning');
                $('#score-calculation').append('<br><i class="fas fa-exclamation-triangle"></i> Initial score must be less than total score.');
            } else {
                $('#score-calculation').removeClass('score-warning').addClass('score-correct');
                $('#score-calculation').append('<br><i class="fas fa-check-circle"></i> Calculation looks correct.');
            }
        }

        // تحديث الحساب عند تغيير القيم
        $('#initial_score, #default_question_score').on('input', updateScoreCalculation);
        $(document).on('input', '.module-questions', updateScoreCalculation);

        // التحقق من النموذج قبل الإرسال
        $('#addTestForm').on('submit', function(e) {
            let isValid = true;

            const initialScore = parseInt($('#initial_score').val()) || 0;
            const totalScore = parseInt($('#total_score').val()) || 0;

            // التحقق من الدرجات
            if (initialScore >= totalScore) {
                alert('Initial score must be less than total score.');
                isValid = false;
            }

            // التحقق من أن جميع حقول الـ Modules مملوءة
            $('.module-questions, .module-time').each(function() {
                if (!$(this).val() || $(this).val() < 1) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields correctly.');
                return false;
            }

            // إظهار رسالة تحميل
            $('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
            
            // السماح بالإرسال الطبيعي
            return true;
        });

        // إعادة تمكين زر الإرسال عند إغلاق المودال
        $('#addTestModal').on('hidden.bs.modal', function () {
            $('button[type="submit"]').html('@lang("l.save_test")').prop('disabled', false);
        });

        // حذف السجل
        $(document).on('click', '.delete-record', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            var testName = $(this).closest('tr').find('td:eq(1)').text();
            
            if (confirm('@lang("l.are_you_sure_delete_test") "' + testName + '"?')) {
                window.location.href = href;
            }
        });

        // تهيئة الـ Modules عند تحميل الصفحة
        updateModules();
        toggleBreakVisibility();

        // إعادة تعيين الفورم عند إغلاق المودال
        $('#addTestModal').on('hidden.bs.modal', function () {
            $('#addTestForm')[0].reset();
            updateModules();
        });
    });
</script>
@endsection
