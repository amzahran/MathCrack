@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.test_preview'): {{ $test->name }}
@endsection

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        body {
            background: #f8fafc;
        }

        .test-preview-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .test-header {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
        }

        .test-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 10px;
        }

        .test-info {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #4a5568;
            font-size: 0.95rem;
        }

        .info-item i {
            color: #667eea;
            width: 16px;
        }

        .part-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
        }

        .part-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .part-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }

        .part1-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .part2-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .part-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1a202c;
            margin: 0;
        }

        .part-info {
            color: #4a5568;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .question-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .question-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        .question-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .question-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .question-type-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .mcq-badge {
            background: #e6fffa;
            color: #234e52;
        }

        .tf-badge {
            background: #fef5e7;
            color: #744210;
        }

        .numeric-badge {
            background: #f0fff4;
            color: #22543d;
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

        .question-image,
        .option-image,
        .explanation-image {
            display: block;
            margin: 10px auto;
            max-width: 100%;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .options-container {
            margin-top: 10px;
        }

        .option-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 10px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            transition: background 0.2s;
        }

        .option-item:hover {
            background: #edf2f7;
        }

        .option-letter {
            background: #4f46e5;
            color: white;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .option-item.correct {
            border-color: #48bb78;
            background: #f0fff4;
        }

        .option-item.correct .option-letter {
            background: #38a169;
        }

        .explanation {
            background: #f9fafb;
            border-left: 4px solid #4f46e5;
            padding: 15px 20px;
            border-radius: 6px;
            margin-top: 20px;
        }

        .explanation h6 {
            margin: 0 0 10px;
            color: #2d3748;
            font-weight: 600;
        }

        .explanation p {
            margin: 0;
            color: #4a5568;
        }

        /* ---------------------------
               إعداد موحد لجميع الصور
               --------------------------- */

        /* الحاوية الموحدة لكل صورة (تطابق الكلاسات الموجودة في القالب) */
        .question-image,
        .option-image,
        .explanation-image,
        .question-text img,
        .option-text img,
        .explanation img {
            display: block;
            margin: 10px auto;
            border-radius: 8px;
            border: 1px solid #e6e6e6;
            /* كيفية التعامل مع اختلاف أبعاد الصور:
                 - object-fit: cover -> يقص الصورة لملء الإطار (ملائم للصور المتنوعة)
                 - object-fit: contain -> يجعل الصورة كاملة داخل الإطار دون قص (قد ينتج فراغات)
              */
            object-fit: contain;
            object-position: center center;

            /* يمنع الصورة من كسر الحاوية على شاشات صغيرة */
            max-width: 100%;
            /* حجم أقصى للعرض */
            max-height: 200px;
            /* حجم أقصى للارتفاع */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }

        /* خيار بديل: تباعد مختلف على الجوال لجعلها أصغر */
        @media (max-width: 600px) {

            .question-image,
            .option-image,
            .explanation-image,
            .question-text img,
            .option-text img,
            .explanation img {
                width: 140px;
                height: 100px;
            }
        }

        .question-text,
        .answers,
        .explanation {
            white-space: pre-line;
        }

        /* تقليل المسافات حول المعادلات */
        mjx-container[display="true"] {
            margin: 0.2em 0 !important;
            /* كان افتراضياً 1em */
        }

        /* لو تريد تقليلها أكثر (شبه ملتصقة بالنص) استخدم هذا */
        .question-text mjx-container[display="true"] {
            margin-top: 0 !important;
            margin-bottom: 0.3em !important;
        }
    </style>
@endsection

@section('content')
    <div class="test-preview-container">
        <!-- Admin Badge -->
        <div class="admin-badge">
            <i class="fas fa-eye me-1"></i>
            @lang('l.admin_preview')
        </div>

        <!-- Back Button -->
        <a href="{{ route('dashboard.admins.tests-questions', ['test_id' => encrypt($test->id)]) }}" class="back-button">
            <i class="fas fa-arrow-left me-2"></i>
            @lang('l.back_to_questions')
        </a>

        <!-- Test Header -->
        <div class="test-header">
            <h1 class="test-title">{{ $test->name }}</h1>
            @if($test->description)
                <p class="text-muted mb-3">{{ $test->description }}</p>
            @endif

            <div class="test-info">
                <div class="info-item">
                    <i class="fas fa-book"></i>
                    <span>@lang('l.course'): {{ $test->course->name }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <span>@lang('l.total_time'): {{ $test->total_time_minutes }} @lang('l.minutes')</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-star"></i>
                    <span>@lang('l.total_score'): {{ $test->total_score }} @lang('l.points')</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-question-circle"></i>
                    <span>@lang('l.total_questions'): {{ $test->total_questions_count }}</span>
                </div>
            </div>
        </div>

        <!-- Part 1 Questions -->
        @if($part1Questions->count() > 0)
            <div class="part-section">
                <div class="part-header">
                    <div class="part-icon part1-icon">1</div>
                    <div>
                        <h2 class="part-title">@lang('l.first_part')</h2>
                        <div class="part-info">
                            {{ $part1Questions->count() }} @lang('l.questions') •
                            {{ $test->part1_time_minutes }} @lang('l.minutes') •
                            {{ $part1Questions->sum('score') }} @lang('l.points')
                        </div>
                    </div>
                </div>

                @foreach($part1Questions as $question)
                    <div class="question-card">
                        <div class="question-header">
                            <div class="question-number">{{ $question->question_order }}</div>
                            <div>
                                <div class="question-type-badge {{ $question->type }}-badge">
                                    @switch($question->type)
                                        @case('mcq')
                                            @lang('l.mcq')
                                            @break
                                        @case('tf')
                                            @lang('l.tf')
                                            @break
                                        @case('numeric')
                                            @lang('l.numeric')
                                            @break
                                    @endswitch
                                </div>
                                <small class="text-muted">{{ $question->score }} @lang('l.points')</small>
                            </div>
                        </div>

                        <div class="question-text tex2jax_process">{!! $question->question_text !!}</div>

                        @if($question->question_image)
                            <img src="{{ asset($question->question_image) }}" alt="@lang('l.question_image')" class="question-image">
                        @endif

                        @if($question->type === 'mcq' && $question->options->count() > 0)
                            <div class="options-container">
                                @foreach($question->options as $option)
                                    <div class="option-item {{ $option->is_correct ? 'correct-answer' : '' }}">
                                        <div class="option-letter">{{ $option->option_letter }}</div>
                                        <div class="option-text tex2jax_process">{!! $option->option_text !!}</div>
                                        @if($option->option_image)
                                            <img src="{{ asset($option->option_image) }}" alt="@lang('l.option_image')" class="option-image">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @elseif($question->type === 'tf')
                            <div class="options-container">
                                <div class="option-item {{ $question->correct_answer == '1' ? 'correct-answer' : '' }}">
                                    <div class="option-letter">✓</div>
                                    <div class="option-text">@lang('l.true')</div>
                                </div>
                                <div class="option-item {{ $question->correct_answer == '0' ? 'correct-answer' : '' }}">
                                    <div class="option-letter">✗</div>
                                    <div class="option-text">@lang('l.false')</div>
                                </div>
                            </div>
                        @elseif($question->type === 'numeric')
                            <div class="options-container">
                                <div class="option-item correct-answer">
                                    <div class="option-letter">#</div>
                                    <div class="option-text">{{ $question->correct_answer }}</div>
                                </div>
                            </div>
                        @endif

                        {{-- الشرح مفعل الآن إذا وُجد --}}
                        {{-- @if($question->explanation)
                            <div class="explanation-section">
                                <div class="explanation-title">
                                    <i class="fas fa-lightbulb"></i>
                                    @lang('l.explanation')
                                </div>
                                <div class="explanation-text tex2jax_process">{!! $question->explanation !!}</div>
                                @if($question->explanation_image)
                                    <img src="{{ asset($question->explanation_image) }}" alt="@lang('l.explanation_image')" class="explanation-image">
                                @endif
                            </div>
                        @endif --}}

                    </div>
                @endforeach
            </div>
        @endif

        <!-- Part 2 Questions -->
        @if($part2Questions->count() > 0)
            <div class="part-section">
                <div class="part-header">
                    <div class="part-icon part2-icon">2</div>
                    <div>
                        <h2 class="part-title">@lang('l.second_part')</h2>
                        <div class="part-info">
                            {{ $part2Questions->count() }} @lang('l.questions') •
                            {{ $test->part2_time_minutes }} @lang('l.minutes') •
                            {{ $part2Questions->sum('score') }} @lang('l.points')
                        </div>
                    </div>
                </div>

                @foreach($part2Questions as $question)
                    <div class="question-card">
                        <div class="question-header">
                            <div class="question-number">{{ $question->question_order }}</div>
                            <div>
                                <div class="question-type-badge {{ $question->type }}-badge">
                                    @switch($question->type)
                                        @case('mcq')
                                            @lang('l.mcq')
                                            @break
                                        @case('tf')
                                            @lang('l.tf')
                                            @break
                                        @case('numeric')
                                            @lang('l.numeric')
                                            @break
                                    @endswitch
                                </div>
                                <small class="text-muted">{{ $question->score }} @lang('l.points')</small>
                            </div>
                        </div>

                        <div class="question-text tex2jax_process">{!! $question->question_text !!}</div>

                        @if($question->question_image)
                            <img src="{{ asset($question->question_image) }}" alt="@lang('l.question_image')" class="question-image">
                        @endif

                        @if($question->type === 'mcq' && $question->options->count() > 0)
                            <div class="options-container">
                                @foreach($question->options as $option)
                                    <div class="option-item {{ $option->is_correct ? 'correct-answer' : '' }}">
                                        <div class="option-letter">{{ $option->option_letter }}</div>
                                        <div class="option-text tex2jax_process">{!! $option->option_text !!}</div>
                                        @if($option->option_image)
                                            <img src="{{ asset($option->option_image) }}" alt="@lang('l.option_image')" class="option-image">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @elseif($question->type === 'tf')
                            <div class="options-container">
                                <div class="option-item {{ $question->correct_answer == '1' ? 'correct-answer' : '' }}">
                                    <div class="option-letter">✓</div>
                                    <div class="option-text">@lang('l.true')</div>
                                </div>
                                <div class="option-item {{ $question->correct_answer == '0' ? 'correct-answer' : '' }}">
                                    <div class="option-letter">✗</div>
                                    <div class="option-text">@lang('l.false')</div>
                                </div>
                            </div>
                        @elseif($question->type === 'numeric')
                            <div class="options-container">
                                <div class="option-item correct-answer">
                                    <div class="option-letter">#</div>
                                    <div class="option-text">{{ $question->correct_answer }}</div>
                                </div>
                            </div>
                        @endif

                        {{-- الشرح مفعل الآن إذا وُجد --}}
                        {{-- @if($question->explanation)
                            <div class="explanation-section">
                                <div class="explanation-title">
                                    <i class="fas fa-lightbulb"></i>
                                    @lang('l.explanation')
                                </div>
                                <div class="explanation-text tex2jax_process">{!! $question->explanation !!}</div>
                                @if($question->explanation_image)
                                    <img src="{{ asset($question->explanation_image) }}" alt="@lang('l.explanation_image')" class="explanation-image">
                                @endif
                            </div>
                        @endif --}}

                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@section('js')
@endsection