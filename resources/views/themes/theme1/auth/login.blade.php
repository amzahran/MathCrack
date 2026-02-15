@extends('themes.default.auth.layout')

@section('title')
    @lang('l.Login')
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
                        <h2 class="fs-20 fw-bolder mb-4">@lang('l.Login')</h2>
                        <form action="{{ route('login') }}" method="POST" class="w-100 mt-4 pt-2">
                            @csrf
                            <div class="mb-4">
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="@lang('l.Enter your email or phone number')" autofocus required
                                    @if (isset($_COOKIE['remember_user'])) value="{{ $_COOKIE['remember_user'] }}"
                            @else
                                value="{{ old('email') }}" @endif />

                                @if ($errors->has('email'))
                                    <div class="mt-2" style="color: red; padding-left: 10px; padding-right: 10px;">
                                        {{ $errors->first('email') }}</div>
                                @endif
                                @if ($errors->has('limit'))
                                    <div class="mt-2" style="color: red; padding-left: 10px; padding-right: 10px;">
                                        {{ $errors->first('limit') }}</div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <input type="password" id="password" class="form-control" name="password" required
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                aria-describedby="password"
                                @if (isset($_COOKIE['remember_pass'])) <?php $password = decrypt($_COOKIE['remember_pass']); ?>
                                    value="{{ $password }}"
                                @else
                                    value="{{ old('password') }}" @endif />
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="form-check-input" type="checkbox" id="remember-me" name="remember"
                                        @if (isset($_COOKIE['remember_user'])) checked @endif />
                                        <label  for="remember-me">@lang('l.Remember Me')</label>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('password.request') }}" class="fs-11 text-primary">@lang('l.Forgot Password?')</a>
                                </div>
                            </div>
                            <div class="mt-5">
                                <button type="submit" class="btn btn-lg btn-primary w-100">@lang('l.Login')</button>
                            </div>
                        </form>
                        @php
                            $activeLogins = collect([
                                $settings['facebookLogin'],
                                $settings['googleLogin'],
                                $settings['twitterLogin'],
                            ])
                                ->filter()
                                ->count();
                        @endphp
                        <div class="w-100 mt-5 text-center mx-auto">
                            @if ($settings['facebookLogin'] || $settings['googleLogin'] || $settings['twitterLogin'])
                                <div class="mb-4 border-bottom position-relative"><span
                                        class="small py-1 px-3 text-uppercase text-muted bg-white position-absolute translate-middle">@lang('l.or')</span>
                                </div>
                            @endif
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                @if ($settings['facebookLogin'])
                                    <a href="{{route('auth.facebook')}}" class="btn btn-light-brand flex-fill" data-bs-toggle="tooltip"
                                        data-bs-trigger="hover" title="Login with Facebook">
                                        <i class="feather-facebook"></i>
                                    </a>
                                @endif
                                @if ($settings['googleLogin'])
                                    <a href="{{ route('auth.google') }}" class="btn btn-light-brand flex-fill" data-bs-toggle="tooltip"
                                        data-bs-trigger="hover" title="Login with Google">
                                        <i class="fa fa-google"></i>
                                    </a>
                                @endif
                                @if ($settings['twitterLogin'])
                                    <a href="{{ route('auth.twitter') }}" class="btn btn-light-brand flex-fill" data-bs-toggle="tooltip"
                                        data-bs-trigger="hover" title="Login with Twitter">
                                        <i class="feather-twitter"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @if ($settings['can_any_register'] == 1)
                            <div class="mt-5 text-muted">
                                <span>@lang('l.New on our platform?')</span>
                                <a href="{{ route('register') }}" class="fw-bold">@lang('l.Create an account')</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection


@section('page-scripts')
@endsection
