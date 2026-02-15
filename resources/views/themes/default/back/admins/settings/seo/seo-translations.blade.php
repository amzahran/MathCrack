@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Translate SEO Page')
@endsection

@section('css')

@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('dashboard.admins.seo') }}" class="btn btn-icon btn-outline-primary back-btn me-3">
                    <i class="bx bx-arrow-back"></i>
                </a>
                <h4 class="fw-bold py-3 mb-0">
                    <i class="bx bx-globe text-primary me-1"></i>
                    @lang('l.Translate SEO Page'): {{ $seoPage->getTranslation('title', $defaultLanguage) }}
                </h4>
            </div>

            <a href="{{ route('dashboard.admins.seo-auto-translate', ['id' => encrypt($seoPage->id)]) }}"
                class="btn btn-dark auto-translate-btn">
                <i class="bx bx-bulb me-1"></i> @lang('l.Auto Translate')
            </a>
        </div>

        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bx bx-info-circle me-1"></i>
            @lang('l.Please note that automatic translation for large content is not efficient and may take some time, so we do not recommend using it for large content')
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li><i class="bx bx-x-circle me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">@lang('l.Translate SEO Content')</h5>
            </div>

            <div class="card-body">
                <form id="translateForm" method="post" action="{{ route('dashboard.admins.seo-translate') }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="id" value="{{ encrypt($seoPage->id) }}">

                    <ul class="nav nav-tabs mb-4" role="tablist">
                        @foreach ($languages as $language)
                            <li class="nav-item" role="presentation">
                                <button type="button"
                                    class="nav-link d-flex align-items-center {{ $loop->first ? 'active' : '' }}" role="tab"
                                    data-bs-toggle="tab" data-bs-target="#lang-{{ $language->code }}"
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
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="lang-{{ $language->code }}"
                                role="tabpanel">

                                <div class="row">
                                    <div class="col-12 mb-4">
                                        <label class="form-label d-flex align-items-center">
                                            <i class="bx bx-text text-primary me-2"></i>
                                            @lang('l.Meta Title') ({{ $language->native }})
                                            <span class="text-danger ms-1">*</span>
                                        </label>
                                        <div class="textarea-wrapper">
                                            <input type="text" class="form-control translation-input"
                                                name="title-{{ $language->code }}" required
                                                placeholder="@lang('l.Enter title in') {{ $language->native }}"
                                                id="title-{{ $language->code }}" maxlength="255"
                                                value="{{ $seoPage->getTranslation('title', $language->code, false) }}">
                                        </div>
                                    </div>

                                    <div class="col-12 mb-4">
                                        <label class="form-label d-flex align-items-center">
                                            <i class="bx bx-detail text-primary me-2"></i>
                                            @lang('l.Meta Description') ({{ $language->native }})
                                        </label>
                                        <div class="textarea-wrapper">
                                            <textarea class="form-control translation-input"
                                                name="description-{{ $language->code }}" rows="3"
                                                placeholder="@lang('l.Enter description in') {{ $language->native }}"
                                                id="description-{{ $language->code }}"
                                                maxlength="500">{{ $seoPage->getTranslation('description', $language->code, false) }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-12 mb-4">
                                        <label class="form-label d-flex align-items-center">
                                            <i class="bx bx-tag text-primary me-2"></i>
                                            @lang('l.Meta Keywords') ({{ $language->native }})
                                        </label>
                                        <div class="textarea-wrapper">
                                            <textarea class="form-control translation-input"
                                                name="keywords-{{ $language->code }}" rows="2"
                                                placeholder="@lang('l.Enter keywords in') {{ $language->native }}"
                                                id="meta_keywords-{{ $language->code }}"
                                                maxlength="500">{{ $seoPage->getTranslation('keywords', $language->code, false) }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-12 mb-4">
                                        <label class="form-label d-flex align-items-center">
                                            <i class="bx bx-share-alt text-primary me-2"></i>
                                            @lang('l.OG Title') ({{ $language->native }})
                                        </label>
                                        <div class="textarea-wrapper">
                                            <input type="text" class="form-control translation-input"
                                                name="og_title-{{ $language->code }}"
                                                placeholder="@lang('l.Enter OG title in') {{ $language->native }}"
                                                id="og_title-{{ $language->code }}" maxlength="255"
                                                value="{{ $seoPage->getTranslation('og_title', $language->code, false) }}">
                                        </div>
                                    </div>

                                    <div class="col-12 mb-4">
                                        <label class="form-label d-flex align-items-center">
                                            <i class="bx bx-detail text-primary me-2"></i>
                                            @lang('l.OG Description') ({{ $language->native }})
                                        </label>
                                        <div class="textarea-wrapper">
                                            <textarea class="form-control translation-input"
                                                name="og_description-{{ $language->code }}" rows="3"
                                                placeholder="@lang('l.Enter OG description in') {{ $language->native }}"
                                                id="og_description-{{ $language->code }}"
                                                maxlength="500">{{ $seoPage->getTranslation('og_description', $language->code, false) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('dashboard.admins.seo') }}" class="btn btn-outline-secondary me-2">
                            <i class="bx bx-x me-1"></i> @lang('l.Cancel')
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> @lang('l.Save Translations')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection