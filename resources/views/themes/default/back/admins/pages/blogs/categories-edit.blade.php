@extends('themes.default.layouts.back.master')

@section('title')
    {{ Str::title($category->name) }}
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
                
                <div class="card-header">
                    <h3 class="card-title">@lang('l.Edit Category')</h3>
                </div>

                <div class="card-body">
                    <form id="translateForm" class="row g-3" method="post"
                        action="{{ route('dashboard.admins.blogs.categories-update') }}">
                        @csrf @method('patch')
                        <input type="hidden" name="id" value="{{ encrypt($category->id) }}">

                        <div class="tab-content">
                            <div class="col-12 mb-2">
                                <label class="form-label">@lang('l.Name')<i class="fi fi-{{ strtolower($defaultLanguage->flag) }} fs-8 me-2 ms-2"></i></label>
                                <input type="text" name="name"
                                    class="form-control"
                                    value="{{ $category->getTranslation('name', $defaultLanguage->code) }}"
                                    placeholder="@lang('l.Enter a name')" required />
                            </div>
                        </div>

                        <div class="col-12 text-center mt-4">
                            <a href="{{ route('dashboard.admins.blogs.categories') }}" class="btn btn-label-secondary">@lang('l.Cancel')</a>
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
