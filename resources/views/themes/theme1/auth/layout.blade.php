<script>
    window.primaryColor = "{{ $settings['primary_color'] ?? '#FFAB1D' }}";
</script>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

        <title>@yield('title') | {{ $settings['name'] }}</title>

        <meta name="description" content="@yield('description')" />

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset($settings['favicon']) }}" />
        <!--! BEGIN: Bootstrap CSS-->
        <link rel="stylesheet" type="text/css" href="{{asset('assets/themes/default/css/bootstrap.min.css')}}">
        <!--! END: Bootstrap CSS-->
        <!--! BEGIN: Vendors CSS-->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/themes/default/vendors/css/vendors.min.css') }}" />
        <script src="https://kit.fontawesome.com/3bcd125e2e.js" crossorigin="anonymous"></script>
        <!--! END: Vendors CSS-->
        <!--! BEGIN: Custom CSS-->
        <link rel="stylesheet" type="text/css" href="{{asset('assets/themes/default/css/theme.min.css')}}">
        <!-- google recaptcha -->
        <script async src="https://www.google.com/recaptcha/api.js?hl={{ app()->getLocale() }}"></script>
        <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
    
    </head>

    <body>

        @yield('content')

        <!-- vendors.min.js {always must need to be top} -->
        <script src="{{asset('assets/themes/default/vendors/js/vendors.min.js')}}"></script>
        <!--! BEGIN: Apps Init  !-->
        <script src="{{asset('assets/themes/default/js/common-init.min.js')}}"></script>
        <!-- Page JS -->
        @yield('page-scripts')
    </body>

</html>
