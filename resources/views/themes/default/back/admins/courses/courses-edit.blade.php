@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Edit') @lang('l.Course')
@endsection

@section('css')
@endsection

@section('content')
    <div class="main-content">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        @can('edit courses')
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0">@lang('l.Edit Course'): <span class="text-primary">{{ $course->name }}</span></h4>
                </div>
                <div>
                    <a href="{{ route('dashboard.admins.courses') }}" class="btn btn-secondary waves-effect waves-light mb-2">
                        <i class="fa fa-arrow-left ti-xs me-1"></i>
                        @lang('l.Back to Courses')
                    </a>
                </div>
            </div>

            <div class="card" id="div1" style="padding: 15px;">
                <div class="card-body">
                    <form action="{{ route('dashboard.admins.courses-update') }}?id={{ encrypt($course->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">@lang('l.Course Name')</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $course->name }}" required>
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('name')" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="level_id" class="form-label">@lang('l.Level')</label>
                                    <select class="form-select" id="level_id" name="level_id" required>
                                        <option value="">@lang('l.Select Level')</option>
                                        @foreach($levels as $level)
                                            <option value="{{ $level->id }}" {{ $course->level_id == $level->id ? 'selected' : '' }}>
                                                {{ $level->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('level_id')" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">@lang('l.Price')</label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="{{ $course->price }}">
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('price')" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tests_price" class="form-label">@lang('l.Tests Price')</label>
                                    <input type="number" class="form-control" id="tests_price" name="tests_price" step="0.01" min="0" value="{{ $course->tests_price }}">
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('tests_price')" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="access_duration_days" class="form-label">@lang('l.Access Duration') (@lang('l.Days')) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="access_duration_days" name="access_duration_days" min="0" max="3650" value="{{ $course->access_duration_days }}" required>
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('access_duration_days')" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image" class="form-label">@lang('l.Image')</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    @if($course->image)
                                        <div class="mt-2">
                                            <img src="{{ asset($course->image) }}" alt="course" width="80" height="80" class="rounded">
                                        </div>
                                    @endif
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('image')" />
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save ti-xs me-1"></i>
                                @lang('l.Update')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan
    </div>
@endsection

@section('js')
@endsection
