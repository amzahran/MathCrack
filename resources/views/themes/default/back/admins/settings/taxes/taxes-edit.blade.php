@extends('themes.default.layouts.back.master')

@section('title')
    {{ $tax->name }}
@endsection


@section('content')
    @can('edit blog_category')
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('dashboard.admins.taxes') }}"
                            class="btn btn-icon btn-outline-primary back-btn me-3">
                            <i class="bx bx-arrow-back"></i>
                        </a>
                        <h4 class="fw-bold py-3 mb-0">
                            <i class="bx bx-edit text-primary me-1"></i>
                            {{ $tax->name }}
                        </h4>
                    </div>

                    <a href="{{ route('dashboard.admins.taxes-get-translations', ['id' => encrypt($tax->id)]) }}"
                        class="btn btn-dark auto-translate-btn">
                        <i class="bx bx-globe me-1"></i> @lang('l.Manage Translations')
                    </a>
                </div>

                <div class="card-body">
                    <form id="translateForm" class="row g-3" method="post"
                        action="{{ route('dashboard.admins.taxes-update') }}">
                        @csrf @method('patch')
                        <input type="hidden" name="id" value="{{ encrypt($tax->id) }}">

                        <div class="row">
                            <div class="col-12 mb-5">
                                <label class="form-label">@lang('l.Name')<i class="fi fi-{{ strtolower($defaultLanguage->flag) }} fs-8 me-2 ms-2"></i> <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control"
                                    value="{{ $tax->getTranslation('name', $defaultLanguage->code) }}"required />
                            </div>
                            <div class="col-6 mb-5">
                                <label class="form-label">@lang('l.Type') <span class="text-danger">*</span></label>
                                <select class="form-control form-select" name="type" required>
                                    <option value="fixed" @if($tax->type == 'fixed') selected @endif>@lang('l.Fixed')</option>
                                    <option value="percentage" @if($tax->type == 'percentage') selected @endif>@lang('l.Percentage')</option>
                                </select>
                            </div>
                            <div class="col-6 mb-5">
                                <label class="form-label">@lang('l.Rate value') <span class="text-danger">*</span></label>
                                <input type="text" name="rate" class="form-control" value="{{ $tax->rate }}" required />
                            </div>

                            <div class="col-6 mb-5">
                                <div class="form-check form-switch mb-2">
                                    <input type="hidden" name="is_default" value="0">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="is_default" value="1" @if($tax->is_default == 1) checked @endif />
                                    <label class="form-check-label" for="flexSwitchCheckDefault">@lang('l.Is Default')</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-center mt-4">
                            <a href="{{ route('dashboard.admins.taxes') }}" class="btn btn-label-secondary">@lang('l.Cancel')</a>
                            <button type="submit" class="btn btn-primary">@lang('l.Update')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
    <!-- / Content -->
@endsection


@section('js')
@endsection
