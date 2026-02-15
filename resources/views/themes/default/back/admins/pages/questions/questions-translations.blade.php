@extends('themes.default.layouts.back.master')

@section('title')
    {{ __('l.Translate Question') }}
@endsection

@section('css')
<style>
    .nav-tabs .nav-link {
        position: relative;
        transition: all 0.3s ease;
    }
    .nav-tabs .nav-link.active {
        border-bottom: 3px solid #696cff;
    }
    .nav-tabs .nav-link:hover:not(.active) {
        background-color: rgba(105, 108, 255, 0.05);
        border-color: transparent;
    }
    .back-btn {
        transition: all 0.3s ease;
    }
    .back-btn:hover {
        transform: translateX(-5px);
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
    .auto-translate-btn {
        transition: all 0.3s ease;
    }
    .auto-translate-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(33, 37, 41, 0.25);
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard.admins.questions') }}" class="btn btn-icon btn-outline-primary back-btn me-3">
                <i class="bx bx-arrow-back"></i>
            </a>
            <h4 class="fw-bold py-3 mb-0">
                <i class="bx bx-globe text-primary me-1"></i>
                {{ __('l.Translate Question') }}
            </h4>
        </div>

        <a href="{{ route('dashboard.admins.questions-auto-translate', ['id' => encrypt($question->id)]) }}" class="btn btn-dark auto-translate-btn">
            <i class="bx bx-bulb me-1"></i> {{ __('l.Auto Translate') }}
        </a>
    </div>

    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bx bx-info-circle me-1"></i>
        {{ __('l.Please note that automatic translation for large content is not efficient and may take some time, so we do not recommend using it for large content') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">{{ __('l.Translate Question and Answer') }}</h5>
        </div>

        <div class="card-body">
            <form id="translateForm" method="post" action="{{ route('dashboard.admins.questions-translate') }}">
                @csrf @method('PATCH')
                <input type="hidden" name="id" value="{{ encrypt($question->id) }}">

                <ul class="nav nav-tabs mb-4" role="tablist">
                    @foreach ($languages as $language)
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link d-flex align-items-center {{ $loop->first ? 'active' : '' }}"
                                role="tab" data-bs-toggle="tab" data-bs-target="#lang-{{ $language->code }}"
                                aria-controls="lang-{{ $language->code }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                <i class="fi fi-{{ strtolower($language->flag) }} me-2"></i>
                                {{ $language->native }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content">
                    @foreach ($languages as $language)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                            id="lang-{{ $language->code }}" role="tabpanel">

                            <div class="row">
                                <div class="col-12 mb-4">
                                    <label class="form-label d-flex align-items-center">
                                        <i class="bx bx-help-circle text-primary me-2"></i>
                                        {{ __('l.Question') }} ({{ $language->native }})
                                        <span class="text-danger ms-1">*</span>
                                    </label>
                                    <div class="textarea-wrapper">
                                        <textarea
                                            class="form-control translation-input"
                                            name="question-{{ $language->code }}"
                                            rows="3"
                                            required
                                            placeholder="{{ __('l.Enter question in') }} {{ $language->native }}"
                                            id="question-{{ $language->code }}"
                                            maxlength="500"
                                        >{{ $question->getTranslation('question', $language->code) }}</textarea>
                                        <div class="textarea-counter">
                                            <span id="question-count-{{ $language->code }}">0</span>/500
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label d-flex align-items-center">
                                        <i class="bx bx-message-rounded-detail text-primary me-2"></i>
                                        {{ __('l.Answer') }} ({{ $language->native }})
                                        <span class="text-danger ms-1">*</span>
                                    </label>
                                    <div class="textarea-wrapper">
                                        <textarea
                                            class="form-control translation-input"
                                            name="answer-{{ $language->code }}"
                                            rows="6"
                                            required
                                            placeholder="{{ __('l.Enter answer in') }} {{ $language->native }}"
                                            id="answer-{{ $language->code }}"
                                            maxlength="2000"
                                        >{{ $question->getTranslation('answer', $language->code) }}</textarea>
                                        <div class="textarea-counter">
                                            <span id="answer-count-{{ $language->code }}">0</span>/2000
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('dashboard.admins.questions') }}" class="btn btn-outline-secondary me-2">
                        <i class="bx bx-x me-1"></i> {{ __('l.Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> {{ __('l.Save Translations') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // تحديث عدادات النصوص
        function updateTextCounter(element) {
            const id = $(element).attr('id');
            const length = $(element).val().length;
            $(`#${id.replace(/(question|answer)/, '$1-count')}`).text(length);
        }

        // تحديث جميع العدادات عند تحميل الصفحة
        $('.translation-input').each(function() {
            updateTextCounter(this);
        });

        // تحديث العدادات عند الكتابة
        $('.translation-input').on('input', function() {
            updateTextCounter(this);
        });
    });
</script>
@endsection