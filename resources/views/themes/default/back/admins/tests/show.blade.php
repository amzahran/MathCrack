@extends('themes.default.layouts.back.master')

@section('title')
    {{ $test->name }}
@endsection

@section('css')
    <style>
        .part-card .card-header {
            font-size: 16px;
            font-weight: 600;
        }
        .stat-item {
            margin-bottom: 15px;
        }
        .progress {
            height: 8px;
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
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0">{{ $test->name }}</h4>
                    <p class="text-muted mb-0">{{ $test->course->name ?? '' }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('dashboard.admins.tests-questions', ['test_id' => encrypt($test->id)]) }}" 
                       class="btn btn-primary waves-effect waves-light">
                        <i class="fa fa-question-circle ti-xs me-1"></i>
                        @lang('l.test_questions')
                    </a>
                    <a href="{{ route('dashboard.admins.tests-edit', ['id' => encrypt($test->id)]) }}" 
                       class="btn btn-warning waves-effect waves-light">
                        <i class="fa fa-edit ti-xs me-1"></i>
                        @lang('l.edit')
                    </a>
                    <a href="{{ route('dashboard.admins.tests') }}" 
                       class="btn btn-secondary waves-effect waves-light">
                        <i class="fa fa-arrow-left ti-xs me-1"></i>
                        @lang('l.back_to_list')
                    </a>
                </div>
            </div>

            <!-- Test Details -->
            <div class="row">
                <div class="col-lg-8">
                    <!-- Basic Info Card -->
                    <div class="card mb-3">
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
                                                    <span class="text-success fw-bold">
                                                        {{ number_format($test->price, 2) }} @lang('l.currency')
                                                    </span>
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
                                          
                                            <td>
                                                <span class="badge bg-warning">
                                                    {{ $stats['total_time'] }} @lang('l.minutes')
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.break_time'):</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $test->break_time_minutes }} @lang('l.minutes')
                                                </span>
                                            </td>
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

                    <!-- Test Structure -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">@lang('l.test_structure')</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @php
                                    $colors = [
                                        1 => ['bg' => 'bg-primary', 'border' => 'border-primary', 'text' => 'text-primary'],
                                        2 => ['bg' => 'bg-success', 'border' => 'border-success', 'text' => 'text-success'],
                                        3 => ['bg' => 'bg-info', 'border' => 'border-info', 'text' => 'text-info'],
                                        4 => ['bg' => 'bg-warning', 'border' => 'border-warning', 'text' => 'text-warning'],
                                        5 => ['bg' => 'bg-secondary', 'border' => 'border-secondary', 'text' => 'text-secondary'],
                                    ];
                                @endphp

                                @for($i = 1; $i <= 5; $i++)
                                    @php
                                        $questionField = "part{$i}_questions_count";
                                        $timeField = "part{$i}_time_minutes";
                                        $hasContent = ($test->$questionField > 0 || $test->$timeField > 0);
                                    @endphp

                                    @if($hasContent)
                                        <div class="col-md-6 mb-3">
                                            <div class="card part-card {{ $colors[$i]['border'] }}">
                                                <div class="card-header {{ $colors[$i]['bg'] }} text-white">
                                                    <h4 class="mb-0">@lang('Module') {{ $i }}</h4>
                                                </div>
                                                <div class="card-body">
                                                    <ul class="list-unstyled mb-0">
                                                        <li>
                                                            <i class="fas fa-question-circle {{ $colors[$i]['text'] }} me-2"></i>
                                                            <strong>@lang('l.questions'):</strong>
                                                            {{ $test->$questionField }}
                                                        </li>
                                                        <li>
                                                            <i class="fas fa-clock {{ $colors[$i]['text'] }} me-2"></i>
                                                            <strong>@lang('l.time'):</strong>
                                                            {{ $test->$timeField }} @lang('l.minutes')
                                                        </li>
                                                        <li>
                                                            <i class="fas fa-chart-line {{ $colors[$i]['text'] }} me-2"></i>
                                                            <strong>@lang('l.added_questions'):</strong>
                                                            {{ $stats["part{$i}_questions"] ?? 0 }}/{{ $test->$questionField }}
                                                        </li>
                                                    </ul>

                                                    @php
                                                        $isComplete = $moduleStats[$i]['complete'] ?? false;
                                                        $remaining = $test->$questionField - ($stats["part{$i}_questions"] ?? 0);
                                                    @endphp

                                                    @if($isComplete)
                                                        <div class="text-success mt-2">
                                                            <i class="fas fa-check-circle"></i>
                                                            @lang('l.complete')
                                                        </div>
                                                    @else
                                                        <div class="text-warning mt-2">
                                                            <i class="fas fa-exclamation-circle"></i>
                                                            @lang('l.incomplete')
                                                            ({{ $remaining }} @lang('l.questions_remaining'))
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics & Quick Actions -->
                <div class="col-lg-4">
                    <!-- Statistics Card -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">@lang('l.statistics')</h5>
                        </div>
                        <div class="card-body">
                            <!-- Total Questions -->
                            <div class="stat-item mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>@lang('l.total_questions')</span>
                                    <strong class="text-primary">
                                        {{ $stats['total_questions'] }}/{{ $stats['expected_questions'] }}
                                    </strong>
                                </div>
                                @php
                                    $progressPercentage = $stats['expected_questions'] > 0 
                                        ? ($stats['total_questions'] / $stats['expected_questions']) * 100 
                                        : 0;
                                @endphp
                                <div class="progress mt-1">
                                    <div class="progress-bar bg-primary" style="width: {{ $progressPercentage }}%"></div>
                                </div>
                            </div>

                            <!-- Students -->
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

                            <!-- Score Statistics (if available) -->
                            @if($stats['completed_students'] > 0)
                                <hr>
                                <h6>@lang('l.score_statistics')</h6>
                                <div class="stat-item mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>@lang('l.average_score')</span>
                                        <strong class="text-primary">
                                            {{ number_format($stats['average_score'], 1) }}
                                        </strong>
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

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">@lang('l.quick_actions')</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <!-- Add Questions -->
                                @if(!$questionStatus['all_complete'])
                                    <a href="{{ route('dashboard.admins.tests-questions', ['test_id' => encrypt($test->id)]) }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> @lang('l.add_question')
                                    </a>
                                @endif

                                <!-- View Questions -->
                                <a href="{{ route('dashboard.admins.tests-questions', ['test_id' => encrypt($test->id)]) }}" 
                                   class="btn btn-info">
                                    <i class="fas fa-list me-2"></i> @lang('l.view_questions')
                                </a>

                                <!-- Reports (if students exist) -->
                                @if($stats['total_students'] > 0)
                                    <button class="btn btn-secondary" 
                                            onclick="alert('@lang('l.feature_coming_soon')')">
                                        <i class="fas fa-chart-bar me-2"></i> @lang('l.detailed_reports')
                                    </button>
                                @endif

                                <!-- Active Toggle -->
                                <div class="form-check form-switch mt-3">
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
        @endcan
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Toggle Test Status
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
                        // Reset toggle
                        $('#test-status').prop('checked', !isActive);
                    }
                });
            });
        });
    </script>
@endsection