@if(!auth()->user()->roles()->exists())
    @php
        $layout = 'themes.default.layouts.back.student-master';
    @endphp
@else
    @php
        $layout = 'themes.default.layouts.back.master';
    @endphp
@endif
@extends($layout)


@section('title')
    @lang('l.My Account')
@endsection

@section('css')
    <!-- تضمين ملفات CSS اللازمة -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
    <style>
        .error-message {
            color: red;
        }

        .iti {
            width: 100%;
        }

        .iti__country {
            direction: ltr;
        }

        .iti__country-list {
            left: 0;
        }

        #phone {
            text-align: left;
        }

        .iti__selected-flag {
            direction: ltr;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="row">
            <div class="col-md-12">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                <div id="page1">
                    <div class="card mb-4">
                        <h5 class="card-header">@lang('l.Profile Details')</h5>
                        <!-- Account -->
                        <div class="card-body">
                            <form class="d-flex align-items-start align-items-sm-center gap-4" method="post"
                                action="{{ route('dashboard.profile-uploadPhoto') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="position-relative">
                                    <img src="{{ auth()->user()->photo }}" alt="user-avatar"
                                        class="d-block w-px-100 h-px-100 rounded" width="100" id="uploadedAvatar" />
                                    <div id="previewOverlay" class="position-absolute top-0 start-0 w-100 h-100 rounded d-none"
                                         style="background-color: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-magnifying-glass text-white" style="font-size: 1.5rem;"></i>
                                    </div>
                                </div>
                                <div class="button-wrapper">
                                    <label for="upload" class="btn btn-secondary me-2 mb-3" tabindex="0"
                                        style="color: #fff !important;">
                                        <span class="d-none d-sm-block">@lang('l.Upload new photo')</span>
                                        <i class="fa-solid fa-upload d-block d-sm-none"></i>
                                        <input type="file" id="upload" name="photo" class="account-file-input"
                                            required hidden accept="image/png, image/jpeg" />
                                    </label>
                                    <button type="submit" class="btn btn-primary account-image-reset mb-3">
                                        <i class="fa-solid fa-save d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">@lang('l.Save')</span>
                                    </button>
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('photo')" />
                                </div>
                            </form>
                        </div>
                        <hr class="my-0" />
                        <div class="card-body">
                            <form id="4" method="POST" action="{{ route('dashboard.profile-update') }}">
                                @csrf @method('patch')
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="firstName" class="form-label">@lang('l.First Name')</label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control" type="text" id="firstName" name="firstname"
                                                value="{{ auth()->user()->firstname }}" placeholder="@lang('l.Enter your first name')"
                                                autofocus required />
                                        </div>
                                        <x-input-error class="mt-2  error-message" :messages="$errors->get('firstname')" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="lastName" class="form-label">@lang('l.Last Name')</label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control" type="text" name="lastname" id="lastName"
                                                value="{{ auth()->user()->lastname }}" placeholder="@lang('l.Enter your last name')"
                                                required />
                                        </div>
                                        <x-input-error class="mt-2  error-message" :messages="$errors->get('lastname')" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="email" class="form-label">@lang('l.E-mail')</label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control" type="text" id="email" name="email"
                                                value="{{ auth()->user()->email }}" placeholder="john.doe@example.com"
                                                readonly disabled />
                                        </div>
                                        <x-input-error class="mt-2  error-message" :messages="$errors->get('email')" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label" for="phone">@lang('l.Phone Number')</label><br>
                                        <input type="tel" id="phone" name="phone"
                                            value="{{ auth()->user()->phone }}" class="form-control" required>
                                        <input type="hidden" id="phone_code" name="phone_code" required>
                                        <x-input-error class="mt-2  error-message" :messages="$errors->get('phone')" />
                                        <x-input-error class="mt-2  error-message" :messages="$errors->get('phone_code')" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label" for="country">@lang('l.Country')</label>
                                        <select id="country" class="select2 form-select" name="country" data-select2-selector="language">
                                            <option value="">@lang('l.Select')</option>
                                            @foreach ($countries as $country)
                                                <option {{ auth()->user()->country == $country->name ? 'selected' : '' }}
                                                    value="{{ $country->name }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2 error-message" :messages="$errors->get('country')" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="city" class="form-label">@lang('l.City')</label>
                                        <div class="input-group input-group-merge">
                                            <input type="text" class="form-control" id="city" name="city"
                                                value="{{ auth()->user()->city }}" placeholder="@lang('l.Enter your city')" />
                                        </div>
                                        <x-input-error class="mt-2  error-message" :messages="$errors->get('city')" />
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label for="address" class="form-label">@lang('l.Address')</label>
                                        <div class="input-group input-group-merge">
                                            <input type="text" class="form-control" id="address" name="address"
                                                value="{{ auth()->user()->address }}" placeholder="@lang('l.Enter your address')" />
                                        </div>
                                        <x-input-error class="mt-2  error-message" :messages="$errors->get('address')" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="state" class="form-label">@lang('l.State')</label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control" type="text" id="state" name="state"
                                                value="{{ auth()->user()->state }}" placeholder="@lang('l.Enter your state')" />
                                        </div>
                                        <x-input-error class="mt-2  error-message" :messages="$errors->get('state')" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="zipCode" class="form-label">@lang('l.Zip Code')</label>
                                        <div class="input-group input-group-merge">
                                            <input type="text" class="form-control" id="zipCode" name="zip_code"
                                                value="{{ auth()->user()->zip_code }}" placeholder="35536"
                                                maxlength="8" />
                                        </div>
                                        <x-input-error class="mt-2  error-message" :messages="$errors->get('zip_code')" />
                                    </div>
                                    @if (auth()->user()->level_id)
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label" for="level_id">@lang('l.Level')</label>
                                            <select id="level_id" class="select2 form-select" name="level_id" data-select2-selector="language">
                                                <option value="">@lang('l.Select')</option>
                                                @foreach ($levels as $level)
                                                    <option value="{{ $level->id }}" {{ $level->id == auth()->user()->level_id ? 'selected' : '' }}>{{ $level->name }}</option>
                                                @endforeach
                                            </select>
                                            <x-input-error class="mt-2 error-message" :messages="$errors->get('level_id')" />
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary me-2">@lang('l.Save changes')</button>
                                </div>
                            </form>
                        </div>
                        <!-- /Account -->
                    </div>
                    {{-- <div class="card">
                        <h5 class="card-header">@lang('l.Delete Account')</h5>
                        <div class="card-body">
                            <div class="mb-3 col-12 mb-0">
                                <div class="alert alert-warning">
                                    <h5 class="alert-heading mb-1">@lang('l.Are you sure you want to delete your account?')</h5>
                                    <p class="mb-0">@lang('l.Once you delete your account, there is no going back. Please be certain.')</p>
                                </div>
                            </div>
                            <form id="formAccountDeactivation" method="post"
                                action="{{ route('dashboard.profile-delete') }}">@csrf
                                @method('delete')
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" name="accountActivation"
                                        id="accountActivation" required />
                                    <label class="form-check-label" for="accountActivation">@lang('l.I confirm my account deactivation')</label>
                                </div>
                                <button type="submit"
                                    class="btn btn-danger deactivate-account">@lang('l.Deactivate Account')</button>
                            </form>
                        </div>
                    </div> --}}
                </div>
                <div id="page2">

                    @if (session('token'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            @lang('l.This is your Key') <br>
                            <div class="d-flex align-items-center">
                                <small style="color:green;" id="apiToken">{{ session('token') }}</small>
                                <i class="bx bx-copy ms-2 cursor-pointer" onclick="copyToken()"
                                    style="font-size: 1.2rem;"></i>
                            </div>
                            @lang('l.Please copy it as it will not be displayed again!')
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                            <script>
                                function copyToken() {
                                    var token = document.getElementById('apiToken');
                                    navigator.clipboard.writeText(token.textContent);
                                    toastr.success('@lang('l.Token copied to clipboard!')');
                                }
                            </script>
                        </div>
                    @endif
                    <!-- Change Password -->
                    <div class="card mb-4">
                        <h5 class="card-header">@lang('l.Change Password')</h5>
                        <div class="card-body">
                            <form id="formAccountSettings" method="POST"
                                action="{{ route('dashboard.profile-updatePassword') }}">
                                @csrf @method('put')
                                <div class="row">
                                    <div class="mb-3 col-md-6 form-password-toggle">
                                        <label class="form-label" for="currentPassword">@lang('l.Current Password')</label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control" type="password" name="current_password"
                                                id="currentPassword" required
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                        </div>
                                        @error('current_password')
                                            <div class="mt-2  error-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-6 form-password-toggle">
                                        <label class="form-label" for="newPassword">@lang('l.New Password')</label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control" type="password" id="newPassword" name="password"
                                                required
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                        </div>
                                        @error('password')
                                            <div class="mt-2  error-message">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-6 form-password-toggle">
                                        <label class="form-label" for="confirmPassword">@lang('l.Confirm New Password')</label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control" type="password" name="password_confirmation"
                                                id="confirmPassword" required
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                        </div>
                                        @error('password_confirmation')
                                            <div class="mt-2  error-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 mb-4">
                                        <h6>@lang('l.Password Requirements')</h6>
                                        <ul class="ps-3 mb-0">
                                            <li class="mb-1">@lang('l.Minimum 8 characters long - the more, the better')</li>
                                            <li class="mb-1">@lang('l.At least one uppercase letter, one lowercase letter')</li>
                                            <li>@lang('l.At least one number, symbol, or whitespace character')</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <button type="submit"
                                            class="btn btn-primary me-2 waves-effect waves-light">@lang('l.Save changes')</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--/ Change Password -->

                    <!-- Two-Factor Authentication -->
                    {{-- <div class="card mb-4">
                        <h5 class="card-header">@lang('l.Two-Factor Authentication')</h5>
                        <div class="card-body">
                            @if (empty(auth()->user()->google2fa_secret))
                                <!-- Setup 2FA Section -->
                                <div class="mb-4">
                                    <h6 class="fw-semibold mb-3">@lang('l.Two-factor authentication is not enabled yet.')</h6>
                                    <p>
                                        @lang('l.Two-factor authentication adds an additional layer of security to your account by requiring more than just a password to log in.')
                                    </p>
                                    <button class="btn btn-primary mt-2" data-bs-toggle="modal"
                                        data-bs-target="#enable2FAModal">
                                        @lang('l.Enable Two-Factor Authentication')
                                    </button>
                                </div>

                                <!-- Enable 2FA Modal -->
                                <div class="modal fade" id="enable2FAModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">@lang('l.Setup Two-Factor Authentication')</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if (session('2fa_secret'))
                                                    <div class="text-center mb-4">
                                                        <p class="mb-2">@lang('l.Scan this QR code with your Google Authenticator app:')</p>
                                                        <div class="mb-4">
                                                            <div
                                                                style="display: inline-block; padding: 15px; background: white; border-radius: 5px; border: 1px solid #ddd;">
                                                                <img src="{!! session('qrImage') !!}" alt="QR Code"
                                                                    style="width: 200px; height: 200px;">
                                                            </div>
                                                        </div>
                                                        <p class="mb-2">@lang('l.Or enter this code manually:')</p>
                                                        <code class="d-block mb-4">{{ session('2fa_secret') }}</code>
                                                    </div>

                                                    <form action="{{ route('profile.2fa.enable') }}" method="POST">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="verify2FACode">@lang('l.Verification Code')</label>
                                                            <input type="text"
                                                                class="form-control @error('code') is-invalid @enderror"
                                                                id="verify2FACode" name="code" required maxlength="6"
                                                                placeholder="@lang('l.Enter 6-digit code')" />
                                                            @error('code')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="text-end">
                                                            <button type="button" class="btn btn-label-secondary"
                                                                data-bs-dismiss="modal">
                                                                @lang('l.Cancel')
                                                            </button>
                                                            <button type="submit" class="btn btn-primary">
                                                                @lang('l.Enable 2FA')
                                                            </button>
                                                        </div>
                                                    </form>
                                                @else
                                                    <div class="text-center">
                                                        <p>@lang('l.Click the button below to start the setup process')</p>
                                                        <a href="{{ route('profile.2fa.form') }}"
                                                            class="btn btn-primary">
                                                            @lang('l.Begin Setup')
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Manage 2FA Section -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="fw-semibold mb-1">@lang('l.Two-factor authentication is enabled')</h6>
                                            <p class="text-muted mb-0">@lang('l.Your account is secured with two-factor authentication')</p>
                                        </div>
                                        <span class="badge bg-success">@lang('l.Enabled')</span>
                                    </div>
                                    <button class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#disable2FAModal">
                                        @lang('l.Disable 2FA')
                                    </button>
                                </div>

                                <!-- Disable 2FA Modal -->
                                <div class="modal fade" id="disable2FAModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">@lang('l.Disable Two-Factor Authentication')</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('profile.2fa.disable') }}" method="POST">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <p class="mb-3">@lang('l.Please enter your verification code to disable 2FA')</p>
                                                        <label class="form-label"
                                                            for="disable2FACode">@lang('l.Verification Code')</label>
                                                        <input type="text"
                                                            class="form-control @error('code') is-invalid @enderror"
                                                            id="disable2FACode" name="code" required autofocus
                                                            minlength="6" maxlength="6"
                                                            placeholder="@lang('l.Enter 6-digit code')" />
                                                        @error('code')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" class="btn btn-label-secondary"
                                                            data-bs-dismiss="modal">
                                                            @lang('l.Cancel')
                                                        </button>
                                                        <button type="submit" class="btn btn-danger">
                                                            @lang('l.Disable 2FA')
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div> --}}
                    <!--/ Two-Factor Authentication -->

                    <!-- Create an API key -->
                    {{-- <div class="card mb-4">
                        <h5 class="card-header">@lang('l.Create an API key')</h5>
                        <div class="row">
                            <div class="col-md-5 order-md-0 order-1">
                                <div class="card-body">
                                    <form id="formAccountSettingsApiKey" method="POST"
                                        action="{{ route('dashboard.profile-apiCreate') }}"> @csrf
                                        <div class="row">
                                            <div class="mb-3 col-12">
                                                <label for="apiAccess" class="form-label">@lang('l.Choose the Api key type you want to create')</label>
                                                <select id="apiAccess" class="select2 form-select"required>
                                                    <option value="full">@lang('l.Full Control')</option>
                                                </select>
                                            </div>
                                            <div class="mb-3 col-12">
                                                <label for="apiKey" class="form-label">@lang('l.Name the API key')</label>
                                                <input type="text" class="form-control" id="apiKey" name="name"
                                                    required placeholder="Server Key 1" />
                                            </div>
                                            <div class="col-12">
                                                <button type="submit"
                                                    class="btn btn-secondary me-2 d-grid w-100">@lang('l.Create Key')</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-7 order-md-1 order-0">
                                <div class="text-center mt-4 mx-3 mx-md-0">
                                    <img src="{{ asset('assets/themes/default/img/illustrations/sitting-girl-with-laptop.png') }}"
                                        class="img-fluid" alt="Api Key Image" width="202" />
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <!--/ Create an API key -->

                    <!-- API Key List & Access -->
                    {{-- <div class="card mb-4">
                        <h5 class="card-header">@lang('l.API Key List & Access')</h5>
                        <div class="card-body">
                            <p>
                                @lang('l.An API key is a simple encrypted string that identifies an application without any principal. They are useful for accessing public data anonymously, and are used to associate API requests with your project for quota and billing.')
                            </p>
                            <div class="row">
                                <div class="col-md-12">
                                    @forelse ($apis as $api)
                                        <div
                                            class="bg-lighter rounded p-3 position-relative mb-3 d-flex justify-content-between align-items-center">
                                            <div>
                                                <h4 class="mb-0 me-3">{{ $api->name }}</h4>
                                                <span class="badge bg-secondary">@lang('l.Full Control')</span>
                                                <span class="text-muted d-block">@lang('l.Created on')
                                                    {{ $api->created_at }}</span>
                                            </div>
                                            <div class="dropdown api-key-actions ms-auto">
                                                <a class="btn dropdown-toggle text-muted hide-arrow p-0"
                                                    data-bs-toggle="dropdown">
                                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="{{ route('dashboard.profile-apiDelete') }}?name={{ encrypt($api->name) }}"
                                                        class="dropdown-item">
                                                        <i class="ti ti-trash me-2"></i>@lang('l.Delete')
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center">@lang('l.No data found')</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <!--/ API Key List & Access -->

                    <!-- Recent Devices -->
                    {{-- <div class="card mb-4">
                        <h5 class="card-header">@lang('l.Recent Devices')</h5>
                        <div class="table-responsive">
                            <table class="table border-top">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-truncate">@lang('l.Device & Browser')</th>
                                        <th class="text-truncate">@lang('l.IP Address')</th>
                                        <th class="text-truncate">@lang('l.Login Time')</th>
                                        <th class="text-truncate">@lang('l.Status')</th>
                                        <th class="text-truncate">@lang('l.Location')</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @php use UAParser\Parser; @endphp
                                    @forelse ($sessions->take(20) as $session)
                                        @php
                                            $parser = Parser::create();
                                            $result = $parser->parse($session->user_agent);

                                            $platform = $result->os->family;
                                            $browser = $result->ua->family;
                                        @endphp
                                        <tr>
                                            <td class="text-truncate">
                                                <div class="d-flex align-items-center">
                                                    <i
                                                        class="@if ($platform == 'Windows') fa-brands fa-windows text-info
                                                    @elseif($platform == 'Android') fa-brands fa-android text-success
                                                    @elseif($platform == 'Mac OS X') fa-brands fa-apple
                                                    @elseif($platform == 'iOS') fa-solid fa-mobile-screen text-danger
                                                    @elseif($platform == 'Linux') fa-brands fa-linux text-dark
                                                    @else fa-solid fa-question text-warning @endif me-2 fa-lg">
                                                    </i>
                                                    <div>
                                                        <strong>{{ $platform }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $browser }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-truncate">{{ $session->ip_address }}</td>
                                            <td class="text-truncate">
                                                {{ \Carbon\Carbon::parse($session->login_at)->format('Y/m/d h:i A') }}
                                            </td>
                                            <td class="text-truncate">
                                                @if ($session->login_successful)
                                                    <span class="badge bg-success">@lang('l.Successful')</span>
                                                @else
                                                    <span class="badge bg-danger">@lang('l.Failed')</span>
                                                @endif
                                            </td>
                                            <td class="text-truncate">
                                                {{ $session->location ?? __('l.Not Available') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">@lang('l.No data found')</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div> --}}
                    <!--/ Recent Devices -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- تضمين ملفات JS اللازمة -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        $(document).ready(function() {
            // إنشاء حقل إدخال رقم الهاتف بشكل دولي
            var input = document.querySelector("#phone");
            var iti = window.intlTelInput(input, {
                initialCountry: "us",
                geoIpLookup: function(callback) {
                    $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
                        var countryCode = (resp && resp.country) ? resp.country : "";
                        callback(countryCode);
                    });
                },
                nationalMode: false,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                separateDialCode: true,
                formatOnDisplay: true,
                preferredCountries: ["us", "ca", "gb"], // يمكن تعديل الدول المفضلة هنا
            });

            // تحديث حقل الخفي "phone_code" بشكل تلقائي عند فتح الصفحة
            var phone_code = document.querySelector("#phone_code");
            var currentDialCode = iti.getSelectedCountryData().dialCode;
            phone_code.value = currentDialCode;

            // تحديث حقل الخفي "phone_code" بشكل تلقائي عند تغيير الكود الدولي فقط
            input.addEventListener("countrychange", function() {
                var currentDialCode = iti.getSelectedCountryData().dialCode;
                phone_code.value = currentDialCode;
            });

            // معاينة الصورة قبل الرفع
            document.getElementById('upload').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    const previewImage = document.getElementById('uploadedAvatar');
                    const previewOverlay = document.getElementById('previewOverlay');

                    reader.onload = function(event) {
                        previewImage.src = event.target.result;

                        // إظهار أيقونة التكبير عند تمرير المؤشر
                        previewImage.parentElement.style.cursor = 'pointer';

                        // إظهار النافذة المنبثقة للصورة عند النقر على الصورة المصغرة
                        previewImage.onclick = function() {
                            Swal.fire({
                                imageUrl: event.target.result,
                                imageAlt: 'صورة البروفايل',
                                confirmButtonText: '@lang('l.Close')',
                                confirmButtonColor: '#696cff',
                                showClass: {
                                    popup: 'animate__animated animate__fadeIn faster'
                                },
                                hideClass: {
                                    popup: 'animate__animated animate__fadeOut faster'
                                }
                            });
                        };

                        // إظهار وإخفاء الطبقة العلوية عند تمرير المؤشر فوق الصورة
                        previewImage.parentElement.addEventListener('mouseenter', function() {
                            previewOverlay.classList.remove('d-none');
                        });

                        previewImage.parentElement.addEventListener('mouseleave', function() {
                            previewOverlay.classList.add('d-none');
                        });
                    };

                    reader.readAsDataURL(file);
                }
            });
        });
    </script>

    <script src="{{ asset('assets/themes/default/js/pages-account-settings-security.js') }}"></script>
    <script src="{{ asset('assets/themes/default/js/modal-enable-otp.js') }}"></script>

    <!-- إظهار المودال مع رسالة الخطأ إذا كان هناك خطأ في التحقق -->
    @if ($errors->has('code') && session('2fa_action'))
        document.addEventListener('DOMContentLoaded', function() {
        var modalId = "{{ session('2fa_action') === 'enable' ? 'enable2FAModal' : 'disable2FAModal' }}";
        var modal = new bootstrap.Modal(document.getElementById(modalId));
        modal.show();
        });
    @endif
@endsection
