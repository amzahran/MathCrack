@extends('themes.default.auth.layout')

@section('title')
    @lang('l.Reset Password')
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
                        <h2 class="fs-20 fw-bolder mb-4">@lang('l.Reset Password')</h2>

                        <form class="w-100 mt-4 pt-2" method="POST" action="{{ route('password.store') }}">
                            @csrf
                            <!-- Password Reset Token -->
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <!-- Email Address -->
                            <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

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

                            <div class="mb-4">
                                <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" required
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password_confirmation" />
                                @if ($errors->has('password_confirmation'))
                                    <div class="mt-2" style="color: red; padding-left: 10px; padding-right: 10px;">
                                        {{ $errors->first('password_confirmation') }}
                                    </div>
                                @endif
                            </div>

                            <div class="mt-5">
                                <button type="submit" class="btn btn-lg btn-primary w-100">@lang('l.Reset Password')</button>
                            </div>
                        </form>

                        <div class="mt-5 text-center">
                            <a href="{{ route('login') }}" class="text-muted">
                                <i class="bx bx-chevron-left me-1"></i>
                                @lang('l.Back to login')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/themes/default/js/pages-auth.js') }}"></script>
@endsection
