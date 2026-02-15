<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
    dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr' }}" data-theme="theme-default"
    data-assets-path="{{ asset('assets/themes/default') }}/" data-template="vertical-menu-template"
    data-style="light">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{$exception->getMessage() ?: 'Forbidden'}}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset($settings['favicon']) }}" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
            rel="stylesheet" />

        <link rel="stylesheet" href="{{ asset('assets/themes/default/vendor/fonts/iconify-icons.css') }}" />

        <!-- Core CSS -->
        <!-- build:css assets/vendor/css/theme.css  -->
        <link rel="stylesheet" href="{{ asset('assets/themes/default/vendor/libs/pickr/pickr-themes.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/themes/default/vendor/css/core.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/themes/default/css/demo.css') }}" />


        <!-- Vendors CSS -->
        <link rel="stylesheet"
            href="{{ asset('assets/themes/default/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

        <!-- Page CSS -->
        <!-- Page -->
        <link rel="stylesheet" href="{{ asset('assets/themes/default/vendor/css/pages/page-misc.css') }}" />

        <!-- Helpers -->
        <script src="{{ asset('assets/themes/default/vendor/js/helpers.js') }}"></script>
        <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

        <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
        <script src="{{ asset('assets/themes/default/vendor/js/template-customizer.js') }}"></script>

        <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

        <script src="{{ asset('assets/themes/default/js/config.js') }}"></script>
    </head>

    <body>
        <!-- Content -->

        <!-- Error -->
        <div class="container-xxl container-p-y">
            <div class="misc-wrapper">
                <h1 class="mb-2 mx-2" style="line-height: 6rem;font-size: 6rem;">911</h1>
                <h4 class="mb-2 mx-2">{{__('l.Invalid Confirmation')}} ⚠️</h4>
                <p class="mb-6 mx-2">{{$exception->getMessage() ?: 'Forbidden'}}</p>

                <!-- License Form -->
                <div class="mb-4 w-100">
                    <form action="{{ $settings['domain'] }}/license-verify" method="POST" class="mb-5">
                        <div class="input-group">
                            <input type="text" class="form-control" name="license_code" placeholder="{{__('l.Enter License Code')}}" required>
                            <button type="submit" class="btn btn-primary">{{__('l.Verify License')}}</button>
                        </div>
                    </form>
                    <div class="text-center">
                        <a href="{{ config('app.companyUrl') }}" class="btn btn-success">{{__('l.Buy Now')}}</a>
                    </div>
                </div>

                <div class="mt-6">
                    <img src="{{ asset('assets/themes/default/img/illustrations/page-misc-error-light.png') }}" alt="page-misc-error-light"
                        width="500" class="img-fluid" data-app-light-img="illustrations/page-misc-error-light.png"
                        data-app-dark-img="illustrations/page-misc-error-dark.png" />
                </div>
            </div>
        </div>
        <!-- /Error -->

        <!-- / Content -->



        <script src="{{ asset('assets/themes/default/vendor/libs/jquery/jquery.js') }}"></script>

        <script src="{{ asset('assets/themes/default/vendor/libs/popper/popper.js') }}"></script>
        <script src="{{ asset('assets/themes/default/vendor/js/bootstrap.js') }}"></script>
        <script src="{{ asset('assets/themes/default/vendor/libs/@algolia/autocomplete-js.js') }}"></script>



        <script src="{{ asset('assets/themes/default/vendor/libs/pickr/pickr.js') }}"></script>



        <script src="{{ asset('assets/themes/default/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>


        <script src="{{ asset('assets/themes/default/vendor/libs/hammer/hammer.js') }}"></script>

        <script src="{{ asset('assets/themes/default/vendor/libs/i18n/i18n.js') }}"></script>


        <script src="{{ asset('assets/themes/default/vendor/js/menu.js') }}"></script>

        <!-- endbuild -->
        <!-- Main JS -->

        <script src="{{ asset('assets/themes/default/js/main.js') }}"></script>

    </body>

</html>
