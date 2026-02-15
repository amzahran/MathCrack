@extends('themes.default.auth.layout')

@section('title')
    @lang('l.Login')
@endsection

@section('description')
@endsection

@section('page-css')
<style>
/* الخلفية */
.auth-minimal-wrapper,
.auth-minimal-inner {
    background: #f4f6fb !important;
}

/* خلي البطاقة بيضاء */
.minimal-card-wrapper .card {
    background: #ffffff !important;
    border: 0;
    border-radius: 14px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
}

/* لا تمسح خلفيات body/main عشان ما تكسرش layout عام الموقع */
.card-body {
    padding-top: 10px !important;
}

/* الهيدر */
.mathcrack-original-header {
    text-align: center;
    margin: 0 auto 18px auto;
    padding-top: 6px;
}

.mathcrack-logo-img {
    height: 60px;
    width: auto;
    margin-bottom: 8px;
    object-fit: contain;
}

.mathcrack-original-title {
    font-size: 28px;
    font-weight: 800;
    color: #1e293b;
    margin: 0 0 4px 0;
    letter-spacing: -0.5px;
}

.prof-name-original {
    font-size: 14px;
    color: #64748b;
    margin: 0;
    font-weight: 500;
}

/* العنوان */
.fs-20 {
    margin-top: 0 !important;
    margin-bottom: 22px !important;
    font-size: 22px !important;
    color: #1e293b;
}

/* اخفاء الشعار القديم */
.wd-50 {
    display: none !important;
}

/* حقول */
.form-control {
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 12px 14px;
}

.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
}

.custom-control label {
    margin-left: 6px;
    margin-bottom: 0;
}
</style>
@endsection

@section('content')
@php
    $rememberUser = $_COOKIE['remember_user'] ?? null;

    $rememberPass = null;
    if (isset($_COOKIE['remember_pass'])) {
        try {
            $rememberPass = decrypt($_COOKIE['remember_pass']);
        } catch (\Throwable $e) {
            $rememberPass = null;
        }
    }
@endphp

<main class="auth-minimal-wrapper">
    <div class="auth-minimal-inner">
        <div class="minimal-card-wrapper">
            <div class="card mb-4 mt-5 mx-4 mx-sm-0">
                <div class="card-body p-sm-5">

                    <div class="mathcrack-original-header">
                        <img src="{{ asset('logo.png') }}"
                             alt="MathCrack Logo"
                             class="mathcrack-logo-img"
                             onerror="this.style.display='none'; document.getElementById('text-logo').style.display='block';">
                        <div id="text-logo" style="display: none;">
                            <h1 class="mathcrack-original-title">MathCrack</h1>
                            <p class="prof-name-original">Prof. Ahmed Omar</p>
                        </div>
                    </div>

                    <h2 class="fs-20 fw-bolder mb-4">@lang('l.Login')</h2>

                    <form action="{{ route('login') }}" method="POST" class="w-100 mt-4">
                        @csrf

                        <div class="mb-4">
                            <input
                                type="text"
                                class="form-control"
                                id="email"
                                name="email"
                                placeholder="@lang('l.Enter your email or phone number')"
                                autofocus
                                required
                                value="{{ $rememberUser ?? old('email') }}"
                            />

                            @if ($errors->has('email'))
                                <div class="mt-2" style="color: red; padding-left: 10px; padding-right: 10px;">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif

                            @if ($errors->has('limit'))
                                <div class="mt-2" style="color: red; padding-left: 10px; padding-right: 10px;">
                                    {{ $errors->first('limit') }}
                                </div>
                            @endif
                        </div>

                        <div class="mb-3 position-relative">
                            <input
                                type="password"
                                id="password"
                                class="form-control"
                                name="password"
                                required
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                aria-describedby="password"
                                value="{{ $rememberPass ?? old('password') }}"
                            />

                            <button
                                type="button"
                                class="btn position-absolute end-0 top-50 translate-middle-y pe-3"
                                onclick="togglePassword()"
                                style="border: none; background: none; z-index: 10;"
                                aria-label="Toggle password visibility"
                            >
                                <i id="password-toggle-icon" class="feather-eye-off text-muted"></i>
                            </button>
                        </div>

                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="custom-control custom-checkbox d-flex align-items-center">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="remember-me"
                                        name="remember"
                                        @if ($rememberUser) checked @endif
                                    />
                                    <label for="remember-me">@lang('l.Remember Me')</label>
                                </div>
                            </div>

                            <div>
                                <a href="{{ route('password.request') }}" class="fs-11 text-primary">
                                    @lang('l.Forgot Password?')
                                </a>
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
                        ])->filter()->count();
                    @endphp

                    <div class="w-100 mt-5 text-center mx-auto">
                        @if ($settings['facebookLogin'] || $settings['googleLogin'] || $settings['twitterLogin'])
                            <div class="mb-4 border-bottom position-relative">
                                <span class="small py-1 px-3 text-uppercase text-muted bg-white position-absolute translate-middle">
                                    @lang('l.or')
                                </span>
                            </div>
                        @endif

                        <div class="d-flex align-items-center justify-content-center gap-2">
                            @if ($settings['facebookLogin'])
                                <a href="{{ route('auth.facebook') }}" class="btn btn-light-brand flex-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Login with Facebook">
                                    <i class="feather-facebook"></i>
                                </a>
                            @endif

                            @if ($settings['googleLogin'])
                                <a href="{{ route('auth.google') }}" class="btn btn-light-brand flex-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Login with Google">
                                    <i class="fa fa-google"></i>
                                </a>
                            @endif

                            @if ($settings['twitterLogin'])
                                <a href="{{ route('auth.twitter') }}" class="btn btn-light-brand flex-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Login with Twitter">
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
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('password-toggle-icon');

    if (!passwordInput || !toggleIcon) return;

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.className = 'feather-eye text-muted';
    } else {
        passwordInput.type = 'password';
        toggleIcon.className = 'feather-eye-off text-muted';
    }
}
</script>
@endsection
