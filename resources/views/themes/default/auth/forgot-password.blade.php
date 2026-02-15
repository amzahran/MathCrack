@extends('themes.default.auth.layout')

@section('title')
    @lang('l.Forgot Password')
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
                        <h2 class="fs-20 fw-bolder mb-4">@lang('l.Forgot Password')</h2>
                        <p class="mb-4">@lang('l.Enter your email and we\'ll send you instructions to reset your password')</p>

                        <form id="formAuthentication" class="w-100 mt-4 pt-2" action="{{ route('password.email') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="@lang('l.Enter your email')" autofocus required />
                                @if ($errors->has('email'))
                                    <div class="mt-2" style="color: red; padding-left: 10px; padding-right: 10px;">
                                        @lang('l.Email is invalid')
                                    </div>
                                @endif
                                @if (session('status'))
                                    <div class="mt-2" style="color: green; padding-left: 10px; padding-right: 10px;">
                                        @lang('l.We have sent you a link to reset your password')
                                    </div>
                                @endif
                            </div>

                            <div class="mt-5">
                                <button class="btn btn-lg btn-primary w-100" type="submit">@lang('l.Send Reset Link')</button>
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
