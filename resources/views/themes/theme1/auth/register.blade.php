@extends('themes.default.auth.layout')

@section('title')
    @lang('l.Register')
@endsection

@section('description')
@endsection

@section('page-css')
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
    <main class="auth-minimal-wrapper">
        <div class="auth-minimal-inner">
            <div class="minimal-card-wrapper">
                <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative">
                    <div
                        class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
                        <img src="{{ asset($settings['favicon']) }}" alt="" class="img-fluid">
                    </div>
                    <div class="card-body p-sm-5">
                        <h2 class="fs-20 fw-bolder mb-4">@lang('l.Register')</h2>

                        <form id="formAuthentication" class="w-100 mt-4 pt-2" method="post"
                            action="{{ route('register') }}">
                            @csrf
                            <div class="mb-4" style="display: flex;">
                                <div class="col-md-6" style="margin-right: 2px;">
                                    <input type="text" class="form-control" id="firstname" name="firstname"
                                        placeholder="@lang('l.Enter your first name')" autofocus required />
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('firstname')" />
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="lastname" name="lastname"
                                        placeholder="@lang('l.Enter your last name')" required />
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('lastname')" />
                                </div>
                            </div>
                            <div class="mb-4">
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="@lang('l.Enter your email')" required />
                                @error('email')
                                    <div class="mt-2" style="color: red; padding-left: 10px; padding-right: 10px;">
                                        @lang('l.This email is already in use!')
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                {{-- <label class="form-label" for="phone">@lang('l.Mobile')</label> --}}
                                <input type="hidden" id="phone_code" name="phone_code" required>
                                <div class="input-group">
                                    <input type="tel" id="phone" name="phone"
                                        class="form-control multi-steps-mobile" required />
                                </div>
                                <x-input-error class="mt-2 error-message" :messages="$errors->get('phone')" />
                            </div>
                            <div class="mb-4">
                                <select id="level_id" class="form-control" name="level_id"
                                    aria-describedby="level_id" required>
                                    <option value="">Select your level</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2 error-message" :messages="$errors->get('level')" />
                            </div>
                            {{-- Fake input to prevent spam bots --}}
                            <input type="text" name="fax_number" style="display:none">
                            <div class="mb-4">
                                <input type="password" id="password" class="form-control" name="password"
                                    aria-describedby="password" minlength="8"
                                    placeholder="Enter your password"
                                    required />
                                <x-input-error class="mt-2 error-message" :messages="$errors->get('password')" />
                            </div>
                            <div class="mb-4">
                                <input type="password" id="multiStepsConfirmPass" name="password_confirmation"
                                    class="form-control" minlength="8"
                                    placeholder="Enter your password again"
                                    aria-describedby="multiStepsConfirmPass2" required />
                            </div>
                            @if ($settings['recaptcha'])
                                <div class="g-recaptcha mb-4" data-sitekey="{{ config('app.recaptcha.key') }}">
                                </div>
                                {{-- disable the submit function in js file and use submit function down --}}
                                <input type="hidden" name="disabled-submit" id="disabled-submit">
                            @endif
                            <div class="mt-5">
                                <button class="btn btn-lg btn-primary w-100" id="submitButton"
                                    type="submit">@lang('l.Sign up')</button>
                            </div>
                        </form>

                        <div class="mt-5 text-muted">
                            <span>@lang('l.Already have an account?')</span>
                            <a href="{{ route('login') }}" class="fw-bold">
                                <span>@lang('l.Sign in instead')</span>
                            </a>
                        </div>

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
                                    <a href="{{ route('auth.facebook') }}" class="btn btn-light-brand flex-fill"
                                        data-bs-toggle="tooltip" data-bs-trigger="hover" title="Login with Facebook">
                                        <i class="feather-facebook"></i>
                                    </a>
                                @endif
                                @if ($settings['googleLogin'])
                                    <a href="{{ route('auth.google') }}" class="btn btn-light-brand flex-fill"
                                        data-bs-toggle="tooltip" data-bs-trigger="hover" title="Login with Google">
                                        <i class="fa fa-google"></i>
                                    </a>
                                @endif
                                @if ($settings['twitterLogin'])
                                    <a href="{{ route('auth.twitter') }}" class="btn btn-light-brand flex-fill"
                                        data-bs-toggle="tooltip" data-bs-trigger="hover" title="Login with Twitter">
                                        <i class="feather-twitter"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection


@section('page-scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script>
    $(document).ready(function() {
        // إنشاء حقل إدخال رقم الهاتف بشكل دولي
        var input = document.querySelector("#phone");
        var iti = window.intlTelInput(input, {
            initialCountry: "eg",
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
            preferredCountries: ["eg", "sa", "ae"], // يمكن تعديل الدول المفضلة هنا
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
    });
</script>

    @if ($settings['recaptcha'])
        <!-- google recaptcha required -->
        <script>
            window.addEventListener('load', () => {
                const $recaptcha = document.querySelector('#g-recaptcha-response');
                if ($recaptcha) {
                    $recaptcha.setAttribute('required', 'required');
                }
            })
            // submit the form by js
            document.addEventListener('DOMContentLoaded', function() {
                const submitButton = document.getElementById('submitButton');
                const form = document.getElementById('formAuthentication');

                submitButton.addEventListener('click', function(e) {
                    e.preventDefault();

                    // التحقق من جميع الحقول المطلوبة
                    const requiredFields = form.querySelectorAll('[required]');
                    let isValid = true;

                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('is-invalid');
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    });

                    if (!isValid) {
                        return;
                    }

                    // إذا تم التحقق من جميع الحقول، قم بإرسال النموذج
                    form.submit();
                });
            });
        </script>
    @endif
@endsection
