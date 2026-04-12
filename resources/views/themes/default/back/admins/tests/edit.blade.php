@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.edit') - {{ $test->name }}
@endsection

@section('css')
    <style>
        .main-content label.form-label,
        .main-content label {
            font-size: 18px !important;
            font-weight: 600;
        }

        .main-content h6.text-primary.border-bottom {
            font-size: 22px;
            font-weight: 700;
        }

        .module-score-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            background: #f9fafb;
            margin-bottom: 16px;
        }

        .module-score-card h6 {
            margin-bottom: 14px;
            font-size: 18px;
            font-weight: 700;
            color: #2563eb;
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

        @can('edit lectures')
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0">@lang('l.edit') - <span class="text-primary">{{ $test->name }}</span></h4>
                    <p class="text-muted mb-0">{{ $test->course->name ?? '' }}</p>
                </div>
                <div>
                    <a href="{{ route('dashboard.admins.tests-show', ['id' => encrypt($test->id)]) }}"
                       class="btn btn-info waves-effect waves-light mb-2">
                        <i class="fa fa-eye ti-xs me-1"></i>
                        @lang('l.View')
                    </a>
                    <a href="{{ route('dashboard.admins.tests') }}"
                       class="btn btn-secondary waves-effect waves-light mb-2">
                        <i class="fa fa-arrow-left ti-xs me-1"></i>
                        @lang('l.back_to_list')
                    </a>
                </div>
            </div>

            @php
                $hasStudents = $test->studentTests()->exists();

                $adminOverride = Gate::check('edit lectures');

                $locked = $hasStudents && !$adminOverride;

                $initialModulesCount = 1;
                for ($i = 5; $i >= 1; $i--) {
                    $field = "part{$i}_questions_count";
                    if (!empty($test->$field) && $test->$field > 0) {
                        $initialModulesCount = $i;
                        break;
                    }
                }
            @endphp

            <div class="card" style="padding: 15px;">
                <div class="card-body">
                    <form action="{{ route('dashboard.admins.tests-update') }}" method="POST" id="editTestForm">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="id" value="{{ encrypt($test->id) }}">

                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">@lang('l.basic_information')</h6>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">@lang('l.test_name') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ old('name', $test->name) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">@lang('l.Course') <span class="text-danger">*</span></label>
                                    <select class="form-select" id="course_id" name="course_id" required>
                                        <option value="">@lang('l.select_course')</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id', $test->course_id) == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">@lang('l.test_description')</label>
                                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $test->description) }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">@lang('l.test_price') (EGP) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="price" name="price" min="0" step="0.01"
                                           value="{{ old('price', $test->price) }}" required>
                                    <small class="form-text text-muted">@lang('l.put_zero_if_free')</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 mt-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                            {{ old('is_active', $test->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            @lang('l.Active')
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">@lang('l.scoring_system')</h6>
                                @if($hasStudents && !$adminOverride)
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>@lang('l.warning'):</strong> @lang('l.cannot_edit_structure_students_taken')
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="initial_score" class="form-label">@lang('l.initial_score') <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="initial_score" name="initial_score" min="0" max="100000"
                                           value="{{ old('initial_score', $test->initial_score) }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info">
                                    <strong>@lang('l.Note'):</strong>
                                    Final score will be calculated automatically after adding the test questions based on module scoring settings.
                                    <br>
                                    <span id="score-calculation-preview" class="fw-bold text-success"></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">Module Scoring</h6>
                            </div>

                            @for($i = 1; $i <= 5; $i++)
                                <div class="col-12 module-score-block" data-module="{{ $i }}">
                                    <div class="module-score-card">
                                        <h6>Module {{ $i }} Scoring</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="module{{ $i }}_easy_score" class="form-label">
                                                        Module {{ $i }} Easy Score <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number"
                                                           class="form-control module-score-input"
                                                           id="module{{ $i }}_easy_score"
                                                           name="module{{ $i }}_easy_score"
                                                           min="0"
                                                           max="100000"
                                                           value="{{ old("module{$i}_easy_score", $test->{"module{$i}_easy_score"} ?? 0) }}"
                                                           {{ $locked ? 'readonly' : '' }}>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="module{{ $i }}_medium_score" class="form-label">
                                                        Module {{ $i }} Medium Score <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number"
                                                           class="form-control module-score-input"
                                                           id="module{{ $i }}_medium_score"
                                                           name="module{{ $i }}_medium_score"
                                                           min="0"
                                                           max="100000"
                                                           value="{{ old("module{$i}_medium_score", $test->{"module{$i}_medium_score"} ?? 0) }}"
                                                           {{ $locked ? 'readonly' : '' }}>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="module{{ $i }}_hard_score" class="form-label">
                                                        Module {{ $i }} Hard Score <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number"
                                                           class="form-control module-score-input"
                                                           id="module{{ $i }}_hard_score"
                                                           name="module{{ $i }}_hard_score"
                                                           min="0"
                                                           max="100000"
                                                           value="{{ old("module{$i}_hard_score", $test->{"module{$i}_hard_score"} ?? 0) }}"
                                                           {{ $locked ? 'readonly' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor

                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">@lang('l.test_structure')</h6>
                            </div>

                            <div class="col-md-4">
                                <label for="modules_count" class="form-label">@lang('l.modules_count')</label>
                                <select class="form-select" id="modules_count">
                                    @for($i=1;$i<=5;$i++)
                                        <option value="{{ $i }}" {{ $initialModulesCount == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                <small class="form-text text-muted">
                                    @lang('l.modules_help_text', [], null) ?? 'Select number of modules (1–5). Hidden modules will be treated as 0 questions.'
                                </small>
                            </div>

                            <div class="col-12"></div>

                            @for($i = 1; $i <= 5; $i++)
                                @php
                                    $questionsField = "part{$i}_questions_count";
                                    $timeField      = "part{$i}_time_minutes";
                                    $isRequired     = $i === 1;
                                @endphp

                                <div class="col-md-6 module-block" data-module="{{ $i }}">
                                    <div class="mb-3">
                                        <label for="{{ $questionsField }}" class="form-label">
                                            Module {{ $i }} Questions Count
                                            @if($isRequired)<span class="text-danger">*</span>@endif
                                        </label>
                                        <input
                                            type="number"
                                            class="form-control"
                                            id="{{ $questionsField }}"
                                            name="{{ $questionsField }}"
                                            min="{{ $isRequired ? 1 : 0 }}"
                                            max="100"
                                            value="{{ old($questionsField, $test->$questionsField ?? 0) }}"
                                            {{ $locked ? 'readonly' : '' }}
                                            {{ $isRequired ? 'required' : '' }}
                                        >
                                    </div>
                                </div>

                                <div class="col-md-6 module-block" data-module="{{ $i }}">
                                    <div class="mb-3">
                                        <label for="{{ $timeField }}" class="form-label">
                                            Module {{ $i }} Time (Minutes)
                                            @if($isRequired)<span class="text-danger">*</span>@endif
                                        </label>
                                        <input
                                            type="number"
                                            class="form-control"
                                            id="{{ $timeField }}"
                                            name="{{ $timeField }}"
                                            min="{{ $isRequired ? 1 : 0 }}"
                                            max="300"
                                            value="{{ old($timeField, $test->$timeField ?? 0) }}"
                                            {{ $locked ? 'readonly' : '' }}
                                            {{ $isRequired ? 'required' : '' }}
                                        >
                                    </div>
                                </div>
                            @endfor

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="break_time_minutes" class="form-label">@lang('l.break_time_minutes')</label>
                                    <input type="number" class="form-control" id="break_time_minutes" name="break_time_minutes" min="0" max="60"
                                           value="{{ old('break_time_minutes', $test->break_time_minutes) }}"
                                           {{ $locked ? 'readonly' : '' }}>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_attempts" class="form-label">@lang('l.max_attempts') <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="max_attempts" name="max_attempts" min="1" max="10"
                                           value="{{ old('max_attempts', $test->max_attempts ?? 1) }}"
                                           {{ $locked ? 'readonly' : '' }} required>
                                    <small class="form-text text-muted">@lang('l.max_attempts_help')</small>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>@lang('l.Note'):</strong> @lang('l.test_timing_info')
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-end d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">@lang('l.Cancel')</button>
                                    <button type="submit" class="btn btn-primary">@lang('l.Update')</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endcan
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            const locked = {{ $locked ? 'true' : 'false' }};

            function getInt(selector) {
                return parseInt($(selector).val()) || 0;
            }

            function updateScorePreview() {
                const modulesCount = getInt('#modules_count');
                const initialScore = getInt('#initial_score');

                let message = `Initial Score: ${initialScore}`;

                for (let i = 1; i <= modulesCount; i++) {
                    const easy = getInt(`#module${i}_easy_score`);
                    const medium = getInt(`#module${i}_medium_score`);
                    const hard = getInt(`#module${i}_hard_score`);

                    message += ` | Module ${i}: Easy ${easy}, Medium ${medium}, Hard ${hard}`;
                }

                message += ` | Final score will be calculated automatically after adding questions.`;

                $('#score-calculation-preview')
                    .removeClass('text-danger')
                    .addClass('text-success')
                    .html(message);
            }

            function toggleModulesEdit() {
                let count = parseInt($('#modules_count').val()) || 1;

                $('.module-block').each(function () {
                    const mod = parseInt($(this).data('module'));
                    if (mod <= count) {
                        $(this).show();
                    } else {
                        $(this).hide();
                        const input = $(this).find('input[type="number"]');
                        if (!locked) {
                            input.val(0);
                        }
                    }
                });

                $('.module-score-block').each(function () {
                    const mod = parseInt($(this).data('module'));
                    if (mod <= count) {
                        $(this).show();
                        $(this).find('input').prop('required', true);
                    } else {
                        $(this).hide();
                        $(this).find('input').prop('required', false);
                        if (!locked) {
                            $(this).find('input').val(0);
                        }
                    }
                });

                if (count <= 1) {
                    if (!locked) {
                        $('#break_time_minutes').val(0);
                    }
                    $('#break_time_minutes').closest('.col-md-6').hide();
                } else {
                    $('#break_time_minutes').closest('.col-md-6').show();
                }

                updateScorePreview();
            }

            $('#modules_count').on('change', toggleModulesEdit);
            $('#initial_score').on('input', updateScorePreview);
            $(document).on('input', '.module-score-input', updateScorePreview);

            toggleModulesEdit();
            updateScorePreview();

            $('#editTestForm').on('submit', function () {
                if (!locked) {
                    let count = parseInt($('#modules_count').val()) || 1;
                    for (let i = count + 1; i <= 5; i++) {
                        $('#part' + i + '_questions_count').val(0);
                        $('#part' + i + '_time_minutes').val(0);

                        $('#module' + i + '_easy_score').val(0);
                        $('#module' + i + '_medium_score').val(0);
                        $('#module' + i + '_hard_score').val(0);
                    }
                }
            });
        });
    </script>
@endsection