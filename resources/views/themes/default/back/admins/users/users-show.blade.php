@extends('themes.default.layouts.back.master')

@section('title')
    {{ $user->firstname }} {{ $user->lastname }}
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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">@lang('l.User Account') /</span> {{ $user->firstname }}</h4>
        @can('show users')
            <div class="row">
                <div class="col-md-12">
                    <div id="page1">
                        <div class="card mb-4">
                            <h5 class="card-header">@lang('l.Profile Details')</h5>
                            <!-- Account -->
                            <div class="card-body">
                                <form class="d-flex align-items-start align-items-sm-center gap-4" method="post" action="#"
                                    enctype="multipart/form-data"> @csrf
                                    <img src="{{ $user->photo }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded"
                                        id="uploadedAvatar" style="max-width: 100px; margin: 10px;" />
                                </form>
                            </div>
                            <hr class="my-0" />
                            <div class="card-body">
                                <form id="formAccountSettings" method="POST">
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label for="firstName" class="form-label">@lang('l.First Name')</label>
                                            <input class="form-control" type="text" id="firstName" name="firstname"
                                                value="{{ $user->firstname }}" autofocus readonly />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="lastName" class="form-label">@lang('l.Last Name')</label>
                                            <input class="form-control" type="text" name="lastname" id="lastName"
                                                value="{{ $user->lastname }}" readonly />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="email" class="form-label">@lang('l.E-mail')</label>
                                            <input class="form-control" type="text" id="email" name="email"
                                                value="{{ $user->email }}" readonly />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label" for="phone">@lang('l.Phone Number')</label><br>
                                            <input type="tel" id="phone" name="phone" value="{{ $user->phone }}"
                                                class="form-control" readonly>
                                            <input type="hidden" id="phone_code" name="phone_code" required>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label" for="country">@lang('l.Country')</label>
                                            <select id="country" class="select2 form-control" name="country" readonly>
                                                <option value="">{{ $user->country }}</option>
                                            </select>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="city" class="form-label">@lang('l.City')</label>
                                            <input type="text" class="form-control" id="city" name="city"
                                                value="{{ $user->city }}" readonly/>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label for="address" class="form-label">@lang('l.Address')</label>
                                            <input type="text" class="form-control" id="address" name="address"
                                                value="{{ $user->address }}" readonly/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="state" class="form-label">@lang('l.State')</label>
                                            <input class="form-control" type="text" id="state" name="state"
                                                value="{{ $user->state }}" readonly/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="zipCode" class="form-label">@lang('l.Zip Code')</label>
                                            <input type="text" class="form-control" id="zipCode" name="zip_code"
                                                value="{{ $user->zip_code }}" maxlength="8"readonly />
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">@lang('l.Back')</a>
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
