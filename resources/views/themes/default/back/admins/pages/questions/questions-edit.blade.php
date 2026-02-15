@extends('themes.default.layouts.back.master')

@section('title')
    {{ __('l.Edit Question') }}
@endsection

@section('css')
<style>
    .question-card {
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .question-card .card-header {
        background-color: #696cff;
        padding: 1.5rem;
        color: white;
    }
    .question-form {
        padding: 1.5rem;
    }
    .back-btn {
        transition: all 0.3s ease;
    }
    .back-btn:hover {
        transform: translateX(-5px);
    }
    .form-label {
        font-weight: 500;
    }
    .textarea-counter {
        position: absolute;
        bottom: 10px;
        right: 10px;
        font-size: 0.75rem;
        color: #697a8d;
        background: rgba(255,255,255,0.9);
        padding: 0 5px;
        border-radius: 4px;
    }
    .textarea-wrapper {
        position: relative;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <a href="{{ route('dashboard.admins.questions') }}" class="btn btn-icon btn-outline-primary back-btn me-3">
                    <i class="bx bx-arrow-back"></i>
                </a>
                <h4 class="fw-bold py-3 mb-0">
                    <i class="bx bx-edit-alt text-primary me-1"></i>
                    {{ __('l.Edit Question') }}
                </h4>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card question-card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('l.Question Information') }}</h5>
                </div>

                <form action="{{ route('dashboard.admins.questions-update') }}" method="POST" class="question-form">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="id" value="{{ encrypt($question->id) }}">

                    <div class="row">
                        <div class="col-12 mb-4">
                            <label class="form-label d-flex align-items-center">
                                <i class="bx bx-help-circle text-primary me-2 fs-5"></i>
                                {{ __('l.Question') }} <span class="text-danger ms-1">*</span>
                                <i class="fi fi-{{ strtolower($defaultLanguage->flag) }} fs-8 me-2 ms-2"></i>
                            </label>
                            <div class="textarea-wrapper">
                                <textarea
                                    class="form-control"
                                    name="question"
                                    rows="3"
                                    required
                                    placeholder="{{ __('l.Enter your question here') }}"
                                    id="questionField"
                                    maxlength="500"
                                    >{{ $question->getTranslation('question', $defaultLanguage->code) }}</textarea>
                                <div class="textarea-counter">
                                    <span id="questionCharCount">0</span>/500
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-4">
                            <label class="form-label d-flex align-items-center">
                                <i class="bx bx-message-rounded-detail text-primary me-2 fs-5"></i>
                                {{ __('l.Answer') }} <span class="text-danger ms-1">*</span>
                                <i class="fi fi-{{ strtolower($defaultLanguage->flag) }} fs-8 me-2 ms-2"></i>
                            </label>
                            <div class="textarea-wrapper">
                                <textarea
                                    class="form-control"
                                    name="answer"
                                    rows="6"
                                    required
                                    placeholder="{{ __('l.Enter the answer here') }}"
                                    id="answerField"
                                    maxlength="2000"
                                >{{ $question->getTranslation('answer', $defaultLanguage->code) }}</textarea>
                                <div class="textarea-counter">
                                    <span id="answerCharCount">0</span>/2000
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-between align-items-center mt-3">
                            <a href="{{ route('dashboard.admins.questions-get-translations', ['id' => encrypt($question->id)]) }}" class="btn btn-dark">
                                <i class="bx bx-globe me-1"></i> {{ __('l.Manage Translations') }}
                            </a>

                            <div>
                                <a href="{{ route('dashboard.admins.questions') }}" class="btn btn-outline-secondary me-2">
                                    <i class="bx bx-x me-1"></i> {{ __('l.Cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> {{ __('l.Save Changes') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // تحديث عداد الأحرف للسؤال
        function updateQuestionCount() {
            const length = $('#questionField').val().length;
            $('#questionCharCount').text(length);
        }

        // تحديث عداد الأحرف للإجابة
        function updateAnswerCount() {
            const length = $('#answerField').val().length;
            $('#answerCharCount').text(length);
        }

        // تحديث العدادين عند تحميل الصفحة
        updateQuestionCount();
        updateAnswerCount();

        // ربط الأحداث بالحقول
        $('#questionField').on('input', updateQuestionCount);
        $('#answerField').on('input', updateAnswerCount);
    });
</script>
@endsection
