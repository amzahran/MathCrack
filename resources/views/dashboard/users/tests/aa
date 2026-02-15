@extends('themes.default.layouts.back.student-master')

@section('title')
    @lang('l.test_comparison')
@endsection

@section('css')
    <style>
        .comparison-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
            border-radius: 15px;
        }

        .comparison-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .comparison-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .comparison-table th {
            background: #f8fafc;
            padding: 15px;
            text-align: center;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
            position: sticky;
            top: 0;
        }

        .comparison-table td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .question-number {
            background: #1e40af;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin: 0 auto;
        }

        .attempt-result {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 500;
        }

        .correct {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .incorrect {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .not-attempted {
            background: #f3f4f6;
            color: #6b7280;
            border: 1px solid #d1d5db;
        }

        .attempt-score {
            font-weight: 600;
            color: #1e40af;
        }

        .attempt-date {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 5px;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-left: 4px solid #1e40af;
        }

        .summary-card.correct {
            border-left-color: #10b981;
        }

        .summary-card.incorrect {
            border-left-color: #ef4444;
        }

        .summary-card.improvement {
            border-left-color: #f59e0b;
        }

        .summary-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .summary-card.correct .summary-number {
            color: #10b981;
        }

        .summary-card.incorrect .summary-number {
            color: #ef4444;
        }

        .summary-card.improvement .summary-number {
            color: #f59e0b;
        }

        .summary-label {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .improvement-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 5px;
        }

        .improvement-positive {
            background: #d1fae5;
            color: #065f46;
        }

        .improvement-negative {
            background: #fee2e2;
            color: #991b1b;
        }

        .improvement-neutral {
            background: #f3f4f6;
            color: #6b7280;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
            color: white;
        }

        .btn-secondary {
            background: white;
            color: #6b7280;
            border: 2px solid #e5e7eb;
        }

        .btn-secondary:hover {
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .attempt-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .attempt-number {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .comparison-table th,
            .comparison-table td {
                padding: 10px 8px;
                font-size: 0.9rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-action {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <!-- Comparison Header -->
        <div class="comparison-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1>@lang('l.test_comparison')</h1>
                        <p>{{ $test->name }} - @lang('l.comparison_between_attempts')</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <i class="fas fa-chart-bar fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attempts Summary -->
        <div class="stats-row">
            @foreach($attempts as $index => $attempt)
                <div class="stat-box">
                    <div class="attempt-header">
                        <div class="attempt-number">@lang('l.attempt') {{ $index + 1 }}</div>
                        <div class="attempt-date">{{ $attempt->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                    <div class="stat-value">{{ $attempt->final_score }}/{{ $test->total_score }}</div>
                    <div class="stat-label">@lang('l.score')</div>
                    <div class="stat-value">{{ number_format(($attempt->final_score / $test->total_score) * 100, 1) }}%</div>
                    <div class="stat-label">@lang('l.percentage')</div>
                </div>
            @endforeach
            
            <!-- Improvement Summary -->
            <div class="stat-box">
                <div class="attempt-header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="attempt-number">@lang('l.improvement')</div>
                </div>
                @php
                    $firstScore = $attempts->first()->final_score;
                    $lastScore = $attempts->last()->final_score;
                    $improvement = $lastScore - $firstScore;
                    $improvementPercent = $firstScore > 0 ? (($improvement / $firstScore) * 100) : 100;
                @endphp
                <div class="stat-value {{ $improvement >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $improvement >= 0 ? '+' : '' }}{{ $improvement }}
                </div>
                <div class="stat-label">@lang('l.score_change')</div>
                <div class="stat-value {{ $improvementPercent >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $improvementPercent >= 0 ? '+' : '' }}{{ number_format($improvementPercent, 1) }}%
                </div>
                <div class="stat-label">@lang('l.percentage_change')</div>
            </div>
        </div>

        <!-- Questions Comparison Table -->
        <div class="comparison-table">
            <div class="table-header">
                <h3 class="mb-0">@lang('l.questions_comparison')</h3>
                <p class="mb-0 text-muted">@lang('l.comparison_table_description')</p>
            </div>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>@lang('l.question')</th>
                            @foreach($attempts as $index => $attempt)
                                <th>
                                    <div>@lang('l.attempt') {{ $index + 1 }}</div>
                                    <div class="attempt-date">{{ $attempt->created_at->format('M d, Y') }}</div>
                                </th>
                            @endforeach
                            <th>@lang('l.progress')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questions as $question)
                            <tr>
                                <td>
                                    <div class="question-number">{{ $loop->iteration }}</div>
                                </td>
                                
                                @foreach($attempts as $attempt)
                                    @php
                                        $answer = $attempt->answers->where('question_id', $question->id)->first();
                                        $isCorrect = $answer ? $answer->is_correct : false;
                                        $isAnswered = $answer ? (!is_null($answer->answer_text) || !is_null($answer->selected_option_id)) : false;
                                    @endphp
                                    <td>
                                        <div class="attempt-result {{ $isAnswered ? ($isCorrect ? 'correct' : 'incorrect') : 'not-attempted' }}">
                                            <i class="fas {{ $isAnswered ? ($isCorrect ? 'fa-check' : 'fa-times') : 'fa-minus' }}"></i>
                                            <span>
                                                @if($isAnswered)
                                                    {{ $isCorrect ? __('l.correct') : __('l.incorrect') }}
                                                @else
                                                    @lang('l.not_attempted')
                                                @endif
                                            </span>
                                        </div>
                                        @if($isAnswered)
                                            <div class="attempt-score">
                                                {{ $answer->score_earned }}/{{ $question->score }}
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                                
                                <td>
                                    @php
                                        $progress = [];
                                        foreach($attempts as $attempt) {
                                            $ans = $attempt->answers->where('question_id', $question->id)->first();
                                            $progress[] = $ans ? ($ans->is_correct ? 'correct' : 'incorrect') : 'not_attempted';
                                        }
                                        
                                        $allCorrect = count(array_filter($progress, fn($p) => $p === 'correct')) === count($progress);
                                        $allIncorrect = count(array_filter($progress, fn($p) => $p === 'incorrect')) === count($progress);
                                        $improved = false;
                                        
                                        if (count($progress) >= 2) {
                                            $last = end($progress);
                                            $first = reset($progress);
                                            $improved = ($first === 'incorrect' || $first === 'not_attempted') && $last === 'correct';
                                        }
                                    @endphp
                                    
                                    @if($allCorrect)
                                        <span class="improvement-badge improvement-positive">
                                            <i class="fas fa-check"></i>
                                            @lang('l.consistent_correct')
                                        </span>
                                    @elseif($allIncorrect)
                                        <span class="improvement-badge improvement-negative">
                                            <i class="fas fa-times"></i>
                                            @lang('l.consistent_incorrect')
                                        </span>
                                    @elseif($improved)
                                        <span class="improvement-badge improvement-positive">
                                            <i class="fas fa-arrow-up"></i>
                                            @lang('l.improved')
                                        </span>
                                    @else
                                        <span class="improvement-badge improvement-neutral">
                                            <i class="fas fa-minus"></i>
                                            @lang('l.mixed_results')
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card correct">
                <div class="summary-number">{{ $stats['total_correct_questions'] }}</div>
                <div class="summary-label">@lang('l.total_correct_questions')</div>
            </div>
            
            <div class="summary-card incorrect">
                <div class="summary-number">{{ $stats['total_incorrect_questions'] }}</div>
                <div class="summary-label">@lang('l.total_incorrect_questions')</div>
            </div>
            
            <div class="summary-card improvement">
                <div class="summary-number">{{ $stats['improved_questions'] }}</div>
                <div class="summary-label">@lang('l.improved_questions')</div>
                <div class="improvement-badge improvement-positive">
                    +{{ $stats['improvement_rate'] }}%
                </div>
            </div>
            
            <div class="summary-card">
                <div class="summary-number">{{ $stats['consistency_rate'] }}%</div>
                <div class="summary-label">@lang('l.consistency_rate')</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('dashboard.users.tests.results', $test->id) }}" class="btn-action btn-primary">
                <i class="fas fa-arrow-left"></i>
                @lang('l.back_to_results')
            </a>
            
            <a href="{{ route('dashboard.users.tests.show', $test->id) }}" class="btn-action btn-secondary">
                <i class="fas fa-redo"></i>
                @lang('l.retake_test')
            </a>
            
            <button class="btn-action btn-secondary" onclick="window.print()">
                <i class="fas fa-print"></i>
                @lang('l.print_comparison')
            </button>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Add animation to table rows
            $('tbody tr').each(function(index) {
                $(this).css('opacity', '0').delay(index * 50).animate({
                    opacity: 1
                }, 300);
            });

            // Highlight improved questions
            $('.improvement-badge.improvement-positive').closest('tr').css('background', '#f0fdf4');
        });
    </script>
@endsection