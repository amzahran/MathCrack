@extends('themes.default.layouts.back.master')

@section('title')
    {{ $assignment->title }} - {{ $assignment->lecture->name }}
@endsection

@section('css')
    <!-- إعداد MathJax -->
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
            background: #f9fafb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1a202c;
        }

        .assignment-preview {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        .assignment-header {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        .assignment-header h1 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .assignment-header p {
            color: #555;
            margin: 0;
        }

        .question-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .question-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .question-number {
            background: #4f46e5;
            color: white;
            font-weight: bold;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .question-type {
            font-size: 0.85rem;
            font-weight: 600;
            color: #4f46e5;
            text-transform: uppercase;
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
    <div class="assignment-preview">

        <div class="assignment-header">
            <h1>{{ $assignment->title }}</h1>
            <p>{{ $assignment->lecture->name }} - {{ $assignment->lecture->course->name ?? '' }}</p>
        </div>

        @forelse($assignment->questions as $index => $question)
            <div class="question-card">
                <div class="question-header">
                    <div class="question-number">{{ $index + 1 }}</div>
                    <div class="question-type">{{ strtoupper($question->type) }}</div>
                </div>

                <div class="question-text tex2jax_process">{!! $question->question_text !!}</div>

                @if ($question->question_image)
                    <img src="{{ asset($question->question_image) }}" class="question-image" alt="Question Image">
                @endif

                {{-- خيارات السؤال --}}
                @if ($question->type === 'mcq' && $question->options->count() > 0)
                    <div class="options-container">
                        @foreach ($question->options as $option)
                            <div class="option-item {{ $option->is_correct ? 'correct' : '' }}">
                                <div class="option-letter">{{ chr(65 + $loop->index) }}</div>
                                <div class="option-text tex2jax_process">{!! $option->option_text !!}</div>
                                @if ($option->option_image)
                                    <img src="{{ asset($option->option_image) }}" class="option-image" alt="Option Image">
                                @endif
                            </div>
                        @endforeach
                    </div>
                @elseif($question->type === 'numeric')
                    <div class="options-container">
                        <div class="option-item correct">
                            <div class="option-letter">#</div>
                            <div class="option-text">{!! $question->correct_answer !!}</div>
                        </div>
                    </div>
                @elseif($question->type === 'tf')
                    <div class="options-container">
                        <div class="option-item {{ $question->correct_answer == '1' ? 'correct' : '' }}">
                            <div class="option-letter">✓</div>
                            <div class="option-text">@lang('l.true')</div>
                        </div>
                        <div class="option-item {{ $question->correct_answer == '0' ? 'correct' : '' }}">
                            <div class="option-letter">✗</div>
                            <div class="option-text">@lang('l.false')</div>
                        </div>
                    </div>
                @endif

                {{-- الشرح --}}
                {{-- @if ($question->explanation)
                    <div class="explanation tex2jax_process">
                        <h6>@lang('l.Explanation')</h6>
                        <p>{!! $question->explanation !!}</p>
                        @if ($question->explanation_image)
                            <img src="{{ asset($question->explanation_image) }}" class="explanation-image"
                                alt="Explanation Image">
                        @endif
                    </div>
                @endif --}}
            </div>
        @empty
            <div class="alert alert-info text-center">@lang('l.no_questions_yet')</div>
        @endforelse
    </div>
@endsection