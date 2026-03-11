@extends('themes.default.layouts.back.student-master')

@section('title')
    Performance Report - {{ $test->name }}
@endsection

@section('css')
<style>
    .report-page{
        max-width: 1450px;
        margin: 30px auto;
    }

    .report-header{
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: white;
        padding: 30px;
        border-radius: 18px;
        margin-bottom: 25px;
    }

    .report-header h2{
        margin: 0 0 10px;
        color: white;
    }

    .report-grid{
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 25px;
    }

    .report-card{
        background: white;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.08);
        border: 1px solid #e5e7eb;
    }

    .section-title{
        font-size: 1.1rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 16px;
    }

    .topic-list{
        display: grid;
        gap: 18px;
    }

    .topic-card{
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 20px;
        background: #f9fafb;
    }

    .topic-head{
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .topic-name{
        font-size: 1rem;
        font-weight: 700;
        color: #1f2937;
    }

    .topic-level{
        padding: 7px 14px;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 700;
    }

    .level-Weak{
        background: #fee2e2;
        color: #991b1b;
    }

    .level-Basic{
        background: #fef3c7;
        color: #92400e;
    }

    .level-Good{
        background: #dbeafe;
        color: #1d4ed8;
    }

    .level-Strong{
        background: #d1fae5;
        color: #065f46;
    }

    .topic-stats{
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
        margin-bottom: 15px;
    }

    .mini-box{
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px;
        text-align: center;
    }

    .mini-box .num{
        font-size: 1.2rem;
        font-weight: 700;
        color: #2563eb;
    }

    .mini-box .label{
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 4px;
    }

    .progress-wrap{
        margin-bottom: 15px;
    }

    .progress{
        height: 12px;
        background: #e5e7eb;
        border-radius: 999px;
        overflow: hidden;
    }

    .progress-bar{
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #2563eb, #10b981);
    }

    .difficulty-table{
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
    }

    .difficulty-table th,
    .difficulty-table td{
        border: 1px solid #e5e7eb;
        padding: 10px;
        font-size: 0.88rem;
        text-align: center;
    }

    .difficulty-table th{
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 700;
    }

    .recommendation-list{
        display: grid;
        gap: 12px;
    }

    .recommendation-item{
        padding: 14px 16px;
        border-radius: 12px;
        background: #f8fafc;
        border-left: 4px solid #2563eb;
        color: #1f2937;
        font-weight: 500;
    }

    .summary-box{
        margin-top: 18px;
        display: grid;
        gap: 10px;
    }

    .summary-item{
        padding: 14px 16px;
        border-radius: 12px;
        background: #eef6ff;
        border: 1px solid #bfdbfe;
        color: #1e3a8a;
        font-weight: 600;
    }

    .topic-recommendation{
        margin-top: 14px;
        padding: 12px 14px;
        background: #ffffff;
        border: 1px solid #dbeafe;
        border-radius: 12px;
        color: #374151;
        font-weight: 500;
    }

    .report-actions{
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 25px;
    }

    .btn-report{
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        border: none;
    }

    .btn-report-primary{
        background: #2563eb;
        color: white;
    }

    .btn-report-secondary{
        background: white;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    canvas{
        max-height: 380px;
    }

    @media (max-width: 992px){
        .report-grid{
            grid-template-columns: 1fr;
        }

        .topic-stats{
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endsection

@section('content')
<div class="report-page">
    <div class="report-header">
        <h2>Performance Report</h2>
        <div>{{ $test->name }}</div>
        <div>Attempt {{ $studentTest->attempt_number }}</div>
        @if($previousAttempt)
            <div>Compared with attempt {{ $previousAttempt->attempt_number }}</div>
        @endif
    </div>

    <div class="report-grid">
        <div class="report-card">
            <div class="section-title">Topic Performance Radar</div>
            <canvas id="topicRadarChart"></canvas>
        </div>

        <div class="report-card">
            <div class="section-title">Topic Comparison Bar Chart</div>
            <canvas id="topicBarChart"></canvas>
        </div>
    </div>

    <div class="report-grid">
        <div class="report-card">
            <div class="section-title">Recommendations</div>
            <div class="recommendation-list">
                @forelse($recommendations as $recommendation)
                    <div class="recommendation-item">
                        {{ $recommendation }}
                    </div>
                @empty
                    <div class="recommendation-item">
                        Your performance is stable. Continue practicing consistently.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="report-card">
            <div class="section-title">Final Summary</div>
            <div class="summary-box">
                @forelse($finalSummary as $line)
                    <div class="summary-item">{{ $line }}</div>
                @empty
                    <div class="summary-item">Your performance is balanced across topics.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="report-card">
        <div class="section-title">Detailed Topic Analysis</div>

        <div class="topic-list">
            @foreach($topicReport as $item)
                <div class="topic-card">
                    <div class="topic-head">
                        <div class="topic-name">{{ $item['topic'] }}</div>
                        <div class="topic-level level-{{ $item['level'] }}">
                            {{ $item['level'] }}
                        </div>
                    </div>

                    <div class="topic-stats">
                        <div class="mini-box">
                            <div class="num">{{ $item['total'] }}</div>
                            <div class="label">Total</div>
                        </div>

                        <div class="mini-box">
                            <div class="num">{{ $item['answered'] }}</div>
                            <div class="label">Answered</div>
                        </div>

                        <div class="mini-box">
                            <div class="num">{{ $item['correct'] }}</div>
                            <div class="label">Correct</div>
                        </div>

                        <div class="mini-box">
                            <div class="num">{{ $item['wrong'] }}</div>
                            <div class="label">Wrong</div>
                        </div>

                        <div class="mini-box">
                            <div class="num">
                                @if(!is_null($item['previous_percentage']))
                                    {{ $item['previous_percentage'] }}%
                                @else
                                    -
                                @endif
                            </div>
                            <div class="label">Previous</div>
                        </div>
                    </div>

                    <div class="progress-wrap">
                        <div style="margin-bottom:8px; font-weight:600;">
                            Current Performance: {{ $item['percentage'] }}%
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $item['percentage'] }}%"></div>
                        </div>
                    </div>

                    <table class="difficulty-table">
                        <thead>
                            <tr>
                                <th>Difficulty</th>
                                <th>Total</th>
                                <th>Correct</th>
                                <th>Wrong</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item['difficulty_breakdown'] as $difficulty)
                                <tr>
                                    <td>{{ $difficulty['difficulty'] }}</td>
                                    <td>{{ $difficulty['total'] }}</td>
                                    <td>{{ $difficulty['correct'] }}</td>
                                    <td>{{ $difficulty['wrong'] }}</td>
                                    <td>{{ $difficulty['percentage'] }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="topic-recommendation">
                        {{ $item['study_recommendation'] }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="report-actions">
            <a href="{{ route('dashboard.users.tests.results', [
                'id' => $test->id,
                'attempt_id' => $studentTest->id
            ]) }}" class="btn-report btn-report-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Results
            </a>

            <button onclick="window.print()" class="btn-report btn-report-primary">
                <i class="fas fa-print"></i>
                Print Report
            </button>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const topicLabels = @json($chartLabels);
    const topicValues = @json($chartValues);
    const previousTopicValues = @json($previousChartValues);

    const radarCtx = document.getElementById('topicRadarChart').getContext('2d');
    new Chart(radarCtx, {
        type: 'radar',
        data: {
            labels: topicLabels,
            datasets: [{
                label: 'Current Performance %',
                data: topicValues,
                fill: true,
                backgroundColor: 'rgba(37, 99, 235, 0.2)',
                borderColor: 'rgba(37, 99, 235, 1)',
                pointBackgroundColor: 'rgba(37, 99, 235, 1)',
                pointBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    suggestedMin: 0,
                    suggestedMax: 100,
                    ticks: {
                        stepSize: 20
                    }
                }
            }
        }
    });

    const barCtx = document.getElementById('topicBarChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: topicLabels,
            datasets: [
                {
                    label: 'Current Attempt',
                    data: topicValues,
                    backgroundColor: 'rgba(37, 99, 235, 0.75)'
                },
                {
                    label: 'Previous Attempt',
                    data: previousTopicValues,
                    backgroundColor: 'rgba(16, 185, 129, 0.65)'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
</script>
@endsection