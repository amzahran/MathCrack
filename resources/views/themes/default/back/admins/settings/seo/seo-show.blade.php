@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.View SEO Page')
@endsection

@section('css')
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5>@lang('l.View SEO Page'): {{ $seoPage->name }}</h5>
                        <div>
                            <a href="{{ route('dashboard.admins.seo-edit', ['id' => encrypt($seoPage->id)]) }}" class="btn btn-primary">
                                <i class="bx bx-edit"></i> @lang('l.Edit')
                            </a>
                            <a href="{{ route('dashboard.admins.seo') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back"></i> @lang('l.Back to List')
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold">@lang('l.Page Name')</h6>
                                <p>{{ $seoPage->title }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold">@lang('l.Slug')</h6>
                                <p><code>{{ $seoPage->slug }}</code></p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold">@lang('l.Updated At')</h6>
                                <p>{{ $seoPage->updated_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3 border-top pt-3">@lang('l.SEO Information')</h5>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="fw-bold">@lang('l.Meta Title')</h6>
                                <p>{{ $seoPage->title ?: __('l.Not Set') }}</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="fw-bold">@lang('l.Meta Description')</h6>
                                <p>{{ $seoPage->description ?: __('l.Not Set') }}</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="fw-bold">@lang('l.Meta Keywords')</h6>
                                <p>{{ $seoPage->keywords ?: __('l.Not Set') }}</p>
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3 border-top pt-3">@lang('l.Social Media Sharing')</h5>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="fw-bold">@lang('l.OG Title')</h6>
                                <p>{{ $seoPage->og_title ?: __('l.Not Set') }}</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="fw-bold">@lang('l.OG Description')</h6>
                                <p>{{ $seoPage->og_description ?: __('l.Not Set') }}</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="fw-bold">@lang('l.OG Image')</h6>
                                @if($seoPage->og_image)
                                <div>
                                    <img src="{{ asset($seoPage->og_image) }}" alt="OG Image" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                                @else
                                <p>@lang('l.Not Set')</p>
                                @endif
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3 border-top pt-3">@lang('l.Structured Data')</h5>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="fw-bold">@lang('l.JSON-LD Structured Data')</h6>
                                @if($seoPage->structured_data)
                                <pre class="bg-light p-3 rounded"><code>{{ json_encode(json_decode($seoPage->structured_data), JSON_PRETTY_PRINT) }}</code></pre>
                                @else
                                <p>@lang('l.Not Set')</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection