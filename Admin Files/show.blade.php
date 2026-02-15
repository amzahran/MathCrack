@extends('themes.default.layouts.back.master')

@section('title')
    {{ $test->name }}
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

        @can('show lectures')
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0">{{ $test->name }}</h4>
                    <p class="text-muted mb-0">{{ $test->course->name ?? '' }}</p>
                </div>
                <div>
                    <a href="{{ route('dashboard.admins.tests-questions', ['test_id' => encrypt($test->id)]) }}" class="btn btn-primary waves-effect waves-light mb-2">
                        <i class="fa fa-question-circle ti-xs me-1"></i>
                        @lang('l.test_questions')
                    </a>
                    <a href="{{ route('dashboard.admins.tests-edit', ['id' => encrypt($test->id)]) }}" class="btn btn-warning waves-effect waves-light mb-2">
                        <i class="fa fa-edit ti-xs me-1"></i>
                        @lang('l.edit')
                    </a>
                    <a href="{{ route('dashboard.admins.tests') }}" class="btn btn-secondary waves-effect waves-light mb-2">
                        <i class="fa fa-arrow-left ti-xs me-1"></i>
                        @lang('l.back_to_list')
                    </a>
                </div>
            </div>

            <!-- معلومات الاختبار -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">@lang('l.test_details')</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">@lang('l.test_name'):</td>
                                            <td>{{ $test->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.Course'):</td>
                                            <td><span class="badge bg-info">{{ $test->course->name }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.Price'):</td>
                                            <td>
                                                @if($test->price > 0)
                                                    <span class="text-success fw-bold">{{ number_format($test->price, 2) }} @lang('l.currency')</span>
                                                @else
                                                    <span class="text-muted">@lang('l.free')</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.Status'):</td>
                                            <td>
                                                @if($test->is_active)
                                                    <span class="badge bg-success">@lang('l.Active')</span>
                                                @else
                                                    <span class="badge bg-secondary">@lang('l.Inactive')</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.created_at'):</td>
                                            <td>{{ $test->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">@lang('l.total_score'):</td>
                                            <td><span class="badge bg-primary">{{ $test->total_score }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.initial_score'):</td>
                                            <td><span class="badge bg-secondary">{{ $test->initial_score }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.default_question_score'):</td>
                                            <td><span class="badge bg-info">{{ $test->default_question_score }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.total_time'):</td>
                                            <td><span class="badge bg-warning">{{ $test->part1_time_minutes + $test->part2_time_minutes }} @lang('l.minutes')</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.break_time'):</td>
                                            <td><span class="badge bg-secondary">{{ $test->break_time_minutes }} @lang('l.minutes')</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($test->description)
                                <div class="mt-3">
                                    <h6 class="fw-bold">@lang('l.description'):</h6>
                                    <p class="text-muted">{{ $test->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- هيكل الاختبار -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">@lang('l.test_structure')</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">@lang('l.first_part')</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li><i class="fas fa-question-circle text-primary"></i> {{ $test->part1_questions_count }} @lang('l.questions')</li>
                                                <li><i class="fas fa-clock text-primary"></i> {{ $test->part1_time_minutes }} @lang('l.minutes')</li>
                                                <li><i class="fas fa-chart-line text-primary"></i> {{ $stats['part1_questions'] }}/{{ $test->part1_questions_count }} @lang('l.questions_added')</li>
                                            </ul>
                                            @if($questionStatus['part1_complete'])
                                                <div class="text-success mt-2">
                                                    <i class="fas fa-check-circle"></i> @lang('l.complete')
                                                </div>
                                            @else
                                                <div class="text-warning mt-2">
                                                    <i class="fas fa-exclamation-circle"></i> @lang('l.incomplete')
                                                    ({{ $test->part1_questions_count - $stats['part1_questions'] }} @lang('l.questions_remaining'))
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">@lang('l.second_part')</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li><i class="fas fa-question-circle text-success"></i> {{ $test->part2_questions_count }} @lang('l.questions')</li>
                                                <li><i class="fas fa-clock text-success"></i> {{ $test->part2_time_minutes }} @lang('l.minutes')</li>
                                                <li><i class="fas fa-chart-line text-success"></i> {{ $stats['part2_questions'] }}/{{ $test->part2_questions_count }} @lang('l.questions_added')</li>
                                            </ul>
                                            @if($questionStatus['part2_complete'])
                                                <div class="text-success mt-2">
                                                    <i class="fas fa-check-circle"></i> @lang('l.complete')
                                                </div>
                                            @else
                                                <div class="text-warning mt-2">
                                                    <i class="fas fa-exclamation-circle"></i> @lang('l.incomplete')
                                                    ({{ $test->part2_questions_count - $stats['part2_questions'] }} @lang('l.questions_remaining'))
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الإحصائيات والإجراءات السريعة -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">@lang('l.statistics')</h5>
                        </div>
                        <div class="card-body">
                            <div class="stat-item mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>@lang('l.total_questions')</span>
                                    <strong class="text-primary">{{ $stats['total_questions'] }}/{{ $test->part1_questions_count + $test->part2_questions_count }}</strong>
                                </div>
                                <div class="progress mt-1">
                                    @php
                                        $totalExpected = $test->part1_questions_count + $test->part2_questions_count;
                                        $progressPercentage = $totalExpected > 0 ? ($stats['total_questions'] / $totalExpected) * 100 : 0;
                                    @endphp
                                    <div class="progress-bar bg-primary" style="width: {{ $progressPercentage }}%"></div>
                                </div>
                            </div>

                            <div class="stat-item mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>@lang('l.total_students')</span>
                                    <strong class="text-info">{{ $stats['total_students'] }}</strong>
                                </div>
                            </div>

                            <div class="stat-item mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>@lang('l.completed_students')</span>
                                    <strong class="text-success">{{ $stats['completed_students'] }}</strong>
                                </div>
                            </div>

                            @if($stats['completed_students'] > 0)
                                <hr>
                                <h6>@lang('l.score_statistics')</h6>
                                <div class="stat-item mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>@lang('l.average_score')</span>
                                        <strong class="text-primary">{{ number_format($stats['average_score'], 1) }}</strong>
                                    </div>
                                </div>
                                <div class="stat-item mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>@lang('l.highest_score')</span>
                                        <strong class="text-success">{{ $stats['highest_score'] }}</strong>
                                    </div>
                                </div>
                                <div class="stat-item mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>@lang('l.lowest_score')</span>
                                        <strong class="text-danger">{{ $stats['lowest_score'] }}</strong>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- الإجراءات السريعة -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">@lang('l.quick_actions')</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if(!$questionStatus['all_complete'])
                                    <a href="{{ route('dashboard.admins.tests-questions', ['test_id' => encrypt($test->id)]) }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> @lang('l.add_question')
                                    </a>
                                @endif

                                <a href="{{ route('dashboard.admins.tests-questions', ['test_id' => encrypt($test->id)]) }}" class="btn btn-info">
                                    <i class="fas fa-list"></i> @lang('l.view_questions')
                                </a>

                                @if($stats['total_students'] > 0)
                                    <button class="btn btn-secondary" onclick="alert('@lang('l.feature_coming_soon')')">
                                        <i class="fas fa-chart-bar"></i> @lang('l.detailed_reports')
                                    </button>
                                @endif

                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="test-status"
                                           data-id="{{ $test->id }}" {{ $test->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="test-status">
                                        @lang('l.active_test')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- @if($test->studentTests()->count() > 0)
                <!-- نتائج الطلاب الأخيرة -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">@lang('l.recent_student_results')</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>@lang('l.student')</th>
                                                <th>@lang('l.Status')</th>
                                                <th>@lang('l.current_score')</th>
                                                <th>@lang('l.final_score')</th>
                                                <th>@lang('l.started_at')</th>
                                                <th>@lang('l.completed_at')</th>
                                                <th>@lang('l.Action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($test->studentTests()->with('student')->latest()->take(10)->get() as $studentTest)
                                            <tr>
                                                <td>{{ $studentTest->student->name ?? 'N/A' }}</td>
                                                <td>
                                                    @php
                                                        $statusBadge = match($studentTest->status) {
                                                            'completed' => 'bg-success',
                                                            'part1_in_progress', 'part2_in_progress' => 'bg-warning',
                                                            'break_time' => 'bg-info',
                                                            default => 'bg-secondary'
                                                        };
                                                        $statusText = match($studentTest->status) {
                                                            'not_started' => __('l.not_started'),
                                                            'part1_in_progress' => __('l.part1_in_progress'),
                                                            'break_time' => __('l.break_time'),
                                                            'part2_in_progress' => __('l.part2_in_progress'),
                                                            'completed' => __('l.completed'),
                                                            default => $studentTest->status
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                                                </td>
                                                <td>{{ $studentTest->current_score ?? '-' }}</td>
                                                <td>{{ $studentTest->final_score ?? '-' }}</td>
                                                <td>{{ $studentTest->started_at ? $studentTest->started_at->format('Y-m-d H:i') : '-' }}</td>
                                                <td>{{ $studentTest->completed_at ? $studentTest->completed_at->format('Y-m-d H:i') : '-' }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" onclick="alert('@lang('l.feature_coming_soon')')">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif --}}
        @endcan
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // تبديل حالة الاختبار
            $('#test-status').change(function() {
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
                            toastr.error(response.message || '@lang("l.error_occurred")');
                        }
                    },
                    error: function() {
                        toastr.error('@lang("l.error_occurred")');
                        // إعادة تعيين الحالة
                        $('#test-status').prop('checked', !isActive);
                    }
                });
            });
        });
    </script>
@endsection
