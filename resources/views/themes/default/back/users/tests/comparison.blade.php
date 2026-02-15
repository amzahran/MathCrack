@extends('themes.default.layouts.back.student-master')

@section('title')
    Test Comparison
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
            white-space: nowrap;
        }

        .comparison-table td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
            white-space: nowrap;
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

        .module-divider {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            min-width: 80px;
        }
        
        .bg-success {
            background-color: #d1fae5 !important;
            color: #065f46 !important;
        }
        
        .bg-danger {
            background-color: #fee2e2 !important;
            color: #991b1b !important;
        }
        
        .bg-primary {
            background-color: #dbeafe !important;
            color: #1e40af !important;
        }
        
        .bg-secondary {
            background-color: #f3f4f6 !important;
            color: #6b7280 !important;
        }
        
        .bg-info {
            background-color: #e0f2fe !important;
            color: #075985 !important;
        }
        
        .bg-warning {
            background-color: #fef3c7 !important;
            color: #92400e !important;
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
            border: none;
            cursor: pointer;
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

        .btn-comparison {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .btn-comparison:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
            color: white;
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

        .attempt-stats {
            display: flex;
            justify-content: space-around;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
        }

        .attempt-stat {
            text-align: center;
        }

        .attempt-stat-value {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .attempt-stat-label {
            font-size: 0.7rem;
            color: #6b7280;
        }

        .correct-stat {
            color: #10b981;
        }

        .incorrect-stat {
            color: #ef4444;
        }

        .debug-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .text-success {
            color: #10b981 !important;
        }

        .text-danger {
            color: #ef4444 !important;
        }

        .overall-stats {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .overall-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            text-align: center;
        }

        .overall-stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .overall-stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .questions-per-module {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #3b82f6;
        }

        @media (max-width: 768px) {
            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .comparison-table table {
                min-width: 600px;
            }
            
            .comparison-table th,
            .comparison-table td {
                padding: 10px 8px;
                font-size: 0.85rem;
            }
            
            .question-number {
                width: 30px;
                height: 30px;
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

            .overall-stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .attempt-stats {
                flex-direction: column;
                gap: 5px;
            }
        }

        @media (max-width: 480px) {
            .stats-row {
                grid-template-columns: 1fr;
            }

            .overall-stats-grid {
                grid-template-columns: 1fr;
            }
        }
        .comparison-header h1,
        .comparison-header p {
        color: white !important;
        }
        .overall-stats h3,
        .overall-stat-value,
        .overall-stat-label {
        color: white !important;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <!-- Debug information section -->
        <div class="debug-info">
            <h5>🔍 Debug Information:</h5>
            <p><strong>Number of Attempts:</strong> {{ $attempts->count() }}</p>
            <p><strong>Number of Questions:</strong> {{ $questions->count() }}</p>
            @php
                $hasModules = isset($modules) && $modules->count() > 0;
                if ($hasModules) {
                    echo '<p><strong>Number of Modules:</strong> ' . $modules->count() . '</p>';
                    foreach($modules as $module) {
                        echo '<p><strong>Module ' . $module->id . ':</strong> ' . ($module->questions->count() ?? 0) . ' questions</p>';
                    }
                }
            @endphp
            @foreach($attempts as $index => $attempt)
                <p><strong>Attempt {{ $index + 1 }}:</strong> 
                   {{ $attempt->answers->count() }} answers - 
                   Score: {{ $attempt->final_score }}/{{ $test->total_score }}
                </p>
            @endforeach
        </div>

        <!-- Comparison Header -->
        <div class="comparison-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1>Test Comparison</h1>
                        <p>{{ $test->name }} - Comparison between attempts</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <i class="fas fa-chart-bar fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overall Statistics -->
        <div class="overall-stats">
            <h3 class="text-center mb-4">Overall Performance Statistics</h3>
            <div class="overall-stats-grid">
                @php
                    $totalScore = 0;
                    $totalPercentage = 0;
                    
                    foreach($attempts as $attempt) {
                        $totalScore += $attempt->final_score;
                        $totalPercentage += ($attempt->final_score / $test->total_score) * 100;
                    }
                    
                    $averageScore = $attempts->count() > 0 ? round($totalScore / $attempts->count(), 1) : 0;
                    $averagePercentage = $attempts->count() > 0 ? round($totalPercentage / $attempts->count(), 1) : 0;
                @endphp
                <div>
                    <div class="overall-stat-value">{{ $averageScore }}/{{ $test->total_score }}</div>
                    <div class="overall-stat-label">Average Score</div>
                </div>
                <div>
                    <div class="overall-stat-value">{{ $averagePercentage }}%</div>
                    <div class="overall-stat-label">Average Percentage</div>
                </div>
                <div>
                    <div class="overall-stat-value">{{ $attempts->count() }}</div>
                    <div class="overall-stat-label">Total Attempts</div>
                </div>
                <div>
                    <div class="overall-stat-value">{{ $questions->count() }}</div>
                    <div class="overall-stat-label">Total Questions</div>
                </div>
            </div>
        </div>

        <!-- Attempts Summary -->
        <div class="stats-row">
            @foreach($attempts as $index => $attempt)
                @php
                    $correctCount = 0;
                    $incorrectCount = 0;
                    
                    foreach($questions as $question) {
                        $answer = $answerIndex[$attempt->id][$question->id] ?? null;
                        if ($answer) {
                            $correct = $answer->is_correct;
                            if (is_null($correct) && isset($answer->score_earned)) {
                                $correct = ((float)$answer->score_earned > 0);
                            }
                            
                            if ($correct === true) {
                                $correctCount++;
                            } elseif ($correct === false) {
                                $incorrectCount++;
                            }
                        }
                    }
                @endphp
                <div class="stat-box">
                    <div class="attempt-header">
                        <div class="attempt-number">Attempt {{ $index + 1 }}</div>
                        <div class="attempt-date">{{ $attempt->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                    <div class="stat-value">{{ number_format($attempt->final_score, $attempt->final_score == intval($attempt->final_score) ? 0 : 1) }}/{{ $test->total_score }}</div>
                    <div class="stat-label">Score</div>
                    <div class="stat-value">{{ number_format(($attempt->final_score / $test->total_score) * 100, 1) }}%</div>
                    <div class="stat-label">Percentage</div>
                    
                    <div class="attempt-stats">
                        <div class="attempt-stat">
                            <div class="attempt-stat-value correct-stat">{{ $correctCount }}</div>
                            <div class="attempt-stat-label">Correct</div>
                        </div>
                        <div class="attempt-stat">
                            <div class="attempt-stat-value incorrect-stat">{{ $incorrectCount }}</div>
                            <div class="attempt-stat-label">Incorrect</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Questions Comparison Table -->
        <div class="comparison-table">
            <div class="table-header">
                <h3 class="mb-0">Questions Comparison by Module</h3>
                <p class="mb-0 text-muted">Compare your performance per module across attempts</p>
            </div>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="min-width: 80px;">#</th>
                            @foreach($attempts as $index => $attempt)
                                <th style="min-width: 150px;">
                                    <div>Attempt {{ $index + 1 }}</div>
                                    <div class="attempt-date">{{ $attempt->created_at->format('M d, Y') }}</div>
                                </th>
                            @endforeach
                            <th style="min-width: 120px;">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $hasModules = isset($modules) && $modules->count() > 0;
                            
                            if (!$hasModules) {
                        @endphp
                                <tr class="module-divider">
                                    <td colspan="{{ $attempts->count() + 2 }}">
                                        All Questions - Questions 1 to {{ $questions->count() }}
                                    </td>
                                </tr>
                                
                                @foreach ($questions as $index => $q)
                                    @php
                                        $answers = [];
                                        foreach ($attempts as $attempt) {
                                            $answers[] = $answerIndex[$attempt->id][$q->id] ?? null;
                                        }

                                        $getStatus = function($ans) {
                                            if (!$ans) return ['Not Attempted', 'secondary'];

                                            $correct = $ans->is_correct;
                                            if (is_null($correct) && isset($ans->score_earned)) {
                                                $correct = ((float)$ans->score_earned > 0);
                                            }

                                            if ($correct === true)  return ['Correct', 'success'];
                                            if ($correct === false) return ['Incorrect', 'danger'];
                                            return ['Answered', 'primary'];
                                        };

                                        $statuses = [];
                                        foreach ($answers as $answer) {
                                            $statuses[] = $getStatus($answer);
                                        }

                                        $progress = ['label' => 'No Progress', 'tone' => 'secondary'];
                                        
                                        if ($attempts->count() >= 2) {
                                            $lastAttempt = $answers[count($answers) - 1] ?? null;
                                            $secondLastAttempt = $answers[count($answers) - 2] ?? null;
                                            
                                            $lastStatus = $getStatus($lastAttempt);
                                            $secondLastStatus = $getStatus($secondLastAttempt);
                                            
                                            if ($lastStatus[0] === 'Correct' && $secondLastStatus[0] !== 'Correct') {
                                                $progress = ['label' => 'Improved', 'tone' => 'success'];
                                            }
                                            elseif ($lastStatus[0] !== 'Correct' && $secondLastStatus[0] === 'Correct') {
                                                $progress = ['label' => 'Declined', 'tone' => 'danger'];
                                            }
                                            elseif ($lastStatus[0] === $secondLastStatus[0]) {
                                                if ($lastStatus[0] === 'Correct') {
                                                    $progress = ['label' => 'Maintained', 'tone' => 'info'];
                                                } else {
                                                    $progress = ['label' => 'No Change', 'tone' => 'secondary'];
                                                }
                                            }
                                            else {
                                                $progress = ['label' => 'Changed', 'tone' => 'warning'];
                                            }
                                        }
                                    @endphp

                                    <tr>
                                        <td>
                                            <div class="question-number">{{ $loop->iteration }}</div>
                                        </td>
                                        
                                        @foreach($statuses as $status)
                                            <td>
                                                <span class="badge bg-{{ $status[1] }}">{{ $status[0] }}</span>
                                            </td>
                                        @endforeach
                                        
                                        <td>
                                            <span class="badge bg-{{ $progress['tone'] }}">{{ $progress['label'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                        @php
                            } else {
                                $questionCounter = 1;
                                foreach ($modules as $moduleIndex => $module) {
                                    $moduleQuestions = $module->questions ?? collect();
                                    if ($moduleQuestions->count() > 0) {
                                        // ✅ التغيير: Module بدلاً من Part
                                        echo '<tr class="module-divider">
                                                <td colspan="' . ($attempts->count() + 2) . '">
                                                    Module ' . ($moduleIndex + 1) . ' - Questions 1 to ' . $moduleQuestions->count() . '
                                                </td>
                                              </tr>';
                                        
                                        $moduleQuestionCounter = 1;
                                        foreach ($moduleQuestions as $qIndex => $q) {
                                            $answers = [];
                                            foreach ($attempts as $attempt) {
                                                $answers[] = $answerIndex[$attempt->id][$q->id] ?? null;
                                            }

                                            $getStatus = function($ans) {
                                                if (!$ans) return ['Not Attempted', 'secondary'];

                                                $correct = $ans->is_correct;
                                                if (is_null($correct) && isset($ans->score_earned)) {
                                                    $correct = ((float)$ans->score_earned > 0);
                                                }

                                                if ($correct === true)  return ['Correct', 'success'];
                                                if ($correct === false) return ['Incorrect', 'danger'];
                                                return ['Answered', 'primary'];
                                            };

                                            $statuses = [];
                                            foreach ($answers as $answer) {
                                                $statuses[] = $getStatus($answer);
                                            }

                                            $progress = ['label' => 'No Progress', 'tone' => 'secondary'];
                                            
                                            if ($attempts->count() >= 2) {
                                                $lastAttempt = $answers[count($answers) - 1] ?? null;
                                                $secondLastAttempt = $answers[count($answers) - 2] ?? null;
                                                
                                                $lastStatus = $getStatus($lastAttempt);
                                                $secondLastStatus = $getStatus($secondLastAttempt);
                                                
                                                if ($lastStatus[0] === 'Correct' && $secondLastStatus[0] !== 'Correct') {
                                                    $progress = ['label' => 'Improved', 'tone' => 'success'];
                                                }
                                                elseif ($lastStatus[0] !== 'Correct' && $secondLastStatus[0] === 'Correct') {
                                                    $progress = ['label' => 'Declined', 'tone' => 'danger'];
                                                }
                                                elseif ($lastStatus[0] === $secondLastStatus[0]) {
                                                    if ($lastStatus[0] === 'Correct') {
                                                        $progress = ['label' => 'Maintained', 'tone' => 'info'];
                                                    } else {
                                                        $progress = ['label' => 'No Change', 'tone' => 'secondary'];
                                                    }
                                                }
                                                else {
                                                    $progress = ['label' => 'Changed', 'tone' => 'warning'];
                                                }
                                            }
                                            
                                            echo '<tr>
                                                    <td>
                                                        <div class="question-number">' . $moduleQuestionCounter . '</div>
                                                    </td>';
                                            
                                            foreach($statuses as $status) {
                                                echo '<td>
                                                        <span class="badge bg-' . $status[1] . '">' . $status[0] . '</span>
                                                      </td>';
                                            }
                                            
                                            echo '<td>
                                                    <span class="badge bg-' . $progress['tone'] . '">' . $progress['label'] . '</span>
                                                  </td>
                                                </tr>';
                                            
                                            $moduleQuestionCounter++;
                                            $questionCounter++;
                                        }
                                    }
                                }
                            }
                        @endphp
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('dashboard.users.tests.results', $test->id) }}" class="btn-action btn-primary">
                <i class="fas fa-arrow-left"></i>
                Back to Results
            </a>
            
            <a href="{{ route('dashboard.users.tests.show', $test->id) }}" class="btn-action btn-secondary">
                <i class="fas fa-redo"></i>
                Retake Test
            </a>
            
            <button class="btn-action btn-comparison" onclick="window.print()">
                <i class="fas fa-print"></i>
                Print Comparison
            </button>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('tbody tr').each(function(index) {
                $(this).css('opacity', '0').delay(index * 50).animate({
                    opacity: 1
                }, 300);
            });

            setTimeout(function() {
                $('.debug-info').fadeOut();
            }, 5000);
        });
    </script>
@endsection