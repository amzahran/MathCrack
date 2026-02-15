@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Settings')
@endsection

@section('css')
@endsection

@section('content')
    @can('show settings')
        <div class="main-content">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('dashboard.admins.settings-update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
                @csrf
                <div class="row">
                    <!-- معلومات الموقع الأساسية -->
                    <div class="col-md-8">
                        <div class="mb-4">
                            <label class="form-label">@lang('l.Site Name')</label>
                            <input type="text" class="form-control" name="name" required
                                value="{{ $settings['name'] ?? '' }}">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">@lang('l.Site Description')</label>
                            <textarea class="form-control" name="description" required
                                rows="3">{{ $settings['description'] ?? '' }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">@lang('Keywords')</label>
                            <textarea class="form-control" name="keywords" required
                                rows="3">{{ $settings['keywords'] ?? '' }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">@lang('Home Video')</label>
                            <textarea class="form-control" name="home_video" required
                                rows="3">{{ $settings['home_video'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <!-- الشعارات والصور -->
                    <div class="col-md-4">
                        <div class="text-center mb-5 mt-5">
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                                <img src="{{ asset($settings['logo'] ?? 'placeholder.png') }}" class="img-fluid current-image" width="180"
                                    style="max-height: 100px" alt="Current Logo">
                                <div class="preview-arrow d-none">
                                    <i class="fas fa-arrow-right fs-3 text-primary"></i>
                                </div>
                                <img src="" class="img-fluid d-none new-preview" id="logoPreview"
                                    style="max-height: 100px" alt="New Logo">
                            </div>
                            <div class="mt-2">
                                <label class="btn btn-primary btn-sm">
                                    @lang('l.Upload Logo')
                                    <input type="file" name="logo" class="d-none image-preview" accept="image/*"
                                        data-preview="logoPreview" data-container="logo-container"
                                        data-width="850" data-height="179">
                                </label>
                            </div>
                        </div>

                        <!-- تحميل شعار اللوجو البلاك -->
                        {{-- <div class="text-center mt-4">
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                                <img src="{{ asset($settings['logo_black'] ?? 'placeholder.png') }}" class="img-fluid current-image" width="180"
                                    style="max-height: 100px" alt="Current Black Logo">
                                <div class="preview-arrow d-none">
                                    <i class="fas fa-arrow-right fs-3 text-secondary"></i>
                                </div>
                                <img src="" class="img-fluid d-none new-preview" id="logoBlackPreview"
                                    style="max-height: 100px" alt="New Black Logo">
                            </div>
                            <div class="mt-2">
                                <label class="btn btn-secondary btn-sm">
                                     @lang('l.Upload Black Logo')
                                    <input type="file" name="logo_black" class="d-none image-preview" accept="image/*"
                                        data-preview="logoBlackPreview" data-container="logo-black-container"
                                        data-width="850" data-height="179">
                                </label>
                            </div>
                        </div> --}}

                        <div class="text-center mt-5">
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                                <img src="{{ asset($settings['favicon'] ?? 'placeholder.png') }}" class="img-fluid"
                                    style="max-height: 50px" alt="Current Favicon">
                                <div class="preview-arrow d-none">
                                    <i class="fas fa-arrow-right fs-3 text-info"></i>
                                </div>
                                <img src="" class="img-fluid d-none new-preview" id="faviconPreview"
                                    style="max-height: 50px" alt="New Favicon">
                            </div>
                            <div class="mt-2">
                                <label class="btn btn-info btn-sm">
                                    @lang('l.Upload Favicon')
                                    <input type="file" name="favicon" class="d-none image-preview" accept="image/*"
                                        data-preview="faviconPreview" data-container="favicon-container"
                                        data-width="500" data-height="500">
                                </label>
                            </div>
                        </div>

                    </div>

                    <!-- إعدادات اللغة والعملة -->
                    <div class="col-md-12 mt-4">
                        <h6 class="mb-3">@lang('l.Language and Currency Settings')</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label class="form-label" for="default_language">@lang('l.Default Language')</label>
                                    <select class="select2 form-select" name="default_language" id="default_language" data-select2-selector="language">
                                        @foreach ($headerLanguages as $language)
                                            <option value="{{ $language->code }}"
                                                {{ ($settings['default_language'] ?? '') == $language->code ? 'selected' : '' }}>
                                                {{ $language->name . ' (' . $language->native . ')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label class="form-label" for="default_currency">@lang('l.Default Currency')</label>
                                    <select class="select2 form-select" name="default_currency" id="default_currency" data-select2-selector="language">
                                        @foreach ($headerCurrencies as $currency)
                                            <option value="{{ $currency->code }}"
                                                {{ ($settings['default_currency'] ?? '') == $currency->code ? 'selected' : '' }}>
                                                {{ $currency->name . ' (' . $currency->symbol . ' - ' . strtoupper($currency->code) . ')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label class="form-label" for="timezone">@lang('l.Timezone')</label>
                                    <select class="select2 form-select" name="timezone" id="timezone" data-select2-selector="language">
                                        @foreach (DateTimeZone::listIdentifiers() as $timezone)
                                            <option value="{{ $timezone }}"
                                                {{ ($settings['timezone'] ?? '') == $timezone ? 'selected' : '' }}>
                                                {{ $timezone }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-4">

                    <!-- إعدادات التسجيل -->
                    <div class="col-12 mt-4">
                        <h6 class="mb-3">@lang('l.Additional Settings')</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-4 d-flex align-items-center">
                                    <label class="form-label me-3 mb-0" for="emailVerified">@lang('l.Email Verification')</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="emailVerified" value="0">
                                        <input type="checkbox" class="form-check-input" style="width: 3em; height: 1.5em;"
                                               name="emailVerified" id="emailVerified" value="1"
                                               {{ ($settings['emailVerified'] ?? 0) == 1 ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4 d-flex align-items-center">
                                    <label class="form-label me-3 mb-0" for="can_any_register">@lang('l.Can Any Register')</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="can_any_register" value="0">
                                        <input type="checkbox" class="form-check-input" style="width: 3em; height: 1.5em;"
                                               name="can_any_register" id="can_any_register" value="1"
                                               {{ ($settings['can_any_register'] ?? 0) == 1 ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="max_sessions" class="form-label">@lang('l.Max Sessions')</label>
                                <input type="number" class="form-control" id="max_sessions" name="max_sessions"
                                    value="{{ old('max_sessions', $settings['max_sessions']) }}">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="session_timeout" class="form-label">@lang('l.Session Timeout (hours)')</label>
                                <input type="number" class="form-control" id="session_timeout" name="session_timeout"
                                    value="{{ old('session_timeout', $settings['session_timeout']) }}">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="max_attempts" class="form-label">@lang('l.Max Login Attempts')</label>
                                <input type="number" class="form-control" id="max_attempts" name="max_attempts"
                                    value="{{ old('max_attempts', $settings['max_attempts']) }}">
                            </div>

                            <hr>

                            <div class="col-md-12 mb-3 mt-3">
                                <label for="recaptcha" class="form-label">@lang('l.Recaptcha')</label>
                                <div class="d-flex align-items-center gap-4">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="recaptcha" value="0">
                                        <input type="checkbox" class="form-check-input me-2" id="recaptcha" name="recaptcha" value="1"
                                            style="width: 3rem; height: 1.5rem;"
                                            {{ old('recaptcha', $settings['recaptcha']) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="recaptcha_site_key" class="form-label">@lang('l.Recaptcha Site Key')</label>
                                <input type="text" class="form-control" id="recaptcha_site_key" name="recaptcha_site_key"
                                    value="{{ old('recaptcha_site_key', $settings['recaptcha_site_key']) }}"
                                    {{ !old('recaptcha', $settings['recaptcha']) ? 'disabled' : '' }}>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="recaptcha_secret" class="form-label">@lang('l.Recaptcha Secret Key')</label>
                                <input type="text" class="form-control" id="recaptcha_secret" name="recaptcha_secret"
                                    value="{{ old('recaptcha_secret', $settings['recaptcha_secret']) }}"
                                    {{ !old('recaptcha', $settings['recaptcha']) ? 'disabled' : '' }}>
                            </div>
                        </div>

                        <hr>

                        <div class="row g-3 mt-3 mb-5">
                            <h6 class="mb-3">@lang('l.SMTP settings')</h6>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="siteEmail" name="email"
                                        value="{{ $settings['email'] }}" placeholder="@lang('l.Email')">
                                    <label for="siteEmail">@lang('l.Email')</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="emailPassword"
                                        name="MAIL_PASSWORD" value="{{ $settings['MAIL_PASSWORD'] }}"
                                        placeholder="@lang('l.Password')">
                                    <label for="emailPassword">@lang('l.Password')</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="emailServer" name="MAIL_HOST"
                                        value="{{ $settings['MAIL_HOST'] }}" placeholder="@lang('l.SMTP server')">
                                    <label for="emailServer">@lang('l.SMTP server')</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="emailPort" name="MAIL_PORT"
                                        value="{{ $settings['MAIL_PORT'] }}" placeholder="@lang('l.Port')">
                                    <label for="emailPort">@lang('l.Port')</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="emailProtocol" name="MAIL_ENCRYPTION"  data-select2-selector="language">
                                        <option value="tls"
                                            {{ $settings['MAIL_ENCRYPTION'] == 'tls' ? 'selected' : '' }}>TLS
                                        </option>
                                        <option value="ssl"
                                            {{ $settings['MAIL_ENCRYPTION'] == 'ssl' ? 'selected' : '' }}>SSL
                                        </option>
                                    </select>
                                    <label for="emailProtocol">@lang('l.Encryption type')</label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row mt-4">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs mb-4" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#google" type="button">
                                        <i class="fab fa-google me-2 text-danger"></i>@lang('l.Google')
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#facebook" type="button">
                                        <i class="fab fa-facebook me-2 text-dark"></i>@lang('l.Facebook')
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#twitter" type="button">
                                        <i class="fab fa-twitter me-2 text-info"></i>@lang('l.Twitter')
                                    </button>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content">
                                <!-- Google Tab -->
                                <div class="tab-pane fade show active" id="google">
                                    <div class="card border">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">
                                                <i class="fab fa-google me-2 text-danger"></i>@lang('l.Google Authentication')
                                            </h5>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="status-indicator">
                                                    <span class="badge rounded-circle p-2 {{ $settings['googleLogin'] == 1 ? 'bg-success' : 'bg-danger' }}">
                                                        <i class="fas fa-circle"></i>
                                                    </span>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="googleLogin" value="0">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        onchange="this.value = this.checked ? '1' : '0'"
                                                        name="googleLogin"
                                                        value="{{ $settings['googleLogin'] }}"
                                                        {{ $settings['googleLogin'] == 1 ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body mt-3">
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="GOOGLE_CLIENT_ID"
                                                            value="{{ $settings['GOOGLE_CLIENT_ID'] }}">
                                                        <label>@lang('l.Google ID')</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="GOOGLE_CLIENT_SECRET"
                                                            value="{{ $settings['GOOGLE_CLIENT_SECRET'] }}">
                                                        <label>@lang('l.Google Secret')</label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="alert alert-info d-flex align-items-center">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        <div>
                                                            <strong>@lang('l.Redirect URL')</strong>
                                                            <code>{{ url('auth/google/callback') }}</code>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Facebook Tab -->
                                <div class="tab-pane fade" id="facebook">
                                    <div class="card border">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">
                                                <i class="fab fa-facebook me-2 text-dark"></i>@lang('l.Facebook Authentication')
                                            </h5>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="status-indicator">
                                                    <span class="badge rounded-circle p-2 {{ $settings['facebookLogin'] == 1 ? 'bg-success' : 'bg-danger' }}">
                                                        <i class="fas fa-circle"></i>
                                                    </span>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="facebookLogin" value="0">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        onchange="this.value = this.checked ? '1' : '0'"
                                                        name="facebookLogin"
                                                        value="{{ $settings['facebookLogin'] }}"
                                                        {{ $settings['facebookLogin'] == 1 ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body mt-3">
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="FACEBOOK_CLIENT_ID"
                                                            value="{{ $settings['FACEBOOK_CLIENT_ID'] }}">
                                                        <label>@lang('l.Facebook ID')</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="FACEBOOK_CLIENT_SECRET"
                                                            value="{{ $settings['FACEBOOK_CLIENT_SECRET'] }}">
                                                        <label>@lang('l.Facebook Secret')</label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="alert alert-info d-flex align-items-center">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        <div>
                                                            <strong>@lang('l.Redirect URL')</strong>
                                                            <code>{{ url('auth/facebook/callback') }}</code>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Twitter Tab -->
                                <div class="tab-pane fade" id="twitter">
                                    <div class="card border">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">
                                                <i class="fab fa-twitter me-2 text-info"></i>@lang('l.Twitter Authentication')
                                            </h5>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="status-indicator">
                                                    <span class="badge rounded-circle p-2 {{ $settings['twitterLogin'] == 1 ? 'bg-success' : 'bg-danger' }}">
                                                        <i class="fas fa-circle"></i>
                                                    </span>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="twitterLogin" value="0">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        onchange="this.value = this.checked ? '1' : '0'"
                                                        name="twitterLogin"
                                                        value="{{ $settings['twitterLogin'] }}"
                                                        {{ $settings['twitterLogin'] == 1 ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body mt-3">
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="TWITTER_CLIENT_API_KEY"
                                                            value="{{ $settings['TWITTER_CLIENT_API_KEY'] }}">
                                                        <label>@lang('l.Twitter ID')</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="TWITTER_CLIENT_API_SECRET_KEY"
                                                            value="{{ $settings['TWITTER_CLIENT_API_SECRET_KEY'] }}">
                                                        <label>@lang('l.Twitter Secret')</label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="alert alert-info d-flex align-items-center">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        <div>
                                                            <strong>@lang('l.Redirect URL')</strong>
                                                            <code>{{ url('auth/twitter/callback') }}</code>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-4">

                    <div class="col-12 mt-4">
                        <h6 class="mb-3">@lang('l.Contact settings')</h6>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="email1" class="form-label fw-medium">
                                    @lang('l.Email')
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $settings['email1'] }}"
                                        id="email1" name="email1" placeholder="@lang('l.Email 1')">
                                </div>
                            </div>
                            {{-- <div class="form-group col-md-6">
                                <label for="email2" class="form-label fw-medium">
                                    @lang('l.Email') 2
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $settings['email2'] }}"
                                        id="email2" name="email2" placeholder="@lang('l.Email 2')">
                                </div>
                            </div> --}}
                            <div class="form-group col-md-6">
                                <label for="phone1" class="form-label fw-medium">
                                    @lang('l.Phone')
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $settings['phone1'] }}"
                                        id="phone1" name="phone1" placeholder="@lang('l.Phone')">
                                </div>
                            </div>
                            {{-- <div class="form-group col-md-6">
                                <label for="phone2" class="form-label fw-medium">
                                    @lang('l.Phone') 2
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $settings['phone2'] }}"
                                        id="phone2" name="phone2" placeholder="@lang('l.Phone')">
                                </div>
                            </div> --}}
                            <div class="form-group col-md-6">
                                <label for="siteWhatsapp" class="form-label fw-medium">
                                    @lang('l.Whatsapp')
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $settings['whatsapp'] }}"
                                        id="siteWhatsapp" name="whatsapp" placeholder="@lang('l.Whatsapp')">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="facebook" class="form-label fw-medium">
                                    </i>@lang('l.Facebook')
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $settings['facebook'] }}"
                                        id="facebook" name="facebook" placeholder="@lang('l.Facebook')">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="twitter" class="form-label fw-medium">
                                    @lang('l.Twitter')
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $settings['twitter'] }}"
                                        id="twitter" name="twitter" placeholder="@lang('l.Twitter')">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="instagram" class="form-label fw-medium">
                                    @lang('l.Instagram')
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $settings['instagram'] }}"
                                        id="instagram" name="instagram" placeholder="@lang('l.Instagram')">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="youtube" class="form-label fw-medium">
                                    @lang('l.Youtube')
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $settings['youtube'] }}"
                                        id="youtube" name="youtube" placeholder="@lang('l.Youtube')">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="linkedin" class="form-label fw-medium">
                                    @lang('l.Linkedin')
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $settings['linkedin'] }}"
                                        id="linkedin" name="linkedin" placeholder="@lang('l.Linkedin')">
                                </div>
                            </div>
                            {{-- <div class="form-group col-md-6">
                                <label for="play_store_link" class="form-label fw-medium">
                                    @lang('l.Play Store Link')
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $settings['play_store_link'] }}"
                                        id="play_store_link" name="play_store_link" placeholder="@lang('l.Play Store Link')">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="app_store_link" class="form-label fw-medium">
                                    @lang('l.App Store Link')
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $settings['app_store_link'] }}"
                                        id="app_store_link" name="app_store_link" placeholder="@lang('l.App Store Link')">
                                </div>
                            </div> --}}
                            <div class="form-group col-md-12">
                                <label for="address" class="form-label fw-medium">
                                    @lang('l.Address')
                                </label>
                                <div class="input-group">
                                    <textarea class="form-control" id="address" rows="4" name="address">{{ $settings['address'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-5">

                    <div class="col-12 mt-4">
                        <h6 class="mb-3">@lang('l.Themes')</h6>
                        <div class="row">
                            @foreach ($themes as $theme)
                                <div class="col-md-4">
                                    <div class="theme-card">
                                        <input type="radio" name="theme" id="theme-{{ $theme->id }}"
                                            value="{{ $theme->name }}"
                                            {{ $settings['theme'] == $theme->name ? 'checked' : '' }}
                                            class="theme-input">
                                        <label for="theme-{{ $theme->id }}"
                                            class="theme-label {{ $settings['theme'] == $theme->name ? 'selected' : '' }}">
                                            <div class="theme-preview">
                                                <img src="{{ asset($theme->image) }}" alt="{{ $theme->name }}"
                                                    class="theme-image">
                                            </div>
                                            <span class="theme-name mt-2">{{ ucfirst($theme->name) }}</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <hr class="mt-5">

                    <div class="col-12 mt-4">
                        <h6 class="mb-3">@lang('l.Coding settings')</h6>
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Header Code Section -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        @lang('l.headerCode')
                                    </label>
                                    <textarea class="form-control code-editor" name="headerCode"
                                        rows="8" placeholder="@lang('l.Enter header code here...')"
                                        style="font-family: monospace;">{{ $settings['headerCode'] ?? '' }}</textarea>
                                </div>

                                <!-- Footer Code Section -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        @lang('l.footerCode')
                                    </label>
                                    <textarea class="form-control code-editor" name="footerCode"
                                        rows="8" placeholder="@lang('l.Enter footer code here...')"
                                        style="font-family: monospace;">{{ $settings['footerCode'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- أزرار التحكم -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary">
                             @lang('l.Save Changes')
                        </button>
                    </div>
                </div>
            </form>

        </div>
    @endcan
@endsection

@section('js')

<script>
    document.getElementById('recaptcha').addEventListener('change', function() {
        const siteKeyInput = document.getElementById('recaptcha_site_key');
        const secretKeyInput = document.getElementById('recaptcha_secret');

        siteKeyInput.disabled = !this.checked;
        secretKeyInput.disabled = !this.checked;
    });
    </script>

<style>
    .theme-label {
        display: block;
        cursor: pointer;
        border: 2px solid transparent;
        padding: 5px;
        border-radius: 5px;
        transition: border-color 0.3s;
    }

    .theme-image {
        width: 100%;
        height: auto;
        border-radius: 5px;
    }

    .theme-name {
        display: block;
        text-align: center;
        margin-top: 5px;
        font-weight: bold;
    }
</style>


@endsection
