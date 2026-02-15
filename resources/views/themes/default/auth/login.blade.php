{{-- TEST-LOGIN-BLADE-123 --}}

@extends('themes.default.auth.layout')

@section('title')
    @lang('l.Login')
@endsection

@section('description')
@endsection

@section('page-css')
<style>
html,body{
    height:100%;
    background:#f4f6fb;
    font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto;
}

.auth-minimal-wrapper{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:40px 16px;
}

.minimal-card-wrapper .card{
    width:520px;
    border:0;
    border-radius:16px;
    background:#ffffff;
    box-shadow:
        0 10px 25px rgba(0,0,0,.08),
        0 2px 6px rgba(0,0,0,.04);
}

.minimal-card-wrapper .card-body{
    padding:56px 48px 44px 48px;
}

.wd-50{
    width:68px;
    height:68px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
}

.wd-50 img{
    width:100%;
    height:100%;
    object-fit:contain;
}

h2{
    font-size:28px;
    font-weight:700;
    color:#0f172a;
    margin-bottom:6px;
}

.form-control{
    height:48px;
    border-radius:10px;
    border:1px solid #e5e7eb;
    font-size:15px;
    padding:12px 14px;
}

.form-control::placeholder{
    color:#9ca3af;
}

.form-control:focus{
    border-color:#3b5bdb;
    box-shadow:0 0 0 3px rgba(59,91,219,.18);
}

.custom-checkbox label{
    font-size:14px;
    color:#475569;
}

.fs-11{
    font-size:14px;
}

.btn-primary{
    height:50px;
    border-radius:10px;
    background:#3b5bdb;
    border-color:#3b5bdb;
    font-size:15px;
    font-weight:700;
    letter-spacing:.4px;
}

.btn-primary:hover{
    background:#334fc9;
    border-color:#334fc9;
}

a{
    color:#3b5bdb;
    text-decoration:none;
}

a:hover{
    text-decoration:underline;
}

.mt-5.text-muted{
    font-size:14px;
}
</style>
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
                            <div class="mb-3 position-relative">
                                <input type="password" id="password" class="form-control" name="password" required
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                aria-describedby="password"
                                @if (isset($_COOKIE['remember_pass'])) <?php $password = decrypt($_COOKIE['remember_pass']); ?>
                                    value="{{ $password }}"
                                @else
                                    value="{{ old('password') }}" @endif />
                                <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y pe-3"
                                        onclick="togglePassword()" style="border: none; background: none; z-index: 10;">
                                    <i id="password-toggle-icon" class="feather-eye-off text-muted"></i>
                                </button>
                            </div>
                            <script>
                                function togglePassword() {
                                    const passwordInput = document.getElementById('password');
                                    const toggleIcon = document.getElementById('password-toggle-icon');

                                    if (passwordInput.type === 'password') {
                                        passwordInput.type = 'text';
                                        toggleIcon.className = 'feather-eye text-muted';
                                    } else {
                                        passwordInput.type = 'password';
                                        toggleIcon.className = 'feather-eye-off text-muted';
                                    }
                                }
                            </script>
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
