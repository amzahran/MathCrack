@extends('themes.default.auth.layout')

@section('title')
    @lang('l.Confirm Password')
@endsection

@section('description')
@endsection

@section('page-css')
@endsection

@section('content')
    <main class="auth-minimal-wrapper">
        <div class="auth-minimal-inner">
            <div class="minimal-card-wrapper">
                <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative">
                    <div
                        class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
                        <img src="{{ asset($settings['favicon']) }}" alt="" class="img-fluid">
                    </div>
                    <div class="card-body p-sm-5">
                        <h2 class="fs-20 fw-bolder mb-4">@lang('l.Confirm Password')</h2>
                        <p class="mb-4">@lang('l.This is a secure area of the application. Please confirm your password before continuing.')</p>

                        <form method="POST" action="{{ route('password.confirm') }}" class="w-100 mt-4 pt-2">
                            @csrf
                            <div class="mb-4">
                                <input type="password" id="password" class="form-control" name="password" required
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" />
                                @if ($errors->has('password'))
                                    <div class="mt-2" style="color: red; padding-left: 10px; padding-right: 10px;">
                                        {{ $errors->first('password') }}
                                    </div>
                                @endif
                            </div>

                            <div class="mt-5">
                                <button type="submit" class="btn btn-lg btn-primary w-100">@lang('l.Confirm')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('page-scripts')
@endsection
