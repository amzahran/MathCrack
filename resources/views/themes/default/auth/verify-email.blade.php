@extends('themes.default.auth.layout')

@section('title')
    @lang('l.Verify Email')
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
                        <h2 class="fs-20 fw-bolder mb-4">@lang('l.Verify your email')</h2>
                        <p class="mb-4">
                            @lang('l.Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.')
                        </p>
                        @if (session('status') == 'verification-link-sent')
                            <div class="mb-4" style="color: green; padding-left: 10px; padding-right: 10px;">
                                @lang('l.A new verification link has been sent to the email address you provided during registration.')
                            </div>
                        @endif

                        <form class="w-100 mt-4 pt-2" action="{{ route('verification.send') }}" method="POST">
                            @csrf
                            <div class="mt-5">
                                <button class="btn btn-lg btn-primary w-100" type="submit">@lang('l.Resend Verification Email')</button>
                            </div>
                        </form>

                        <div class="mt-5 text-center">
                            <span class="text-muted">@lang('l.or')</span>
                            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                @csrf
                                <a href="javascript:void(0);" onclick="event.preventDefault(); this.closest('form').submit();" class="text-danger">
                                    @lang('l.Logout')
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('page-scripts')
@endsection
