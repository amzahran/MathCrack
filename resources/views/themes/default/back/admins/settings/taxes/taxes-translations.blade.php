@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Translate Tax')
@endsection

@section('css')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard.admins.taxes') }}" class="btn btn-icon btn-outline-primary back-btn me-3">
                <i class="bx bx-arrow-back"></i>
            </a>
            <h4 class="fw-bold py-3 mb-0">
                <i class="bx bx-globe text-primary me-1"></i>
                {{ $tax->name }}
            </h4>
        </div>

        <a href="{{ route('dashboard.admins.taxes-auto-translate', ['id' => encrypt($tax->id)]) }}" class="btn btn-dark auto-translate-btn">
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
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">@lang('l.Translate Tax')</h5>
        </div>

        <div class="card-body">
            <form id="translateForm" method="post" action="{{ route('dashboard.admins.taxes-translate') }}">
                @csrf @method('PATCH')
                <input type="hidden" name="id" value="{{ encrypt($tax->id) }}">

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
                                        <i class="bx bx-text text-primary me-2"></i>
                                        @lang('l.Name') ({{ $language->native }})
                                        <span class="text-danger ms-1">*</span>
                                    </label>
                                    <div class="textarea-wrapper">
                                        <input
                                            type="text"
                                            class="form-control translation-input"
                                            name="name-{{ $language->code }}"
                                            required
                                            placeholder="@lang('l.Enter name in') {{ $language->native }}"
                                            id="name-{{ $language->code }}"
                                            maxlength="255"
                                            value="{{ $tax->getTranslation('name', $language->code, false) }}"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('dashboard.admins.taxes') }}" class="btn btn-outline-secondary me-2">
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