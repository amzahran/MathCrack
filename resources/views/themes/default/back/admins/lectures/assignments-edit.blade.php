@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Edit') @lang('l.Assignment')
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

        @can('edit lectures')
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0">@lang('l.Edit Assignment'): <span class="text-primary">{{ $assignment->title }}</span></h4>
                    <p class="text-muted mb-0">{{ $lecture->name }} - {{ $lecture->course->name ?? '' }}</p>
                </div>
                <div>
                    <a href="{{ route('dashboard.admins.lectures-assignments') }}?id={{ encrypt($lecture->id) }}" class="btn btn-secondary waves-effect waves-light mb-2">
                        <i class="fa fa-arrow-left ti-xs me-1"></i>
                        @lang('l.Back to Assignments')
                    </a>
                </div>
            </div>

            <div class="card" id="div1" style="padding: 15px;">
                <div class="card-body">
                    <form action="{{ route('dashboard.admins.lectures-assignments-update') }}?id={{ encrypt($assignment->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">@lang('l.Assignment Title')</label>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ $assignment->title }}" required>
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('title')" />
                                </div>
                            </div>

                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">@lang('l.Assignment Description')</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ $assignment->description }}</textarea>
                            <x-input-error class="mt-2 error-message" :messages="$errors->get('description')" />
                        </div>

                        <div class="mb-3">
                            <label for="time_limit" class="form-label">@lang('l.Time Limit (minutes)')</label>
                            <input type="number" class="form-control" id="time_limit" name="time_limit" min="1" value="{{ $assignment->time_limit }}" placeholder="@lang('l.No Limit')">
                            <small class="form-text text-muted">@lang('l.Leave empty for no time limit')</small>
                            <x-input-error class="mt-2 error-message" :messages="$errors->get('time_limit')" />
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <input type="hidden" name="show_answers" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="show_answers" name="show_answers" value="1" {{ $assignment->show_answers ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_answers">
                                        @lang('l.Show Answers')
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input type="hidden" name="is_active" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ $assignment->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        @lang('l.Active')
                                    </label>
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
