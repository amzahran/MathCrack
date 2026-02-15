@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Questions') - {{ $assignment->title }}
@endsection

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/theme/default.min.css">
<style>
    .quiz-container {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .question-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        border-left: 4px solid #007bff;
    }
    .question-card.new-question {
        border-left: 4px solid #28a745;
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
    }
    .question-card.new-question .question-header {
        background: linear-gradient(45deg, #f8fff9 0%, #ffffff 100%);
        border-radius: 6px;
        margin: -10px -10px 15px -10px;
        padding: 15px;
    }
    .question-card.saved-question {
        border-left: 4px solid #28a745;
        animation: savedPulse 1s ease-in-out;
    }
    @keyframes savedPulse {
        0% { transform: scale(1); box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        50% { transform: scale(1.02); box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3); }
        100% { transform: scale(1); box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    }
    .question-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 15px;
    }
    .question-number {
        background: #007bff;
        color: white;
        padding: 5px 12px;
        border-radius: 50px;
        font-weight: bold;
        margin-right: 15px;
    }
    .question-type-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
    }
    .mcq-badge { background: #e3f2fd; color: #1976d2; }
    .tf-badge { background: #f3e5f5; color: #7b1fa2; }
    .essay-badge { background: #e8f5e8; color: #388e3c; }
    .numeric-badge { background: #fff3e0; color: #f57c00; }

    .question-content {
        margin-bottom: 20px;
    }
    .question-text-editor {
        min-height: 120px;
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        padding: 15px;
        font-size: 14px;
        line-height: 1.5;
    }
    .question-text-editor:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }

    .options-container {
        margin-top: 15px;
    }
    .option-item {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }
    .option-item:hover {
        border-color: #007bff;
        box-shadow: 0 2px 8px rgba(0,123,255,0.15);
    }
    .option-item.correct-answer {
        border-color: #28a745;
        background: #f8fff9;
    }
    .option-header {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        background: #fff;
        border-radius: 6px 6px 0 0;
        border-bottom: 1px solid #e9ecef;
    }
    .option-letter {
        background: #6c757d;
        color: white;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 10px;
    }
    .option-letter.correct {
        background: #28a745;
    }
    .option-content {
        padding: 15px;
    }
    .option-text-editor {
        min-height: 60px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 10px;
        width: 100%;
        resize: vertical;
    }

    .add-question-btn {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        padding: 12px 25px;
        border-radius: 25px;
        color: white;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(0,123,255,0.3);
        transition: all 0.3s ease;
    }
    .add-question-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,123,255,0.4);
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .btn-icon {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    .btn-edit { background: #17a2b8; color: white; }
    .btn-delete { background: #dc3545; color: white; }
    .btn-save { background: #28a745; color: white; }
    .btn-cancel { background: #6c757d; color: white; }

    .numeric-answer-input {
        background: #fff3cd;
        border: 2px solid #ffc107;
        border-radius: 6px;
        padding: 10px 15px;
        font-size: 16px;
        text-align: center;
        font-weight: bold;
    }

    .essay-answer-area {
        background: #f8f9fa;
        border: 2px solid #6c757d;
        border-radius: 6px;
        padding: 15px;
        min-height: 100px;
        font-style: italic;
        color: #6c757d;
    }

    .math-support-note {
        background: #e7f3ff;
        border: 1px solid #b3d9ff;
        border-radius: 5px;
        padding: 10px;
        margin-top: 10px;
        font-size: 12px;
        color: #0066cc;
    }

    .math-preview {
        background: #f8f9fa;
        border: 1px dashed #dee2e6;
        border-radius: 5px;
        min-height: 30px;
        font-size: 14px;
        line-height: 1.4;
    }

    .math-preview:empty::before {
        content: "@lang('l.math_preview')";
        color: #6c757d;
        font-style: italic;
    }

    .question-stats {
        display: flex;
        gap: 15px;
        align-items: center;
        margin-top: 10px;
        font-size: 13px;
        color: #6c757d;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .loading-spinner {
        background: white;
        padding: 30px;
        border-radius: 10px;
        text-align: center;
    }

    /* زر الإضافة العائم */
    .floating-add-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        border-radius: 50%;
        color: white;
        font-size: 24px;
        box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4);
        transition: all 0.3s ease;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .floating-add-btn:hover {
        transform: translateY(-3px) scale(1.1);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.6);
        background: linear-gradient(135deg, #20c997, #28a745);
    }

    .floating-add-btn:active {
        transform: translateY(-1px) scale(1.05);
    }

    .floating-add-btn i {
        transition: transform 0.3s ease;
    }

    .floating-add-btn:hover i {
        transform: rotate(90deg);
    }

    /* تأثير النبض للزر العائم */
    @keyframes pulse {
        0% {
            box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4);
        }
        50% {
            box-shadow: 0 4px 20px rgba(40, 167, 69, 0.8);
        }
        100% {
            box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4);
        }
    }

    .floating-add-btn.pulse {
        animation: pulse 2s infinite;
    }

    /* إخفاء الزر العائم في الشاشات الصغيرة */
    @media (max-width: 768px) {
        .floating-add-btn {
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
    }
</style>
@endsection

@section('content')
    <div class="main-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @can('show lectures')
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">@lang('l.Questions') - <span class="text-primary">{{ $assignment->title }}</span></h4>
                    <p class="text-muted mb-0">{{ $assignment->lecture->name }} - {{ $assignment->lecture->course->name ?? '' }}</p>
                </div>
                <div>
                    <button type="button" class="btn add-question-btn d-none" onclick="addNewQuestion()">
                        <i class="fas fa-plus me-2"></i>
                        @lang('l.Add Question')
                    </button>
                    <a href="{{ route('dashboard.admins.lectures-assignments-preview', ['id' => encrypt($assignment->id)]) }}" class="btn btn-info waves-effect waves-light me-2" target="_blank">
                        <i class="fas fa-eye me-2"></i>
                        @lang('l.Preview Assignment')
                    </a>
                    <a href="{{ route('dashboard.admins.lectures-assignments') }}?id={{ encrypt($assignment->lecture->id) }}" class="btn btn-secondary waves-effect waves-light">
                        <i class="fas fa-arrow-left me-2"></i>
                        @lang('l.Back to Assignments')
                    </a>
                </div>
            </div>

            <!-- Quiz Statistics -->
            @if($assignment->questions->count() > 0)
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title">@lang('l.quiz_statistics')</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-primary">{{ $assignment->questions->count() }}</h4>
                                <small class="text-muted">@lang('l.total_questions')</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success">{{ $assignment->questions->sum('points') }}</h4>
                                <small class="text-muted">@lang('l.total_points')</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h4 class="text-info">{{ $assignment->questions->where('type', 'mcq')->count() }}</h4>
                                <small class="text-muted">@lang('l.multiple_choice')</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h4 class="text-warning">{{ $assignment->questions->where('type', 'tf')->count() }}</h4>
                                <small class="text-muted">@lang('l.true_false')</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h4 class="text-warning">{{ $assignment->questions->where('type', 'numeric')->count() }}</h4>
                                <small class="text-muted">@lang('l.numeric')</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quiz Container -->
            <div class="quiz-container">
                <div id="questionsContainer">
                    @forelse($assignment->questions()->orderBy('order')->get() as $index => $question)
                        <div class="question-card" data-question-id="{{ $question->id }}">
                            @include('themes.default.back.admins.lectures.partials.question-view', ['question' => $question, 'index' => $index])
                        </div>
                    @empty
                        <div class="text-center py-5" id="emptyState">
                            <i class="fas fa-question-circle fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">@lang('l.no_questions_yet')</h5>
                            <p class="text-muted">@lang('l.start_adding_first_question')</p>
                            <button type="button" class="btn btn-primary" onclick="addNewQuestion()">
                                <i class="fas fa-plus me-2"></i>
                                @lang('l.add_first_question')
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        @endcan
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">@lang('l.loading')...</span>
            </div>
            <p class="mt-2 mb-0">@lang('l.saving')...</p>
        </div>
    </div>

    <!-- Floating Add Question Button -->
    @can('add lectures')
    <button type="button" class="floating-add-btn" onclick="addNewQuestion()" title="@lang('l.add_new_question')" id="floatingAddBtn">
        <i class="fas fa-plus"></i>
    </button>
    @endcan

@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/stex/stex.min.js"></script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<script>
window.MathJax = {
    tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']],
        displayMath: [['$$', '$$'], ['\\[', '\\]']]
    },
    svg: {
        fontCache: 'global'
    }
};

let questionCounter = {{ $assignment->questions->count() }};
let currentEditingQuestion = null;

// تهيئة الصفحة
$(document).ready(function() {
    @if($assignment->questions->count() === 0)
        $('#emptyState').show();
    @else
        $('#emptyState').hide();
    @endif

    // تهيئة MathJax للمحتوى الموجود
    if (typeof MathJax !== 'undefined') {
        MathJax.typesetPromise().then(() => {
            console.log('MathJax initialized successfully');
            // تطبيق MathJax على الحقول الموجودة
            $('.question-text-editor, .option-text-editor').each(function() {
                if ($(this).val().trim()) {
                    renderMath(this);
                }
            });
        }).catch((err) => {
            console.error('MathJax initialization error:', err);
        });
    } else {
        console.warn('MathJax not loaded');
    }
});

// إضافة سؤال جديد
function addNewQuestion() {
    questionCounter++;
    const questionHtml = createNewQuestionHtml(questionCounter);

    $('#emptyState').hide();
    $('#floatingAddBtn').removeClass('pulse');
    $('#questionsContainer').append(questionHtml);

    // تطبيق MathJax على المحتوى الجديد
    if (typeof MathJax !== 'undefined') {
        MathJax.typesetPromise();
    }

    // التمرير إلى السؤال الجديد
    $(`#question-${questionCounter}`)[0].scrollIntoView({ behavior: 'smooth' });

    // التركيز على حقل النص
    $(`#question-${questionCounter} .question-text-editor`).focus();

    // إضافة تأثير بصري لطيف للسؤال الجديد
    $(`#question-${questionCounter}`).hide().fadeIn(500);

    // تحديث الإحصائيات
    updateStatistics();
}

// إنشاء HTML للسؤال الجديد
function createNewQuestionHtml(questionId) {
    return `
        <div class="question-card new-question" data-question-id="new-${questionId}" id="question-${questionId}">
            <div class="question-header">
                <div class="d-flex align-items-center">
                    <span class="question-number">${questionId}</span>
                    <select class="form-select question-type-select" onchange="handleQuestionTypeChange('new-${questionId}', this.value)">
                        <option value="mcq">@lang('l.Multiple Choice')</option>
                        <option value="tf">@lang('l.true_false')</option>
                        <option value="essay">@lang('l.Essay')</option>
                        <option value="numeric">@lang('l.Numeric')</option>
                    </select>
                    <span class="question-type-badge mcq-badge ms-2">@lang('l.Multiple Choice')</span>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-icon btn-delete" onclick="deleteQuestion('new-${questionId}')" title="@lang('l.delete')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <div class="question-content">
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">@lang('l.question_text'):</label>
                        <textarea class="form-control question-text-editor"
                                placeholder="@lang('l.question_text_placeholder')"
                                onblur="renderMath(this)"></textarea>

                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">@lang('l.question_image_optional'):</label>
                        <input type="file" class="form-control question-image" accept="image/*">
                        <label class="form-label fw-bold">@lang('l.explanation_image_optional'):</label>
                        <input type="file" class="form-control explanation-image" accept="image/*">
                        <div class="mt-2">
                            <label class="form-label fw-bold">@lang('l.points'):</label>
                            <input type="number" class="form-control question-points" min="1" value="1">
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">@lang('l.answer_explanation_optional'):</label>
                        <textarea class="form-control question-explanation" rows="2"
                                placeholder="@lang('l.answer_explanation_placeholder')" onblur="renderMath(this)"></textarea>
                    </div>
                </div>
            </div>

            <div class="options-container" id="options-${questionId}">
                ${createMCQOptions(questionId)}
            </div>
            <div class="d-flex justify-content-end mt-3">
                <button class="btn btn-success" onclick="saveQuestion('new-${questionId}')">@lang('l.save')</button>
            </div>
        </div>
    `;
}

// إنشاء خيارات الاختيار من متعدد
function createMCQOptions(questionId) {
    return `
        <label class="form-label fw-bold">@lang('l.options'):</label>
        <div class="mcq-options">
            ${createOptionHtml(questionId, 0, 'A')}
            ${createOptionHtml(questionId, 1, 'B')}
            ${createOptionHtml(questionId, 2, 'C')}
            ${createOptionHtml(questionId, 3, 'D')}
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addMCQOption(${questionId})">
            <i class="fas fa-plus me-1"></i> @lang('l.add_option')
        </button>
    `;
}

// إنشاء HTML للخيار
function createOptionHtml(questionId, optionIndex, letter) {
    return `
        <div class="option-item" data-option-index="${optionIndex}">
            <div class="option-header">
                <span class="option-letter">${letter}</span>
                                    <input type="radio" name="correct-new-${questionId}" value="${optionIndex}" class="form-check-input ms-2">
                <span class="ms-2 small text-muted">@lang('l.correct_answer')</span>
                <div class="ms-auto">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMCQOption(this)" ${optionIndex < 2 ? 'style="display:none"' : ''}>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="option-content">
                <textarea class="form-control option-text-editor"
                        placeholder="@lang('l.option_text_placeholder')"
                        onblur="renderMath(this)"></textarea>
                <input type="file" class="form-control mt-2" accept="image/*" placeholder="@lang('l.option_image_optional')">
            </div>
        </div>
    `;
}

// التحكم في تغيير نوع السؤال
function handleQuestionTypeChange(questionId, type) {
    console.log('handleQuestionTypeChange called with:', questionId, type);

    // معالجة questionId للحصول على الرقم الفعلي
    let actualQuestionId = questionId;
    if (questionId.startsWith('new-')) {
        actualQuestionId = questionId.replace('new-', '');
    }

    // البحث عن السؤال باستخدام data-question-id
    const questionCard = $(`[data-question-id="${questionId}"]`);
    console.log('Found question card:', questionCard);

    if (questionCard.length === 0) {
        console.error('Question card not found for ID:', questionId);
        return;
    }

    const container = questionCard.find('.options-container');
    const badge = questionCard.find('.question-type-badge');

    // تحديث الشارة
    badge.removeClass('mcq-badge tf-badge essay-badge numeric-badge');

    switch(type) {
        case 'mcq':
            badge.addClass('mcq-badge').text('@lang('l.Multiple Choice')');
            container.html(createMCQOptions(actualQuestionId));
            break;
        case 'tf':
            badge.addClass('tf-badge').text('@lang('l.true_false')');
            container.html(createTrueFalseOptions(actualQuestionId));
            break;
        case 'essay':
            badge.addClass('essay-badge').text('@lang('l.Essay')');
            container.html(createEssayAnswer(actualQuestionId));
            break;
        case 'numeric':
            badge.addClass('numeric-badge').text('@lang('l.Numeric')');
            container.html(createNumericAnswer(actualQuestionId));
            break;
    }

    // إعادة تطبيق MathJax على العناصر الجديدة
    if (typeof MathJax !== 'undefined') {
        setTimeout(() => {
            MathJax.typesetPromise().then(() => {
                console.log('MathJax applied to new content');
                // تطبيق renderMath على العناصر الجديدة
                container.find('.question-text-editor, .option-text-editor').each(function() {
                    if ($(this).val().trim()) {
                        renderMath(this);
                    }
                });
            });
        }, 100);
    }
}

// إنشاء خيارات صواب/خطأ
function createTrueFalseOptions(questionId) {
    return `
        <label class="form-label fw-bold">@lang('l.options'):</label>
        <div class="tf-options">
            <div class="option-item" data-option-index="0">
                <div class="option-header">
                    <span class="option-letter">✓</span>
                    <input type="radio" name="correct-new-${questionId}" value="0" class="form-check-input ms-2">
                    <span class="ms-2 fw-bold text-success">@lang('l.true')</span>
                </div>
            </div>
            <div class="option-item" data-option-index="1">
                <div class="option-header">
                    <span class="option-letter">✗</span>
                    <input type="radio" name="correct-new-${questionId}" value="1" class="form-check-input ms-2">
                    <span class="ms-2 fw-bold text-danger">@lang('l.false')</span>
                </div>
            </div>
        </div>
    `;
}

// إنشاء إجابة مقالية
function createEssayAnswer(questionId) {
    return `
        <label class="form-label fw-bold">@lang('l.model_answer'):</label>
        <div class="essay-answer-area">
            <textarea class="form-control" rows="4"
                    placeholder="@lang('l.model_answer_placeholder')"></textarea>
        </div>
    `;
}

// إنشاء إجابة رقمية
function createNumericAnswer(questionId) {
    return `
        <label class="form-label fw-bold">@lang('l.correct_numeric_answer'):</label>
        <input type="number" class="form-control numeric-answer-input"
               placeholder="@lang('l.enter_correct_number')" step="any">
        <small class="text-muted mt-1">@lang('l.decimal_numbers_allowed')</small>
    `;
}

// إضافة خيار جديد للاختيار من متعدد
function addMCQOption(questionId) {
    const container = $(`#options-${questionId} .mcq-options`);
    const optionCount = container.children('.option-item').length;
    const letter = String.fromCharCode(65 + optionCount); // A, B, C, D, E, F...

    container.append(createOptionHtml(questionId, optionCount, letter));

    // إظهار أزرار الحذف إذا كان هناك أكثر من خيارين
    if (optionCount >= 2) {
        $(`#options-${questionId} .btn-outline-danger`).show();
    }
}

// حذف خيار من الاختيار من متعدد
function removeMCQOption(button) {
    const optionItem = $(button).closest('.option-item');
    const container = optionItem.closest('.mcq-options');

    optionItem.remove();

    // إعادة ترقيم الخيارات
    container.children('.option-item').each(function(index) {
        const letter = String.fromCharCode(65 + index);
        $(this).find('.option-letter').text(letter);
        $(this).attr('data-option-index', index);
    });

    // إخفاء أزرار الحذف إذا كان هناك خيارين فقط
    if (container.children('.option-item').length <= 2) {
        container.find('.btn-outline-danger').hide();
    }
}

// حفظ السؤال
function saveQuestion(questionId) {
    console.log('Saving question:', questionId);

    // البحث عن السؤال باستخدام data-question-id
    const questionCard = $(`[data-question-id="${questionId}"]`);
    console.log('Found question card:', questionCard);

    if (questionCard.length === 0) {
        console.error('Question card not found:', questionId);
        showErrorMessage('لم يتم العثور على السؤال');
        return;
    }

    const questionData = extractQuestionData(questionCard);
    console.log('Question data:', questionData);

    if (!validateQuestionData(questionData)) {
        console.log('Question data validation failed');
        return;
    }

    console.log('Question data validation passed');

    showLoading(true);

    // إنشاء FormData للملفات
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('lecture_assignment_id', '{{ $assignment->id }}');

    // إضافة بيانات السؤال
    Object.keys(questionData).forEach(key => {
        if (key === 'options') {
            questionData[key].forEach((option, index) => {
                formData.append(`options[${index}][option_text]`, option.text);
                formData.append(`options[${index}][is_correct]`, option.isCorrect ? 1 : 0);
                if (option.image) {
                    formData.append(`options[${index}][option_image]`, option.image);
                }
            });
        } else if (key === 'question_image' && questionData[key]) {
            formData.append(key, questionData[key]);
        } else {
            formData.append(key, questionData[key]);
        }
    });

    const questionDataId = questionCard.attr('data-question-id');
    const isNewQuestion = !questionDataId || questionDataId.toString().startsWith('new-');
    const url = isNewQuestion ?
        '{{ route("dashboard.admins.lectures-questions-store") }}' :
        '{{ route("dashboard.admins.lectures-questions-update") }}';

    console.log('Request URL:', url);
    console.log('Is new question:', isNewQuestion);
    console.log('Question data-question-id:', questionDataId);
    console.log('Question ID being sent:', questionId);
    console.log('Question data extracted:', questionData);

    if (!isNewQuestion) {
        // للأسئلة المحفوظة، questionDataId هو الرقم مباشرة
        formData.append('id', questionDataId);
        formData.append('_method', 'PATCH');
        console.log('Added to FormData - ID:', questionDataId);
    } else {
        console.log('New question - not adding ID to FormData');
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Save response:', response);
            showLoading(false);

            if (isNewQuestion && response.question_id) {
                // تحديث ID السؤال الجديد
                questionCard.attr('data-question-id', response.question_id);

                // تحديث معالجات الأحداث للأزرار
                questionCard.find('.btn-save').attr('onclick', `saveQuestion('${response.question_id}')`);
                questionCard.find('.btn-delete').attr('onclick', `deleteQuestion('${response.question_id}')`);
                questionCard.find('.question-type-select').attr('onchange', `handleQuestionTypeChange('${response.question_id}', this.value)`);

                // إزالة كلاس السؤال الجديد وإضافة تأثير بصري للنجاح
                questionCard.removeClass('new-question').addClass('saved-question');
                setTimeout(() => {
                    questionCard.removeClass('saved-question');
                }, 2000);

                console.log('Updated question card with new ID:', response.question_id);
            }

            showSuccessMessage('@lang('l.question_saved_successfully')');

            // إعادة تطبيق MathJax
            if (typeof MathJax !== 'undefined') {
                MathJax.typesetPromise();
            }
        },
        error: function(xhr) {
            console.error('Save error:', xhr);
            console.error('Status:', xhr.status);
            console.error('Status Text:', xhr.statusText);
            console.error('Response Text:', xhr.responseText);
            if (xhr.responseJSON) {
                console.error('Response JSON:', xhr.responseJSON);
            }
            showLoading(false);

            if (xhr.status === 422) {
                const errors = xhr.responseJSON?.errors || {};
                console.error('Validation errors:', errors);
                showValidationErrors(errors);
            } else {
                showErrorMessage('@lang('l.error_saving_question'): ' + (xhr.responseJSON?.message || xhr.responseText || '@lang('l.unknown_error')'));
            }
        }
    });
}

// استخراج بيانات السؤال
function extractQuestionData(questionCard) {
    try {
        console.log('Extracting data from question card:', questionCard);
        const questionType = questionCard.find('.question-type-select').val();
        const questionText = questionCard.find('.question-text-editor').val();
        const questionImage = questionCard.find('.question-image')[0]?.files[0];
        const explanationImage = questionCard.find('.explanation-image')[0]?.files[0];
        const points = questionCard.find('.question-points').val();
        const explanation = questionCard.find('.question-explanation').val();

        console.log('Extracted basic data:', {
            questionType, questionText, points, explanation
        });
        console.log('Question Type Check - Type:', questionType, 'Valid types:', ['mcq', 'tf', 'essay', 'numeric']);

        const data = {
            question_text: questionText || '',
            type: questionType || 'mcq',
            points: parseInt(points) || 1,
            explanation: explanation || ''
        };

        if (questionImage) {
            data.question_image = questionImage;
        }

        if (explanationImage) {
            data.explanation_image = explanationImage;
        }

        // استخراج بيانات الخيارات حسب نوع السؤال
        switch(questionType) {
            case 'mcq':
                data.options = extractMCQOptions(questionCard);
                console.log('MCQ Options extracted:', data.options);
                break;
            case 'tf':
                data.correct_answer = extractTFAnswer(questionCard);
                console.log('TF Answer extracted:', data.correct_answer);
                break;
            case 'essay':
                data.correct_answer = questionCard.find('.essay-answer-area textarea').val() || '';
                console.log('Essay Answer extracted:', data.correct_answer);
                break;
            case 'numeric':
                data.correct_answer = questionCard.find('.numeric-answer-input').val() || '';
                console.log('Numeric Answer extracted:', data.correct_answer);
                break;
        }

        console.log('Final extracted data:', data);
        return data;
    } catch (error) {
        console.error('Error extracting question data:', error);
        return null;
    }
}

// استخراج خيارات الاختيار من متعدد
function extractMCQOptions(questionCard) {
    const options = [];
    let questionId = questionCard.attr('id') ? questionCard.attr('id').replace('question-', '') : questionCard.attr('data-question-id');

    // معالجة حالة الأسئلة الجديدة
    if (questionId && questionId.toString().startsWith('new-')) {
        questionId = questionId.replace('new-', '');
    }

    const isNewQuestion = questionCard.attr('data-question-id') && questionCard.attr('data-question-id').toString().startsWith('new-');

    // تحديد اسم حقل الراديو المناسب
    const radioName = isNewQuestion ? `correct-new-${questionId}` : `correct-${questionId}`;

    console.log('MCQ Options - Question ID:', questionId, 'Is New:', isNewQuestion, 'Radio Name:', radioName);

    questionCard.find('.mcq-options .option-item').each(function(index) {
        const optionText = $(this).find('.option-text-editor').val();
        const isCorrect = $(this).find(`input[name="${radioName}"]`).is(':checked');
        const optionImage = $(this).find('input[type="file"]')[0].files[0];

        const option = {
            text: optionText,
            isCorrect: isCorrect
        };

        if (optionImage) {
            option.image = optionImage;
        }

        console.log(`Option ${index + 1}:`, option);
        options.push(option);
    });

    return options;
}

// استخراج إجابة صواب/خطأ
function extractTFAnswer(questionCard) {
    let questionId = questionCard.attr('id') ? questionCard.attr('id').replace('question-', '') : questionCard.attr('data-question-id');

    // معالجة حالة الأسئلة الجديدة
    if (questionId && questionId.toString().startsWith('new-')) {
        questionId = questionId.replace('new-', '');
    }

    const isNewQuestion = questionCard.attr('data-question-id') && questionCard.attr('data-question-id').toString().startsWith('new-');

    // تحديد اسم حقل الراديو المناسب
    const radioName = isNewQuestion ? `correct-new-${questionId}` : `correct-${questionId}`;
    const selectedOption = questionCard.find(`input[name="${radioName}"]:checked`).val();

    console.log('TF Answer - Question ID:', questionId, 'Is New:', isNewQuestion, 'Radio Name:', radioName, 'Selected:', selectedOption);

    return selectedOption === '0' ? 'true' : 'false';
}

// التحقق من صحة بيانات السؤال
function validateQuestionData(data) {
    if (!data) {
        showErrorMessage('@lang('l.error_extracting_question_data')');
        return false;
    }

    if (!data.question_text.trim()) {
        showErrorMessage('@lang('l.question_text_required')');
        return false;
    }

    if (data.type === 'mcq') {
        if (!data.options || data.options.length === 0) {
            showErrorMessage('@lang('l.options_required')');
            return false;
        }

        const hasCorrectAnswer = data.options.some(option => option.isCorrect);
        if (!hasCorrectAnswer) {
            showErrorMessage('@lang('l.correct_answer_required')');
            return false;
        }

        const hasValidOptions = data.options.some(option => option.text && option.text.trim());
        if (!hasValidOptions) {
            showErrorMessage('@lang('l.option_text_required')');
            return false;
        }
    }

    if (data.type === 'tf') {
        if (!data.correct_answer) {
            showErrorMessage('@lang('l.true_false_answer_required')');
            return false;
        }
    }

    if (data.type === 'numeric') {
        if (!data.correct_answer || data.correct_answer.trim() === '') {
            showErrorMessage('@lang('l.numeric_answer_required')');
            return false;
        }
        if (isNaN(data.correct_answer)) {
            showErrorMessage('@lang('l.numeric_answer_invalid')');
            return false;
        }
    }

    if (data.type === 'essay') {
        if (!data.correct_answer || data.correct_answer.trim() === '') {
            showErrorMessage('@lang('l.essay_answer_required')');
            return false;
        }
    }

    return true;
}

// حذف السؤال
function deleteQuestion(questionId) {
    console.log('Deleting question:', questionId);

    if (!confirm('@lang('l.confirm_delete_question')')) {
        return;
    }

    // البحث عن السؤال باستخدام data-question-id
    const questionCard = $(`[data-question-id="${questionId}"]`);
    console.log('Found question card for deletion:', questionCard);

    if (questionCard.length === 0) {
        console.error('Question card not found:', questionId);
        showErrorMessage('@lang('l.question_not_found')');
        return;
    }

    const questionDataId = questionCard.attr('data-question-id');
    const isNewQuestion = !questionDataId || questionDataId.toString().startsWith('new-');

    if (isNewQuestion) {
        // حذف مباشر للأسئلة الجديدة غير المحفوظة
        questionCard.fadeOut(300, function() {
            $(this).remove();
            checkEmptyState();
        });
        return;
    }

    showLoading(true);

    $.ajax({
        url: '{{ route("dashboard.admins.lectures-questions-delete") }}',
        method: 'GET',
        data: {
            id: isNewQuestion ? questionId : questionDataId // إرسال الـ ID الصحيح
        },
        success: function(response) {
            console.log('Delete response:', response);
            showLoading(false);
            questionCard.fadeOut(300, function() {
                $(this).remove();
                checkEmptyState();
            });
            showSuccessMessage('@lang('l.question_deleted_successfully')');
        },
        error: function(xhr) {
            console.error('Delete error:', xhr);
            showLoading(false);
            showErrorMessage('@lang('l.error_deleting_question'): ' + (xhr.responseJSON?.message || '@lang('l.unknown_error')'));
        }
    });
}

// التحقق من حالة عدم وجود أسئلة
function checkEmptyState() {
    const questionCount = $('#questionsContainer .question-card').length;
    console.log('Checking empty state, question count:', questionCount);

    if (questionCount === 0) {
        $('#emptyState').show();
        $('#floatingAddBtn').addClass('pulse');
    } else {
        $('#emptyState').hide();
        $('#floatingAddBtn').removeClass('pulse');
    }

    // تحديث الإحصائيات
    updateStatistics();
}

// تحديث إحصائيات الاختبار
function updateStatistics() {
    const questions = $('#questionsContainer .question-card');
    const totalQuestions = questions.length;

    if (totalQuestions === 0) {
        $('.card-body h6:contains("إحصائيات الاختبار")').closest('.card').hide();
        return;
    }

    let totalPoints = 0;
    let mcqCount = 0;
    let tfCount = 0;
    let essayCount = 0;
    let numericCount = 0;

    questions.each(function() {
        const points = parseInt($(this).find('.question-points').val()) || 1;
        totalPoints += points;

        const type = $(this).find('.question-type-select').val();
        switch(type) {
            case 'mcq': mcqCount++; break;
            case 'tf': tfCount++; break;
            case 'essay': essayCount++; break;
            case 'numeric': numericCount++; break;
        }
    });

    // تحديث الأرقام في الإحصائيات
    $('.card-body h6:contains("إحصائيات الاختبار")').closest('.card').show();
    $('.card-body .text-primary').text(totalQuestions);
    $('.card-body .text-success').text(totalPoints);
    $('.card-body .text-info').text(mcqCount);
    $('.card-body .text-warning').text(tfCount);
}

// عرض رسالة النجاح
function showSuccessMessage(message) {
    const alert = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('.main-content').prepend(alert);
    setTimeout(() => {
        $('.alert-success').fadeOut();
    }, 5000);
}

// عرض رسالة الخطأ
function showErrorMessage(message) {
    const alert = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('.main-content').prepend(alert);
    setTimeout(() => {
        $('.alert-danger').fadeOut();
    }, 7000);
}

// عرض أخطاء التحقق
function showValidationErrors(errors) {
    let errorMessage = 'يرجى تصحيح الأخطاء التالية:<ul>';
    Object.values(errors).forEach(error => {
        if (Array.isArray(error)) {
            error.forEach(err => {
                errorMessage += `<li>${err}</li>`;
            });
        } else {
            errorMessage += `<li>${error}</li>`;
        }
    });
    errorMessage += '</ul>';
    showErrorMessage(errorMessage);
}

// إظهار/إخفاء شاشة التحميل
function showLoading(show) {
    if (show) {
        $('#loadingOverlay').css('display', 'flex');
    } else {
        $('#loadingOverlay').hide();
    }
}

// تشفير البيانات (محسن للعمل مع Laravel)
function encrypt(value) {
    // استخدام base64 مؤقتاً للاختبار
    return btoa(value.toString());
}

// تطبيق MathJax على العنصر
function renderMath(element) {
    if (typeof MathJax !== 'undefined' && MathJax.typesetPromise) {
        const $element = $(element);
        const text = $element.val() || '';

        console.log('Rendering math for text:', text.substring(0, 100));

        // التحقق من وجود معادلات رياضية
        if (text.includes('$') || text.includes('\\(') || text.includes('\\[') || text.includes('\\{') || text.includes('\\frac') || text.includes('\\sqrt')) {
            // إنشاء div مؤقت لعرض المعادلة
            let previewDiv = $element.siblings('.math-preview');
            if (previewDiv.length === 0) {
                $element.after('<div class="math-preview mt-2 p-2 border rounded bg-light" style="min-height: 30px;"></div>');
                previewDiv = $element.siblings('.math-preview');
            }

            // عرض النص مع المعادلات
            previewDiv.html('<div class="text-muted small mb-1">@lang('l.math_preview'):</div>' + text).show();

            // إزالة عناصر MathJax القديمة
            previewDiv.find('.MathJax, .mjx-container').remove();

            // تطبيق MathJax على المعاينة
            setTimeout(() => {
                MathJax.typesetPromise([previewDiv[0]]).then(() => {
                    console.log('Math rendered successfully in preview');
                    previewDiv.find('.text-muted').first().html('<i class="fas fa-check-circle text-success me-1"></i>@lang('l.math_preview'):');
                }).catch((err) => {
                    console.log('MathJax Error:', err);
                    previewDiv.html('<div class="text-danger small"><i class="fas fa-exclamation-triangle me-1"></i>@lang('l.math_error'): ' + err.message + '</div>');
                });
            }, 100);
        } else {
            // إخفاء معاينة المعادلة إذا لم تكن هناك معادلات
            $element.siblings('.math-preview').hide();
        }
    } else {
        console.warn('MathJax not available for rendering');
    }
}

// تهيئة أحداث إضافية
$(document).ready(function() {
    // تطبيق MathJax على التحديث التلقائي للحقول
    $(document).on('input', '.question-text-editor, .option-text-editor', function() {
        const element = this;
        clearTimeout(element.mathTimeout);
        element.mathTimeout = setTimeout(() => {
            renderMath(element);
        }, 500); // تقليل الوقت للاستجابة السريعة
    });

    // تطبيق MathJax فور فقدان التركيز
    $(document).on('blur', '.question-text-editor, .option-text-editor', function() {
        renderMath(this);
    });

    // تحديث إشارة الخيار الصحيح
    $(document).on('change', 'input[type="radio"]', function() {
        const questionCard = $(this).closest('.question-card');
        const radioName = $(this).attr('name');

        // إعادة تعيين جميع الخيارات لنفس السؤال (نفس اسم الراديو)
        questionCard.find(`input[name="${radioName}"]`).each(function() {
            $(this).closest('.option-item').find('.option-letter').removeClass('correct');
            $(this).closest('.option-item').removeClass('correct-answer');
        });

        // تعيين الخيار المحدد كصحيح
        $(this).closest('.option-item').find('.option-letter').addClass('correct');
        $(this).closest('.option-item').addClass('correct-answer');
    });

    // تحديث الإحصائيات عند تغيير نوع السؤال أو النقاط
    $(document).on('change', '.question-type-select, .question-points', function() {
        updateStatistics();
    });

    // Auto-save للأسئلة (كل 30 ثانية)
    setInterval(function() {
        $('.question-card').each(function() {
            const questionId = $(this).attr('data-question-id');
            if (!questionId.startsWith('new-')) {
                const questionData = extractQuestionData($(this));
                if (validateQuestionData(questionData)) {
                    // حفظ تلقائي صامت
                    saveQuestionSilent(questionId, questionData);
                }
            }
        });
    }, 30000);

    // تطبيق التنسيق على النصوص الرياضية الموجودة
    if (typeof MathJax !== 'undefined') {
        MathJax.typesetPromise().then(() => {
            console.log('MathJax initialized successfully');
        });
    }

    // تفعيل تأثير النبض للزر العائم عند عدم وجود أسئلة
    if ($('#questionsContainer .question-card').length === 0) {
        $('#floatingAddBtn').addClass('pulse');
    }

    // إخفاء تأثير النبض عند إضافة أول سؤال
    $(document).on('click', '#floatingAddBtn', function() {
        $(this).removeClass('pulse');
    });

    // إضافة تأثير لطيف عند التمرير
    let lastScrollTop = 0;
    $(window).scroll(function() {
        const st = $(this).scrollTop();
        const floatingBtn = $('#floatingAddBtn');

        if (st > lastScrollTop && st > 100) {
            // التمرير للأسفل - إخفاء الزر قليلاً
            floatingBtn.css({
                'transform': 'translateY(10px) scale(0.9)',
                'opacity': '0.7'
            });
        } else {
            // التمرير للأعلى أو في الأعلى - إظهار الزر
            floatingBtn.css({
                'transform': 'translateY(0) scale(1)',
                'opacity': '1'
            });
        }
        lastScrollTop = st;
    });
});

// حفظ صامت للسؤال
function saveQuestionSilent(questionId, questionData) {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('id', questionId); // إرسال الـ ID مباشرة بدون تشفير
    formData.append('_method', 'PATCH');

    // إضافة بيانات السؤال
    Object.keys(questionData).forEach(key => {
        if (key === 'options') {
            questionData[key].forEach((option, index) => {
                formData.append(`options[${index}][option_text]`, option.text);
                formData.append(`options[${index}][is_correct]`, option.isCorrect ? 1 : 0);
                if (option.image) {
                    formData.append(`options[${index}][option_image]`, option.image);
                }
            });
        } else if (key === 'question_image' && questionData[key]) {
            formData.append(key, questionData[key]);
        } else {
            formData.append(key, questionData[key]);
        }
    });

    $.ajax({
        url: '{{ route("dashboard.admins.lectures-questions-update") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            // إضافة مؤشر للحفظ التلقائي
            $(`[data-question-id="${questionId}"] .question-header`).append('<small class="text-success ms-2 auto-saved"><i class="fas fa-check"></i> محفوظ</small>');
            setTimeout(() => {
                $('.auto-saved').fadeOut();
            }, 2000);
        },
        error: function(xhr) {
            // فشل في الحفظ التلقائي - لا نعرض رسالة خطأ
            console.log('Auto-save failed for question:', questionId);
        }
    });
}

// إضافة دالة لتحديد نوع السؤال التلقائي بناءً على المحتوى
function detectQuestionType(questionText) {
    const text = questionText.toLowerCase();

    if (text.includes('صواب') && text.includes('خطأ')) {
        return 'tf';
    }
    if (text.includes('احسب') || text.includes('كم') || text.includes('عدد') || /\d+/.test(text)) {
        return 'numeric';
    }
    if (text.includes('اشرح') || text.includes('وضح') || text.includes('اكتب') || text.includes('ناقش')) {
        return 'essay';
    }

    return 'mcq'; // افتراضي
}

// إضافة دالة للتحقق من صحة المعادلات الرياضية
function validateMathExpression(text) {
    // تحقق بسيط من صحة معادلات LaTeX
    const openBraces = (text.match(/\{/g) || []).length;
    const closeBraces = (text.match(/\}/g) || []).length;
    const openDollar = (text.match(/\$/g) || []).length;

    if (openBraces !== closeBraces) {
        return false;
    }
    if (openDollar % 2 !== 0) {
        return false;
    }

    return true;
}

// إضافة دالة لنسخ السؤال
function duplicateQuestion(questionId) {
    const originalCard = $(`[data-question-id="${questionId}"]`);
    const questionData = extractQuestionData(originalCard);

    questionCounter++;
    const newQuestionHtml = createNewQuestionHtml(questionCounter);
    originalCard.after(newQuestionHtml);

    // ملء البيانات المنسوخة
    const newCard = $(`#question-${questionCounter}`);
    newCard.find('.question-text-editor').val(questionData.question_text + ' (نسخة)');
    newCard.find('.question-points').val(questionData.points);
    newCard.find('.question-explanation').val(questionData.explanation);
    newCard.find('.question-type-select').val(questionData.type).trigger('change');

    // تطبيق MathJax
    if (typeof MathJax !== 'undefined') {
        MathJax.typesetPromise();
    }

    showSuccessMessage('تم نسخ السؤال بنجاح');
}

// تحسينات الواجهة - إضافة tooltips
$(document).ready(function() {
    // إضافة tooltips للأزرار
    $('[title]').tooltip();

    // إضافة تأثيرات بصرية للحفظ
    $(document).on('click', '.btn-save', function() {
        const button = $(this);
        const originalContent = button.html();

        button.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        setTimeout(() => {
            button.html(originalContent).prop('disabled', false);
        }, 2000);
    });

    // تحميل MathJax إذا لم يكن متوفراً
    if (typeof MathJax === 'undefined') {
        console.log('Loading MathJax...');
        const script = document.createElement('script');
        script.src = 'https://polyfill.io/v3/polyfill.min.js?features=es6';
        document.head.appendChild(script);

        script.onload = function() {
            const mathJaxScript = document.createElement('script');
            mathJaxScript.id = 'MathJax-script';
            mathJaxScript.src = 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js';
            mathJaxScript.async = true;
            document.head.appendChild(mathJaxScript);

            window.MathJax = {
                tex: {
                    inlineMath: [['$', '$'], ['\\(', '\\)']],
                    displayMath: [['$$', '$$'], ['\\[', '\\]']]
                },
                options: {
                    menuOptions: {
                        settings: {
                            assistiveMml: true
                        }
                    }
                }
            };
        };
    }
});
</script>

<style>
.math-preview {
    min-height: 40px;
    font-size: 16px;
    line-height: 1.6;
}

.math-preview:empty {
    display: none;
}
</style>
@endsection