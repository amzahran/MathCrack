@extends('themes.default.layouts.back.student-master')

@section('title')
    @lang('l.assignment_results') - {{ $studentAssignment->lectureAssignment->title }}
@endsection

@section('css')
    <!-- MathJax Configuration for Assignment Results -->
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [
                    ['$', '$'],
                    ['\\(', '\\)']
                ],
                displayMath: [
                    ['$$', '$$'],
                    ['\\[', '\\]']
                ],
                processEscapes: true
            },
            options: {
                skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code'],
                processHtmlClass: 'tex2jax_process'
            },
            chtml: {
                displayAlign: 'center'
            },
            startup: {
                ready: () => {
                    MathJax.startup.defaultReady();
                    MathJax.typesetPromise();
                    console.log("✅ MathJax ready and centered.");
                }
            }
        };
    </script>
    <script async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <style>
        /* MathJax Styling for Results Page */
        mjx-container {
            display: inline-block !important;
            margin: 2px 4px !important;
        }

        .question-text {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        mjx-container[display="true"] {
            display: block !important;
            text-align: center !important;
            margin: 1em auto !important;
        }

        .question-text mjx-container {
            font-size: 1.1em !important;
        }

        .option-text mjx-container {
            font-size: 1em !important;
        }

        /* Ensure visibility of math expressions */
        .question-text,
        .option-text,
        .correct-answer,
        .user-answer {
            line-height: 1.6 !important;
        }

        /* Prevent text from being cut off */
        .review-item {
            overflow: visible !important;
        }

        /* Improved math rendering */
        .question-text,
        .option-text,
        .correct-answer,
        .user-answer,
        .your-answer {
            min-height: 1.5em;
            overflow: visible !important;
            word-wrap: break-word;
        }

        /* Ensure math content is visible */
        .contains-math {
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Hide content until MathJax processes it */
        .question-text:has(mjx-container[jax="CHTML"][display="true"]),
        .option-text:has(mjx-container[jax="CHTML"][display="true"]) {
            min-height: 2em;
        }

        /* إصلاح مشكلة الفواصل الأسطر في النص الرياضي */
        .question-text {
            white-space: pre-line !important;
            line-height: 1.8 !important;
            text-align: justify !important;
        }

        /* تحسين عرض المعادلات الرياضية */
        .question-text mjx-container {
            display: inline !important;
            margin: 0 2px !important;
            vertical-align: middle !important;
        }

        /* إصلاح المسافات بين العناصر الرياضية */
        .question-text p {
            margin-bottom: 0.5em !important;
            margin-top: 0.5em !important;
        }

        /* تحسين عرض النص مع المعادلات */
        .question-text br {
            display: none !important;
        }

        .question-text br+br {
            display: block !important;
            margin-top: 0.5em !important;
        }

        /* إصلاح عرض المعادلات الطويلة */
        .question-text mjx-container[jax="CHTML"][display="true"] {
            display: block !important;
            text-align: center !important;
            margin: 1em auto !important;
        }

        /* تحسين عرض النص العربي والإنجليزي */
        .question-text {
            direction: ltr !important;
            text-align: left !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        }

        /* إصلاح مشكلة الفواصل الأسطر في النص الرياضي - تحسينات إضافية */
        .question-text {
            word-spacing: normal !important;
            letter-spacing: normal !important;
            text-indent: 0 !important;
        }

        /* إصلاح عرض المعادلات الرياضية المتعددة الأسطر */
        .question-text mjx-container[jax="CHTML"] {
            line-height: 1.2 !important;
            margin: 0.2em 0 !important;
        }

        /* إصلاح المسافات بين النص والمعادلات */
        .question-text mjx-container[jax="CHTML"]+br,
        .question-text br+mjx-container[jax="CHTML"] {
            display: none !important;
        }

        /* تحسين عرض النص مع المعادلات الرياضية */
        .question-text {
            overflow-wrap: break-word !important;
            hyphens: auto !important;
        }

        /* إصلاح عرض المعادلات الطويلة */
        .question-text mjx-container[jax="CHTML"][display="true"] {
            max-width: 100% !important;
            overflow-x: auto !important;
        }

        /* تحسين عرض النص الرياضي للشاشات الصغيرة */
        @media (max-width: 768px) {
            .question-text {
                font-size: 0.95em !important;
                line-height: 1.6 !important;
            }

            .question-text mjx-container[jax="CHTML"] {
                font-size: 0.9em !important;
            }
        }

        /* إصلاح مشكلة الفواصل الأسطر في النص الرياضي - تحسينات نهائية */
        .question-text {
            /* إزالة الفواصل الأسطر غير المناسبة */
            white-space: normal !important;
            /* تحسين المسافات بين السطور */
            line-height: 1.7 !important;
            /* تحسين المسافات بين الفقرات */
            margin-bottom: 1em !important;
        }

        /* إصلاح عرض المعادلات الرياضية */
        .question-text mjx-container {
            /* منع انكسار المعادلات */
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            /* تحسين المسافات */
            margin: 0.3em 0 !important;
        }

        /* إصلاح عرض النص مع المعادلات */
        .question-text {
            /* تحسين التفاف النص */
            text-align: left !important;
            /* منع انكسار الكلمات */
            word-break: keep-all !important;
            /* تحسين المسافات */
            padding: 0.5em 0 !important;
        }

        /* إصلاح عرض المعادلات الطويلة */
        .question-text mjx-container[jax="CHTML"][display="true"] {
            /* توسيط المعادلات الطويلة */
            text-align: center !important;
            /* إضافة مسافات مناسبة */
            margin: 1em auto !important;
            /* منع التمرير الأفقي */
            max-width: 100% !important;
            overflow-x: hidden !important;
        }

        /* إصلاح عرض النص الرياضي المختلط */
        .question-text {

            /* تحسين المسافات بين العناصر */
            >* {
                margin-bottom: 0.5em !important;
            }

            /* إزالة المسافة من آخر عنصر */
            >*:last-child {
                margin-bottom: 0 !important;
            }
        }

        /* إصلاح نهائي لمشكلة الفواصل الأسطر في النص الرياضي */
        .question-text {
            /* إزالة الفواصل الأسطر غير المناسبة */
            white-space: normal !important;
            /* تحسين المسافات بين السطور */
            line-height: 1.8 !important;
            /* تحسين المسافات بين الفقرات */
            margin-bottom: 1.5em !important;
            /* تحسين المسافات الداخلية */
            padding: 1em !important;
            /* تحسين التفاف النص */
            text-align: left !important;
            /* منع انكسار الكلمات */
            word-break: keep-all !important;
            /* تحسين التفاف النص */
            overflow-wrap: break-word !important;
        }

        /* إصلاح عرض المعادلات الرياضية - تحسينات نهائية */
        .question-text mjx-container {
            /* منع انكسار المعادلات */
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            /* تحسين المسافات */
            margin: 0.5em 0 !important;
            /* تحسين العرض */
            display: inline-block !important;
            /* منع التمرير */
            overflow: visible !important;
        }

        /* إصلاح عرض المعادلات الطويلة - تحسينات نهائية */
        .question-text mjx-container[jax="CHTML"][display="true"] {
            /* توسيط المعادلات الطويلة */
            text-align: center !important;
            /* إضافة مسافات مناسبة */
            margin: 1.5em auto !important;
            /* منع التمرير الأفقي */
            max-width: 100% !important;
            overflow-x: hidden !important;
            /* تحسين العرض */
            display: block !important;
        }

        /* إصلاح عرض النص مع المعادلات - تحسينات نهائية */
        .question-text {
            /* تحسين التفاف النص */
            text-align: left !important;
            /* منع انكسار الكلمات */
            word-break: keep-all !important;
            /* تحسين المسافات */
            padding: 1em !important;
            /* تحسين التفاف النص */
            overflow-wrap: break-word !important;
            /* تحسين المسافات بين السطور */
            line-height: 1.8 !important;
        }

        .results-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            color: white;
        }

        .results-content {
            position: relative;
            z-index: 2;
        }

        .score-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #e3f2fd;
            text-align: center;
        }

        .score-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: bold;
            color: white;
            position: relative;
        }

        .score-excellent {
            background: linear-gradient(135deg, #4caf50 0%, #8bc34a 100%);
        }

        .score-good {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }

        .score-average {
            background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%);
        }

        .score-poor {
            background: linear-gradient(135deg, #f44336 0%, #e91e63 100%);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-item {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e3f2fd;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
            display: block;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .question-review {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e3f2fd;
        }

        .question-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .question-number {
            background: #667eea;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .question-status {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-correct {
            background: #e8f5e8;
            color: #2e7d32;
        }

        .status-incorrect {
            background: #ffebee;
            color: #c62828;
        }

        .status-pending {
            background: #fff3e0;
            color: #ef6c00;
        }

        .status-no-answer {
            background: #f5f5f5;
            color: #757575;
        }

        .question-text {
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
        }

        .question-image {
            max-width: 100%;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .answer-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .answer-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .your-answer {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 0.5rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
        }

        .correct-answer {
            background: #e8f5e8;
            border-left: 4px solid #4caf50;
            padding: 0.5rem;
            border-radius: 5px;
        }

        .explanation {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
        }

        .btn-custom {
            border-radius: 25px;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-success-custom {
            background: linear-gradient(135deg, #4caf50 0%, #8bc34a 100%);
            border: none;
            color: white;
        }

        .btn-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
            color: white;
        }

        /* MathJax styling */
        .MathJax {
            font-size: 1.2em !important;
            margin: 0.5em 0 !important;
        }

        .question-text .MathJax,
        .explanation-text .MathJax {
            display: block !important;
            text-align: center !important;
            margin: 1em auto !important;
        }

        .option-text .MathJax {
            display: inline-block !important;
            margin: 0 0.2em !important;
        }

        /* تحسين عرض المعادلات للشاشات الصغيرة */
        @media (max-width: 576px) {
            .MathJax {
                font-size: 1em !important;
            }

            .question-text .MathJax,
            .explanation-text .MathJax {
                font-size: 0.9em !important;
            }
        }

        /* تحسين التباعد للمعادلات */
        .question-text,
        .option-text,
        .explanation-text {
            line-height: 1.6;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .question-text p,
        .explanation-text p {
            margin-bottom: 1em;
        }

        /* إصلاح مشاكل التخطيط */
        .answer-section {
            margin-top: 1rem;
        }

        .answer-label {
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .your-answer,
        .correct-answer {
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            word-wrap: break-word;
        }

        .your-answer {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
        }

        .correct-answer {
            background-color: #e8f5e8;
            border: 1px solid #c8e6c8;
        }

        /* تحسين عرض الخيارات */
        .options-list {
            margin-top: 0.5rem;
        }

        .option-item {
            display: flex;
            align-items: flex-start;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            background-color: #fafafa;
        }

        .option-item.correct-option {
            background-color: #e8f5e8;
            border-color: #4caf50;
        }

        .option-item.selected-option {
            background-color: #ffebee;
            border-color: #f44336;
        }

        .option-item.correct-option.selected-option {
            background-color: #e8f5e8;
            border-color: #4caf50;
        }

        .option-marker {
            margin-right: 0.75rem;
            margin-top: 0.2rem;
            flex-shrink: 0;
        }

        .option-text {
            flex: 1;
            word-wrap: break-word;
        }

        /* Explanation styling */
        .explanation-section {
            margin-top: 1rem;
        }

        .explanation-toggle {
            border-radius: 20px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .explanation-toggle:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }

        .explanation-content {
            margin-top: 1rem;
            animation: slideDown 0.3s ease;
        }

        .explanation-box {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border: 1px solid #bbdefb;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .explanation-header {
            color: #1976d2;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .explanation-text {
            color: #333;
            line-height: 1.6;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .results-header {
                padding: 1rem;
            }

            .score-circle {
                width: 120px;
                height: 120px;
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Image styling for responsiveness and fit */
        .question-image {
            max-width: 100%;
            height: auto;
            /* Maintain aspect ratio */
            max-height: 400px;
            /* Limit height to prevent overly large images */
            display: block;
            /* Remove extra space below image */
            margin: 10px auto;
            /* Center the image */
            object-fit: contain;
            /* Ensure the entire image is visible */
            border-radius: 8px;
            /* Slightly rounded corners */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Subtle shadow */
        }

        /* Style for images within question text or option text */
        .question-text img,
        .option-text img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
            vertical-align: middle;
            /* Align with text */
        }

        .option-image {
            max-width: 100px;
            max-height: 100px;
            margin-top: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <!-- Results Header -->
            <div class="results-header">
                <div class="results-content">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h1 class="mb-3">@lang('l.assignment_results')</h1>
                            <h3 class="mb-3">{{ $studentAssignment->lectureAssignment->title }}</h3>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark px-3 py-2">
                                    <i
                                        class="fas fa-book me-1"></i>{{ $studentAssignment->lectureAssignment->lecture->course->name }}
                                </span>
                                <span class="badge bg-light text-dark px-3 py-2">
                                    <i
                                        class="fas fa-video me-1"></i>{{ $studentAssignment->lectureAssignment->lecture->name }}
                                </span>
                                <span class="badge bg-light text-dark px-3 py-2">
                                    <i
                                        class="fas fa-calendar me-1"></i>{{ $studentAssignment->submitted_at->format('Y-m-d H:i') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-4 text-center">
                            <div
                                class="score-circle {{ $studentAssignment->percentage >= 90 ? 'score-excellent' : ($studentAssignment->percentage >= 80 ? 'score-good' : ($studentAssignment->percentage >= 60 ? 'score-average' : 'score-poor')) }}">
                                {{ round($studentAssignment->percentage) }}%
                            </div>
                            <h4 class="mb-0">@lang('l.final_score')</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">{{ round($studentAssignment->score) }}</span>
                    <span class="stat-label">@lang('l.points_earned')</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ round($studentAssignment->total_points) }}</span>
                    <span class="stat-label">@lang('l.total_points')</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $studentAssignment->lectureAssignment->questions->count() }}</span>
                    <span class="stat-label">@lang('l.total_questions')</span>
                </div>
                <div class="stat-item">
                    <span
                        class="stat-number">{{ $studentAssignment->answers->where('is_correct', true)->count() ?? 0 }}</span>
                    <span class="stat-label">@lang('l.correct_answers')</span>
                </div>
                @if ($studentAssignment->time_spent)
                    <div class="stat-item">
                        <span class="stat-number">{{ abs(round($studentAssignment->time_spent)) }}</span>
                        <span class="stat-label">@lang('l.minutes_spent')</span>
                    </div>
                @endif
                @if ($studentAssignment->lectureAssignment->time_limit)
                    <div class="stat-item">
                        <span class="stat-number">{{ $studentAssignment->lectureAssignment->time_limit }}</span>
                        <span class="stat-label">@lang('l.time_limit')</span>
                    </div>
                @endif
            </div>

            <!-- Questions Review -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-list-alt me-2"></i>@lang('l.questions_review')
                    </h4>
                </div>
                <div class="card-body">
                    @foreach ($studentAssignment->lectureAssignment->questions as $index => $question)
                        @php
                            $studentAnswer = $studentAssignment->answers
                                ->where('lecture_question_id', $question->id)
                                ->first();
                            $status = 'no-answer';
                            $statusText = __('l.no_answer');

                            if ($studentAnswer) {
                                if ($question->type === 'essay') {
                                    $status = 'pending';
                                    $statusText = __('l.pending_review');
                                } elseif ($studentAnswer->is_correct === null) {
                                    $status = 'pending';
                                    $statusText = __('l.pending_review');
                                } elseif ($studentAnswer->is_correct === true) {
                                    $status = 'correct';
                                    $statusText = __('l.correct');
                                } else {
                                    $status = 'incorrect';
                                    $statusText = __('l.incorrect');
                                }
                            }
                        @endphp

                        <div class="question-review">
                            <div class="question-header">
                                <div class="d-flex align-items-center">
                                    <div class="question-number me-3">{{ $index + 1 }}</div>
                                    <div>
                                        <h5 class="mb-1">{{ ucfirst($question->type) }} @lang('l.question')</h5>
                                        <small class="text-muted">@lang('l.points'): {{ $question->points }}</small>
                                    </div>
                                </div>
                                <span class="question-status status-{{ $status }}">{{ $statusText }}</span>
                            </div>

                            <div class="">
                                {{-- <div class="question-text"> --}}
                                {!! $question->question_text !!}
                            </div>

                            @if ($question->question_image)
                                <img src="{{ asset($question->question_image) }}" alt="Question Image"
                                    class="question-image">
                            @endif

                            <div class="answer-section">
                                <!-- عرض جميع الخيارات للأسئلة متعددة الخيارات -->
                                @if ($question->type === 'mcq' && $question->options->count() > 0)
                                    <div class="all-options mb-3">
                                        <div class="answer-label">@lang('l.all_options'):</div>
                                        <div class="options-list">
                                            @foreach ($question->options as $option)
                                                <div
                                                    class="option-item {{ $option->is_correct ? 'correct-option' : '' }} {{ $studentAnswer && $studentAnswer->selected_option_id == $option->id ? 'selected-option' : '' }}">
                                                    <span class="option-marker">
                                                        @if ($option->is_correct)
                                                            <i class="fas fa-check-circle text-success"></i>
                                                        @elseif ($studentAnswer && $studentAnswer->selected_option_id == $option->id)
                                                            <i class="fas fa-times-circle text-danger"></i>
                                                        @else
                                                            <i class="fas fa-circle text-muted"></i>
                                                        @endif
                                                    </span>
                                                    <span class="option-text">{!! $option->option_text !!}</span>
                                                    @if ($option->option_image)
                                                        <img src="{{ asset($option->option_image) }}" class="option-image"
                                                            alt="Option Image">
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="answer-label">@lang('l.your_answer'):</div>
                                <div class="your-answer">
                                    @if ($studentAnswer)
                                        @if ($question->type === 'mcq')
                                            {!! $studentAnswer->selectedOption ? $studentAnswer->selectedOption->option_text : __('l.no_answer') !!}
                                        @elseif ($question->type === 'tf')
                                            @php
                                                $tfAnswer = $studentAnswer->answer_text;
                                                // التحقق من أن الإجابة هي 'true'
                                                $isTrue = strtolower($tfAnswer) === 'true';
                                            @endphp
                                            {{ $isTrue ? __('l.true') : __('l.false') }}
                                        @elseif ($question->type === 'numeric')
                                            {{ $studentAnswer->answer_text ?: __('l.no_answer') }}
                                        @else
                                            {{ $studentAnswer->answer_text ?: __('l.no_answer') }}
                                        @endif
                                    @else
                                        @lang('l.no_answer')
                                    @endif
                                </div>

                                @if ($question->type !== 'essay')
                                    <div class="answer-label mt-2">@lang('l.correct_answer'):</div>
                                    <div class="correct-answer">
                                        @if ($question->type === 'mcq')
                                            @php
                                                $correctOption = $question->options->where('is_correct', true)->first();
                                            @endphp
                                            {!! $correctOption ? $correctOption->option_text : __('l.not_available') !!}
                                        @elseif ($question->type === 'tf')
                                            {{ $question->correct_answer === 'true' ? __('l.true') : __('l.false') }}
                                        @elseif ($question->type === 'numeric')
                                            {{ $question->correct_answer ?: __('l.not_available') }}
                                        @else
                                            {{ $question->correct_answer ?: __('l.not_available') }}
                                        @endif
                                    </div>
                                @endif

                                @if ($question->explanation)
                                    <div class="explanation-section">
                                        <button class="btn btn-outline-info btn-sm explanation-toggle"
                                            onclick="toggleExplanation({{ $index }})">
                                            <i class="fas fa-lightbulb me-1"></i>
                                            @lang('l.show_explanation')
                                        </button>
                                        <div class="explanation-content" id="explanation-{{ $index }}"
                                            style="display: none;">
                                            <div class="explanation-box">
                                                <div class="explanation-header">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    <strong>@lang('l.explanation')</strong>
                                                </div>
                                                <div class="explanation-text">
                                                    @if($question->explanation_image)
                                                        <img src="{{ asset($question->explanation_image) }}" alt="Explanation Image" class="img-fluid mb-3">
                                                    @endif
                                                    {!! $question->explanation !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($studentAnswer && $studentAnswer->teacher_feedback)
                                <div class="explanation">
                                    <strong>@lang('l.teacher_feedback'):</strong><br>
                                    {{ $studentAnswer->teacher_feedback }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <a href="{{ route('dashboard.users.courses-lectures-show', ['id' => encrypt($studentAssignment->lectureAssignment->lecture->id)]) }}"
                    class="btn btn-primary-custom btn-custom me-3 mb-3">
                    <i class="fas fa-arrow-left me-2"></i>@lang('l.back_to_lecture')
                </a>

                <a href="{{ route('dashboard.users.courses-lectures', ['id' => encrypt($studentAssignment->lectureAssignment->lecture->course->id)]) }}"
                    class="btn btn-success-custom btn-custom">
                    <i class="fas fa-book me-2"></i>@lang('l.back_to_course')
                </a>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        // تأثيرات الحركة عند التحميل
        $(document).ready(function() {
            // تأثير ظهور البطاقات
            $('.question-review, .stat-item').each(function(index) {
                $(this).css('opacity', '0').css('transform', 'translateY(20px)');
                $(this).delay(index * 100).animate({
                    opacity: 1
                }, 500).animate({
                    transform: 'translateY(0)'
                }, 500);
            });

            // تأثير ظهور دائرة النتيجة
            $('.score-circle').css('opacity', '0').css('transform', 'scale(0.5)');
            $('.score-circle').delay(500).animate({
                opacity: 1
            }, 1000).animate({
                transform: 'scale(1)'
            }, 1000, 'easeOutBack');
        });

        // دالة للتأكد من تحميل MathJax
        function ensureMathJaxLoaded() {
            if (window.MathJax && window.MathJax.typesetPromise) {
                return Promise.resolve();
            }

            return new Promise((resolve) => {
                const checkMathJax = setInterval(() => {
                    if (window.MathJax && window.MathJax.typesetPromise) {
                        clearInterval(checkMathJax);
                        resolve();
                    }
                }, 100);

                // timeout للتأكد من عدم الانتظار إلى ما لا نهاية
                setTimeout(() => {
                    clearInterval(checkMathJax);
                    resolve();
                }, 10000);
            });
        }

        // دالة لإعادة تحديث MathJax لعنصر معين
        function updateMathJaxForElement(element) {
            if (!element) return Promise.resolve();

            return ensureMathJaxLoaded().then(() => {
                // إزالة جميع عناصر MathJax الموجودة
                const mathElements = element.querySelectorAll(
                    '.MathJax, .MathJax_Display, .MathJax_Display > .MathJax, .mjx-container');
                mathElements.forEach(el => {
                    if (el.parentNode) {
                        el.parentNode.removeChild(el);
                    }
                });

                // إعادة معالجة النصوص الرياضية
                const textElements = element.querySelectorAll('.question-text, .option-text, .explanation-text');
                textElements.forEach(el => {
                    const content = el.innerHTML;
                    if (content.includes('$') || content.includes('\\(') || content.includes('\\[')) {
                        el.innerHTML = content;
                    }
                });

                // إعادة تحميل MathJax
                return MathJax.typesetPromise([element]);
            }).catch(err => {
                console.error('MathJax update error:', err);
                return Promise.resolve();
            });
        }

        // تفعيل زر الشرح
        function toggleExplanation(index) {
            const explanationContent = document.getElementById(`explanation-${index}`);
            const toggleButton = explanationContent.previousElementSibling;

            if (explanationContent.style.display === 'none') {
                explanationContent.style.display = 'block';
                toggleButton.innerHTML = '<i class="fas fa-eye-slash me-1"></i>@lang('l.hide_explanation')';
                toggleButton.classList.remove('btn-outline-info');
                toggleButton.classList.add('btn-info');

                // إعادة تحميل MathJax عند عرض الشرح
                updateMathJaxForElement(explanationContent).then(() => {
                    console.log('MathJax updated for explanation', index);
                });
            } else {
                explanationContent.style.display = 'none';
                toggleButton.innerHTML = '<i class="fas fa-lightbulb me-1"></i>@lang('l.show_explanation')';
                toggleButton.classList.remove('btn-info');
                toggleButton.classList.add('btn-outline-info');
            }
        }

        // تهيئة MathJax عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded - Initializing MathJax for results page');

            // تنظيف النص الرياضي وإزالة الفواصل الأسطر غير المناسبة
            function cleanMathText() {
                const questionTexts = document.querySelectorAll('.question-text');
                questionTexts.forEach(element => {
                    let content = element.innerHTML;

                    // إزالة الفواصل الأسطر المتكررة
                    content = content.replace(/(<br\s*\/?>\s*){2,}/g, '<br>');

                    // إزالة الفواصل الأسطر قبل وبعد المعادلات الرياضية
                    content = content.replace(/(<br\s*\/?>\s*)(\$\$.*?\$\$)(\s*<br\s*\/?>\s*)/g, '$2');
                    content = content.replace(/(<br\s*\/?>\s*)(\$.*?\$)(\s*<br\s*\/?>\s*)/g, ' $2 ');

                    // إزالة الفواصل الأسطر قبل وبعد النص الرياضي
                    content = content.replace(/(<br\s*\/?>\s*)(\\\(.*?\\\))(\s*<br\s*\/?>\s*)/g, ' $2 ');
                    content = content.replace(/(<br\s*\/?>\s*)(\\\[.*?\\\])(\s*<br\s*\/?>\s*)/g, '$2');

                    // تنظيف المسافات الزائدة
                    content = content.replace(/\s+/g, ' ').trim();

                    element.innerHTML = content;
                });
            }

            // دالة إضافية لتنظيف النص الرياضي بشكل أفضل
            function advancedMathTextCleaning() {
                const questionTexts = document.querySelectorAll('.question-text');
                questionTexts.forEach(element => {
                    let content = element.innerHTML;

                    // إزالة الفواصل الأسطر غير المناسبة بين النص والمعادلات
                    content = content.replace(/([a-zA-Z0-9])\s*<br\s*\/?>\s*(\$)/g, '$1 $2');
                    content = content.replace(/(\$)\s*<br\s*\/?>\s*([a-zA-Z0-9])/g, '$1 $2');

                    // إزالة الفواصل الأسطر بين المتغيرات والنص
                    content = content.replace(/([a-zA-Z])\s*<br\s*\/?>\s*([a-zA-Z])/g, '$1 $2');

                    // إزالة الفواصل الأسطر بين الأرقام والنص
                    content = content.replace(/([0-9])\s*<br\s*\/?>\s*([a-zA-Z])/g, '$1 $2');

                    // إزالة الفواصل الأسطر بين النص والرموز الرياضية
                    content = content.replace(/([a-zA-Z0-9])\s*<br\s*\/?>\s*([+\-*/=<>])/g, '$1 $2');
                    content = content.replace(/([+\-*/=<>])\s*<br\s*\/?>\s*([a-zA-Z0-9])/g, '$1 $2');

                    // تنظيف المسافات الزائدة
                    content = content.replace(/\s+/g, ' ').trim();

                    element.innerHTML = content;
                });
            }

            // إضافة class لتمييز النصوص الرياضية
            document.querySelectorAll(
                '.question-text, .option-text, .explanation-text, .correct-answer, .user-answer').forEach(
                element => {
                    const content = element.innerHTML;
                    if (content.includes('$') || content.includes('\\(') || content.includes('\\[')) {
                        element.classList.add('contains-math');
                    }
                });

            // تنظيف النص الرياضي أولاً
            cleanMathText();

            // تنظيف النص الرياضي بشكل متقدم
            advancedMathTextCleaning();

            // تحديث MathJax متعدد المراحل
            function initializeMathJax() {
                if (typeof MathJax !== 'undefined' && MathJax.typesetPromise) {
                    console.log('MathJax loaded, running initial typeset for all visible content');
                    return MathJax.typesetPromise().then(() => {
                        console.log('Initial MathJax typeset completed for results page');

                        // إعادة معالجة العناصر التي قد تحتاج معالجة إضافية
                        setTimeout(() => {
                            const mathElements = document.querySelectorAll('.contains-math');
                            console.log(
                                `Re-processing ${mathElements.length} elements with mathematical content`
                                );

                            mathElements.forEach((element, index) => {
                                setTimeout(() => {
                                    MathJax.typesetPromise([element]).catch(err => {
                                        console.error(
                                            'Error processing element:',
                                            err);
                                    });
                                }, index * 50);
                            });

                            // تنظيف إضافي للنص الرياضي بعد معالجة MathJax
                            setTimeout(() => {
                                advancedMathTextCleaning();
                                console.log(
                                    'Additional math text cleaning completed after MathJax processing'
                                    );
                            }, 1000);
                        }, 500);
                    });
                } else {
                    console.log('MathJax not ready, retrying in 500ms...');
                    setTimeout(initializeMathJax, 500);
                }
            }

            initializeMathJax();
        });

        // إضافة معالجة إضافية عند انتهاء تحميل النافذة
        window.addEventListener('load', function() {
            console.log('Window loaded - Final MathJax check');
            setTimeout(() => {
                if (typeof MathJax !== 'undefined' && MathJax.typesetPromise) {
                    MathJax.typesetPromise().then(() => {
                        console.log('Final MathJax processing completed');

                        // تنظيف نهائي للنص الرياضي
                        setTimeout(() => {
                            advancedMathTextCleaning();
                            console.log('Final math text cleaning completed');
                        }, 500);
                    });
                }
            }, 1000);
        });
    </script>
@endsection