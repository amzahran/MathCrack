@extends('themes.default.layouts.back.master')

@section('title')
   @lang('l.Add New User')
@endsection

@section('css')
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
    <!-- تضمين ملفات CSS اللازمة -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
@endsection


@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">@lang('l.User Account') /</span> @lang('l.Add')</h4>
        @can('add users')
            <div class="row">
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

                <div class="col-md-12">
                    <div id="page1">
                        <div class="card mb-4">
                            <h5 class="card-header">@lang('l.Profile Details')</h5>
                            <hr class="my-0" />
                            <div class="card-body">
                                <form id="formAccountSettings" method="POST"
                                    action="{{ route('dashboard.admins.users-store') }}"> @csrf
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label for="firstName" class="form-label">@lang('l.First Name')</label>
                                            <input class="form-control" type="text" id="firstName" name="firstname"
                                                autofocus required />
                                            <x-input-error class="mt-2  error-message" :messages="$errors->get('firstname')" />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="lastName" class="form-label">@lang('l.Last Name')</label>
                                            <input class="form-control" type="text" name="lastname" id="lastName"
                                                required />
                                            <x-input-error class="mt-2  error-message" :messages="$errors->get('lastname')" />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="email" class="form-label">@lang('l.E-mail')</label>
                                            <input class="form-control" type="text" id="email" name="email"
                                                placeholder="john.doe@example.com" required />
                                            <x-input-error class="mt-2  error-message" :messages="$errors->get('email')" />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label" for="phone">@lang('l.Phone Number')</label><br>
                                            <input type="tel" id="phone" name="phone" class="form-control" required>
                                            <input type="hidden" id="phone_code" name="phone_code">
                                            <x-input-error class="mt-2  error-message" :messages="$errors->get('phone')" />
                                            <x-input-error class="mt-2  error-message" :messages="$errors->get('phone_code')" />

                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label class="form-label" for="country">@lang('l.Country')</label>
                                            <select id="country" class="select2 form-select" name="country" data-select2-selector="language">
                                                <option value="">{{ __('l.Select') }}</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->name }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                            <x-input-error class="mt-2  error-message" :messages="$errors->get('country')" />
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label for="city" class="form-label">@lang('l.City')</label>
                                            <input class="form-control" type="text" id="city" name="city"
                                                placeholder="{{ __('l.City') }}" />
                                            <x-input-error class="mt-2  error-message" :messages="$errors->get('city')" />
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label for="state" class="form-label">@lang('l.State')</label>
                                            <input class="form-control" type="text" id="state" name="state"
                                                placeholder="{{ __('l.State') }}" />
                                            <x-input-error class="mt-2  error-message" :messages="$errors->get('state')" />
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label for="zipCode" class="form-label">@lang('l.Zip Code')</label>
                                            <input type="text" class="form-control" id="zipCode" name="zip_code"
                                                placeholder="11511" maxlength="12" />
                                            <x-input-error class="mt-2  error-message" :messages="$errors->get('zip_code')" />
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label for="address" class="form-label">@lang('l.Address')</label>
                                            <input type="text" class="form-control" id="address" name="address"
                                                placeholder="{{ __('l.Address') }}" />
                                            <x-input-error class="mt-2  error-message" :messages="$errors->get('address')" />
                                        </div>
                                        <div class="mb-3 col-md-2">
                                            <label class="form-label" for="role">@lang('l.Role')</label>
                                            <select id="role" class="select2 form-select" name="role" required data-select2-selector="language">
                                                <option value="">{{ __('l.Select') }}</option>
                                                @foreach ($roles as $role)
                                                    @if ($role->name != 'root')
                                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <x-input-error class="mt-2  error-message" :messages="$errors->get('role')" />
                                        </div>
                                        <div class="mb-3 col-md-5">
                                            <label for="pass" class="form-label">@lang('l.Password')</label>
                                            <input type="password" class="form-control" id="pass"
                                                name="password" value="" placeholder="********" minlength="8"
                                                required />
                                            <x-input-error class="mt-2  error-message" :messages="$errors->get('password')" />
                                        </div>
                                        <div class="mb-3 col-md-5">
                                            <label for="password_confirmation" class="form-label">@lang('l.Confirm Password')</label>
                                            <input type="password" class="form-control" id="password_confirmation"
                                                name="password_confirmation" value="" placeholder="********"
                                                minlength="8" required />
                                            <x-input-error class="mt-2  error-message" :messages="$errors->get('password_confirmation')" />
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary me-2">@lang('l.Add Account')</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /Account -->
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endsection


@section('js')
    <!-- تضمين ملفات JS اللازمة -->
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

    <!-- تهيئة الحقل -->
    <script>
        $(document).ready(function() {
            // إنشاء حقل إدخال رقم الهاتف بشكل دولي
            var input = document.querySelector("#phone");
            var iti = window.intlTelInput(input, {
                initialCountry: "gb",
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
        });
    </script>
@endsection
