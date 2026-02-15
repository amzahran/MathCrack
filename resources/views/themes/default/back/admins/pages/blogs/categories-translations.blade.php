@extends('themes.default.layouts.back.master')

@section('title')
    {{ Str::title($category->name) }}
@endsection


@section('content')
    @can('edit blog_category')
        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="card-action-element mb-2" style="text-align: end;">
                <a href="{{ route('dashboard.admins.blogs.categories-auto-translate', ['id' => encrypt($category->id)]) }}"
                    class="btn btn-dark waves-effect waves-light">
                    <i class="fa fa-language fa-xs me-1"></i> @lang('l.Auto Translate')
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('l.Translate Category')</h3>
                </div>

                <div class="card-body">
                    <form id="translateForm" class="row g-3" method="post"
                        action="{{ route('dashboard.admins.blogs.categories-translate') }}">
                        @csrf @method('patch')
                        <input type="hidden" name="id" value="{{ encrypt($category->id) }}">

                        <ul class="nav nav-tabs" role="tablist">
                            @foreach ($languages as $language)
                                <li class="nav-item" role="presentation">
                                    <button type="button" class="nav-link {{ $loop->first ? 'active' : '' }}" role="tab"
                                        data-bs-toggle="tab" data-bs-target="#lang-{{ $language->code }}"
                                        aria-controls="lang-{{ $language->code }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                        <i class="fi fi-{{ strtolower($language->flag) }} fs-8 me-2 ms-2"></i>
                                        {{ $language->native }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach ($languages as $language)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                    id="lang-{{ $language->code }}" role="tabpanel">

                                    <!-- حقل Meta Keywords -->
                                    <div class="col-12 mb-3">
                                        <label class="form-label">@lang('l.Meta Keywords') ({{ $language->native }})</label>
                                        <input type="text" name="meta_keywords-{{ $language->code }}"
                                            class="form-control translation-input form-control-tags"
                                            value="{{ $category->getTranslation('meta_keywords', $language->code, false) ?? '' }}"
                                            placeholder="@lang('l.Enter keywords in') {{ $language->native }}" />
                                    </div>

                                    <!-- حقل Meta Description -->
                                    <div class="col-12 mb-3">
                                        <label class="form-label">@lang('l.Meta Description') ({{ $language->native }})</label>
                                        <textarea name="meta_description-{{ $language->code }}"
                                            class="form-control translation-input"
                                            placeholder="@lang('l.Enter description in') {{ $language->native }}"
                                            maxlength="160" rows="3">{{ $category->getTranslation('meta_description', $language->code, false) ?? '' }}</textarea>
                                    </div>

                                    <div class="col-12 mb-2">
                                        <label class="form-label">@lang('l.Name') ({{ $language->native }})</label>
                                        <input type="text" name="name-{{ $language->code }}"
                                            class="form-control translation-input"
                                            value="{{ $category->getTranslation('name', $language->code) ?? '' }}"
                                            placeholder="@lang('l.Enter name in') {{ $language->native }}" required />
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="col-12 text-center mt-4">
                            <a href="{{ route('dashboard.admins.blogs.categories') }}"
                                class="btn btn-label-secondary">@lang('l.Cancel')</a>
                            <button type="submit" class="btn btn-primary">@lang('l.Save Translations')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
    <!-- / Content -->
@endsection


@section('js')
<script>
    // تهيئة حقول التاجات للكلمات المفتاحية
    $('.form-control-tags').each(function() {
        new Tagify(this, {
            whitelist: [],
            maxTags: 10,
            dropdown: {
                maxItems: 20,
                classname: "tags-dropdown",
                enabled: 0,
                closeOnSelect: true
            }
        });
    });
</script>
@endsection
