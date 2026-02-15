@extends('themes.default.auth.layout')

@section('title')
    Reset Password
@endsection

@section('page-css')
<style>
.auth-minimal-wrapper { background: #f4f6fb !important; }
body, main, .auth-minimal-inner, .minimal-card-wrapper, .card-body { background: transparent !important; }
.card { background: #ffffff !important; }

.mathcrack-original-header { text-align: center; margin: 0 auto 20px auto; padding-top: 20px; }
.mathcrack-logo-img { height: 60px; width: auto; margin-bottom: 10px; object-fit: contain; }
.mathcrack-original-title { font-size: 28px; font-weight: 800; color: #1e293b; margin: 0 0 4px 0; letter-spacing: -0.5px; }
.prof-name-original { font-size: 14px; color: #64748b; margin: 0; font-weight: 500; }

.card-body { padding-top: 5px !important; }
.fs-20 { margin-top: 0 !important; margin-bottom: 20px !important; font-size: 22px !important; color: #1e293b; }
.wd-50 { display: none !important; }

.form-control { border: 1px solid #e5e7eb; }
.form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
</style>
@endsection

@section('content')
<main class="auth-minimal-wrapper">
    <div class="auth-minimal-inner">
        <div class="minimal-card-wrapper">
            <div class="card mb-4 mt-5 mx-4 mx-sm-0">
                <div class="card-body p-sm-5" style="padding-top: 15px !important;">

                    <div class="mathcrack-original-header">
                        <img src="{{ asset('logo.png') }}" alt="MathCrack Logo" class="mathcrack-logo-img"
                             onerror="this.style.display='none'; document.getElementById('text-logo').style.display='block';">
                        <div id="text-logo" style="display: none;">
                            <h1 class="mathcrack-original-title">MathCrack</h1>
                            <p class="prof-name-original">Prof. Ahmed Omar</p>
                        </div>
                    </div>

                    <h2 class="fs-20 fw-bolder mb-4">Reset Password</h2>

                    <form action="{{ route('password.reset') }}" method="POST" class="w-100">
                        @csrf

                        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                        <input type="hidden" name="token" value="{{ $token ?? old('token') }}">

                        @error('token')
                            <div class="mb-3" style="color: red;">{{ $message }}</div>
                        @enderror
                        @error('email')
                            <div class="mb-3" style="color: red;">{{ $message }}</div>
                        @enderror

                        <div class="mb-3">
                            <input type="password" class="form-control" name="password" placeholder="New password" required>
                            @error('password')
                                <div class="mt-2" style="color: red; padding-left: 10px; padding-right: 10px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm new password" required>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-lg btn-primary w-100">Update password</button>
                        </div>

                        <div class="mt-4 text-muted">
                            <a href="{{ route('login') }}" class="fw-bold">Back to login</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</main>
@endsection
