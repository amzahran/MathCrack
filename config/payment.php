<?php

return [
    /*
    |--------------------------------------------------------------------------
    | إعدادات الدفع
    |--------------------------------------------------------------------------
    */

    'monthly_subscription_price' => env('MONTHLY_SUBSCRIPTION_PRICE', 50),

    'course_discount_percentage' => env('COURSE_DISCOUNT_PERCENTAGE', 20),

    'currency' => env('PAYMENT_CURRENCY', 'ج.م'),

    'payment_gateway' => env('PAYMENT_GATEWAY', 'paymob'), // paymob, fawry, etc.

    /*
    |--------------------------------------------------------------------------
    | إعدادات بوابة الدفع
    |--------------------------------------------------------------------------
    */

    'gateways' => [
        'paymob' => [
            'api_key' => env('PAYMOB_API_KEY'),
            'integration_id' => env('PAYMOB_INTEGRATION_ID'),
            'hmac' => env('PAYMOB_HMAC'),
        ],

        'fawry' => [
            'merchant_code' => env('FAWRY_MERCHANT_CODE'),
            'security_key' => env('FAWRY_SECURITY_KEY'),
        ]
    ]
];
