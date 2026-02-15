@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Edit SEO Page')
@endsection

@section('css')
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('dashboard.admins.seo') }}"
                                class="btn btn-icon btn-outline-primary back-btn me-3">
                                <i class="bx bx-arrow-back"></i>
                            </a>
                            <h4 class="fw-bold py-3 mb-0">
                                <i class="bx bx-edit text-primary me-1"></i>
                                @lang('l.Edit SEO Page'): {{ $seoPage->getTranslation('title', $defaultLanguage) }}
                            </h4>
                        </div>

                        <a href="{{ route('dashboard.admins.seo-get-translations', ['id' => encrypt($seoPage->id)]) }}"
                            class="btn btn-dark auto-translate-btn">
                            <i class="bx bx-globe me-1"></i> @lang('l.Manage Translations')
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form action="{{ route('dashboard.admins.seo-update', ['id' => encrypt($seoPage->id)]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <h5 class="mt-4 mb-3 border-top pt-3">@lang('l.SEO Information')</h5>

                        <div class="mb-3">
                            <label for="title" class="form-label">@lang('l.Meta Title')</label>
                            <input type="text" class="form-control" id="title" name="title"
                                value="{{ old('title', $seoPage->getTranslation('title', $defaultLanguage)) }}">
                            <small class="text-muted">@lang('l.The title that appears in search engines')</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">@lang('l.Meta Description')</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $seoPage->getTranslation('description', $defaultLanguage)) }}</textarea>
                            <small class="text-muted">@lang('l.The description that appears in search results')</small>
                        </div>

                        <div class="mb-3">
                            <label for="keywords" class="form-label">@lang('l.Meta Keywords')</label>
                            <textarea class="form-control" id="meta_keywords" name="keywords" rows="2">{{ old('keywords', $seoPage->getTranslation('keywords', $defaultLanguage)) }}</textarea>
                            <small class="text-muted">@lang('l.Comma separated keywords related to the page')</small>
                        </div>

                        <h5 class="mt-4 mb-3 border-top pt-3">@lang('l.Social Media Sharing')</h5>

                        <div class="mb-3">
                            <label for="og_title" class="form-label">@lang('l.OG Title')</label>
                            <input type="text" class="form-control" id="og_title" name="og_title"
                                value="{{ old('og_title', $seoPage->getTranslation('og_title', $defaultLanguage)) }}">
                            <small class="text-muted">@lang('l.Title that appears when sharing on social media')</small>
                        </div>

                        <div class="mb-3">
                            <label for="og_description" class="form-label">@lang('l.OG Description')</label>
                            <textarea class="form-control" id="og_description" name="og_description" rows="3">{{ old('og_description', $seoPage->getTranslation('og_description', $defaultLanguage)) }}</textarea>
                            <small class="text-muted">@lang('l.Description that appears when sharing on social media')</small>
                        </div>

                        <div class="mb-3">
                            <label for="og_image" class="form-label">@lang('l.OG Image')</label>
                            @if ($seoPage->og_image)
                                <div class="mb-2">
                                    <img src="{{ asset($seoPage->og_image) }}" alt="OG Image" class="img-thumbnail"
                                        style="max-height: 100px;">
                                </div>
                            @endif
                            <input class="form-control" type="file" id="og_image" name="og_image">
                            <small class="text-muted">@lang('l.The image that appears when sharing on social media')</small>
                        </div>

                        <h5 class="mt-4 mb-3 border-top pt-3">@lang('l.Structured Data')</h5>

                        <div class="mb-3">
                            <label for="structured_data" class="form-label">@lang('l.JSON-LD Structured Data')</label>
                            <textarea class="form-control" id="structured_data" name="structured_data" rows="5">{{ old('structured_data', $seoPage->structured_data) }}</textarea>
                            <small class="text-muted">@lang('l.JSON-LD format structured data for rich snippets')</small>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">@lang('l.Update')</button>
                            <a href="{{ route('dashboard.admins.seo') }}" class="btn btn-secondary">@lang('l.Cancel')</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('js')
    <script>
        // التحقق من صحة JSON للبيانات المنظمة
        const structuredDataInput = document.getElementById('structured_data');
        structuredDataInput.addEventListener('blur', function() {
            try {
                if (structuredDataInput.value) {
                    const json = JSON.parse(structuredDataInput.value);
                    structuredDataInput.value = JSON.stringify(json, null, 2);
                }
            } catch (e) {
                alert('@lang('l.Invalid JSON format')');
            }
        });
    </script>
@endsection
