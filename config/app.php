<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'MAINTENANCE_MODE' => env('MAINTENANCE_MODE', false),
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],


    'recaptcha' => [
        'key' => env('RECAPTCHA_SITE_KEY'),
        'secret' => env('RECAPTCHA_SECRET_KEY'),
    ],

    'secretCode' => 'XOedWo7DuxHXpvXhC1psw9FlEc6eWznAoMf9mZ',
    'model' => 'starter',
    'version' => '1.0.0',
    'companyUrl' => 'https://dp-soft.com',

    // 'checkLicenseUrl' => 'https://dp-soft.com/api/license-check',
    // 'exchangeCurrencyUrl' => 'https://dp-soft.com/api/exchange-currency',
    // 'integrateSmsUrl' => 'https://dp-soft.com/api/sms',
    // 'updateSystemUrl' => 'https://dp-soft.com/api/update',
    // 'upgradeEditionUrl' => 'https://dp-soft.com/api/upgrade-edition',
    // 'renewLicenseUrl' => 'https://dp-soft.com/api/renew-license',
    // 'renewHostUrl' => 'https://dp-soft.com/api/renew-host',
    'checkLicenseUrl' => 'http://127.0.0.1:8000/api/license-check',
    'exchangeCurrencyUrl' => 'http://127.0.0.1:8000/api/exchange-currency',
    'integrateSmsUrl' => 'http://127.0.0.1:8000/api/sms',
    'updateSystemUrl' => 'http://127.0.0.1:8000/api/update',
    'upgradeEditionUrl' => 'http://127.0.0.1:8000/api/upgrade-edition',
    'renewLicenseUrl' => 'http://127.0.0.1:8000/api/renew-license',
    'renewHostUrl' => 'http://127.0.0.1:8000/api/renew-host',
    
    'technicalSupportUrl' => 'https://dp-soft.com/support',
    'docsSupportUrl' => 'https://dp-soft.com/docs/xxx',
    'licenseGuideUrl' => 'https://dp-soft.com/license',
    'moreSystemsUrl' => 'https://dp-soft.com/laravel',
    'socialAuthGuideUrl' => 'https://dp-soft.com/blog/social-auth-setup',
    'recaptchaGuideUrl' => 'https://dp-soft.com/blog/recaptcha-setup',
    'googleAnalyticsGuideUrl' => 'https://dp-soft.com/blog/google-analytics-setup',
];
