@extends('themes.default.auth.layout')

@section('title')
    Forgot Password
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

.method-box { border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; }
.method-row { display:flex; gap:12px; align-items:flex-start; }
.method-row input { margin-top: 4px; }
.msg-success { color:#16a34a; margin-bottom:12px; }
.msg-error { color:#dc2626; margin-bottom:12px; }
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

                    <h2 class="fs-20 fw-bolder mb-4">Forgot Password</h2>

                    @if (session('status'))
                        <div class="msg-success">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="msg-error">
                            @foreach ($errors->all() as $e)
                                <div>{{ $e }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('password.email') }}" method="POST" class="w-100 mt-2">
                        @csrf

                        <div class="mb-3">
                            <input type="email"
                                   class="form-control"
                                   name="email"
                                   placeholder="Enter your email"
                                   required
                                   value="{{ old('email') }}">
                        </div>

                        <div class="mb-3 method-box">
                            <div class="method-row">
                                <input type="radio" name="method" value="email" id="m_email"
                                       {{ old('method','email') === 'email' ? 'checked' : '' }}>
                                <div>
                                    <label for="m_email" style="font-weight: 700; color:#1e293b;">Email link</label>
                                    <div style="font-size: 12px; color:#64748b;">We will send a reset link to your email</div>
                                </div>
                            </div>

                            <div style="height:10px;"></div>

                            <div class="method-row">
                                <input type="radio" name="method" value="sms" id="m_sms"
                                       {{ old('method') === 'sms' ? 'checked' : '' }}>
                                <div>
                                    <label for="m_sms" style="font-weight: 700; color:#1e293b;">SMS OTP</label>
                                    <div style="font-size: 12px; color:#64748b;">SMS not enabled now</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-lg btn-primary w-100">Continue</button>
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
